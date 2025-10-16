<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBranchTransfersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
            'type'=>'INT',
            'unsigned'=>true,
            'auto_increment'=>true
            ],
            'from_branch_id'=>[
            'type'=>'INT',
            'unsigned'=>true
            ],
            'to_branch_id'=>[
            'type'=>'INT',
            'unsigned'=>true
            ],
            'stock_in_id'=>[
            'type'=>'INT',
            'unsigned'=>true
            ],
            'quantity'=>[
            'type'=>'INT',
            'unsigned'=>true
            ],
            'status'=>[
            'type'=>'ENUM("pending","approved","completed")',
            'default'=>'pending'
            ],
            'created_at'=>[
            'type'=>'DATETIME',
            'null'=>false
            ],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->addForeignKey('from_branch_id','branches','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('to_branch_id','branches','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('stock_in_id','stock_in','id','CASCADE','CASCADE');
        $this->forge->createTable('branch_transfers');
    }

    public function down()
    {
        $this->forge->dropTable('branch_transfers');
    }
}