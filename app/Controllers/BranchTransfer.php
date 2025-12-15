<?php

namespace App\Controllers;

use App\Models\BranchTransferModel;
use App\Models\BranchModel;
use App\Models\InventoryModel;

class BranchTransfer extends BaseController
{
    protected BranchTransferModel $transferModel;
    protected BranchModel $branchModel;
    protected InventoryModel $inventoryModel;
    protected $db;

    public function __construct()
    {
        $this->transferModel = new BranchTransferModel();
        $this->branchModel = new BranchModel();
        $this->inventoryModel = new InventoryModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    /**
     * List all branch transfers
     */
    public function index()
    {
        if ($redirect = $this->authorize('branch_transfers.view')) {
            return $redirect;
        }

        $session = session();
        $branchId = (int)($session->get('branch_id') ?? 0);
        $role = $session->get('role');

        $status = $this->request->getGet('status');

        // Get transfers based on role
        if ($role === 'Central Office Admin') {
            $transfers = $this->transferModel->getTransfersWithRelations(null, $status);
            $pendingCount = $this->transferModel->where('status', 'pending')->countAllResults(false);
        } else {
            $transfers = $this->transferModel->getTransfersWithRelations($branchId, $status);
            $pendingCount = $this->transferModel->where('to_branch_id', $branchId)
                                               ->where('status', 'pending')
                                               ->countAllResults(false);
        }

        $data = [
            'role' => $role,
            'title' => 'Branch Transfers',
            'transfers' => $transfers,
            'currentStatus' => $status,
            'pendingCount' => $pendingCount ?? 0
        ];

        return view('reusables/sidenav', $data) . view('branch_transfer/index', $data);
    }

    /**
     * Show form to create new transfer request
     */
    public function create()
    {
        if ($redirect = $this->authorize('branch_transfers.create')) {
            return $redirect;
        }

        $session = session();
        $branchId = (int)($session->get('branch_id') ?? 0);

        if ($branchId <= 0 && $session->get('role') !== 'Central Office Admin') {
            return redirect()->to(site_url('branch-transfers'))
                           ->with('error', 'Branch not assigned.');
        }

        // Get available inventory for this branch
        if ($branchId > 0) {
            $inventory = $this->inventoryModel->getBalance($branchId);
        } else {
            $inventory = [];
        }
        
        // Filter out items with zero or negative stock
        $availableInventory = array_filter($inventory, function($item) {
            return ($item['current_stock'] ?? 0) > 0;
        });

        // Get other branches (excluding current branch)
        if ($branchId > 0) {
            $otherBranches = $this->branchModel->where('id !=', $branchId)->findAll();
        } else {
            $otherBranches = $this->branchModel->findAll();
        }

        $data = [
            'role' => $session->get('role'),
            'title' => 'Create Transfer Request',
            'inventory' => $availableInventory,
            'branches' => $otherBranches,
            'currentBranchId' => $branchId
        ];

        return view('reusables/sidenav', $data) . view('branch_transfer/create', $data);
    }

    /**
     * Get item details for transfer
     */
    public function getItemDetails()
    {
        if (!$this->canAccess(['branch_transfers.create', 'branch_transfers.view'])) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $itemName = $this->request->getGet('item_name');
        $branchId = (int)($this->request->getGet('branch_id') ?? session()->get('branch_id'));

        if (!$itemName || !$branchId) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing parameters']);
        }

        $inventory = $this->inventoryModel->getBalance($branchId);
        $item = null;

        foreach ($inventory as $invItem) {
            if ($invItem['item_name'] === $itemName) {
                $item = $invItem;
                break;
            }
        }

        if (!$item || ($item['current_stock'] ?? 0) <= 0) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found or out of stock']);
        }

