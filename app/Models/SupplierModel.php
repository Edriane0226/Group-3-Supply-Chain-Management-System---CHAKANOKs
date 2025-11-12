<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'suppliers';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'supplier_name',
        'contact_info',
        'address',
        'terms',
        'password',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    //Get ang Active Suppliers
    public function getActiveSuppliers(): array
    {
        return $this->where('status', 'active')
                    ->orderBy('supplier_name', 'ASC')
                    ->findAll();
    }

    // Para sa search sa form
    public function searchSuppliers(string $keyword): array
    {
        return $this->like('supplier_name', $keyword)
                    ->orLike('contact_info', $keyword)
                    ->orLike('address', $keyword)
                    ->findAll();
    }

    // Kuha Supplier detail with order history
    public function getSupplierWithOrders(int $supplierId): ?array
    {
        return $this->select('suppliers.*, COUNT(purchase_orders.id) as total_orders,
                              SUM(purchase_orders.total_amount) as total_spent')
                    ->join('purchase_orders', 'purchase_orders.supplier_id = suppliers.id', 'left')
                    ->where('suppliers.id', $supplierId)
                    ->groupBy('suppliers.id')
                    ->first();
    }

    // Calculate supplier performance metrics
    public function calculatePerformanceMetrics(int $supplierId): array
    {
        // Get total orders and deliveries
        $metrics = $this->select('
                COUNT(DISTINCT po.id) as total_orders,
                COUNT(DISTINCT CASE WHEN po.status = "delivered" THEN po.id END) as total_deliveries,
                COUNT(DISTINCT CASE WHEN po.status = "delivered" AND po.actual_delivery_date <= po.expected_delivery_date THEN po.id END) as on_time_deliveries
            ')
            ->join('purchase_orders po', 'po.supplier_id = suppliers.id', 'left')
            ->where('suppliers.id', $supplierId)
            ->first();

        $totalOrders = (int)($metrics['total_orders'] ?? 0);
        $totalDeliveries = (int)($metrics['total_deliveries'] ?? 0);
        $onTimeDeliveries = (int)($metrics['on_time_deliveries'] ?? 0);

        // Calculate on-time delivery rate
        $onTimeRate = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100, 2) : 0.00;

        // Calculate quality rating (placeholder - would need actual quality feedback)
        $qualityRating = 4.5; // Default rating, can be updated with actual feedback system

        return [
            'total_orders' => $totalOrders,
            'total_deliveries' => $totalDeliveries,
            'on_time_delivery_rate' => $onTimeRate,
            'quality_rating' => $qualityRating,
        ];
    }

    // Update supplier performance metrics
    public function updatePerformanceMetrics(int $supplierId): bool
    {
        $metrics = $this->calculatePerformanceMetrics($supplierId);

        return $this->update($supplierId, [
            'total_orders' => $metrics['total_orders'],
            'total_deliveries' => $metrics['total_deliveries'],
            'on_time_delivery_rate' => $metrics['on_time_delivery_rate'],
            'quality_rating' => $metrics['quality_rating'],
        ]);
    }

    // Get suppliers with performance metrics for admin
    public function getSuppliersWithPerformance(): array
    {
        return $this->select('suppliers.*, branches.branch_name')
                    ->join('branches', 'branches.id = suppliers.branch_id', 'left')
                    ->orderBy('suppliers.supplier_name', 'ASC')
                    ->findAll();
    }

    // CRUD methods for Central Office Admin
    public function createSupplier(array $data): int
    {
        $insertData = [
            'supplier_name' => $data['supplier_name'],
            'contact_info' => $data['contact_info'] ?? null,
            'address' => $data['address'] ?? null,
            'terms' => $data['terms'] ?? null,
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->insert($insertData);
        return $this->insertID();
    }

    public function updateSupplier(int $supplierId, array $data): bool
    {
        $updateData = [];
        $allowedFields = ['supplier_name', 'contact_info', 'address', 'terms', 'status'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (!empty($updateData)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
            return $this->update($supplierId, $updateData);
        }

        return false;
    }

    public function deactivateSupplier(int $supplierId): bool
    {
        return $this->update($supplierId, [
            'status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
