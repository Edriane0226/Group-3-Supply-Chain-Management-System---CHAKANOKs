<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'branch_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'supplier_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'approved_by' => [ 
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'purchase_request_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'status' => [
                'type'    => 'ENUM("Pending","Approved","Rejected","Delivered","In_Transit")',
                'default' => 'Pending',
            ],
            'logistics_status' => [ 
                'type' => 'ENUM',
                'constraint' => ['pending_review', 'supplier_coordination', 'supplier_coordinated', 'delivery_scheduled', 'delivery_started', 'branch_notified', 'completed'],
                'default' => 'pending_review',
                'null' => false,
                'after' => 'status',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'expected_delivery_date' => [ 
                'type' => 'DATE',
                'null' => true,
            ],
            'actual_delivery_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tracking_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'delivery_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('purchase_request_id', 'purchase_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_orders');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_orders');
    }
}
