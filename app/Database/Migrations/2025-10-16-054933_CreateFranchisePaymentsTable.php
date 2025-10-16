<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFranchisePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
            'type'=>'INT',
            'unsigned'=>true,
            'auto_increment'=>true
            ],
            'franchise_id'=>[
            'type'=>'INT',
            'unsigned'=>true
            ],
            'amount'=>[
            'type'=>'DECIMAL',
            'constraint'=>'12,2'
            ],
            'payment_date'=>[
            'type'=>'DATETIME',
            'null'=>false
            ],
            'remarks'=>[
            'type'=>'TEXT',
            'null'=>true
            ],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->addForeignKey('franchise_id','franchises','id','CASCADE','CASCADE');
        $this->forge->createTable('franchise_payments');
    }

    public function down()
    {
        $this->forge->dropTable('franchise_payments');
    }
}