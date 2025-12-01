<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceFranchisePaymentsTable extends Migration
{
    public function up()
    {
        // Add new columns to franchise_payments table
        $this->forge->addColumn('franchise_payments', [
            'payment_type' => [
                'type' => 'ENUM',
                'constraint' => ['franchise_fee', 'royalty', 'supply_payment', 'penalty', 'other'],
                'default' => 'royalty',
                'after' => 'franchise_id'
            ],
            'reference_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'amount'
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['cash', 'bank_transfer', 'check', 'gcash', 'maya', 'other'],
                'default' => 'cash',
                'after' => 'reference_number'
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'completed', 'failed', 'refunded'],
                'default' => 'completed',
                'after' => 'payment_method'
            ],
            'period_start' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'payment_status'
            ],
            'period_end' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'period_start'
            ],
            'recorded_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'remarks'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'recorded_by'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at'
            ],
        ]);

        // Add foreign key for recorded_by
        $this->forge->addForeignKey('recorded_by', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Remove foreign key first
        $this->forge->dropForeignKey('franchise_payments', 'franchise_payments_recorded_by_foreign');

        // Remove added columns
        $this->forge->dropColumn('franchise_payments', [
            'payment_type',
            'reference_number',
            'payment_method',
            'payment_status',
            'period_start',
            'period_end',
            'recorded_by',
            'created_at',
            'updated_at'
        ]);
    }
}

