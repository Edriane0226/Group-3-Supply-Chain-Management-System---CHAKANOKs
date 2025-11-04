<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'item_type_id' => 1, // Stock Supplies
                'branch_id'     => 1,
                'item_name'     => 'Chicken Breast',
                'category'      => 'Meat',
                'quantity'      => 50,
                'unit'          => 'kg',
                'price'         => 45.00,
                'expiry_date'   => '2025-09-30',
                'barcode'       => 'CHKN-001',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'item_type_id' => 2, // Store Equipment
                'branch_id'     => 1,
                'item_name'     => 'Chair',
                'category'      => 'Equipment',
                'quantity'      => 10,
                'unit'          => 'Pcs',
                'price'         => 911.00,
                'expiry_date'   => null,
                'barcode'       => 'Chair-123',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'item_type_id' => 1, // Stock Supplies
                'branch_id'     => 2,
                'item_name'     => 'Rice',
                'category'      => 'Grains',
                'quantity'      => 100,
                'unit'          => 'kg',
                'price'         => 50.00,
                'expiry_date'   => null,
                'barcode'       => 'RICE-456',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'item_type_id' => 1, // Stock Supplies
                'branch_id'     => 2,
                'item_name'     => 'Whole Chicken',
                'category'      => 'Meat',
                'quantity'      => 10,
                'unit'          => 'pcs',
                'price'         => 250.00,
                'expiry_date'   => null,
                'barcode'       => 'CHICK-456',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'item_type_id' => 1, // Stock Supplies
                'branch_id'     => 3,
                'item_name'     => 'Rice',
                'category'      => 'Grains',
                'quantity'      => 100,
                'unit'          => 'kg',
                'price'         => 50.00,
                'expiry_date'   => null,
                'barcode'       => 'RICE-452',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'item_type_id' => 2, // Store Equipment
                'branch_id'     => 3,
                'item_name'     => 'Chair',
                'category'      => 'Equipment',
                'quantity'      => 10,
                'unit'          => 'Pcs',
                'price'         => 911.00,
                'expiry_date'   => null,
                'barcode'       => 'CHAIR-133',
                'created_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('stock_in')->insertBatch($data);
    }
}
