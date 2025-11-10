<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeliverySchedulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
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
            'scheduled_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'scheduled_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'route_sequence' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'estimated_duration' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'comment'  => 'Duration in minutes',
            ],
            'route_coordinates' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON string of route coordinates',
            ],
            'status' => [
                'type' => 'ENUM("Scheduled","In Progress","Completed","Cancelled")',
                'default' => 'Scheduled',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_at' => [
                'type'     => 'DATETIME',
                'null'     => true,
                'default'  => null,
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('delivery_id', 'deliveries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('logistics_coordinator_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('delivery_schedules');
    }

    public function down()
    {
        $this->forge->dropTable('delivery_schedules');
    }
}
