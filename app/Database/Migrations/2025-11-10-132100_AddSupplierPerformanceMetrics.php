<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSupplierPerformanceMetrics extends Migration
{
    public function up()
    {
        $this->forge->addColumn('suppliers', [
            'on_time_delivery_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
                'null'       => false,
                'after'      => 'terms',
            ],
            'quality_rating' => [
                'type'       => 'DECIMAL',
                'constraint' => '3,2',
                'default'    => 0.00,
                'null'       => false,
                'after'      => 'on_time_delivery_rate',
            ],
            'total_orders' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
                'null'     => false,
                'after'    => 'quality_rating',
            ],
            'total_deliveries' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
                'null'     => false,
                'after'    => 'total_orders',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('suppliers', [
            'on_time_delivery_rate',
            'quality_rating',
            'total_orders',
            'total_deliveries',
        ]);
    }
}
