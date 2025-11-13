<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseRequestModel extends Model
{
    protected $table            = 'purchase_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'branch_id',
        'supplier_id',
        'item_name',
        'quantity',
        'unit',
        'price',
        'description',
        'remarks',
        'request_date',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Join Kuhaon ang branch name and supplier name kay id raman ang gi fk sa purchase_requests table
    public function withRelations()
    {
        return $this->select('purchase_requests.*, branches.branch_name, suppliers.supplier_name as supplier_name')
                    ->join('branches', 'branches.id = purchase_requests.branch_id')
                    ->join('suppliers', 'suppliers.id = purchase_requests.supplier_id');
    }

    // Count pending PRs for a branch
    public function getPendingPRs(int $branchId): int
    {
        return $this->where('branch_id', $branchId)
                    ->where('status', 'pending')
                    ->countAllResults();
    }

    // Fetch all requests for a specific branch with relations
    public function findByBranchWithRelations(int $branchId): array
    {
        return $this->withRelations()
                    ->where('purchase_requests.branch_id', $branchId)
                    ->orderBy('purchase_requests.created_at', 'DESC')
                    ->findAll();
    }

    // Fetch all requests across branches (for Central Office Admin)
    public function findAllWithRelations(): array
    {
        return $this->withRelations()
                    ->orderBy('purchase_requests.created_at', 'DESC')
                    ->findAll();
    }

    // Approve purchase request
    public function approveRequest(int $requestId, int $approvedBy): bool
    {
        $result = $this->update($requestId, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($result) {
            // Create purchase order from approved request
            $purchaseOrderModel = new \App\Models\PurchaseOrderModel();
            $poId = $purchaseOrderModel->createFromPurchaseRequest($requestId, $approvedBy);

            // Notify relevant users
            $this->notifyApproval($requestId, $approvedBy, $poId);
        }

        return $result;
    }

    // Notify users about approval
    private function notifyApproval(int $requestId, int $approvedBy, ?int $poId = null): void
    {
        $notificationModel = new \App\Models\NotificationModel();

        // Get request details
        $request = $this->find($requestId);
        if (!$request) {
            return;
        }

        // Get branch users for notifications
        $branchUsers = $this->db->table('users')
                               ->where('branch_id', $request['branch_id'])
                               ->get()
                               ->getResultArray();

        $userIds = array_column($branchUsers, 'id');

        // Also notify central office admins
        $centralAdmins = $this->db->table('users')
                                 ->where('role', 'Central Office Admin')
                                 ->get()
                                 ->getResultArray();

        $userIds = array_merge($userIds, array_column($centralAdmins, 'id'));

        // Notify Logistics Coordinators about new approved PO
        $logisticsCoordinators = $this->db->table('users')
                                         ->where('role', 'Logistics Coordinator')
                                         ->get()
                                         ->getResultArray();

        $coordinatorIds = array_column($logisticsCoordinators, 'id');

        // Notify supplier about new purchase order
        $supplierNotification = [
            'user_id' => $request['supplier_id'], // Supplier ID is used as user_id for suppliers
            'type' => 'in_app',
            'title' => 'New Purchase Order Assigned',
            'message' => 'A new purchase order has been assigned to you from ' . $request['branch_name'] ?? 'Branch',
            'reference_type' => 'purchase_order',
            'reference_id' => $poId,
        ];
        $notificationModel->createNotification($supplierNotification);

        $notificationModel->notifyStatusChange('purchase_request', $requestId, 'pending', 'approved', $userIds);

        // Send specific notification to logistics coordinators
        $notificationModel->notifyLogisticsCoordinator('new_po_ready', $requestId, $coordinatorIds);
    }

    public function rejectRequest(int $requestId, ?int $rejectedBy = null): bool
    {
        $data = [
            'status' => 'rejected',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($rejectedBy !== null) {
            $data['rejected_by'] = $rejectedBy;
            $data['rejected_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($requestId, $data);
    }
}
