<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeliveryItemsTable extends Migration
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
            'item_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'quantity' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'pcs',
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'item_type_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('delivery_id', 'deliveries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_type_id', 'stock_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('delivery_items');
    }

    public function down()
    {
        $this->forge->dropTable('delivery_items');
    }
}
