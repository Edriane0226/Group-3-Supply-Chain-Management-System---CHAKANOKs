<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@gmail.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'name'     => 'User',
                'email'    => 'user@gmail.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role'     => 'user',
            ]
        ];

        $builder = $this->db->table('users');

        foreach ($users as $user) {
            // Check if email already exists
            $existing = $builder->where('email', $user['email'])->get()->getRow();
            if (!$existing) {
                $builder->insert($user);
            }
        }
    }
}
