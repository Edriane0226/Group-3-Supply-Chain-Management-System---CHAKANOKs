<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\PurchaseRequestModel;

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
        'purchase_request_id',
        'status',
        'logistics_status',
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
            'purchase_request_id' => $requestId,
            'status' => 'Pending', // Supplier workflow starts with Pending status
            'total_amount' => 0.00, // Will be calculated based on items
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'expected_delivery_date' => date('Y-m-d', strtotime('+7 days')), // Default 7 days
            'logistics_status' => 'pending_review', // New field for logistics workflow
        ];

        $this->insert($poData);
        $poId = $this->insertID();

        // Create purchase order items from request with actual item details
        $unitPrice = 1.00; // Placeholder unit price to reflect quantity in total_amount
        $this->db->table('purchase_order_items')->insert([
            'purchase_order_id' => $poId,
            'stock_in_id' => null, // Not linked to existing stock yet
            'item_name' => $request['item_name'],
            'quantity' => $request['quantity'],
            'unit' => $request['unit'] ?? 'pcs',
            'description' => $request['description'] ?? '',
            'unit_price' => $unitPrice,
            'subtotal' => ($request['quantity'] ?? 0) * $unitPrice,
        ]);

        // Recalculate total_amount based on items
        $this->recalculateTotalAmount($poId);

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

        // Get items from purchase_order_items table
        $items = $this->db->table('purchase_order_items')
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

    // Get pending purchase orders for logistics workflow
    public function getPendingForLogisticsWorkflow(): array
    {
        return $this->select('purchase_orders.*, branches.branch_name, suppliers.supplier_name, suppliers.contact_info')
                    ->join('branches', 'branches.id = purchase_orders.branch_id')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                    ->whereIn('purchase_orders.status', ['Pending', 'Confirmed', 'Preparing', 'Ready for Pickup'])
                    ->whereIn('purchase_orders.logistics_status', ['pending_review', 'supplier_coordination', 'supplier_coordinated', 'supplier_confirmed', 'supplier_preparing', 'ready_for_pickup', 'delivery_scheduled', 'delivery_started', 'branch_notified'])
                    ->orderBy('purchase_orders.created_at', 'ASC')
                    ->findAll();
    }

    // Recalculate total_amount based on items subtotals
    public function recalculateTotalAmount(int $poId): bool
    {
        $total = $this->db->table('purchase_order_items')
                          ->selectSum('subtotal')
                          ->where('purchase_order_id', $poId)
                          ->get()
                          ->getRow()
                          ->subtotal ?? 0;

        return $this->update($poId, ['total_amount' => $total]);
    }
}
