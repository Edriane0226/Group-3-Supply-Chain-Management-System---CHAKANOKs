<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeliveriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
            'type'=>'INT',
            'unsigned'=>true,
            'auto_increment'=>true
            ],
            'purchase_order_id'=>[
            'type'=>'INT',
            'unsigned'=>true
            ],
            'delivered_by'=>[
            'type'=>'VARCHAR',
            'constraint'=>100,
            'null'=>true
            ],
            'delivery_date'=>[
            'type'=>'DATETIME',
            'null'=>false
            ],
            'status'=>[
            'type'=>'ENUM("in_transit","delivered","delayed")',
            'default'=>'in_transit'
            ],
            'remarks'=>[
            'type'=>'TEXT',
            'null'=>true
            ],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->addForeignKey('purchase_order_id','purchase_orders','id','CASCADE','CASCADE');
        $this->forge->createTable('deliveries');
    }

    public function down()
    {
        $this->forge->dropTable('deliveries');
    }
}