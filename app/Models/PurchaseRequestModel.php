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
        return $this->select('purchase_requests.*, branches.branch_name, suppliers.name as supplier_name')
                    ->join('branches', 'branches.id = purchase_requests.branch_id')
                    ->join('suppliers', 'suppliers.id = purchase_requests.supplier_id');
    }
}