<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchTransferModel extends Model
{
    protected $table            = 'branch_transfers';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'from_branch_id',
        'to_branch_id',
        'stock_in_id',
        'item_name',
        'quantity',
        'unit',
        'status',
        'requested_by',
        'approved_by',
        'notes',
        'approved_at',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'from_branch_id' => 'required|integer',
        'to_branch_id' => 'required|integer|differs[from_branch_id]',
        'stock_in_id' => 'required|integer',
        'item_name' => 'required|max_length[150]',
        'quantity' => 'required|integer|greater_than[0]',
        'unit' => 'permit_empty|max_length[50]',
        'notes' => 'permit_empty'
    ];

    protected $validationMessages = [
        'to_branch_id' => [
            'differs' => 'Cannot transfer items to the same branch.'
        ]
    ];

    /**
     * Get transfers with branch and user information
     */
    public function getTransfersWithRelations(?int $branchId = null, ?string $status = null): array
    {
        // Check if new columns exist
        $hasRequestedBy = $this->db->fieldExists('requested_by', 'branch_transfers');
        $hasApprovedBy = $this->db->fieldExists('approved_by', 'branch_transfers');
        
        $selectFields = 'bt.*, 
                from_branch.branch_name as from_branch_name,
                to_branch.branch_name as to_branch_name';
        
        if ($hasRequestedBy) {
            $selectFields .= ',
                requester.first_Name as requester_first_name,
                requester.last_Name as requester_last_name';
        } else {
            $selectFields .= ',
                NULL as requester_first_name,
                NULL as requester_last_name';
        }
        
        if ($hasApprovedBy) {
            $selectFields .= ',
                approver.first_Name as approver_first_name,
                approver.last_Name as approver_last_name';
        } else {
            $selectFields .= ',
                NULL as approver_first_name,
                NULL as approver_last_name';
        }
        
        $builder = $this->db->table('branch_transfers bt')
            ->select($selectFields)
            ->join('branches from_branch', 'from_branch.id = bt.from_branch_id', 'left')
            ->join('branches to_branch', 'to_branch.id = bt.to_branch_id', 'left');
        
        if ($hasRequestedBy) {
            $builder->join('users requester', 'requester.id = bt.requested_by', 'left');
        }
        
        if ($hasApprovedBy) {
            $builder->join('users approver', 'approver.id = bt.approved_by', 'left');
        }
        
        $builder->orderBy('bt.created_at', 'DESC');

        if ($branchId) {
            $builder->groupStart()
                   ->where('bt.from_branch_id', $branchId)
                   ->orWhere('bt.to_branch_id', $branchId)
                   ->groupEnd();
        }

        if ($status) {
            $builder->where('bt.status', $status);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get pending transfers for a branch manager to approve
     */
    public function getPendingForApproval(int $branchId): array
    {
        $hasRequestedBy = $this->db->fieldExists('requested_by', 'branch_transfers');
        
        $selectFields = 'bt.*, 
                from_branch.branch_name as from_branch_name,
                to_branch.branch_name as to_branch_name';
        
        if ($hasRequestedBy) {
            $selectFields .= ',
                requester.first_Name as requester_first_name,
                requester.last_Name as requester_last_name';
        } else {
            $selectFields .= ',
                NULL as requester_first_name,
                NULL as requester_last_name';
        }
        
        $builder = $this->db->table('branch_transfers bt')
            ->select($selectFields)
            ->join('branches from_branch', 'from_branch.id = bt.from_branch_id', 'left')
            ->join('branches to_branch', 'to_branch.id = bt.to_branch_id', 'left');
        
        if ($hasRequestedBy) {
            $builder->join('users requester', 'requester.id = bt.requested_by', 'left');
        }
        
        return $builder->where('bt.to_branch_id', $branchId)
            ->where('bt.status', 'pending')
            ->orderBy('bt.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get outgoing transfers (transfers from this branch)
     */
    public function getOutgoingTransfers(int $branchId): array
    {
        return $this->db->table('branch_transfers bt')
            ->select('bt.*, to_branch.branch_name as to_branch_name')
            ->join('branches to_branch', 'to_branch.id = bt.to_branch_id', 'left')
            ->where('bt.from_branch_id', $branchId)
            ->orderBy('bt.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get incoming transfers (transfers to this branch)
     */
    public function getIncomingTransfers(int $branchId): array
    {
        return $this->db->table('branch_transfers bt')
            ->select('bt.*, from_branch.branch_name as from_branch_name')
            ->join('branches from_branch', 'from_branch.id = bt.from_branch_id', 'left')
            ->where('bt.to_branch_id', $branchId)
            ->orderBy('bt.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Check if there's enough stock for transfer
     */
    public function checkStockAvailability(int $stockInId, int $quantity): bool
    {
        // Get the stock_in record
        $stockIn = $this->db->table('stock_in')->where('id', $stockInId)->get()->getRowArray();
        
        if (!$stockIn) {
            return false;
        }

        // Calculate current available stock
        $totalIn = $this->db->table('stock_in')
            ->where('item_name', $stockIn['item_name'])
            ->where('branch_id', $stockIn['branch_id'])
            ->selectSum('quantity')
            ->get()
            ->getRowArray();

        $totalOut = $this->db->table('stock_out')
            ->where('item_name', $stockIn['item_name'])
            ->where('branch_id', $stockIn['branch_id'])
            ->selectSum('quantity')
            ->get()
            ->getRowArray();

        // Check pending transfers (reserved stock)
        $pendingTransfersQuery = $this->db->table('branch_transfers')
            ->where('from_branch_id', $stockIn['branch_id'])
            ->whereIn('status', ['pending', 'approved']);
        
        // Only filter by item_name if column exists
        if ($this->db->fieldExists('item_name', 'branch_transfers')) {
            $pendingTransfersQuery->where('item_name', $stockIn['item_name']);
        } else {
            // If item_name doesn't exist, use stock_in_id
            $pendingTransfersQuery->where('stock_in_id', $stockInId);
        }
        
        $pendingTransfers = $pendingTransfersQuery->selectSum('quantity')
            ->get()
            ->getRowArray();

        $availableStock = ($totalIn['quantity'] ?? 0) 
                        - ($totalOut['quantity'] ?? 0) 
                        - ($pendingTransfers['quantity'] ?? 0);

        return $availableStock >= $quantity;
    }

    /**
     * Approve transfer
     */
    public function approveTransfer(int $transferId, int $approvedBy): bool
    {
        return $this->update($transferId, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Complete transfer - move stock between branches
     */
    public function completeTransfer(int $transferId): bool
    {
        $transfer = $this->find($transferId);
        
        if (!$transfer || $transfer['status'] !== 'approved') {
            return false;
        }

        $this->db->transStart();

        try {
            // 1. Create stock_out record in source branch
            $stockIn = $this->db->table('stock_in')
                ->where('id', $transfer['stock_in_id'])
                ->get()
                ->getRowArray();

            if (!$stockIn) {
                throw new \Exception('Stock record not found');
            }

            $stockOutData = [
                'branch_id' => $transfer['from_branch_id'],
                'item_type_id' => $stockIn['item_type_id'] ?? 1,
                'quantity' => $transfer['quantity'],
                'unit' => $transfer['unit'] ?? 'pcs',
                'reason' => 'Branch Transfer to ' . $transfer['to_branch_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Add item_name if it exists in transfer, otherwise use stock_in item_name
            if (isset($transfer['item_name']) && $transfer['item_name']) {
                $stockOutData['item_name'] = $transfer['item_name'];
            } else {
                $stockOutData['item_name'] = $stockIn['item_name'] ?? 'Transfer Item';
            }
            
            $this->db->table('stock_out')->insert($stockOutData);

            // 2. Create stock_in record in destination branch
            $itemName = $transfer['item_name'] ?? $stockIn['item_name'] ?? 'Transfer Item';
            
            $this->db->table('stock_in')->insert([
                'item_type_id' => $stockIn['item_type_id'] ?? 1,
                'branch_id' => $transfer['to_branch_id'],
                'item_name' => $itemName,
                'category' => $stockIn['category'] ?? null,
                'quantity' => $transfer['quantity'],
                'unit' => $transfer['unit'] ?? 'pcs',
                'price' => $stockIn['price'] ?? 0,
                'expiry_date' => $stockIn['expiry_date'] ?? null,
                'barcode' => $stockIn['barcode'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // 3. Update transfer status to completed
            $this->update($transferId, [
                'status' => 'completed',
                'completed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->transComplete();

            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Transfer completion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject transfer
     */
    public function rejectTransfer(int $transferId, string $reason = null): bool
    {
        $currentTransfer = $this->find($transferId);
        $existingNotes = $currentTransfer['notes'] ?? '';
        
        $updateData = [
            'status' => 'rejected',
            'notes' => $reason ? ($existingNotes ? $existingNotes . "\nRejection reason: " . $reason : "Rejection reason: " . $reason) : $existingNotes,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($transferId, $updateData);
    }
}
