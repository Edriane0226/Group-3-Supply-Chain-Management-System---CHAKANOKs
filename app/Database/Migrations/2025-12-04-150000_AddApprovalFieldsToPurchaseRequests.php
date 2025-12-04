<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovalFieldsToPurchaseRequests extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Add approved_by column if it doesn't exist
        if (!$db->fieldExists('approved_by', 'purchase_requests')) {
            $this->forge->addColumn('purchase_requests', [
                'approved_by' => [
                    'type' => 'INT',
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'status',
                    'comment' => 'User ID who approved this request'
                ]
            ]);
        }
        
        // Add approved_at column if it doesn't exist
        if (!$db->fieldExists('approved_at', 'purchase_requests')) {
            $this->forge->addColumn('purchase_requests', [
                'approved_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'approved_by',
                    'comment' => 'When the request was approved'
                ]
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Remove columns if they exist
        if ($db->fieldExists('approved_at', 'purchase_requests')) {
            $this->forge->dropColumn('purchase_requests', 'approved_at');
        }
        
        if ($db->fieldExists('approved_by', 'purchase_requests')) {
            $this->forge->dropColumn('purchase_requests', 'approved_by');
        }
    }
}

