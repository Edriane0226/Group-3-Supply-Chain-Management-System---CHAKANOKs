<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table      = 'inventory';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'branch_id', 'item_name', 'category', 'type', 'quantity', 'unit',
        'expiry_date', 'barcode', 'reorder_level', 'price', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getBranchSummary(int $branchId): array
    {
        $builder = $this->builder();

        $totals = $builder
            ->select('COUNT(id) AS total_skus, COALESCE(SUM(quantity),0) AS total_quantity')
            ->where('branch_id', $branchId)
            ->get()->getRowArray() ?? ['total_skus' => 0, 'total_quantity' => 0];

        $lowStock = $this->where('branch_id', $branchId)
            ->where('quantity <= reorder_level')
            ->orderBy('quantity', 'ASC')
            ->findAll(10);

        $expiringSoon = $this->where('branch_id', $branchId)
            ->where('expiry_date IS NOT NULL')
            ->where('expiry_date <=', date('Y-m-d', strtotime('+30 days')))
            ->orderBy('expiry_date', 'ASC')
            ->findAll(10);

        return [
            'totals' => $totals,
            'lowStock' => $lowStock,
            'expiringSoon' => $expiringSoon,
        ];
    }

    // Calculate total inventory value (quantity * price)
    public function getInventoryValue(int $branchId): float
    {
        $builder = $this->builder();
        $result = $builder
            ->select('SUM(quantity * price) AS total_value')
            ->where('branch_id', $branchId)
            ->get()
            ->getRowArray();

        return (float) ($result['total_value'] ?? 0);
    }

    // Get a simple breakdown of inventory by category
    public function getInventoryLevels(int $branchId): array
    {
        return $this->select('category, SUM(quantity) AS total_quantity')
            ->where('branch_id', $branchId)
            ->groupBy('category')
            ->findAll();
    }

    // Return items that are low in stock
    public function getLowStockAlerts(int $branchId): array
    {
        return $this->where('branch_id', $branchId)
            ->where('quantity <= reorder_level')
            ->orderBy('quantity', 'ASC')
            ->findAll(5); // limit to 5 items for dashboard
    }

    public function adjustStock(int $id, int $delta): bool
    {
        $item = $this->find($id);
        if (!$item) {
            return false;
        }
        $newQty = max(0, (int)$item['quantity'] + (int)$delta);
        return $this->update($id, ['quantity' => $newQty]);
    }

    public function findByBarcode(string $barcode, ?int $branchId = null): ?array
    {
        $builder = $this->where('barcode', $barcode);
        if ($branchId !== null) {
            $builder = $this->where('branch_id', $branchId);
        }
        $item = $builder->first();
        return $item ?: null;
    }
}
