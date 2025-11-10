<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingFieldsToPurchaseRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('purchase_requests', [
            'item_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'after'      => 'supplier_id',
            ],
            'quantity' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'item_name',
            ],
            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pcs',
                'null'       => false,
                'after'      => 'quantity',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'unit',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('purchase_requests', ['item_name', 'quantity', 'unit', 'description']);
    }
}
