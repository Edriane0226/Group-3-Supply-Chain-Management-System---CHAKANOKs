<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurchaseOrdersSeeder extends Seeder
{
    public function run()
    {
        // Get purchase requests that are approved
        $purchaseRequests = $this->db->table('purchase_requests')
            ->where('status', 'approved')
            ->orWhere('status', 'ordered')
            ->get()
            ->getResultArray();
        
        if (empty($purchaseRequests)) {
            // If no approved requests, create orders from any requests
            $purchaseRequests = $this->db->table('purchase_requests')
                ->limit(3)
                ->get()
                ->getResultArray();
        }
        
        $data = [];
        $statuses = ['Pending', 'Approved', 'In_Transit', 'Delivered'];
        $logisticsStatuses = ['pending_review', 'supplier_coordination', 'delivery_scheduled', 'completed'];
        
        foreach ($purchaseRequests as $index => $request) {
            $status = $statuses[min($index, count($statuses) - 1)];
            $logisticsStatus = $logisticsStatuses[min($index, count($logisticsStatuses) - 1)];
            
            // Calculate total amount (quantity * price)
            $totalAmount = $request['quantity'] * $request['price'];
            
            $orderData = [
                'branch_id' => $request['branch_id'],
                'supplier_id' => $request['supplier_id'],
                'purchase_request_id' => $request['id'],
                'approved_by' => $request['approved_by'] ?? 23116000,
                'approved_at' => $request['approved_at'] ?? date('Y-m-d H:i:s', strtotime('-1 day')),
                'status' => $status,
                'logistics_status' => $logisticsStatus,
                'total_amount' => $totalAmount,
                'expected_delivery_date' => date('Y-m-d', strtotime('+' . ($index + 2) . ' days')),
                'actual_delivery_date' => $status === 'Delivered' ? date('Y-m-d', strtotime('-' . ($index + 1) . ' days')) : null,
                'tracking_number' => $status !== 'Pending' ? 'TRK-' . str_pad($request['id'], 6, '0', STR_PAD_LEFT) : null,
                'delivery_notes' => $status === 'Delivered' ? 'Delivered on time. All items in good condition.' : null,
                'created_at' => $request['created_at'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $data[] = $orderData;
        }
        
        // Limit to 4 orders
        $data = array_slice($data, 0, 4);
        
        $this->db->table('purchase_orders')->insertBatch($data);
    }
}

