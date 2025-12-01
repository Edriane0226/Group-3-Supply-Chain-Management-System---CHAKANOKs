<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceFranchisesTable extends Migration
{
    public function up()
    {
        // Add new columns to franchises table
        $this->forge->addColumn('franchises', [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'contact_info'
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'email'
            ],
            'proposed_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'address'
            ],
            'business_experience' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'proposed_location'
            ],
            'investment_capacity' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'after' => 'business_experience'
            ],
            'franchise_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'after' => 'royalty_rate'
            ],
            'contract_start' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'franchise_fee'
            ],
            'contract_end' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'contract_start'
            ],
            'branch_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'contract_end'
            ],
            'approved_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'branch_id'
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_by'
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'approved_at'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'rejection_reason'
            ],
        ]);

        // Modify status enum to include more options
        $this->db->query("ALTER TABLE franchises MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected', 'active', 'suspended', 'terminated') DEFAULT 'pending'");

        // Add foreign keys
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Remove foreign keys first
        $this->forge->dropForeignKey('franchises', 'franchises_branch_id_foreign');
        $this->forge->dropForeignKey('franchises', 'franchises_approved_by_foreign');

        // Remove added columns
        $this->forge->dropColumn('franchises', [
            'email',
            'address',
            'proposed_location',
            'business_experience',
            'investment_capacity',
            'franchise_fee',
            'contract_start',
            'contract_end',
            'branch_id',
            'approved_by',
            'approved_at',
            'rejection_reason',
            'notes'
        ]);

        // Revert status enum
        $this->db->query("ALTER TABLE franchises MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
}