        // Get the most recent stock_in record for this item
        // This will be used as a reference for the transfer
        $stockIn = $this->db->table('stock_in')
            ->where('item_name', $itemName)
            ->where('branch_id', $branchId)
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!$stockIn || !isset($stockIn['id'])) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Stock record not found for this item']);
        }

        return $this->response->setJSON([
            'item_name' => $item['item_name'],
            'available_stock' => $item['current_stock'],
            'unit' => $item['unit'] ?? 'pcs',
            'stock_in_id' => $stockIn['id']
        ]);
    }

    /**
     * Store new transfer request
     */
    public function store()
    {
        if ($redirect = $this->authorize('branch_transfers.create')) {
            return $redirect;
        }

        $session = session();
        $branchId = (int)($session->get('branch_id') ?? 0);
        $userId = (int)($session->get('user_id') ?? 0);

        // Get from_branch_id from POST or session
        $fromBranchId = (int)$this->request->getPost('from_branch_id');
        if (!$fromBranchId) {
            $fromBranchId = $branchId;
        }
        
        // For Central Office Admin, allow selecting from branch
        if ($session->get('role') === 'Central Office Admin' && $this->request->getPost('from_branch_id')) {
            $fromBranchId = (int)$this->request->getPost('from_branch_id');
        }

        // Basic validation rules
        $rules = [
            'from_branch_id' => 'required|integer',
            'to_branch_id' => 'required|integer',
            'item_name' => 'required|max_length[150]',
            'stock_in_id' => 'required|integer',
            'quantity' => 'required|integer|greater_than[0]',
            'unit' => 'permit_empty|max_length[50]',
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessages = [];
            foreach ($errors as $field => $message) {
                if (is_array($message)) {
                    $errorMessages = array_merge($errorMessages, $message);
                } else {
                    $errorMessages[] = $message;
                }
            }
            return redirect()->back()->withInput()->with('errors', $errorMessages);
        }

        $toBranchId = (int)$this->request->getPost('to_branch_id');
        $stockInId = (int)$this->request->getPost('stock_in_id');
        $quantity = (int)$this->request->getPost('quantity');
        $itemName = $this->request->getPost('item_name');

        // Manual validation: check if branches are different
        if ($fromBranchId == $toBranchId) {
            return redirect()->back()->withInput()->with('error', 'Cannot transfer items to the same branch.');
        }

        // Check stock availability
        if (!$this->transferModel->checkStockAvailability($stockInId, $quantity)) {
            return redirect()->back()->withInput()->with('error', 'Insufficient stock available for transfer.');
        }

        $transferData = [
            'from_branch_id' => $fromBranchId,
            'to_branch_id' => $toBranchId,
            'stock_in_id' => $stockInId,
            'item_name' => $itemName,
            'quantity' => $quantity,
            'unit' => $this->request->getPost('unit') ?? 'pcs',
            'status' => 'pending',
            'requested_by' => $userId,
            'notes' => $this->request->getPost('notes')
        ];

        try {
            // Skip model validation since we already validated in controller
            $this->transferModel->skipValidation(true);
            
            if ($this->transferModel->insert($transferData)) {
                return redirect()->to(site_url('branch-transfers'))
                               ->with('success', 'Transfer request created successfully. Waiting for approval.');
            } else {
                $errors = $this->transferModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                log_message('error', 'Branch Transfer Insert Failed: ' . json_encode($errors));
                log_message('error', 'Transfer Data: ' . json_encode($transferData));
                return redirect()->back()->withInput()->with('error', 'Failed to create transfer request: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            log_message('error', 'Branch Transfer Exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            log_message('error', 'Transfer Data: ' . json_encode($transferData));
            return redirect()->back()->withInput()->with('error', 'An error occurred. Please check the logs for details.');
        }
    }

    /**
     * View transfer details
     */
    public function view(int $id)
    {
        if ($redirect = $this->authorize('branch_transfers.view')) {
            return $redirect;
        }

        $allTransfers = $this->transferModel->getTransfersWithRelations();
        $transfer = null;
        
        foreach ($allTransfers as $t) {
            if ($t['id'] == $id) {
                $transfer = $t;
                break;
            }
        }

        if (!$transfer) {
            return redirect()->to(site_url('branch-transfers'))->with('error', 'Transfer not found.');
        }

        // Check authorization - user must be from source or destination branch
        $session = session();
        $branchId = (int)($session->get('branch_id') ?? 0);
        $role = $session->get('role');

        if ($role !== 'Central Office Admin' && 
            $transfer['from_branch_id'] != $branchId && 
            $transfer['to_branch_id'] != $branchId) {
            return redirect()->to(site_url('branch-transfers'))->with('error', 'Unauthorized access.');
        }

        $data = [
            'role' => $role,
            'title' => 'Transfer Details',
            'transfer' => $transfer
        ];

        return view('reusables/sidenav', $data) . view('branch_transfer/view', $data);
    }

    /**
     * Approve transfer request
     */
    public function approve(int $id)
    {
        if ($redirect = $this->authorize('branch_transfers.approve')) {
            return $redirect;
        }

        $session = session();
        $branchId = (int)($session->get('branch_id') ?? 0);
        $userId = (int)($session->get('user_id') ?? 0);
        $role = $session->get('role');

        $transfer = $this->transferModel->find($id);

        if (!$transfer) {
            return redirect()->back()->with('error', 'Transfer not found.');
        }

        // Only the destination branch manager can approve
        if ($role !== 'Central Office Admin' && $transfer['to_branch_id'] != $branchId) {
            return redirect()->back()->with('error', 'You can only approve transfers to your branch.');
        }

        if ($transfer['status'] !== 'pending') {
            return redirect()->back()->with('error', 'This transfer has already been processed.');
        }

        // Check stock availability again
        if (!$this->transferModel->checkStockAvailability($transfer['stock_in_id'], $transfer['quantity'])) {
            return redirect()->back()->with('error', 'Insufficient stock available for transfer.');
        }

        if ($this->transferModel->approveTransfer($id, $userId)) {
            return redirect()->back()->with('success', 'Transfer approved successfully.');
        }

        return redirect()->back()->with('error', 'Failed to approve transfer.');
    }

    /**
     * Reject transfer request
     */
    public function reject(int $id)
    {
        if ($redirect = $this->authorize('branch_transfers.reject')) {
            return $redirect;
        }

        $session = session();
        $branchId = (int)($session->get('branch_id') ?? 0);
        $role = $session->get('role');

        $transfer = $this->transferModel->find($id);

        if (!$transfer) {
            return redirect()->back()->with('error', 'Transfer not found.');
        }

        // Only the destination branch manager can reject
        if ($role !== 'Central Office Admin' && $transfer['to_branch_id'] != $branchId) {
            return redirect()->back()->with('error', 'You can only reject transfers to your branch.');
        }

        if ($transfer['status'] !== 'pending') {
            return redirect()->back()->with('error', 'This transfer has already been processed.');
        }

        $reason = $this->request->getPost('rejection_reason') ?? 'No reason provided';

        if ($this->transferModel->rejectTransfer($id, $reason)) {
            return redirect()->back()->with('success', 'Transfer rejected.');
        }

        return redirect()->back()->with('error', 'Failed to reject transfer.');
    }

    /**
     * Complete transfer (after approval, when items are physically moved)
     */
    public function complete(int $id)
    {
        if ($redirect = $this->authorize('branch_transfers.complete')) {
            return $redirect;
        }

        $session = session();
        $branchId = (int)($session->get('branch_id') ?? 0);
        $role = $session->get('role');

        $transfer = $this->transferModel->find($id);

        if (!$transfer) {
            return redirect()->back()->with('error', 'Transfer not found.');
        }

        // Only the destination branch manager can complete
        if ($role !== 'Central Office Admin' && $transfer['to_branch_id'] != $branchId) {
            return redirect()->back()->with('error', 'You can only complete transfers to your branch.');
        }

        if ($transfer['status'] !== 'approved') {
            return redirect()->back()->with('error', 'Only approved transfers can be completed.');
        }

        if ($this->transferModel->completeTransfer($id)) {
            return redirect()->back()->with('success', 'Transfer completed. Stock has been moved between branches.');
        }

        return redirect()->back()->with('error', 'Failed to complete transfer.');
    }
}
