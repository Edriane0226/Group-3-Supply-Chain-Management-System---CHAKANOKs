<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurchaseRequestsSeeder extends Seeder
{
    public function run()
    {
        // Get branch and supplier IDs
        $branches = $this->db->table('branches')->select('id, branch_name')->get()->getResultArray();
        $suppliers = $this->db->table('suppliers')->select('id, supplier_name')->get()->getResultArray();
        
        // Create a mapping for easier access
        $branchMap = [];
        foreach ($branches as $branch) {
            $branchMap[$branch['branch_name']] = $branch['id'];
        }
        
        $supplierMap = [];
        foreach ($suppliers as $supplier) {
            $supplierMap[$supplier['supplier_name']] = $supplier['id'];
        }
        
        $data = [
            [
                'branch_id' => $branchMap['Central'] ?? 1,
                'supplier_id' => $supplierMap['San Miguel Foods Inc.'] ?? 1001,
                'item_name' => 'Chicken Breast',
                'quantity' => 50,
                'unit' => 'kg',
                'price' => 210.00,
                'description' => 'Fresh chicken breast for daily operations',
                'request_date' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'status' => 'approved',
                'approved_by' => 23116000, // Central Office Admin
                'approved_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
            ],
            [
                'branch_id' => $branchMap['Gensan'] ?? 2,
                'supplier_id' => $supplierMap['Bounty Fresh Chicken Supply'] ?? 1002,
                'item_name' => 'Whole Chicken',
                'quantity' => 30,
                'unit' => 'pcs',
                'price' => 190.00,
                'description' => 'Whole chicken for weekend specials',
                'request_date' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'status' => 'ordered',
                'approved_by' => 23116000,
                'approved_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'branch_id' => $branchMap['Polomolok'] ?? 3,
                'supplier_id' => $supplierMap['NutriAsia Condiments Distributor'] ?? 1003,
                'item_name' => 'Soy Sauce',
                'quantity' => 100,
                'unit' => 'bottles',
                'price' => 85.00,
                'description' => 'Soy sauce for condiments station',
                'request_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => null,
            ],
            [
                'branch_id' => $branchMap['Central'] ?? 1,
                'supplier_id' => $supplierMap['PureOil Philippines'] ?? 1005,
                'item_name' => 'Cooking Oil',
                'quantity' => 40,
                'unit' => 'liters',
                'price' => 180.00,
                'description' => 'Cooking oil for frying operations',
                'request_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'status' => 'approved',
                'approved_by' => 23116000,
                'approved_at' => date('Y-m-d H:i:s', strtotime('-12 hours')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-12 hours')),
            ],
        ];

        $this->db->table('purchase_requests')->insertBatch($data);
    }
}

