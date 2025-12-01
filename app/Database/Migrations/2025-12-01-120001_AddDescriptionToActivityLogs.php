<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescriptionToActivityLogs extends Migration
{
    public function up()
    {
        // Add description column if it doesn't exist
        if (!$this->db->fieldExists('description', 'activity_logs')) {
            $this->forge->addColumn('activity_logs', [
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'module',
                ],
            ]);
        }
    }

    public function down()
    {
        // Remove description column if it exists
        if ($this->db->fieldExists('description', 'activity_logs')) {
            $this->forge->dropColumn('activity_logs', 'description');
        }
    }
}

