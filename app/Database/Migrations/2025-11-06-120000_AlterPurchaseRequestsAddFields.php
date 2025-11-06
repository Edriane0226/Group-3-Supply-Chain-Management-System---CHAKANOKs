<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPurchaseRequestsAddFields extends Migration
{
    public function up()
    {
        // Add new columns if they don't exist
        $fields = [
            'item_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'after' => 'supplier_id',
            ],
            'quantity' => [
                'type' => 'INT',
                'null' => false,
                'after' => 'item_name',
            ],
            'unit' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'quantity',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'unit',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'description',
            ],
        ];

        foreach ($fields as $name => $definition) {
            if (!$this->db->fieldExists($name, 'purchase_requests')) {
                $this->forge->addColumn('purchase_requests', [ $name => $definition ]);
            }
        }

        // Update ENUM values for status to include 'cancelled' and standardize lowercase
        $this->db->query("ALTER TABLE `purchase_requests` MODIFY `status` ENUM('pending','approved','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert status enum to previous (pending, approved, rejected)
        $this->db->query("ALTER TABLE `purchase_requests` MODIFY `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");

        // Drop the added columns if they exist
        $drop = [];
        foreach (['remarks','description','unit','quantity','item_name'] as $col) {
            if ($this->db->fieldExists($col, 'purchase_requests')) {
                $drop[] = $col;
            }
        }
        if (!empty($drop)) {
            $this->forge->dropColumn('purchase_requests', $drop);
        }
    }
}
