<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    // No single table, methods will use stock_in, stock_out, stock_types

    // Get stock types for dropdown
    public function getStockTypes(): array
    {
        return $this->db->table('stock_types')->get()->getResultArray();
    }

    // Insert into stock_in
    public function stockIn(array $data): bool
    {
        return $this->db->table('stock_in')->insert($data);
    }

    // Insert into stock_out
    public function stockOut(array $data): bool
    {
        return $this->db->table('stock_out')->insert($data);
    }

    // Compute current stock balance
    public function getBalance(int $branchId): array
    {
        $query = "
            SELECT
                si.item_name,
                si.item_type_id,
                si.branch_id,
                SUM(si.quantity) - IFNULL(SUM(so.quantity), 0) AS current_stock,
                si.unit,
                si.expiry_date,
                si.barcode
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
            WHERE si.branch_id = ?
            GROUP BY si.item_type_id, si.branch_id, si.item_name
        ";
        return $this->db->query($query, [$branchId])->getResultArray();
    }

    // Low-stock alert (threshold 10)
    public function getLowStockAlerts(int $branchId): array
    {
        $query = "
            SELECT
                si.item_name,
                si.branch_id,
                (SUM(si.quantity) - IFNULL(SUM(so.quantity), 0)) AS available_stock
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_name = so.item_name
                AND si.branch_id = so.branch_id
            WHERE si.branch_id = ?
            GROUP BY si.item_name, si.branch_id
            HAVING available_stock <= 10
        ";
        return $this->db->query($query, [$branchId])->getResultArray();
    }

    // Items near expiry (within 7 days)
    public function getExpiringAlerts(int $branchId): array
    {
        $query = "
            SELECT
                item_name,
                branch_id,
                expiry_date
            FROM stock_in
            WHERE branch_id = ? AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ";
        return $this->db->query($query, [$branchId])->getResultArray();
    }

    // Summary for dashboard
    public function getBranchSummary(int $branchId): array
    {
        $balance = $this->getBalance($branchId);
        $totals = [
            'total_skus' => count($balance),
            'total_quantity' => array_sum(array_column($balance, 'current_stock'))
        ];
        $lowStock = $this->getLowStockAlerts($branchId);
        $expiringSoon = $this->getExpiringAlerts($branchId);

        return [
            'totals' => $totals,
            'lowStock' => $lowStock,
            'expiringSoon' => $expiringSoon,
        ];
    }

    // Find by barcode (from stock_in)
    public function findByBarcode(string $barcode, ?int $branchId = null): ?array
    {
        $builder = $this->db->table('stock_in')->where('barcode', $barcode);
        if ($branchId !== null) {
            $builder->where('branch_id', $branchId);
        }
        $item = $builder->get()->getRowArray();

        if ($item) {
            // Calculate current stock for this item
            $query = "
                SELECT
                    SUM(si.quantity) - IFNULL(SUM(so.quantity), 0) AS available_stock,
                    si.price,
                    si.unit
                FROM stock_in si
                LEFT JOIN stock_out so
                    ON si.item_type_id = so.item_type_id
                    AND si.branch_id = so.branch_id
                    AND si.item_name = so.item_name
                WHERE si.item_name = ? AND si.branch_id = ?
                GROUP BY si.item_name, si.branch_id
            ";
            $stockInfo = $this->db->query($query, [$item['item_name'], $item['branch_id']])->getRowArray();

            return [
                'item_name' => $item['item_name'],
                'available_stock' => $stockInfo['available_stock'] ?? 0,
                'unit' => $item['unit'],
                'price' => $item['price'],
                'expiry_date' => $item['expiry_date'],
                'barcode' => $item['barcode'],
            ];
        }

        return null;
    }

    // Get current stock balance (alias for getBalance)
    public function getStockBalance(int $branchId = 0): array
    {
        return $this->getBalance($branchId);
    }

    // Add stock in (insert into stock_in)
    public function addStockIn(array $data): bool
    {
        $insertData = [
            'item_type_id' => $data['item_type_id'],
            'branch_id' => $data['branch_id'],
            'item_name' => $data['item_name'],
            'category' => $data['category'] ?? null,
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'price' => $data['price'],
            'expiry_date' => $data['expiry_date'] ?? null,
            'barcode' => $data['barcode'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->table('stock_in')->insert($insertData);
    }

    // Add stock out (insert into stock_out)
    public function addStockOut(array $data): bool
    {
        $insertData = [
            'branch_id' => $data['branch_id'],
            'item_type_id' => $data['item_type_id'],
            'item_name' => $data['item_name'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'reason' => $data['reason'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->table('stock_out')->insert($insertData);
    }

    // Get export data for reports
    public function getExportData(int $branchId = 0, ?int $itemTypeId = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = "
            SELECT
                si.item_name,
                SUM(si.quantity) - IFNULL(SUM(so.quantity), 0) AS current_stock,
                si.unit,
                si.expiry_date,
                si.barcode,
                MAX(si.updated_at) as updated_at
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
        ";

        $params = [];
        $where = [];

        if ($branchId > 0) {
            $where[] = "si.branch_id = ?";
            $params[] = $branchId;
        }

        if ($itemTypeId) {
            $where[] = "si.item_type_id = ?";
            $params[] = $itemTypeId;
        }

        if ($dateFrom) {
            $where[] = "si.created_at >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $where[] = "si.created_at <= ?";
            $params[] = $dateTo;
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $query .= " GROUP BY si.item_name, si.branch_id, si.unit, si.expiry_date, si.barcode";

        return $this->db->query($query, $params)->getResultArray();
    }
}
