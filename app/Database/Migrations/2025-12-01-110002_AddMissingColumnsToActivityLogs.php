<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingColumnsToActivityLogs extends Migration
{
    public function up()
    {
        // Add missing columns to existing activity_logs table
        $fields = [];

        // Check and add user_name
        if (!$this->db->fieldExists('user_name', 'activity_logs')) {
            $fields['user_name'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'user_id',
            ];
        }

        // Check and add user_role
        if (!$this->db->fieldExists('user_role', 'activity_logs')) {
            $fields['user_role'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'user_name',
            ];
        }

        // Check and add module
        if (!$this->db->fieldExists('module', 'activity_logs')) {
            $fields['module'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'action',
            ];
        }

        // Check and add ip_address
        if (!$this->db->fieldExists('ip_address', 'activity_logs')) {
            $fields['ip_address'] = [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ];
        }

        // Check and add user_agent
        if (!$this->db->fieldExists('user_agent', 'activity_logs')) {
            $fields['user_agent'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ];
        }

        // Check and add old_data
        if (!$this->db->fieldExists('old_data', 'activity_logs')) {
            $fields['old_data'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        // Check and add new_data
        if (!$this->db->fieldExists('new_data', 'activity_logs')) {
            $fields['new_data'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('activity_logs', $fields);
        }
    }

    public function down()
    {
        // Remove added columns
        $columns = ['user_name', 'user_role', 'module', 'ip_address', 'user_agent', 'old_data', 'new_data'];
        
        foreach ($columns as $column) {
            if ($this->db->fieldExists($column, 'activity_logs')) {
                $this->forge->dropColumn('activity_logs', $column);
            }
        }
    }
}

