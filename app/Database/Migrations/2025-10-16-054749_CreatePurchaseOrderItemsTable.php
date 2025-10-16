<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'=>'INT',
                'unsigned'=>true,
                'auto_increment'=>true
            ],
            'purchase_order_id' => [
                'type'=>'INT',
                'unsigned'=>true
            ],
            'stock_in_id' => [  // fixed reference
                'type'=>'INT',
                'unsigned'=>true
            ],
            'quantity' => [
                'type'=>'INT',
                'unsigned'=>true
            ],
            'unit_price' => [
                'type'=>'DECIMAL',
                'constraint'=>'10,2'
            ],
            'subtotal' => [
                'type'=>'DECIMAL',
                'constraint'=>'12,2'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_order_id', 'purchase_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('stock_in_id', 'stock_in', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_order_items');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_order_items');
    }
}