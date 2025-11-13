<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SupplierItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'supplier_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'null'           => false,
            ],
            'stock_type_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'null'           => false,
            ],
            'item_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 150,
                'null'           => false,
            ],
            'unit_price' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,2',
                'null'           => false,
            ],
            'price_type' => [
                'type'           => 'ENUM',
                'constraint'     => ['retail', 'bulk'],
                'default'        => 'retail',
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => false,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('stock_type_id', 'stock_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('supplier_items');
    }


    public function down()
    {
        $this->forge->dropTable('supplier_items');
    }
}
