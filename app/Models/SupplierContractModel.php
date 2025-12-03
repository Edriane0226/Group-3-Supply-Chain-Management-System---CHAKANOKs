<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierContractModel extends Model
{
    protected $table            = 'supplier_contracts';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'supplier_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'renewal_date',
        'auto_renewal',
        'payment_terms',
        'minimum_order_value',
        'discount_rate',
        'delivery_terms',
        'quality_standards',
        'penalty_clauses',
        'status',
        'signed_by_supplier',
        'signed_by_admin',
        'signed_date',
        'notes',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Get all contracts with supplier information
     */
    public function getAllContracts(): array
    {
        return $this->select('supplier_contracts.*, suppliers.supplier_name, suppliers.contact_info, users.first_Name, users.last_Name')
                    ->join('suppliers', 'suppliers.id = supplier_contracts.supplier_id', 'left')
                    ->join('users', 'users.id = supplier_contracts.created_by', 'left')
                    ->orderBy('supplier_contracts.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get contracts by supplier
     */
    public function getContractsBySupplier(int $supplierId): array
    {
        return $this->where('supplier_id', $supplierId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get active contracts
     */
    public function getActiveContracts(): array
    {
        $today = date('Y-m-d');
        return $this->select('supplier_contracts.*, suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id = supplier_contracts.supplier_id', 'left')
                    ->where('supplier_contracts.status', 'active')
                    ->where('supplier_contracts.start_date <=', $today)
                    ->where('supplier_contracts.end_date >=', $today)
                    ->orderBy('supplier_contracts.end_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get expiring contracts (within 30 days)
     */
    public function getExpiringContracts(int $days = 30): array
    {
        $today = date('Y-m-d');
        $expiryDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->select('supplier_contracts.*, suppliers.supplier_name, suppliers.contact_info')
                    ->join('suppliers', 'suppliers.id = supplier_contracts.supplier_id', 'left')
                    ->where('supplier_contracts.status', 'active')
                    ->where('supplier_contracts.end_date >=', $today)
                    ->where('supplier_contracts.end_date <=', $expiryDate)
                    ->orderBy('supplier_contracts.end_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get expired contracts
     */
    public function getExpiredContracts(): array
    {
        $today = date('Y-m-d');
        return $this->select('supplier_contracts.*, suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id = supplier_contracts.supplier_id', 'left')
                    ->where('supplier_contracts.end_date <', $today)
                    ->where('supplier_contracts.status !=', 'renewed')
                    ->orderBy('supplier_contracts.end_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get contract details with supplier info
     */
    public function getContractDetails(int $contractId): ?array
    {
        return $this->select('supplier_contracts.*, suppliers.supplier_name, suppliers.contact_info, suppliers.address, users.first_Name, users.last_Name, users.email')
                    ->join('suppliers', 'suppliers.id = supplier_contracts.supplier_id', 'left')
                    ->join('users', 'users.id = supplier_contracts.created_by', 'left')
                    ->where('supplier_contracts.id', $contractId)
                    ->first();
    }

    /**
     * Generate unique contract number
     */
    public function generateContractNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get last contract number for this month
        $lastContract = $this->select('contract_number')
                             ->like('contract_number', "CNT-{$year}{$month}", 'after')
                             ->orderBy('contract_number', 'DESC')
                             ->first();
        
        if ($lastContract) {
            $lastNumber = (int)substr($lastContract['contract_number'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return sprintf('CNT-%s%s-%04d', $year, $month, $nextNumber);
    }

    /**
     * Check if contract is expiring soon
     */
    public function isExpiringSoon(int $contractId, int $days = 30): bool
    {
        $contract = $this->find($contractId);
        if (!$contract || $contract['status'] !== 'active') {
            return false;
        }
        
        $today = date('Y-m-d');
        $expiryDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $contract['end_date'] >= $today && $contract['end_date'] <= $expiryDate;
    }

    /**
     * Renew contract
     */
    public function renewContract(int $contractId, array $newContractData): int
    {
        // Mark old contract as renewed
        $this->update($contractId, ['status' => 'renewed']);
        
        // Create new contract
        $newContractData['contract_number'] = $this->generateContractNumber();
        $newContractData['status'] = 'draft';
        $newContractData['signed_by_supplier'] = 0;
        $newContractData['signed_by_admin'] = 0;
        
        return $this->insert($newContractData);
    }

    /**
     * Get contract statistics
     */
    public function getStatistics(): array
    {
        $today = date('Y-m-d');
        $expiringContracts = $this->getExpiringContracts(30);
        
        return [
            'total' => $this->countAllResults(false),
            'active' => $this->where('status', 'active')
                            ->where('start_date <=', $today)
                            ->where('end_date >=', $today)
                            ->countAllResults(false),
            'expiring_soon' => count($expiringContracts),
            'expired' => $this->where('end_date <', $today)
                             ->where('status !=', 'renewed')
                             ->countAllResults(false),
            'draft' => $this->where('status', 'draft')->countAllResults(false),
        ];
    }
}

