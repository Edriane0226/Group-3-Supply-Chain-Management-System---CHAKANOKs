<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFranchisesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
            'type'=>'INT',
            'unsigned'=>true,
            'auto_increment'=>true
            ],
            'applicant_name'=>[
            'type'=>'VARCHAR',
            'constraint'=>150
            ],
            'contact_info'=>[
            'type'=>'VARCHAR',
            'constraint'=>150
            ],
            'status'=>[
            'type'=>'ENUM("pending","approved","rejected")',
            'default'=>'pending'
            ],
            'royalty_rate'=>[
            'type'=>'DECIMAL',
            'constraint'=>'5,2',
            'default'=>5.00
            ],
            'created_at'=>[
            'type'=>'DATETIME',
            'null'=>false
            ],
            'updated_at'=>[
            'type'=>'DATETIME',
            'null'=>true,
            'on_update'=>'CURRENT_TIMESTAMP'
            ],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('franchises');
    }

    public function down()
    {
        $this->forge->dropTable('franchises');
    }
}