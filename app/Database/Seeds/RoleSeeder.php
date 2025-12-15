<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'role_name' => 'Central Office Admin',
                'permissions' => json_encode(['dashboard.view', 'system.full_access']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'role_name' => 'Branch Manager',
                'permissions' => json_encode([
                    'dashboard.view',
                    'purchase_requests.view',
                    'purchase_requests.create',
                    'branch_transfers.view',
                    'branch_transfers.create',
                    'branch_transfers.approve',
                    'branch_transfers.reject',
                    'branch_transfers.complete',
                    'deliveries.view',
                    'inventory.view'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'role_name' => 'Inventory Staff',
                'permissions' => json_encode([
                    'dashboard.view',
                    'inventory.view',
                    'inventory.manage',
                    'inventory.staff_portal',
                    'inventory.reports',
                    'deliveries.view'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'role_name' => 'Logistics Coordinator',
                'permissions' => json_encode(['dashboard.view', 'logistics.view', 'logistics.manage']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'role_name' => 'Franchise Manager',
                'permissions' => json_encode([
                    'dashboard.view',
                    'franchise.view',
                    'franchise.manage',
                    'franchise.dashboard',
                    'franchise.applications',
                    'franchise.franchises',
                    'franchise.payments',
                    'franchise.allocations',
                    'franchise.reports',
                    'franchise.manage_franchises',
                    'franchise.send_reminders'
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'role_name' => 'Supplier',
                'permissions' => json_encode(['dashboard.view']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'role_name' => 'System Administrator',
                'permissions' => json_encode(['system.full_access']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Check if roles already exist, update permissions if they exist, otherwise insert
        foreach ($data as $role) {
            $existing = $this->db->table('roles')->where('role_name', $role['role_name'])->get()->getRow();
            if ($existing) {
                // Always update permissions to ensure they're current
                $this->db->table('roles')
                    ->where('role_name', $role['role_name'])
                    ->update(['permissions' => $role['permissions'], 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $this->db->table('roles')->insert($role);
            }
        }
    }
}
