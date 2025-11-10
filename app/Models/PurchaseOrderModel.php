<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table            = 'purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'supplier_id',
        'status',
        'total_amount',
        'approved_by',
        'approved_at',
        'expected_delivery_date',
        'actual_delivery_date',
        'tracking_number',
        'delivery_notes',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $afterFind      = [];

    // Create purchase order from approved purchase request
    public function createFromPurchaseRequest(int $requestId, int $approvedBy): ?int
    {
        $purchaseRequestModel = new PurchaseRequestModel();
        $request = $purchaseRequestModel->find($requestId);

        if (!$request || $request['status'] !== 'approved') {
            return null;
        }

        $poData = [
            'branch_id' => $request['branch_id'],
            'supplier_id' => $request['supplier_id'],
            'status' => 'approved',
            'total_amount' => 0.00, // Will be calculated based on items
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'expected_delivery_date' => date('Y-m-d', strtotime('+7 days')), // Default 7 days
        ];

        $this->insert($poData);
        $poId = $this->insertID();

        // Create purchase order items from request (placeholder - would need actual itemization)
        // For now, create a single item based on the request
        $this->db->table('purchase_order_items')->insert([
            'purchase_order_id' => $poId,
            'stock_in_id' => null, // Would need to link to actual stock item
            'quantity' => $request['quantity'],
            'unit_price' => 0.00, // Would need pricing logic
            'subtotal' => 0.00,
        ]);

        return $poId;
    }

    // Get purchase orders with relations
    public function getWithRelations(?int $branchId = null, ?string $status = null): array
    {
        $builder = $this->select('purchase_orders.*, branches.branch_name, suppliers.supplier_name, users.first_name, users.last_name')
                        ->join('branches', 'branches.id = purchase_orders.branch_id')
                        ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                        ->join('users', 'users.id = purchase_orders.approved_by', 'left')
                        ->orderBy('purchase_orders.created_at', 'DESC');

        if ($branchId) {
            $builder->where('purchase_orders.branch_id', $branchId);
        }

        if ($status) {
            $builder->where('purchase_orders.status', $status);
        }

        return $builder->findAll();
    }

    // Update purchase order status
    public function updateStatus(int $poId, string $status, ?int $userId = null): bool
    {
        $updateData = ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')];

        if ($status === 'approved' && $userId) {
            $updateData['approved_by'] = $userId;
            $updateData['approved_at'] = date('Y-m-d H:i:s');
        }

        if ($status === 'delivered') {
            $updateData['actual_delivery_date'] = date('Y-m-d');
        }

        return $this->update($poId, $updateData);
    }

    // Get purchase order details with items
    public function getDetails(int $poId): ?array
    {
        $po = $this->find($poId);
        if (!$po) {
            return null;
        }

        $items = $this->db->table('purchase_order_items')
                          ->select('purchase_order_items.*, stock_in.item_name, stock_in.unit')
                          ->join('stock_in', 'stock_in.id = purchase_order_items.stock_in_id', 'left')
                          ->where('purchase_order_id', $poId)
                          ->get()
                          ->getResultArray();

        $po['items'] = $items;
        return $po;
    }

    // Get pending purchase orders for logistics
    public function getPendingForLogistics(): array
    {
        return $this->select('purchase_orders.*, branches.branch_name, suppliers.supplier_name, suppliers.contact_info')
                    ->join('branches', 'branches.id = purchase_orders.branch_id')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                    ->whereIn('purchase_orders.status', ['approved', 'in_transit'])
                    ->orderBy('purchase_orders.expected_delivery_date', 'ASC')
                    ->findAll();
    }
}
