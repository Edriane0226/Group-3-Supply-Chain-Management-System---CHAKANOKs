<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPermissionsToRoles extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('description', 'roles')) {
            $this->forge->addColumn('roles', [
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'role_name',
                ],
            ]);
        }

        if (!$this->db->fieldExists('permissions', 'roles')) {
            $this->forge->addColumn('roles', [
                'permissions' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'description',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('permissions', 'roles')) {
            $this->forge->dropColumn('roles', 'permissions');
        }
    }
}
