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
                'id'              => 23116000,
                'first_Name'      => 'Edriane',
                'last_Name'       => 'Bangonon',
                'middle_Name'     => 'Ordiz',
                'email'           => 'Ed@gmail.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Central Office Admin'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'id'              => 23116001,
                'first_Name'      => 'Jasper',
                'last_Name'       => 'Canitan',
                'middle_Name'     => '',
                'email'           => 'jaspercanitan@gmail.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Inventory Staff'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'id'              => 23116002,
                'first_Name'      => 'Marco',
                'last_Name'       => 'Batiller',
                'middle_Name'     => '',
                'email'           => 'marcobatiller@gmail.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Branch Manager'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'id'              => 23116003,
                'first_Name'      => 'Niel Vincent',
                'last_Name'       => 'Dionio',
                'middle_Name'     => '',
                'email'           => 'vincentdionio@gmail.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Logistics Coordinator'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'id'              => 23116004,
                'first_Name'      => 'Kristine',
                'last_Name'       => 'Amojallas',
                'middle_Name'     => '',
                'email'           => 'kristineamojallas@gmail.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['Franchise Manager'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'id'              => 23116005,
                'first_Name'      => 'Admin',
                'last_Name'       => 'System',
                'middle_Name'     => '',
                'email'           => 'admin@chakanoks.com',
                'password'        => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'         => $roleIds['System Administrator'] ?? null,
                'branch_id'       => $mainBranch->id ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]
        ];

        $usersTable = $this->db->table('users');
        foreach ($data as $user) {
            $userId = $user['id'];
            
            // Check if user exists by ID
            $existingById = $usersTable->where('id', $userId)->get()->getRowArray();
            
            if ($existingById) {
                // Update existing user by ID (keep the ID)
                $usersTable->where('id', $userId)->update($user);
            } else {
                // Check if user exists by email
                $existingByEmail = $usersTable->where('email', $user['email'])->get()->getRowArray();
                if ($existingByEmail) {
                    // Update existing user by email and set the ID
                    $usersTable->where('email', $user['email'])->update($user);
                } else {
                    // Insert new user with explicit ID
                    $usersTable->insert($user);
                }
            }
        }
    }
}
