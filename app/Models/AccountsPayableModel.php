<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountsPayableModel extends Model
{
    protected $table            = 'accounts_payable';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'purchase_order_id',
        'supplier_id',
        'invoice_amount',
        'due_date',
        'payment_status',
        'amount_paid',
        'balance_due',
        'payment_terms',
        'invoice_date',
        'paid_date',
        'payment_method',
        'payment_reference',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'purchase_order_id' => 'required|integer',
        'supplier_id' => 'required|integer',
        'invoice_amount' => 'required|decimal',
        'balance_due' => 'required|decimal'
    ];

    /**
     * Get accounts payable with related information
     */
    public function getAccountsPayableWithRelations(?int $supplierId = null, ?string $status = null): array
    {
        $builder = $this->db->table('accounts_payable ap')
            ->select('ap.*,
                po.id as po_id,
                po.total_amount,
                po.actual_delivery_date,
                branches.branch_name,
                suppliers.supplier_name')
            ->join('purchase_orders po', 'po.id = ap.purchase_order_id', 'left')
            ->join('branches', 'branches.id = po.branch_id', 'left')
            ->join('suppliers', 'suppliers.id = ap.supplier_id', 'left')
            ->orderBy('ap.due_date', 'ASC')
            ->orderBy('ap.created_at', 'DESC');

        if ($supplierId) {
            $builder->where('ap.supplier_id', $supplierId);
        }

        if ($status) {
            $builder->where('ap.payment_status', $status);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get summary statistics for a supplier
     */
    public function getSupplierSummary(int $supplierId): array
    {
        $db = \Config\Database::connect();
        
        $totalPending = $db->table($this->table)
            ->selectSum('balance_due', 'total')
            ->where('supplier_id', $supplierId)
            ->where('payment_status', 'pending')
            ->get()
            ->getRowArray();

        $totalOverdue = $db->table($this->table)
            ->selectSum('balance_due', 'total')
            ->where('supplier_id', $supplierId)
            ->where('payment_status', 'overdue')
            ->get()
            ->getRowArray();

        $totalPaid = $db->table($this->table)
            ->selectSum('amount_paid', 'total')
            ->where('supplier_id', $supplierId)
            ->where('payment_status', 'paid')
            ->get()
            ->getRowArray();

        $pendingCount = $this->where('supplier_id', $supplierId)
            ->where('payment_status', 'pending')
            ->countAllResults(false);

        $overdueCount = $this->where('supplier_id', $supplierId)
            ->where('payment_status', 'overdue')
            ->countAllResults(false);

        return [
            'total_pending' => (float)($totalPending['total'] ?? 0),
            'total_overdue' => (float)($totalOverdue['total'] ?? 0),
            'total_paid' => (float)($totalPaid['total'] ?? 0),
            'pending_count' => $pendingCount,
            'overdue_count' => $overdueCount
        ];
    }

    /**
     * Create accounts payable entry from purchase order
     */
    public function createFromPurchaseOrder(array $poData, ?string $paymentTerms = null): ?int
    {
        // Calculate due date based on payment terms
        $dueDate = null;
        if ($paymentTerms && $poData['actual_delivery_date']) {
            $days = $this->parsePaymentTerms($paymentTerms);
            if ($days) {
                $dueDate = date('Y-m-d', strtotime($poData['actual_delivery_date'] . " +{$days} days"));
            }
        }

        $data = [
            'purchase_order_id' => $poData['id'],
            'supplier_id' => $poData['supplier_id'],
            'invoice_amount' => $poData['total_amount'],
            'balance_due' => $poData['total_amount'],
            'due_date' => $dueDate,
            'payment_terms' => $paymentTerms,
            'invoice_date' => $poData['invoice_uploaded_at'] ? date('Y-m-d', strtotime($poData['invoice_uploaded_at'])) : date('Y-m-d'),
            'payment_status' => 'pending'
        ];

        $this->insert($data);
        return $this->insertID();
    }

    /**
     * Parse payment terms to get days (e.g., "Net 30" = 30 days)
     */
    private function parsePaymentTerms(string $terms): ?int
    {
        if (preg_match('/Net\s+(\d+)/i', $terms, $matches)) {
            return (int)$matches[1];
        }
        if (stripos($terms, 'COD') !== false) {
            return 0;
        }
        return null;
    }

    /**
     * Update payment status based on due date
     */
    public function updateOverdueStatus(): int
    {
        $today = date('Y-m-d');
        
        return $this->where('payment_status', 'pending')
            ->where('due_date <', $today)
            ->set('payment_status', 'overdue')
            ->update();
    }

    /**
     * Record payment
     */
    public function recordPayment(int $apId, float $amount, string $paymentMethod = 'bank_transfer', ?string $reference = null, ?string $notes = null): bool
    {
        $ap = $this->find($apId);
        if (!$ap) {
            return false;
        }

        $newAmountPaid = ($ap['amount_paid'] ?? 0) + $amount;
        $newBalanceDue = $ap['invoice_amount'] - $newAmountPaid;
        
        $paymentStatus = 'partial';
        if ($newBalanceDue <= 0) {
            $paymentStatus = 'paid';
            $newBalanceDue = 0;
        }

        return $this->update($apId, [
            'amount_paid' => $newAmountPaid,
            'balance_due' => $newBalanceDue,
            'payment_status' => $paymentStatus,
            'paid_date' => $paymentStatus === 'paid' ? date('Y-m-d') : null,
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference,
            'notes' => $notes ? ($ap['notes'] ? $ap['notes'] . "\n" . $notes : $notes) : $ap['notes'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}

