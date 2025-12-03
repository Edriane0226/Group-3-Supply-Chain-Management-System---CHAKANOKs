<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Create Accounts Payable Table
 * Tracks payments to suppliers for invoices
 */
class CreateAccountsPayableTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'purchase_order_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'comment'  => 'Linked purchase order/invoice'
            ],
            'supplier_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'invoice_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => false,
                'comment'    => 'Total invoice amount'
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Payment due date based on terms'
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'partial', 'paid', 'overdue'],
                'default'    => 'pending',
            ],
            'amount_paid' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
                'comment'    => 'Total amount paid so far'
            ],
            'balance_due' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => false,
                'comment'    => 'Remaining balance to pay'
            ],
            'payment_terms' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Payment terms (Net 30, Net 15, etc.)'
            ],
            'invoice_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Date invoice was uploaded'
            ],
            'paid_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Date payment was completed'
            ],
            'payment_method' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'bank_transfer', 'check', 'gcash', 'maya', 'other'],
                'null'       => true,
            ],
            'payment_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Payment reference number'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type'     => 'DATETIME',
                'null'     => true,
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_order_id', 'purchase_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('accounts_payable');
    }

    public function down()
    {
        $this->forge->dropTable('accounts_payable');
    }
}

