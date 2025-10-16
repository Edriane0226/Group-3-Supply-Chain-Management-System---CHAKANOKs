<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBranchesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'branch_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'contact_info' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [       //Para Makahandle Ug Status sa branches
                'type'       => 'ENUM("existing","upcoming","franchise")',
                'default'    => 'existing',
                'null'       => false,
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
        $this->forge->createTable('branches');
    }

    public function down()
    {
        $this->forge->dropTable('branches');
    }
}