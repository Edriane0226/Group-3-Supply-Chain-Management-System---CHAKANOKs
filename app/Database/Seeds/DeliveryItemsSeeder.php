<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeliveryItemsSeeder extends Seeder
{
    public function run()
    {
        // Get deliveries
        $deliveries = $this->db->table('deliveries')
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        // Get purchase requests to get item details
        $purchaseRequests = $this->db->table('purchase_requests')
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        // Get stock types
        $stockTypes = $this->db->table('stock_types')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
        
        $defaultStockTypeId = $stockTypes[0]['id'] ?? 1;
        
        $data = [];
        
        foreach ($deliveries as $index => $delivery) {
            if (!isset($purchaseRequests[$index])) {
                continue;
            }
            
            $request = $purchaseRequests[$index];
            
            // Determine stock type based on item category
            $itemName = strtolower($request['item_name']);
            $stockTypeId = $defaultStockTypeId;
            
            if (strpos($itemName, 'chicken') !== false || strpos($itemName, 'meat') !== false) {
                // Raw Materials
                $stockTypeId = isset($stockTypes[1]) ? $stockTypes[1]['id'] : $defaultStockTypeId;
            } elseif (strpos($itemName, 'soy') !== false || strpos($itemName, 'oil') !== false) {
                // Seasonings
                $stockTypeId = isset($stockTypes[0]) ? $stockTypes[0]['id'] : $defaultStockTypeId;
            }
            
            $expiryDate = null;
            if (strpos($itemName, 'chicken') !== false || strpos($itemName, 'meat') !== false) {
                $expiryDate = date('Y-m-d', strtotime('+7 days'));
            }
            
            $data[] = [
                'delivery_id' => $delivery['id'],
                'item_name' => $request['item_name'],
                'quantity' => $request['quantity'],
                'unit' => $request['unit'],
                'price' => $request['price'],
                'expiry_date' => $expiryDate,
                'barcode' => 'BC-' . str_pad($delivery['id'], 6, '0', STR_PAD_LEFT) . '-' . ($index + 1),
                'item_type_id' => $stockTypeId,
                'created_at' => $delivery['created_at'],
            ];
        }
        
        if (!empty($data)) {
            $this->db->table('delivery_items')->insertBatch($data);
        }
    }
}

