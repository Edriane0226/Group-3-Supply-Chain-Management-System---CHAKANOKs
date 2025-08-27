<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                 'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'email' => [
                'type' => 'VARCHAR',
                 'constraint' => 100,
                  'unique' => true
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'branch' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        $this->db->query('ALTER TABLE users AUTO_INCREMENT = 23116000;');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
