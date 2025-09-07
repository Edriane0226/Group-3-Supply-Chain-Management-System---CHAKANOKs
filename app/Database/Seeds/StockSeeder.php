<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'branch_id'     => 1,
                'item_name'     => 'Chicken Breast',
                'category'      => 'Meat',
                'type'          => 'Stock Supplies',
                'quantity'      => 50,
                'unit'          => 'kg',
                'expiry_date'   => '2025-09-30',
                'barcode'       => 'CHKN-001',
                'reorder_level' => 10,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id'     => 1,
                'item_name'     => 'Chair',
                'category'      => 'Equipment',
                'type'          => 'Store Equipment',
                'quantity'      => 10,
                'unit'          => 'Pcs',
                'expiry_date'   => null,
                'barcode'       => 'Chair-123',
                'reorder_level' => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'branch_id'     => 2,
                'item_name'     => 'Rice',
                'category'      => 'Grains',
                'type'          => 'Stock Supplies',
                'quantity'      => 100,
                'unit'          => 'kg',
                'expiry_date'   => null,
                'barcode'       => 'RICE-456',
                'reorder_level' => 20,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('inventory')->insertBatch($data);
    }
}
