<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupplierItemSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // San Miguel Foods Inc. - Raw Materials
            [
                'supplier_id' => 1001,
                'stock_type_id' => 2, // Raw Materials
                'item_name' => 'Chicken Breast',
                'unit_price' => 210.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'supplier_id' => 1001,
                'stock_type_id' => 2,
                'item_name' => 'Pork Belly',
                'unit_price' => 230.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'supplier_id' => 1001,
                'stock_type_id' => 2,
                'item_name' => 'Ground Beef',
                'unit_price' => 250.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],

            // Bounty Fresh Chicken Supply - Raw Materials
            [
                'supplier_id' => 1002,
                'stock_type_id' => 2,
                'item_name' => 'Whole Chicken',
                'unit_price' => 190.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'supplier_id' => 1002,
                'stock_type_id' => 2,
                'item_name' => 'Chicken Wings',
                'unit_price' => 175.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],

            // NutriAsia Condiments Distributor - Seasonings
            [
                'supplier_id' => 1003,
                'stock_type_id' => 1, // Seasonings
                'item_name' => 'Soy Sauce',
                'unit_price' => 85.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'supplier_id' => 1003,
                'stock_type_id' => 1,
                'item_name' => 'Vinegar',
                'unit_price' => 70.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],

            // Mega Packaging Solutions - MRO
            [
                'supplier_id' => 1004,
                'stock_type_id' => 3, // MRO
                'item_name' => 'Plastic Container',
                'unit_price' => 120.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'supplier_id' => 1004,
                'stock_type_id' => 3,
                'item_name' => 'Packaging Tape',
                'unit_price' => 100.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],

            // PureOil Philippines - Raw Materials
            [
                'supplier_id' => 1005,
                'stock_type_id' => 2,
                'item_name' => 'Cooking Oil',
                'unit_price' => 180.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],

            // FastServe Kitchen Equipment Corp. - MRO
            [
                'supplier_id' => 1006,
                'stock_type_id' => 3,
                'item_name' => 'Gas Stove',
                'unit_price' => 4500.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'supplier_id' => 1006,
                'stock_type_id' => 3,
                'item_name' => 'Oven',
                'unit_price' => 6200.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],

            // CleanPro Janitorial Supplies - MRO
            [
                'supplier_id' => 1007,
                'stock_type_id' => 3,
                'item_name' => 'Detergent',
                'unit_price' => 150.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'supplier_id' => 1007,
                'stock_type_id' => 3,
                'item_name' => 'Bleach',
                'unit_price' => 130.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],

            // FreshVeg Produce Supplier - Raw Materials
            [
                'supplier_id' => 1008,
                'stock_type_id' => 2,
                'item_name' => 'Tomato',
                'unit_price' => 60.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'supplier_id' => 1008,
                'stock_type_id' => 2,
                'item_name' => 'Onion',
                'unit_price' => 80.00,
                'price_type' => 'bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
        ];

        $this->db->table('supplier_items')->insertBatch($data);
    }
}
