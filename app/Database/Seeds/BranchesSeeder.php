<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BranchesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'branch_name' => 'Central',
                'location' => 'Davao City',
                // Add ug contact_info kung i try niyo
            ],
            [
                'branch_name' => 'Gensan Branch',
                'location' => 'Poineer, GSC',
            ],
            [
                'branch_name' => 'Polomolok Branch',
                'location' => 'Poblacion, Polomolok',
            ]
        ];

        $this->db->table('branches')->insertBatch($data);
    }
}
