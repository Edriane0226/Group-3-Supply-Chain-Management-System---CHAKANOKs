<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccountsPayableSeeder extends Seeder
{
    public function run()
    {
        // Get purchase orders that are delivered or approved
        $purchaseOrders = $this->db->table('purchase_orders')
            ->whereIn('status', ['Delivered', 'Approved', 'In_Transit'])
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        // Get suppliers to get payment terms
        $suppliers = $this->db->table('suppliers')
            ->select('id, terms')
            ->get()
            ->getResultArray();
        
        $supplierTerms = [];
        foreach ($suppliers as $supplier) {
            $supplierTerms[$supplier['id']] = $supplier['terms'];
        }
        
        $data = [];
        $paymentStatuses = ['pending', 'partial', 'paid', 'pending'];
        $paymentMethods = ['bank_transfer', 'gcash', 'bank_transfer', null];
        
        foreach ($purchaseOrders as $index => $po) {
            $terms = $supplierTerms[$po['supplier_id']] ?? 'Net 30';
            $days = 30; // Default
            
            if (strpos($terms, '15') !== false) {
                $days = 15;
            } elseif (strpos($terms, '7') !== false) {
                $days = 7;
            } elseif (strpos($terms, '45') !== false) {
                $days = 45;
            }
            
            $invoiceDate = $po['actual_delivery_date'] ?? $po['created_at'] ?? date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime($invoiceDate . ' +' . $days . ' days'));
            
            $paymentStatus = $paymentStatuses[min($index, count($paymentStatuses) - 1)];
            $invoiceAmount = (float)$po['total_amount'];
            
            $amountPaid = 0.00;
            $balanceDue = $invoiceAmount;
            $paidDate = null;
            $paymentMethod = $paymentMethods[min($index, count($paymentMethods) - 1)];
            $paymentReference = null;
            
            if ($paymentStatus === 'paid') {
                $amountPaid = $invoiceAmount;
                $balanceDue = 0.00;
                $paidDate = date('Y-m-d', strtotime($invoiceDate . ' +' . ($days - 5) . ' days'));
                $paymentReference = 'PAY-' . str_pad($po['id'], 6, '0', STR_PAD_LEFT);
            } elseif ($paymentStatus === 'partial') {
                $amountPaid = $invoiceAmount * 0.5;
                $balanceDue = $invoiceAmount - $amountPaid;
                $paymentReference = 'PAY-PART-' . str_pad($po['id'], 6, '0', STR_PAD_LEFT);
            }
            
            $data[] = [
                'purchase_order_id' => $po['id'],
                'supplier_id' => $po['supplier_id'],
                'invoice_amount' => $invoiceAmount,
                'due_date' => $dueDate,
                'payment_status' => $paymentStatus,
                'amount_paid' => $amountPaid,
                'balance_due' => $balanceDue,
                'payment_terms' => $terms,
                'invoice_date' => $invoiceDate,
                'paid_date' => $paidDate,
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
                'notes' => $paymentStatus === 'paid' ? 'Payment completed on time' : ($paymentStatus === 'partial' ? 'Partial payment received' : 'Awaiting payment'),
                'created_at' => $po['created_at'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($data)) {
            $this->db->table('accounts_payable')->insertBatch($data);
        }
    }
}

