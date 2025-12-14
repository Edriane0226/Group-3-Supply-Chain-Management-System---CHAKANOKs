<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BranchTransfersSeeder extends Seeder
{
    public function run()
    {
        // Get stock_in items from Central branch
        $centralBranch = $this->db->table('branches')
            ->where('branch_name', 'Central')
            ->get()
            ->getRow();
        
        if (!$centralBranch) {
            return;
        }
        
        $stockItems = $this->db->table('stock_in')
            ->where('branch_id', $centralBranch->id)
            ->where('quantity', '>', 10) // Only items with sufficient quantity
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        // Get other branches (not Central)
        $otherBranches = $this->db->table('branches')
            ->where('id !=', $centralBranch->id)
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        $data = [];
        $statuses = ['pending', 'approved', 'completed', 'pending'];
        
        foreach ($stockItems as $index => $stock) {
            if (!isset($otherBranches[$index])) {
                continue;
            }
            
            $toBranch = $otherBranches[$index];
            $transferQuantity = min(5, (int)($stock['quantity'] * 0.2)); // Transfer 20% or max 5
            
            if ($transferQuantity <= 0) {
                $transferQuantity = 1;
            }
            
            $status = $statuses[min($index, count($statuses) - 1)];
            
            $data[] = [
                'from_branch_id' => $centralBranch->id,
                'to_branch_id' => $toBranch['id'],
                'stock_in_id' => $stock['id'],
                'quantity' => $transferQuantity,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . ($index + 1) . ' days')),
            ];
        }
        
        if (!empty($data)) {
            $this->db->table('branch_transfers')->insertBatch($data);
        }
    }
}

