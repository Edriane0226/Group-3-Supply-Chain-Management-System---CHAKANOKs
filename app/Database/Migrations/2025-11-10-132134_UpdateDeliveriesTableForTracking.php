<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateDeliveriesTableForTracking extends Migration
{
    public function up()
    {
        // Update status enum to include new statuses
        $this->db->query("ALTER TABLE deliveries MODIFY COLUMN status ENUM('Pending','Approved','In Transit','Delivered','Received','Cancelled') DEFAULT 'Pending'");

        $this->forge->addColumn('deliveries', [
            'logistics_coordinator_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'branch_id',
            ],
            'estimated_eta' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'delivery_date',
            ],
            'actual_delivery_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'estimated_eta',
            ],
            'route_optimized' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'remarks',
            ],
            'tracking_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'route_optimized',
            ],
            'vehicle_info' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'tracking_number',
            ],
        ]);

        // Add foreign key for logistics coordinator
        $this->forge->addForeignKey('logistics_coordinator_id', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('deliveries', 'deliveries_logistics_coordinator_id_foreign');

        $this->forge->dropColumn('deliveries', [
            'logistics_coordinator_id',
            'estimated_eta',
            'actual_delivery_time',
            'route_optimized',
            'tracking_number',
            'vehicle_info',
        ]);

        // Revert status enum
        $this->db->query("ALTER TABLE deliveries MODIFY COLUMN status ENUM('Pending','Received','Cancelled') DEFAULT 'Pending'");
    }
}
