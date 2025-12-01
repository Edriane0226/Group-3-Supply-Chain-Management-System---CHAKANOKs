<?php

namespace App\Models;

use CodeIgniter\Model;

class FranchiseModel extends Model
{
    protected $table            = 'franchises';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'applicant_name',
        'contact_info',
        'email',
        'address',
        'proposed_location',
        'business_experience',
        'investment_capacity',
        'status',
        'royalty_rate',
        'franchise_fee',
        'contract_start',
        'contract_end',
        'branch_id',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'applicant_name' => 'required|min_length[3]|max_length[150]',
        'contact_info'   => 'required|max_length[150]',
        'email'          => 'permit_empty|valid_email|max_length[150]',
    ];

    protected $validationMessages = [
        'applicant_name' => [
            'required' => 'Applicant name is required.',
            'min_length' => 'Applicant name must be at least 3 characters.',
        ],
        'contact_info' => [
            'required' => 'Contact information is required.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Get all pending applications
     */
    public function getPendingApplications(): array
    {
        return $this->whereIn('status', ['pending', 'under_review'])
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all active franchises
     */
    public function getActiveFranchises(): array
    {
        return $this->select('franchises.*, branches.branch_name, branches.location as branch_location')
                    ->join('branches', 'branches.id = franchises.branch_id', 'left')
                    ->whereIn('franchises.status', ['approved', 'active'])
                    ->orderBy('franchises.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get franchise with all related data
     */
    public function getFranchiseDetails(int $id): ?array
    {
        $franchise = $this->select('franchises.*, branches.branch_name, branches.location as branch_location, users.first_Name as approver_first, users.last_Name as approver_last')
                         ->join('branches', 'branches.id = franchises.branch_id', 'left')
                         ->join('users', 'users.id = franchises.approved_by', 'left')
                         ->where('franchises.id', $id)
                         ->first();

        if ($franchise) {
            // Get payment history
            $franchise['payments'] = $this->db->table('franchise_payments')
                                             ->where('franchise_id', $id)
                                             ->orderBy('payment_date', 'DESC')
                                             ->get()
                                             ->getResultArray();

            // Get supply allocations
            $franchise['supply_allocations'] = $this->db->table('franchise_supply_allocations')
                                                       ->where('franchise_id', $id)
                                                       ->orderBy('allocation_date', 'DESC')
                                                       ->get()
                                                       ->getResultArray();

            // Calculate totals
            $franchise['total_payments'] = array_sum(array_column($franchise['payments'], 'amount'));
            $franchise['total_supplies'] = array_sum(array_column($franchise['supply_allocations'], 'total_amount'));
        }

        return $franchise;
    }

    /**
     * Approve a franchise application
     */
    public function approveApplication(int $id, int $approvedBy, array $data = []): bool
    {
        $updateData = [
            'status'      => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        // Merge additional data if provided
        if (!empty($data['royalty_rate'])) {
            $updateData['royalty_rate'] = $data['royalty_rate'];
        }
        if (!empty($data['franchise_fee'])) {
            $updateData['franchise_fee'] = $data['franchise_fee'];
        }
        if (!empty($data['contract_start'])) {
            $updateData['contract_start'] = $data['contract_start'];
        }
        if (!empty($data['contract_end'])) {
            $updateData['contract_end'] = $data['contract_end'];
        }
        if (!empty($data['notes'])) {
            $updateData['notes'] = $data['notes'];
        }

        return $this->update($id, $updateData);
    }

    /**
     * Reject a franchise application
     */
    public function rejectApplication(int $id, string $reason = ''): bool
    {
        return $this->update($id, [
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Activate a franchise (after approval and setup)
     */
    public function activateFranchise(int $id, int $branchId): bool
    {
        return $this->update($id, [
            'status'     => 'active',
            'branch_id'  => $branchId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Suspend a franchise
     */
    public function suspendFranchise(int $id, string $reason = ''): bool
    {
        return $this->update($id, [
            'status'     => 'suspended',
            'notes'      => $reason,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Reactivate a suspended franchise
     */
    public function reactivateFranchise(int $id): bool
    {
        return $this->update($id, [
            'status'     => 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Terminate a franchise
     */
    public function terminateFranchise(int $id, string $reason = ''): bool
    {
        return $this->update($id, [
            'status'     => 'terminated',
            'notes'      => $reason,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get franchise statistics
     */
    public function getStatistics(): array
    {
        $stats = [
            'total_applications' => $this->countAllResults(false),
            'pending'            => $this->where('status', 'pending')->countAllResults(false),
            'under_review'       => $this->where('status', 'under_review')->countAllResults(false),
            'approved'           => $this->where('status', 'approved')->countAllResults(false),
            'active'             => $this->where('status', 'active')->countAllResults(false),
            'suspended'          => $this->where('status', 'suspended')->countAllResults(false),
            'rejected'           => $this->where('status', 'rejected')->countAllResults(false),
            'terminated'         => $this->where('status', 'terminated')->countAllResults(false),
        ];

        // Get total revenue from payments
        $totalRevenue = $this->db->table('franchise_payments')
                                ->selectSum('amount')
                                ->where('payment_status', 'completed')
                                ->get()
                                ->getRow();

        $stats['total_revenue'] = $totalRevenue->amount ?? 0;

        return $stats;
    }

    /**
     * Get franchises by status
     */
    public function getByStatus(string $status): array
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Search franchises
     */
    public function search(string $keyword): array
    {
        return $this->like('applicant_name', $keyword)
                    ->orLike('contact_info', $keyword)
                    ->orLike('email', $keyword)
                    ->orLike('proposed_location', $keyword)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get expiring contracts (within 30 days)
     */
    public function getExpiringContracts(int $days = 30): array
    {
        $futureDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->where('status', 'active')
                    ->where('contract_end <=', $futureDate)
                    ->where('contract_end >=', date('Y-m-d'))
                    ->orderBy('contract_end', 'ASC')
                    ->findAll();
    }

    /**
     * Create new franchise application
     */
    public function createApplication(array $data): int
    {
        $insertData = [
            'applicant_name'       => $data['applicant_name'],
            'contact_info'         => $data['contact_info'],
            'email'                => $data['email'] ?? null,
            'address'              => $data['address'] ?? null,
            'proposed_location'    => $data['proposed_location'] ?? null,
            'business_experience'  => $data['business_experience'] ?? null,
            'investment_capacity'  => $data['investment_capacity'] ?? null,
            'status'               => 'pending',
            'royalty_rate'         => $data['royalty_rate'] ?? 5.00,
            'created_at'           => date('Y-m-d H:i:s'),
        ];

        $this->insert($insertData);
        return $this->insertID();
    }
}

