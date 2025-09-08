<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BranchesSeeder extends Seeder
{
    public function run()
    {   // Dungag ko data para dali lng Testing
        $data = [
            [
                'branch_name' => 'Central',
                'location' => 'Davao City',
                'contact_info' => '09123456789',
                'status' => 'existing'
            ],
            [
                'branch_name' => 'Gensan',
                'location' => 'Poineer, GSC',
                'contact_info' => '09123456789',
                'status' => 'existing'
            ],
            [
                'branch_name' => 'Polomolok',
                'location' => 'Poblacion, Polomolok',
                'contact_info' => '09123456789',
                'status' => 'existing'
            ],
            [
                'branch_name' => 'Malapatan',
                'location' => 'Sa kanto',
                'contact_info' => '09123456789',
                'status' => 'existing'
            ],
            [
                'branch_name' => 'Digos',
                'location' => 'Digos',
                'contact_info' => '09123456789',
                'status' => 'existing'
            ],
            [
                'branch_name' => 'City Heights',
                'location' => 'City Heights, GSC',
                'contact_info' => '09123456789',
                'status' => 'franchise'
            ],
            [
                'branch_name' => 'Tupi',
                'location' => 'Tupi',
                'contact_info' => '09123456789',
                'status' => 'franchise'
            ],
            [
                'branch_name' => 'Saturn',
                'location' => 'Saturn',
                'contact_info' => '09123456789',
                'status' => 'upcoming'
            ],
            [
                'branch_name' => 'Mars',
                'location' => 'Mars',
                'contact_info' => '09123456789',
                'status' => 'upcoming'
            ]
        ];

        $this->db->table('branches')->insertBatch($data);
    }
}
