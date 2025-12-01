<?php

namespace App\Models;

use CodeIgniter\Model;

class FranchiseSupplyAllocationModel extends Model
{
    protected $table            = 'franchise_supply_allocations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'franchise_id',
        'item_name',
        'quantity',
        'unit',
        'unit_price',
        'total_amount',
        'allocation_date',
        'delivery_date',
        'status',
        'notes',
        'allocated_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'franchise_id' => 'required|integer',
        'item_name'    => 'required|max_length[255]',
        'quantity'     => 'required|integer|greater_than[0]',
    ];

    protected $skipValidation = false;

    /**
     * Get allocations for a specific franchise
     */
    public function getByFranchise(int $franchiseId): array
    {
        return $this->select('franchise_supply_allocations.*, users.first_Name as allocator_first, users.last_Name as allocator_last')
                    ->join('users', 'users.id = franchise_supply_allocations.allocated_by', 'left')
                    ->where('franchise_id', $franchiseId)
                    ->orderBy('allocation_date', 'DESC')
                    ->findAll();
    }

    /**
     * Create new supply allocation
     */
    public function createAllocation(array $data): int
    {
        $totalAmount = ($data['quantity'] ?? 0) * ($data['unit_price'] ?? 0);

        $insertData = [
            'franchise_id'    => $data['franchise_id'],
            'item_name'       => $data['item_name'],
            'quantity'        => $data['quantity'],
            'unit'            => $data['unit'] ?? 'pcs',
            'unit_price'      => $data['unit_price'] ?? 0,
            'total_amount'    => $totalAmount,
            'allocation_date' => $data['allocation_date'] ?? date('Y-m-d H:i:s'),
            'delivery_date'   => $data['delivery_date'] ?? null,
            'status'          => $data['status'] ?? 'pending',
            'notes'           => $data['notes'] ?? null,
            'allocated_by'    => $data['allocated_by'] ?? null,
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        $this->insert($insertData);
        return $this->insertID();
    }

    /**
     * Update allocation status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get allocations by status
     */
    public function getByStatus(string $status, ?int $franchiseId = null): array
    {
        $builder = $this->select('franchise_supply_allocations.*, franchises.applicant_name')
                       ->join('franchises', 'franchises.id = franchise_supply_allocations.franchise_id')
                       ->where('franchise_supply_allocations.status', $status);
        
        if ($franchiseId) {
            $builder->where('franchise_id', $franchiseId);
        }
        
        return $builder->orderBy('allocation_date', 'DESC')->findAll();
    }

    /**
     * Get pending allocations
     */
    public function getPendingAllocations(): array
    {
        return $this->getByStatus('pending');
    }

    /**
     * Get total supply cost for a franchise
     */
    public function getTotalByFranchise(int $franchiseId): float
    {
        $result = $this->selectSum('total_amount')
                      ->where('franchise_id', $franchiseId)
                      ->whereNotIn('status', ['cancelled'])
                      ->get()
                      ->getRow();
        
        return (float) ($result->total_amount ?? 0);
    }

    /**
     * Get allocation statistics
     */
    public function getStatistics(?int $franchiseId = null): array
    {
        $builder = $this->db->table('franchise_supply_allocations');
        
        if ($franchiseId) {
            $builder->where('franchise_id', $franchiseId);
        }

        $stats = [
            'total_allocations' => $builder->countAllResults(false),
            'pending'           => $builder->where('status', 'pending')->countAllResults(false),
            'approved'          => $builder->where('status', 'approved')->countAllResults(false),
            'preparing'         => $builder->where('status', 'preparing')->countAllResults(false),
            'shipped'           => $builder->where('status', 'shipped')->countAllResults(false),
            'delivered'         => $builder->where('status', 'delivered')->countAllResults(false),
            'cancelled'         => $builder->where('status', 'cancelled')->countAllResults(false),
        ];

        // Total value
        $totalBuilder = $this->db->table('franchise_supply_allocations');
        if ($franchiseId) {
            $totalBuilder->where('franchise_id', $franchiseId);
        }
        $totalResult = $totalBuilder->selectSum('total_amount')
                                   ->whereNotIn('status', ['cancelled'])
                                   ->get()
                                   ->getRow();
        $stats['total_value'] = (float) ($totalResult->total_amount ?? 0);

        return $stats;
    }

    /**
     * Get recent allocations
     */
    public function getRecentAllocations(int $limit = 10): array
    {
        return $this->select('franchise_supply_allocations.*, franchises.applicant_name')
                    ->join('franchises', 'franchises.id = franchise_supply_allocations.franchise_id')
                    ->orderBy('allocation_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Batch create allocations (multiple items)
     */
    public function createBatchAllocation(int $franchiseId, array $items, int $allocatedBy): array
    {
        $insertedIds = [];
        
        foreach ($items as $item) {
            $item['franchise_id'] = $franchiseId;
            $item['allocated_by'] = $allocatedBy;
            $insertedIds[] = $this->createAllocation($item);
        }
        
        return $insertedIds;
    }
}

