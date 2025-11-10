<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePurchaseOrdersTableForTracking extends Migration
{
    public function up()
    {
        // Update status enum to include new statuses
        $this->db->query("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('pending','approved','in_transit','delivered','cancelled') DEFAULT 'pending'");

        $this->forge->addColumn('purchase_orders', [
            'approved_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'supplier_id',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_by',
            ],
            'expected_delivery_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'total_amount',
            ],
            'actual_delivery_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'expected_delivery_date',
            ],
            'tracking_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'actual_delivery_date',
            ],
            'delivery_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'tracking_number',
            ],
        ]);

        // Add foreign key for approved_by
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('purchase_orders', 'purchase_orders_approved_by_foreign');

        $this->forge->dropColumn('purchase_orders', [
            'approved_by',
            'approved_at',
            'expected_delivery_date',
            'actual_delivery_date',
            'tracking_number',
            'delivery_notes',
        ]);

        // Revert status enum
        $this->db->query("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('pending','approved','rejected','delivered') DEFAULT 'pending'");
    }
}
