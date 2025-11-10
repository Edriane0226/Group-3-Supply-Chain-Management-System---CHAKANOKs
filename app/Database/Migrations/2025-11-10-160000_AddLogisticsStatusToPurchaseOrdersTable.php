<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLogisticsStatusToPurchaseOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('purchase_orders', [
            'logistics_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending_review', 'supplier_coordination', 'supplier_coordinated', 'delivery_scheduled', 'delivery_started', 'branch_notified', 'completed'],
                'default' => 'pending_review',
                'null' => false,
                'after' => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('purchase_orders', 'logistics_status');
    }
}
