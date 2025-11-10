<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    
    public function run()
    {
        // First, ensure the RoleSeeder has been run
        $this->call('App\Database\Seeds\RoleSeeder');
        
        // Get role IDs
        $roles = $this->db->table('roles')
                         ->select('id, role_name')
                         ->get()
                         ->getResultArray();
        
        // Convert roles array to a more accessible format
        $roleIds = [];
        foreach ($roles as $role) {
            $roleIds[$role['role_name']] = $role['id'];
        }
        
        // Get branch IDs
        $mainBranch = $this->db->table('branches')->where('branch_name', 'Central')->get()->getRow();
        $branchA    = $this->db->table('branches')->where('branch_name', 'Gensan')->get()->getRow();
        $branchB    = $this->db->table('branches')->where('branch_name', 'Polomolok')->get()->getRow();
        // Same Branch for all users for testing lng sa ngayon
        $data = [
            [
                'first_Name'      => 'Edriane',
                'last_Name'       => 'Bangonon',
                'middle_Name'     => 'Ortiz',
                'email'           => 'Ed@gmail.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Central Office Admin'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'first_Name'      => 'Maria',
                'last_Name'       => 'Santos',
                'middle_Name'     => '',
                'email'           => 'maria@example.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Inventory Staff'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'first_Name'      => 'Pedro',
                'last_Name'       => 'Reyes',
                'middle_Name'     => '',
                'email'           => 'pedro@example.com',
                'password'       => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Branch Manager'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'first_Name'      => 'Juan',
                'last_Name'       => 'Dela Cruz',
                'middle_Name'     => '',
                'email'           => 'juan@example.com',
                'password'       => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Logistics Coordinator'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
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
