<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixActivityLogsColumns extends Migration
{
    public function up()
    {
        // Ensure all required columns exist in activity_logs table
        $columns = [
            'user_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'user_id',
            ],
            'user_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'user_name',
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'action',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'module',
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'old_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];

        foreach ($columns as $columnName => $columnDef) {
            if (!$this->db->fieldExists($columnName, 'activity_logs')) {
                $this->forge->addColumn('activity_logs', [$columnName => $columnDef]);
            }
        }

        // Also ensure ip_address exists (it might be missing)
        if (!$this->db->fieldExists('ip_address', 'activity_logs')) {
            $this->forge->addColumn('activity_logs', [
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 45,
                    'null'       => true,
                ]
            ]);
        }
    }

    public function down()
    {
        // Don't remove columns in down() to avoid breaking existing data
    }
}

