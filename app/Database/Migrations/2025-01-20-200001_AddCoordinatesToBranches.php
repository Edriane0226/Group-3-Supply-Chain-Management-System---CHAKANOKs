<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add Coordinates to Branches Table
 * Adds latitude and longitude fields for route optimization
 */
class AddCoordinatesToBranches extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check if columns already exist
        if (!$db->fieldExists('latitude', 'branches')) {
            $this->forge->addColumn('branches', [
                'latitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,8',
                    'null' => true,
                    'after' => 'location',
                    'comment' => 'Branch latitude for route optimization'
                ],
                'longitude' => [
                    'type' => 'DECIMAL',
                    'constraint' => '11,8',
                    'null' => true,
                    'after' => 'latitude',
                    'comment' => 'Branch longitude for route optimization'
                ]
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        if ($db->fieldExists('latitude', 'branches')) {
            $this->forge->dropColumn('branches', ['latitude', 'longitude']);
        }
    }
}

