<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixBranchTransfersItemName extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Add item_name column if it doesn't exist
        if (!$db->fieldExists('item_name', 'branch_transfers')) {
            $this->forge->addColumn('branch_transfers', [
                'item_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 150,
                    'null' => true,
                    'after' => 'stock_in_id',
                    'comment' => 'Item name for easier reference'
                ]
            ]);
        }

        // Add unit column if it doesn't exist
        if (!$db->fieldExists('unit', 'branch_transfers')) {
            $this->forge->addColumn('branch_transfers', [
                'unit' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'pcs',
                    'null' => true,
                    'after' => 'quantity',
                    'comment' => 'Unit of measurement'
                ]
            ]);
        }

        // Add requested_by column if it doesn't exist
        if (!$db->fieldExists('requested_by', 'branch_transfers')) {
            $this->forge->addColumn('branch_transfers', [
                'requested_by' => [
                    'type' => 'INT',
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'from_branch_id',
                    'comment' => 'User ID who requested the transfer'
                ]
            ]);
        }

        // Add approved_by column if it doesn't exist
        if (!$db->fieldExists('approved_by', 'branch_transfers')) {
            $this->forge->addColumn('branch_transfers', [
                'approved_by' => [
                    'type' => 'INT',
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'status',
                    'comment' => 'Branch Manager ID who approved the transfer'
                ]
            ]);
        }

        // Add notes column if it doesn't exist
        if (!$db->fieldExists('notes', 'branch_transfers')) {
            $this->forge->addColumn('branch_transfers', [
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'approved_by',
                    'comment' => 'Transfer notes or reason'
                ]
            ]);
        }

        // Add approved_at column if it doesn't exist
        if (!$db->fieldExists('approved_at', 'branch_transfers')) {
            $this->forge->addColumn('branch_transfers', [
                'approved_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'notes',
                    'comment' => 'When the transfer was approved'
                ]
            ]);
        }

        // Add completed_at column if it doesn't exist
        if (!$db->fieldExists('completed_at', 'branch_transfers')) {
            $this->forge->addColumn('branch_transfers', [
                'completed_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'approved_at',
                    'comment' => 'When the transfer was completed'
                ]
            ]);
        }

        // Add updated_at column if it doesn't exist
        if (!$db->fieldExists('updated_at', 'branch_transfers')) {
            $this->forge->addColumn('branch_transfers', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'completed_at',
                    'on_update' => 'CURRENT_TIMESTAMP',
                    'comment' => 'Last update timestamp'
                ]
            ]);
        }
    }

    public function down()
    {
        // This migration is safe to rollback, but we'll keep the columns
        // as they're needed for the system to function
    }
}

