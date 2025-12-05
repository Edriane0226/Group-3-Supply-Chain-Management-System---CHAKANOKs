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

    /**
     * Get franchise performance metrics
     */
    public function getPerformanceMetrics(int $franchiseId, ?string $startDate = null, ?string $endDate = null): array
    {
        $franchise = $this->find($franchiseId);
        if (!$franchise) {
            return [];
        }

        // Set default date range (last 12 months)
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-12 months'));
        }
        if (!$endDate) {
            $endDate = date('Y-m-d');
        }

        // Get payment statistics
        $paymentModel = new \App\Models\FranchisePaymentModel();
        $payments = $paymentModel->getByDateRange($startDate, $endDate, $franchiseId);
        
        $totalPayments = array_sum(array_column($payments, 'amount'));
        $royaltyPayments = array_sum(array_column(
            array_filter($payments, fn($p) => $p['payment_type'] === 'royalty'),
            'amount'
        ));
        $franchiseFeePayments = array_sum(array_column(
            array_filter($payments, fn($p) => $p['payment_type'] === 'franchise_fee'),
            'amount'
        ));
        $supplyPayments = array_sum(array_column(
            array_filter($payments, fn($p) => $p['payment_type'] === 'supply_payment'),
            'amount'
        ));

        // Get supply allocation statistics
        $allocationModel = new \App\Models\FranchiseSupplyAllocationModel();
        $allocations = $allocationModel->getByFranchise($franchiseId);
        
        $totalAllocations = count($allocations);
        $deliveredAllocations = count(array_filter($allocations, fn($a) => $a['status'] === 'delivered'));
        $totalSupplyValue = array_sum(array_column($allocations, 'total_amount'));

        // Calculate performance scores
        $paymentCompliance = $totalPayments > 0 ? 100 : 0; // Simplified - can be enhanced
        $supplyUtilization = $totalAllocations > 0 ? ($deliveredAllocations / $totalAllocations) * 100 : 0;

        // Calculate days since activation
        $daysActive = 0;
        if ($franchise['contract_start']) {
            $daysActive = (strtotime($endDate) - strtotime($franchise['contract_start'])) / 86400;
        }

        // Calculate average monthly revenue
        $monthsActive = max(1, ceil($daysActive / 30));
        $avgMonthlyRevenue = $monthsActive > 0 ? $totalPayments / $monthsActive : 0;

        return [
            'franchise_id' => $franchiseId,
            'franchise_name' => $franchise['applicant_name'],
            'status' => $franchise['status'],
            'contract_start' => $franchise['contract_start'],
            'contract_end' => $franchise['contract_end'],
            'days_active' => max(0, floor($daysActive)),
            'months_active' => $monthsActive,
            // Payment metrics
            'total_payments' => $totalPayments,
            'royalty_payments' => $royaltyPayments,
            'franchise_fee_payments' => $franchiseFeePayments,
            'supply_payments' => $supplyPayments,
            'payment_count' => count($payments),
            'avg_monthly_revenue' => $avgMonthlyRevenue,
            // Supply metrics
            'total_allocations' => $totalAllocations,
            'delivered_allocations' => $deliveredAllocations,
            'total_supply_value' => $totalSupplyValue,
            'avg_allocation_value' => $totalAllocations > 0 ? $totalSupplyValue / $totalAllocations : 0,
            // Performance scores
            'payment_compliance' => round($paymentCompliance, 2),
            'supply_utilization' => round($supplyUtilization, 2),
            'overall_score' => round(($paymentCompliance + $supplyUtilization) / 2, 2),
            // Period
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];
    }

    /**
     * Get all franchises with performance metrics
     */
    public function getAllFranchisesPerformance(?string $startDate = null, ?string $endDate = null): array
    {
        $activeFranchises = $this->getActiveFranchises();
        $performanceData = [];

        foreach ($activeFranchises as $franchise) {
            $performanceData[] = $this->getPerformanceMetrics($franchise['id'], $startDate, $endDate);
        }

        // Sort by overall score (descending)
        usort($performanceData, fn($a, $b) => $b['overall_score'] <=> $a['overall_score']);

        return $performanceData;
    }

    /**
     * Get franchises with overdue payments
     */
    public function getFranchisesWithOverduePayments(int $daysOverdue = 30): array
    {
        $activeFranchises = $this->getActiveFranchises();
        $overdueFranchises = [];

        foreach ($activeFranchises as $franchise) {
            // Calculate expected payments based on contract
            $lastPaymentDate = $this->db->table('franchise_payments')
                ->selectMax('payment_date')
                ->where('franchise_id', $franchise['id'])
                ->where('payment_type', 'royalty')
                ->get()
                ->getRow();

            if ($lastPaymentDate && $lastPaymentDate->payment_date) {
                $daysSinceLastPayment = (time() - strtotime($lastPaymentDate->payment_date)) / 86400;
                
                if ($daysSinceLastPayment > $daysOverdue) {
                    $franchise['days_overdue'] = floor($daysSinceLastPayment);
                    $franchise['last_payment_date'] = $lastPaymentDate->payment_date;
                    $overdueFranchises[] = $franchise;
                }
            } elseif ($franchise['contract_start']) {
                // No payments yet, check if contract started more than X days ago
                $daysSinceContractStart = (time() - strtotime($franchise['contract_start'])) / 86400;
                if ($daysSinceContractStart > $daysOverdue) {
                    $franchise['days_overdue'] = floor($daysSinceContractStart);
                    $franchise['last_payment_date'] = null;
                    $overdueFranchises[] = $franchise;
                }
            }
        }

        return $overdueFranchises;
    }
}

