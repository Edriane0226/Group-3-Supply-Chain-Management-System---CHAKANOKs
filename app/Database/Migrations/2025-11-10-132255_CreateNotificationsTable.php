<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'type' => [
                'type' => 'ENUM("email","sms","in_app")',
                'null' => false,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'reference_type' => [
                'type' => 'ENUM("purchase_request","purchase_order","delivery","supplier")',
                'null' => false,
            ],
            'reference_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'status' => [
                'type' => 'ENUM("pending","sent","failed")',
                'default' => 'pending',
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_at' => [
                'type'     => 'DATETIME',
                'null'     => true,
                'default'  => null,
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}
