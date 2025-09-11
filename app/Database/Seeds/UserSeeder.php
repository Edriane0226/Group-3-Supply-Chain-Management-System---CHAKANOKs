<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    
    public function run()
    {
        $mainBranch = $this->db->table('branches')->where('branch_name', 'Central')->get()->getRow();
        $branchA    = $this->db->table('branches')->where('branch_name', 'Gensan')->get()->getRow();
        $branchB    = $this->db->table('branches')->where('branch_name', 'Polomolok')->get()->getRow();

        $data = [
            [
                'first_Name'      => 'Edriane',
                'last_Name'       => 'Bangonon',
                'middle_Name'     => 'Ortiz',
                'email'           => 'Ed@gmail.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role'            => 'Central Office Admin',
                'branch_id'       => $mainBranch->id ?? null,
            ],
            [
                'first_Name'      => 'Maria',
                'last_Name'       => 'Santos',
                'middle_Name'     => '',
                'email'           => 'maria@example.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role'            => 'Inventory Staff',
                'branch_id'       => $branchA->id ?? null,
            ],
            [
                'first_Name'      => 'Pedro',
                'last_Name'       => 'Reyes',
                'middle_Name'     => '',
                'email'           => 'pedro@example.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role'            => 'Branch Manager',
                'branch_id'       => $branchB->id ?? null,
            ],
        ];

        $usersTable = $this->db->table('users');
        foreach ($data as $user) {
            $existing = $usersTable->where('email', $user['email'])->get()->getRowArray();
            if ($existing) {
                // Update existing user to ensure seeder is idempotent
                $usersTable->where('email', $user['email'])->update($user);
            } else {
                $usersTable->insert($user);
            }
        }
    }
}
