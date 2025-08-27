<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'   => 'Edriane Bangonon',
                'email'  => 'juan@example.com',
                'role'   => 'admin',
                'branch' => 'Main Branch',
            ],
            [
                'name'   => 'Maria Santos',
                'email'  => 'maria@example.com',
                'role'   => 'Inventory Staff',
                'branch' => 'Branch A',
            ],
            [
                'name'   => 'Pedro Reyes',
                'email'  => 'pedro@example.com',
                'role'   => 'Branch Manager',
                'branch' => 'Branch A',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
