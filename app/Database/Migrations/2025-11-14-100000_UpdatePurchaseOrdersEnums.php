<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePurchaseOrdersEnums extends Migration
{
    public function up()
    {
        // Update status enum to include supplier workflow statuses
        $this->db->query("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('Pending','Approved','Rejected','Delivered','In_Transit','Confirmed','Preparing','Ready for Pickup') DEFAULT 'Pending'");
        
        // Update logistics_status enum to include supplier confirmation statuses
        $this->db->query("ALTER TABLE purchase_orders MODIFY COLUMN logistics_status ENUM('pending_review', 'supplier_coordination', 'supplier_coordinated', 'supplier_confirmed', 'supplier_preparing', 'ready_for_pickup', 'delivery_scheduled', 'delivery_started', 'branch_notified', 'completed') DEFAULT 'pending_review'");
    }

    public function down()
    {
        // Revert status enum to original values
        $this->db->query("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('Pending','Approved','Rejected','Delivered','In_Transit') DEFAULT 'Pending'");
        
        // Revert logistics_status enum to original values
        $this->db->query("ALTER TABLE purchase_orders MODIFY COLUMN logistics_status ENUM('pending_review', 'supplier_coordination', 'supplier_coordinated', 'delivery_scheduled', 'delivery_started', 'branch_notified', 'completed') DEFAULT 'pending_review'");
    }
}

