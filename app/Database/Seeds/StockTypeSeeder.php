<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StockTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'type_name' => 'Seasonings'
            ],
            [
                'type_name' => 'Raw Materials'
            ],
            [
                'type_name' => 'MRO(Maintenance, Repair, Operating)'
            ]
        ];
        $this->db->table('stock_types')->insertBatch($data);
    }
}
