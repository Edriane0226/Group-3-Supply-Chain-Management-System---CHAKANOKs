<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeliveriesSeeder extends Seeder
{
    public function run()
    {
        // Get purchase orders that are delivered or in transit
        $purchaseOrders = $this->db->table('purchase_orders')
            ->whereIn('status', ['Delivered', 'In_Transit', 'Approved'])
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        // Get suppliers for supplier names
        $suppliers = $this->db->table('suppliers')
            ->select('id, supplier_name')
            ->get()
            ->getResultArray();
        
        $supplierMap = [];
        foreach ($suppliers as $supplier) {
            $supplierMap[$supplier['id']] = $supplier['supplier_name'];
        }
        
        $data = [];
        $statuses = ['Pending', 'Received', 'Pending', 'Received'];
        
        foreach ($purchaseOrders as $index => $po) {
            $supplierName = $supplierMap[$po['supplier_id']] ?? 'Unknown Supplier';
            $deliveryDate = $po['actual_delivery_date'] ?? $po['expected_delivery_date'] ?? date('Y-m-d', strtotime('+' . ($index + 1) . ' days'));
            $status = $statuses[min($index, count($statuses) - 1)];
            
            $data[] = [
                'supplier_name' => $supplierName,
                'branch_id' => $po['branch_id'],
                'delivery_date' => $deliveryDate,
                'status' => $status,
                'remarks' => $status === 'Received' ? 'All items received in good condition' : 'Delivery scheduled',
                'created_at' => $po['created_at'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($data)) {
            $this->db->table('deliveries')->insertBatch($data);
        }
    }
}

