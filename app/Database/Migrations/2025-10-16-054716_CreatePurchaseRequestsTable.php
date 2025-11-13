<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'branch_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'supplier_id' => [ // must match suppliers.id
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'request_date' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'status' => [
                'type'       => 'ENUM("pending","approved","rejected")',
                'default'    => 'pending',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'on update' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_requests');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_requests');
    }
}