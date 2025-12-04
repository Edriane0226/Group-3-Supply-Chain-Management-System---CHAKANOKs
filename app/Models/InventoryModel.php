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
            GROUP BY 
                si.item_type_id, 
                si.branch_id, 
                si.item_name, 
                si.unit, 
                si.expiry_date, 
                si.barcode
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
        if ($branchId > 0) {
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
    // By branch total inventory value
    public function getTotalInventoryValue(int $branchId): float
    {
        $query = "
            SELECT
                SUM((si.quantity - IFNULL(so.quantity, 0)) * si.price) AS total_value
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
            WHERE si.branch_id = ?
            GROUP BY si.branch_id
        ";

        $result = $this->db->query($query, [$branchId])->getRowArray();

        return $result['total_value'] ?? 0.0;
    }

    public function getOverallInventoryValue(): float
    {
        $query = "
            SELECT
                SUM((si.quantity - IFNULL(so.quantity, 0)) * si.price) AS total_value
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
        ";

        $result = $this->db->query($query)->getRowArray();

        return $result['total_value'] ?? 0.0;
    }

    public function getOverallExpiredValue(): float
    {
        $query = "
            SELECT
                SUM((si.quantity - IFNULL(so.quantity, 0)) * si.price) AS expired_value
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
            WHERE si.expiry_date < CURDATE()
        ";

        $result = $this->db->query($query)->getRowArray();

        return $result['expired_value'] ?? 0.0;
    }

    /**
     * Get wastage breakdown by branch
     */
    public function getWastageByBranch(): array
    {
        $query = "
            SELECT
                branches.id,
                branches.branch_name,
                COUNT(DISTINCT CASE WHEN si.expiry_date < CURDATE() THEN si.id END) as expired_items_count,
                SUM(CASE WHEN si.expiry_date < CURDATE() THEN (si.quantity - IFNULL(so.quantity, 0)) * si.price ELSE 0 END) as expired_value,
                COUNT(DISTINCT CASE WHEN so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%' THEN so.id END) as damaged_items_count,
                SUM(CASE WHEN so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%' THEN so.quantity * si.price ELSE 0 END) as damaged_value
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
            JOIN branches ON branches.id = si.branch_id
            WHERE (si.expiry_date < CURDATE() OR so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%')
            GROUP BY branches.id, branches.branch_name
            ORDER BY (SUM(CASE WHEN si.expiry_date < CURDATE() THEN (si.quantity - IFNULL(so.quantity, 0)) * si.price ELSE 0 END) + SUM(CASE WHEN so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%' THEN so.quantity * si.price ELSE 0 END)) DESC
        ";

        return $this->db->query($query)->getResultArray();
    }

    /**
     * Get wastage breakdown by item
     */
    public function getWastageByItem(?int $branchId = null, int $limit = 10): array
    {
        $whereClause = "WHERE (si.expiry_date < CURDATE() OR so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%')";
        $params = [];

        if ($branchId) {
            $whereClause .= " AND si.branch_id = ?";
            $params[] = $branchId;
        }

        $query = "
            SELECT
                si.item_name,
                si.branch_id,
                branches.branch_name,
                SUM(CASE WHEN si.expiry_date < CURDATE() THEN (si.quantity - IFNULL(so.quantity, 0)) ELSE 0 END) as expired_quantity,
                SUM(CASE WHEN si.expiry_date < CURDATE() THEN (si.quantity - IFNULL(so.quantity, 0)) * si.price ELSE 0 END) as expired_value,
                SUM(CASE WHEN so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%' THEN so.quantity ELSE 0 END) as damaged_quantity,
                SUM(CASE WHEN so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%' THEN so.quantity * si.price ELSE 0 END) as damaged_value
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
            LEFT JOIN branches ON branches.id = si.branch_id
            {$whereClause}
            GROUP BY si.item_name, si.branch_id, branches.branch_name
            HAVING (SUM(CASE WHEN si.expiry_date < CURDATE() THEN (si.quantity - IFNULL(so.quantity, 0)) * si.price ELSE 0 END) + SUM(CASE WHEN so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%' THEN so.quantity * si.price ELSE 0 END)) > 0
            ORDER BY (SUM(CASE WHEN si.expiry_date < CURDATE() THEN (si.quantity - IFNULL(so.quantity, 0)) * si.price ELSE 0 END) + SUM(CASE WHEN so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%' THEN so.quantity * si.price ELSE 0 END)) DESC
            LIMIT {$limit}
        ";

        return $this->db->query($query, $params)->getResultArray();
    }

    /**
     * Get wastage by reason (expired, damaged, etc.)
     */
    public function getWastageByReason(): array
    {
        // Expired items
        $expiredQuery = "
            SELECT
                'expired' as reason,
                COUNT(DISTINCT si.id) as item_count,
                SUM((si.quantity - IFNULL(so.quantity, 0)) * si.price) as total_value
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
            WHERE si.expiry_date < CURDATE()
        ";

        $expired = $this->db->query($expiredQuery)->getRowArray();

        // Damaged items
        $damagedQuery = "
            SELECT
                'damaged' as reason,
                COUNT(DISTINCT so.id) as item_count,
                SUM(so.quantity * si.price) as total_value
            FROM stock_out so
            JOIN stock_in si
                ON so.item_type_id = si.item_type_id
                AND so.branch_id = si.branch_id
                AND so.item_name = si.item_name
            WHERE so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%'
        ";

        $damaged = $this->db->query($damagedQuery)->getRowArray();

        return [
            'expired' => [
                'reason' => 'expired',
                'item_count' => (int)($expired['item_count'] ?? 0),
                'total_value' => (float)($expired['total_value'] ?? 0),
            ],
            'damaged' => [
                'reason' => 'damaged',
                'item_count' => (int)($damaged['item_count'] ?? 0),
                'total_value' => (float)($damaged['total_value'] ?? 0),
            ],
        ];
    }

    /**
     * Get wastage trends (monthly wastage for last N months)
     */
    public function getWastageTrends(int $months = 6): array
    {
        $startDate = date('Y-m-01', strtotime("-{$months} months"));

        $query = "
            SELECT
                DATE_FORMAT(so.created_at, '%Y-%m') as month,
                COUNT(DISTINCT so.id) as wastage_count,
                SUM(so.quantity * si.price) as wastage_value
            FROM stock_out so
            JOIN stock_in si
                ON so.item_type_id = si.item_type_id
                AND so.branch_id = si.branch_id
                AND so.item_name = si.item_name
            WHERE so.created_at >= ?
                AND (so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%' OR so.reason LIKE '%expired%')
            GROUP BY DATE_FORMAT(so.created_at, '%Y-%m')
            ORDER BY month ASC
        ";

        return $this->db->query($query, [$startDate])->getResultArray();
    }

    /**
     * Get total wastage summary
     */
    public function getWastageSummary(): array
    {
        $expiredValue = $this->getOverallExpiredValue();

        $damagedQuery = "
            SELECT
                SUM(so.quantity * si.price) as total_value,
                COUNT(DISTINCT so.id) as item_count
            FROM stock_out so
            JOIN stock_in si
                ON so.item_type_id = si.item_type_id
                AND so.branch_id = si.branch_id
                AND so.item_name = si.item_name
            WHERE so.reason LIKE '%damage%' OR so.reason LIKE '%damaged%'
        ";

        $damaged = $this->db->query($damagedQuery)->getRowArray();

        $totalWastage = $expiredValue + (float)($damaged['total_value'] ?? 0);

        return [
            'total_wastage_value' => $totalWastage,
            'expired_value' => $expiredValue,
            'damaged_value' => (float)($damaged['total_value'] ?? 0),
            'expired_items_count' => 0, // Can be calculated separately if needed
            'damaged_items_count' => (int)($damaged['item_count'] ?? 0),
        ];
    }

    // Deliveries methods

    // Create a new delivery
    public function createDelivery(array $data): int
    {
        $insertData = [
            'supplier_name' => $data['supplier_name'],
            'branch_id' => $data['branch_id'],
            'delivery_date' => $data['delivery_date'],
            'status' => 'Pending',
            'remarks' => $data['remarks'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('deliveries')->insert($insertData);
        return $this->db->insertID();
    }

    // Add items to a delivery
    public function addDeliveryItems(int $deliveryId, array $items): bool
    {
        $insertData = [];
        foreach ($items as $item) {
            $insertData[] = [
                'delivery_id' => $deliveryId,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'expiry_date' => $item['expiry_date'] ?? null,
                'barcode' => $item['barcode'] ?? null,
                'item_type_id' => $item['item_type_id'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        return $this->db->table('delivery_items')->insertBatch($insertData);
    }

    // Get deliveries for a branch
    public function getDeliveries(int $branchId, ?string $status = null): array
    {
        $builder = $this->db->table('deliveries')
            ->select('deliveries.*, COUNT(di.item_name) AS total_items')
            ->join('delivery_items di', 'deliveries.id = di.delivery_id', 'left')
            ->where('deliveries.branch_id', $branchId)
            ->groupBy('deliveries.id')
            ->orderBy('deliveries.delivery_date', 'DESC');

        if ($status) {
            $builder->where('deliveries.status', $status);
        }

        return $builder->get()->getResultArray();
    }

    // Get delivery details with items
    public function getDeliveryDetails(int $deliveryId): ?array
    {
        $delivery = $this->db->table('deliveries')->where('id', $deliveryId)->get()->getRowArray();
        if (!$delivery) {
            return null;
        }

        $items = $this->db->table('delivery_items')->where('delivery_id', $deliveryId)->get()->getResultArray();
        $delivery['items'] = $items;

        return $delivery;
    }

    // Mark delivery as received and insert items into stock_in
    public function receiveDelivery(int $deliveryId): bool
    {
        $this->db->transStart();

        // Update delivery status
        $this->db->table('deliveries')
            ->where('id', $deliveryId)
            ->update(['status' => 'Received', 'updated_at' => date('Y-m-d H:i:s')]);

        // Get delivery items
        $items = $this->db->table('delivery_items')->where('delivery_id', $deliveryId)->get()->getResultArray();

        // Get delivery branch
        $delivery = $this->db->table('deliveries')->select('branch_id')->where('id', $deliveryId)->get()->getRowArray();

        // Insert into stock_in
        $stockInData = [];
        foreach ($items as $item) {
            $stockInData[] = [
                'item_type_id' => $item['item_type_id'],
                'branch_id' => $delivery['branch_id'],
                'item_name' => $item['item_name'],
                'category' => null, // Can be added later if needed
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'expiry_date' => $item['expiry_date'],
                'barcode' => $item['barcode'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($stockInData)) {
            $this->db->table('stock_in')->insertBatch($stockInData);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    // Cancel delivery
    public function cancelDelivery(int $deliveryId): bool
    {
        return $this->db->table('deliveries')
            ->where('id', $deliveryId)
            ->update(['status' => 'Cancelled', 'updated_at' => date('Y-m-d H:i:s')]);
    }

    // Update delivery status with notifications
    public function updateDeliveryStatus(int $deliveryId, string $status, ?int $userId = null): bool
    {
        $oldDelivery = $this->db->table('deliveries')->where('id', $deliveryId)->get()->getRowArray();
        if (!$oldDelivery) {
            return false;
        }

        $updateData = ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')];

        if ($status === 'Delivered') {
            $updateData['actual_delivery_time'] = date('Y-m-d H:i:s');
        }

        $result = $this->db->table('deliveries')
                          ->where('id', $deliveryId)
                          ->update($updateData);

        if ($result) {
            // Create notifications for relevant users
            $this->notifyDeliveryStatusChange($deliveryId, $oldDelivery['status'], $status);
        }

        return $result;
    }

    // Notify users about delivery status changes
    private function notifyDeliveryStatusChange(int $deliveryId, string $oldStatus, string $newStatus): void
    {
        $notificationModel = new \App\Models\NotificationModel();

        // Get branch users for notifications
        $delivery = $this->db->table('deliveries')
                            ->select('deliveries.*, branches.branch_name')
                            ->join('branches', 'branches.id = deliveries.branch_id')
                            ->where('deliveries.id', $deliveryId)
                            ->get()
                            ->getRowArray();

        if (!$delivery) {
            return;
        }

        // Get users from the branch
        $branchUsers = $this->db->table('users')
                               ->where('branch_id', $delivery['branch_id'])
                               ->get()
                               ->getResultArray();

        $userIds = array_column($branchUsers, 'id');

        // Also notify central office admins
        $centralAdmins = $this->db->table('users')
                                 ->where('role', 'Central Office Admin')
                                 ->get()
                                 ->getResultArray();

        $userIds = array_merge($userIds, array_column($centralAdmins, 'id'));

        $notificationModel->notifyStatusChange('delivery', $deliveryId, $oldStatus, $newStatus, $userIds);
    }

    // Get deliveries with enhanced tracking info
    public function getDeliveriesWithTracking(int $branchId, ?string $status = null): array
    {
        $builder = $this->db->table('deliveries')
            ->select('deliveries.*, COUNT(di.item_name) AS total_items, ds.scheduled_date, ds.scheduled_time, ds.status as schedule_status')
            ->join('delivery_items di', 'deliveries.id = di.delivery_id', 'left')
            ->join('delivery_schedules ds', 'deliveries.id = ds.delivery_id', 'left')
            ->where('deliveries.branch_id', $branchId)
            ->groupBy('deliveries.id')
            ->orderBy('deliveries.delivery_date', 'DESC');

        if ($status) {
            $builder->where('deliveries.status', $status);
        }

        return $builder->get()->getResultArray();
    }

    // Get delivery performance metrics
    public function getDeliveryPerformanceMetrics(int $branchId): array
    {
        $query = "
            SELECT
                COUNT(*) as total_deliveries,
                SUM(CASE WHEN status = 'Delivered' THEN 1 ELSE 0 END) as completed_deliveries,
                SUM(CASE WHEN status = 'Delivered' AND actual_delivery_time <= estimated_eta THEN 1 ELSE 0 END) as on_time_deliveries,
                AVG(CASE WHEN status = 'Delivered' THEN TIMESTAMPDIFF(MINUTE, delivery_date, actual_delivery_time) END) as avg_delivery_time_minutes
            FROM deliveries
            WHERE branch_id = ?
        ";

        $result = $this->db->query($query, [$branchId])->getRowArray();

        $totalDeliveries = (int)($result['total_deliveries'] ?? 0);
        $completedDeliveries = (int)($result['completed_deliveries'] ?? 0);
        $onTimeDeliveries = (int)($result['on_time_deliveries'] ?? 0);

        return [
            'total_deliveries' => $totalDeliveries,
            'completed_deliveries' => $completedDeliveries,
            'completion_rate' => $totalDeliveries > 0 ? round(($completedDeliveries / $totalDeliveries) * 100, 2) : 0,
            'on_time_rate' => $completedDeliveries > 0 ? round(($onTimeDeliveries / $completedDeliveries) * 100, 2) : 0,
            'avg_delivery_time_hours' => round(($result['avg_delivery_time_minutes'] ?? 0) / 60, 2),
        ];
    }
}
