<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupplierContractsSeeder extends Seeder
{
    public function run()
    {
        // Get suppliers
        $suppliers = $this->db->table('suppliers')
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        // Get any existing user for created_by
        // First try to get admin user
        $adminUser = $this->db->query("
            SELECT u.id 
            FROM users u 
            INNER JOIN roles r ON r.id = u.role_id 
            WHERE r.role_name IN ('System Administrator', 'Central Office Admin')
            LIMIT 1
        ")->getRow();
        
        // If no admin user, get any user
        if (!$adminUser) {
            $anyUser = $this->db->table('users')
                ->select('id')
                ->orderBy('id', 'ASC')
                ->limit(1)
                ->get()
                ->getRow();
            $createdBy = $anyUser ? (int)$anyUser->id : null;
        } else {
            $createdBy = (int)$adminUser->id;
        }
        
        // If still no user found, skip this seeder
        if (!$createdBy) {
            log_message('warning', 'No users found. Skipping SupplierContractsSeeder.');
            return;
        }
        
        $data = [];
        $contractTypes = ['Supply Agreement', 'Exclusive Agreement', 'Supply Agreement', 'Non-Exclusive Agreement'];
        $statuses = ['active', 'active', 'draft', 'active'];
        
        foreach ($suppliers as $index => $supplier) {
            $startDate = date('Y-m-d', strtotime('-' . ($index * 30) . ' days'));
            $endDate = date('Y-m-d', strtotime($startDate . ' +1 year'));
            $renewalDate = date('Y-m-d', strtotime($endDate . ' -30 days'));
            
            $contractType = $contractTypes[min($index, count($contractTypes) - 1)];
            $status = $statuses[min($index, count($statuses) - 1)];
            
            $signedBySupplier = ($status === 'active') ? 1 : 0;
            $signedByAdmin = ($status === 'active') ? 1 : 0;
            $signedDate = ($status === 'active') ? $startDate : null;
            
            // Get payment terms from supplier
            $terms = $supplier['terms'] ?? 'Net 30';
            
            $data[] = [
                'supplier_id' => $supplier['id'],
                'contract_number' => 'CNT-' . str_pad($supplier['id'], 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
                'contract_type' => $contractType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'renewal_date' => $renewalDate,
                'auto_renewal' => 1,
                'payment_terms' => $terms,
                'minimum_order_value' => 5000.00,
                'discount_rate' => ($index % 2 === 0) ? 5.00 : 0.00, // 5% discount for some
                'delivery_terms' => 'FOB Destination, Delivery within 7-14 business days',
                'quality_standards' => 'All products must meet food safety standards and pass quality inspection',
                'penalty_clauses' => 'Late delivery: 2% penalty per day. Quality issues: Full refund or replacement',
                'status' => $status,
                'signed_by_supplier' => $signedBySupplier,
                'signed_by_admin' => $signedByAdmin,
                'signed_date' => $signedDate,
                'notes' => $status === 'active' ? 'Contract is active and in good standing' : 'Contract pending approval',
                'created_by' => $createdBy,
                'created_at' => date('Y-m-d H:i:s', strtotime($startDate)),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($data)) {
            $this->db->table('supplier_contracts')->insertBatch($data);
        }
    }
}

