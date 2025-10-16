<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockInTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'item_type_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'branch_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'item_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'quantity' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
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
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_at' => [
                'type'     => 'DATETIME',
                'null'     => true,
                'default'  => null,
                'on_update'=> 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_type_id', 'stock_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_in');
    }

    public function down()
    {
        $this->forge->dropTable('stock_in');
    }
}