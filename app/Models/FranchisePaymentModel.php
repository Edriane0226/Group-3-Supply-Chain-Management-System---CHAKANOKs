<?php

namespace App\Models;

use CodeIgniter\Model;

class FranchisePaymentModel extends Model
{
    protected $table            = 'franchise_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'franchise_id',
        'payment_type',
        'amount',
        'reference_number',
        'payment_method',
        'payment_status',
        'payment_date',
        'period_start',
        'period_end',
        'remarks',
        'recorded_by',
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
        'amount'       => 'required|decimal',
        'payment_date' => 'required|valid_date',
    ];

    protected $skipValidation = false;

    /**
     * Get payments for a specific franchise
     */
    public function getByFranchise(int $franchiseId): array
    {
        return $this->select('franchise_payments.*, users.first_Name as recorder_first, users.last_Name as recorder_last')
                    ->join('users', 'users.id = franchise_payments.recorded_by', 'left')
                    ->where('franchise_id', $franchiseId)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get payments by type
     */
    public function getByType(string $type, ?int $franchiseId = null): array
    {
        $builder = $this->where('payment_type', $type);
        
        if ($franchiseId) {
            $builder->where('franchise_id', $franchiseId);
        }
        
        return $builder->orderBy('payment_date', 'DESC')->findAll();
    }

    /**
     * Record a new payment
     */
    public function recordPayment(array $data): int
    {
        $insertData = [
            'franchise_id'     => $data['franchise_id'],
            'payment_type'     => $data['payment_type'] ?? 'royalty',
            'amount'           => $data['amount'],
            'reference_number' => $data['reference_number'] ?? null,
            'payment_method'   => $data['payment_method'] ?? 'cash',
            'payment_status'   => $data['payment_status'] ?? 'completed',
            'payment_date'     => $data['payment_date'],
            'period_start'     => $data['period_start'] ?? null,
            'period_end'       => $data['period_end'] ?? null,
            'remarks'          => $data['remarks'] ?? null,
            'recorded_by'      => $data['recorded_by'] ?? null,
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        $this->insert($insertData);
        return $this->insertID();
    }

    /**
     * Get total payments for a franchise
     */
    public function getTotalByFranchise(int $franchiseId, ?string $type = null): float
    {
        $builder = $this->selectSum('amount')
                       ->where('franchise_id', $franchiseId)
                       ->where('payment_status', 'completed');
        
        if ($type) {
            $builder->where('payment_type', $type);
        }
        
        $result = $builder->get()->getRow();
        return (float) ($result->amount ?? 0);
    }

    /**
     * Get payments within date range
     */
    public function getByDateRange(string $startDate, string $endDate, ?int $franchiseId = null): array
    {
        $builder = $this->select('franchise_payments.*, franchises.applicant_name')
                       ->join('franchises', 'franchises.id = franchise_payments.franchise_id')
                       ->where('payment_date >=', $startDate)
                       ->where('payment_date <=', $endDate);
        
        if ($franchiseId) {
            $builder->where('franchise_id', $franchiseId);
        }
        
        return $builder->orderBy('payment_date', 'DESC')->findAll();
    }

    /**
     * Get payment statistics
     */
    public function getStatistics(?int $franchiseId = null): array
    {
        $stats = [];
        
        // Base builder
        $baseBuilder = $this->db->table('franchise_payments')
                               ->where('payment_status', 'completed');
        
        if ($franchiseId) {
            $baseBuilder->where('franchise_id', $franchiseId);
        }

        // Total by type
        $types = ['franchise_fee', 'royalty', 'supply_payment', 'penalty', 'other'];
        foreach ($types as $type) {
            $typeBuilder = clone $baseBuilder;
            $result = $typeBuilder->selectSum('amount')
                                 ->where('payment_type', $type)
                                 ->get()
                                 ->getRow();
            $stats[$type] = (float) ($result->amount ?? 0);
        }

        // Total overall
        $totalBuilder = clone $baseBuilder;
        $totalResult = $totalBuilder->selectSum('amount')->get()->getRow();
        $stats['total'] = (float) ($totalResult->amount ?? 0);

        // Count of payments
        $countBuilder = clone $baseBuilder;
        $stats['count'] = $countBuilder->countAllResults();

        // This month's revenue
        $monthBuilder = clone $baseBuilder;
        $thisMonth = $monthBuilder->selectSum('amount')
                                 ->where('MONTH(payment_date)', date('m'))
                                 ->where('YEAR(payment_date)', date('Y'))
                                 ->get()
                                 ->getRow();
        $stats['this_month'] = (float) ($thisMonth->amount ?? 0);

        return $stats;
    }

    /**
     * Get recent payments
     */
    public function getRecentPayments(int $limit = 10): array
    {
        return $this->select('franchise_payments.*, franchises.applicant_name')
                    ->join('franchises', 'franchises.id = franchise_payments.franchise_id')
                    ->where('payment_status', 'completed')
                    ->orderBy('payment_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Update payment status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, [
            'payment_status' => $status,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get monthly revenue report
     */
    public function getMonthlyReport(int $year): array
    {
        $query = "
            SELECT 
                MONTH(payment_date) as month,
                payment_type,
                SUM(amount) as total
            FROM franchise_payments
            WHERE YEAR(payment_date) = ?
            AND payment_status = 'completed'
            GROUP BY MONTH(payment_date), payment_type
            ORDER BY month ASC
        ";

        return $this->db->query($query, [$year])->getResultArray();
    }
}

