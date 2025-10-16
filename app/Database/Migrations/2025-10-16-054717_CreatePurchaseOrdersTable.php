<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
            'type'=>'INT',
            'unsigned'=>true,
            'auto_increment'=>true
            ],
            'branch_id' => [
            'type'=>'INT',
            'unsigned'=>true
            ],
            'supplier_id' => [
            'type'=>'INT',
            'unsigned'=>true
            ],
            'status' => [
            'type'=>'ENUM("pending","approved","rejected","delivered")',
            'default'=>'pending',
            ],
            'total_amount' => [
            'type'=>'DECIMAL',
            'constraint'=>'12,2',
            'default'=>0
            ],
            'created_at' => [
            'type'=>'DATETIME',
            'null'=>false
            ],
            'updated_at' => [
            'type'=>'DATETIME',
            'null'=>true,
            'on_update'=>'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_orders');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_orders');
    }
}