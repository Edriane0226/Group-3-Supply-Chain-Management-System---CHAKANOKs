<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PurchaseOrderItemsSeeder extends Seeder
{
    public function run()
    {
        // Get purchase orders
        $purchaseOrders = $this->db->table('purchase_orders')
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        // Get purchase requests to match items
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
        
        foreach ($purchaseOrders as $index => $po) {
            if (!isset($purchaseRequests[$index])) {
                continue;
            }
            
            $request = $purchaseRequests[$index];
            
            // Try to find existing stock_in item that matches
            $stockItem = $this->db->table('stock_in')
                ->where('branch_id', $po['branch_id'])
                ->where('item_name', $request['item_name'])
                ->get()
                ->getRowArray();
            
            // If no matching stock_in item, create one
            if (!$stockItem) {
                $stockData = [
                    'item_type_id' => $defaultStockTypeId,
                    'branch_id' => $po['branch_id'],
                    'item_name' => $request['item_name'],
                    'category' => 'General',
                    'quantity' => $request['quantity'],
                    'unit' => $request['unit'],
                    'price' => $request['price'],
                    'expiry_date' => null,
                    'barcode' => 'PO-' . str_pad($po['id'], 6, '0', STR_PAD_LEFT),
                    'created_at' => $po['created_at'],
                ];
                
                $this->db->table('stock_in')->insert($stockData);
                $stockItemId = $this->db->insertID();
            } else {
                $stockItemId = $stockItem['id'];
            }
            
            // Calculate unit price from request
            $quantity = (int)$request['quantity'];
            $totalPrice = (float)$request['price'];
            $unitPrice = $quantity > 0 ? ($totalPrice / $quantity) : $totalPrice;
            $subtotal = $totalPrice;
            
            $data[] = [
                'purchase_order_id' => $po['id'],
                'stock_in_id' => $stockItemId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }
        
        if (!empty($data)) {
            $this->db->table('purchase_order_items')->insertBatch($data);
        }
    }
}

