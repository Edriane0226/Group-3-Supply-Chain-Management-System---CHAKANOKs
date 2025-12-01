<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFranchiseSupplyAllocationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'franchise_id' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'item_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'quantity' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'pcs'
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ],
            'allocation_date' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'delivery_date' => [
                'type' => 'DATE',
                'null' => true
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'preparing', 'shipped', 'delivered', 'cancelled'],
                'default' => 'pending'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'allocated_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('franchise_id', 'franchises', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('allocated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('franchise_supply_allocations');
    }

    public function down()
    {
        $this->forge->dropTable('franchise_supply_allocations');
    }
}

