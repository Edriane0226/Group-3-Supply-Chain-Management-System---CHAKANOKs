<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateDeliverySchedulesTableForPO extends Migration
{
    public function up()
    {
        // Drop the old foreign key
        $this->forge->dropForeignKey('delivery_schedules', 'delivery_schedules_delivery_id_foreign');

        // Drop the old column
        $this->forge->dropColumn('delivery_schedules', 'delivery_id');

        // Add new columns
        $this->forge->addColumn('delivery_schedules', [
            'po_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'after'    => 'id',
            ],
            'coordinator_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'after'    => 'po_id',
            ],
            'driver_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'coordinator_id',
            ],
            'vehicle_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'driver_id',
            ],
        ]);

        // Drop the old foreign key for logistics_coordinator_id
        $this->forge->dropForeignKey('delivery_schedules', 'delivery_schedules_logistics_coordinator_id_foreign');

        // Drop the old column
        $this->forge->dropColumn('delivery_schedules', 'logistics_coordinator_id');

        // Add new foreign keys
        $this->forge->addForeignKey('po_id', 'purchase_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('coordinator_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('driver_id', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('vehicle_id', 'vehicles', 'id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        // Drop new foreign keys
        $this->forge->dropForeignKey('delivery_schedules', 'delivery_schedules_po_id_foreign');
        $this->forge->dropForeignKey('delivery_schedules', 'delivery_schedules_coordinator_id_foreign');
        $this->forge->dropForeignKey('delivery_schedules', 'delivery_schedules_driver_id_foreign');
        $this->forge->dropForeignKey('delivery_schedules', 'delivery_schedules_vehicle_id_foreign');

        // Drop new columns
        $this->forge->dropColumn('delivery_schedules', 'po_id');
        $this->forge->dropColumn('delivery_schedules', 'coordinator_id');
        $this->forge->dropColumn('delivery_schedules', 'driver_id');
        $this->forge->dropColumn('delivery_schedules', 'vehicle_id');

        // Add back old columns
        $this->forge->addColumn('delivery_schedules', [
            'delivery_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'logistics_coordinator_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
        ]);

        // Add back old foreign keys
        $this->forge->addForeignKey('delivery_id', 'deliveries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('logistics_coordinator_id', 'users', 'id', 'CASCADE', 'CASCADE');
    }
}
