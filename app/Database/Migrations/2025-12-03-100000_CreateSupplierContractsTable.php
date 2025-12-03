<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupplierContractsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'supplier_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'contract_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'contract_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Supply Agreement', 'Service Contract', 'Exclusive Agreement', 'Non-Exclusive Agreement'],
                'default'    => 'Supply Agreement',
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'renewal_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'auto_renewal' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'payment_terms' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'e.g., Net 30, Net 15, COD',
            ],
            'minimum_order_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'discount_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'default'    => 0.00,
                'comment'    => 'Percentage discount',
            ],
            'delivery_terms' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'quality_standards' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'penalty_clauses' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'active', 'expired', 'terminated', 'renewed'],
                'default'    => 'draft',
            ],
            'signed_by_supplier' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'signed_by_admin' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'signed_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('contract_number');
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('supplier_contracts');
    }

    public function down()
    {
        $this->forge->dropTable('supplier_contracts');
    }
}

