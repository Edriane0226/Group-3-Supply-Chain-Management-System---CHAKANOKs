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
}
