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
}
