<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryTable extends Migration
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
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'unique'     => true,
            ],
            'reorder_level' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 10, 
            ],
            'updated_at' => [
                'type'     => 'DATETIME',
                'null'     => true,
                'default'  => null,
                'on_update'=> 'CURRENT_TIMESTAMP',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inventory');
    }

    public function down()
    {
        $this->forge->dropTable('inventory', true);
    }
}