<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add Invoice Document Upload Support
 * Adds invoice_document_path column to purchase_orders table for suppliers to upload invoice documents
 */
class AddInvoiceDocumentToPurchaseOrders extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check if column already exists
        if (!$db->fieldExists('invoice_document_path', 'purchase_orders')) {
            $this->forge->addColumn('purchase_orders', [
                'invoice_document_path' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'delivery_notes',
                    'comment' => 'Path to uploaded invoice document file'
                ],
                'invoice_uploaded_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'invoice_document_path',
                    'comment' => 'When the invoice document was uploaded'
                ]
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        if ($db->fieldExists('invoice_document_path', 'purchase_orders')) {
            $this->forge->dropColumn('purchase_orders', ['invoice_document_path', 'invoice_uploaded_at']);
        }
    }
}

