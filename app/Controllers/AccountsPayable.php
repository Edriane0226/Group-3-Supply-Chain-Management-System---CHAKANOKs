<?php

namespace App\Controllers;

use App\Models\AccountsPayableModel;
use App\Models\PurchaseOrderModel;
use App\Models\SupplierModel;
use App\Models\UserModel;

class AccountsPayable extends BaseController
{
    protected $accountsPayableModel;
    protected $purchaseOrderModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->accountsPayableModel = new AccountsPayableModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->supplierModel = new SupplierModel();
    }

    /**
     * List all accounts payable
     */
    public function index()
    {
        if ($redirect = $this->authorize('accounts_payable.view')) {
            return $redirect;
        }

        $status = $this->request->getGet('status') ?? null;
        $supplierId = $this->request->getGet('supplier_id') ?? null;
        $startDate = $this->request->getGet('start_date') ?? null;
        $endDate = $this->request->getGet('end_date') ?? null;

        // Get all accounts payable (no supplier filter for admin)
        $accountsPayable = $this->accountsPayableModel->getAccountsPayableWithRelations(null, $status);

        // Filter by supplier if provided
        if ($supplierId) {
            $accountsPayable = array_filter($accountsPayable, function($ap) use ($supplierId) {
                return $ap['supplier_id'] == $supplierId;
            });
        }

        // Filter by date range if provided
        if ($startDate && $endDate) {
            $accountsPayable = array_filter($accountsPayable, function($ap) use ($startDate, $endDate) {
                $invoiceDate = $ap['invoice_date'] ?? $ap['created_at'];
                return $invoiceDate >= $startDate && $invoiceDate <= $endDate;
            });
        }

        // Update overdue status
        $this->accountsPayableModel->updateOverdueStatus();

        // Get summary statistics for all suppliers
        $summary = $this->getOverallSummary();

        // Get all suppliers for filter dropdown
        $suppliers = $this->supplierModel->findAll();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Accounts Payable Management',
            'accountsPayable' => $accountsPayable,
            'summary' => $summary,
            'suppliers' => $suppliers,
            'status' => $status,
            'supplierId' => $supplierId,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('reusables/sidenav', $data) . view('accounts_payable/index', $data);
    }

    /**
     * View individual account payable details
     */
    public function view(int $id)
    {
        if ($redirect = $this->authorize('accounts_payable.view')) {
            return $redirect;
        }

        $ap = $this->accountsPayableModel->find($id);

        if (!$ap) {
            return redirect()->to(site_url('accounts-payable'))->with('error', 'Account payable not found.');
        }

        // Get related purchase order details
        $purchaseOrder = $this->purchaseOrderModel->select('purchase_orders.*, branches.branch_name')
            ->join('branches', 'branches.id = purchase_orders.branch_id')
            ->find($ap['purchase_order_id']);

        // Get supplier details
        $supplier = $this->supplierModel->find($ap['supplier_id']);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Account Payable Details',
            'ap' => $ap,
            'purchaseOrder' => $purchaseOrder,
            'supplier' => $supplier
        ];

        return view('reusables/sidenav', $data) . view('accounts_payable/view', $data);
    }

    /**
     * Record payment (mark as paid or partial payment)
     */
    public function recordPayment(int $id)
    {
        if ($redirect = $this->authorize('accounts_payable.manage')) {
            return $redirect;
        }

        $ap = $this->accountsPayableModel->find($id);

        if (!$ap) {
            return redirect()->back()->with('error', 'Account payable not found.');
        }

        $amount = (float)$this->request->getPost('amount');
        $paymentMethod = $this->request->getPost('payment_method') ?? 'bank_transfer';
        $paymentReference = $this->request->getPost('payment_reference') ?? null;
        $notes = $this->request->getPost('notes') ?? null;

        // Validation
        if ($amount <= 0) {
            return redirect()->back()->withInput()->with('error', 'Payment amount must be greater than 0.');
        }

        // Handle overpayment
        $overpayment = 0;
        $paymentAmount = $amount;
        if ($amount > $ap['balance_due']) {
            $overpayment = $amount - $ap['balance_due'];
            $paymentAmount = $ap['balance_due']; // Only record the balance due as payment
            // Add overpayment info to notes
            $overpaymentNote = "\n[Overpayment: ₱" . number_format($overpayment, 2) . " - Change/Refund to be processed]";
            $notes = ($notes ? $notes . $overpaymentNote : trim($overpaymentNote));
        }

        // Record payment (only record up to balance due)
        if ($this->accountsPayableModel->recordPayment($id, $paymentAmount, $paymentMethod, $paymentReference, $notes)) {
            // Refresh AP data to get updated status
            $updatedAp = $this->accountsPayableModel->find($id);
            $status = $updatedAp['payment_status'] ?? 'partial';
            
            // Automatically redirect to print receipt after successful payment
            return redirect()->to(site_url('accounts-payable/receipt/' . $id));
        }

        return redirect()->back()->withInput()->with('error', 'Failed to record payment.');
    }

    /**
     * Mark as fully paid
     */
    public function markAsPaid(int $id)
    {
        if ($redirect = $this->authorize('accounts_payable.manage')) {
            return $redirect;
        }

        $ap = $this->accountsPayableModel->find($id);

        if (!$ap) {
            return redirect()->back()->with('error', 'Account payable not found.');
        }

        $paymentMethod = $this->request->getPost('payment_method') ?? 'bank_transfer';
        $paymentReference = $this->request->getPost('payment_reference') ?? null;
        $notes = $this->request->getPost('notes') ?? null;

        // Record full payment
        $remainingBalance = $ap['balance_due'];
        if ($this->accountsPayableModel->recordPayment($id, $remainingBalance, $paymentMethod, $paymentReference, $notes)) {
            return redirect()->back()->with('success', 'Account payable marked as fully paid.');
        }

        return redirect()->back()->with('error', 'Failed to mark as paid.');
    }

    /**
     * Print payment receipt
     */
    public function printReceipt(int $id)
    {
        if ($redirect = $this->authorize('accounts_payable.print')) {
            return $redirect;
        }

        $ap = $this->accountsPayableModel->find($id);

        if (!$ap) {
            return redirect()->to(site_url('accounts-payable'))->with('error', 'Account payable not found.');
        }

        // Allow printing for any payment (paid or partial)
        // Receipt will show current payment status

        // Get related purchase order details
        $purchaseOrder = $this->purchaseOrderModel->select('purchase_orders.*, branches.branch_name')
            ->join('branches', 'branches.id = purchase_orders.branch_id')
            ->find($ap['purchase_order_id']);

        // Get supplier details
        $supplier = $this->supplierModel->find($ap['supplier_id']);

        // Get current user details for receipt
        $userModel = new UserModel();
        $currentUser = $userModel->find(session()->get('user_id'));

        $data = [
            'ap' => $ap,
            'purchaseOrder' => $purchaseOrder,
            'supplier' => $supplier,
            'currentUser' => $currentUser
        ];

        return view('accounts_payable/receipt', $data);
    }

    /**
     * Get overall summary statistics
     */
    private function getOverallSummary(): array
    {
        $db = \Config\Database::connect();
        
        $totalPending = $db->table('accounts_payable')
            ->selectSum('balance_due', 'total')
            ->where('payment_status', 'pending')
            ->get()
            ->getRowArray();

        $totalOverdue = $db->table('accounts_payable')
            ->selectSum('balance_due', 'total')
            ->where('payment_status', 'overdue')
            ->get()
            ->getRowArray();

        $totalPaid = $db->table('accounts_payable')
            ->selectSum('amount_paid', 'total')
            ->where('payment_status', 'paid')
            ->get()
            ->getRowArray();

        $pendingCount = $this->accountsPayableModel
            ->where('payment_status', 'pending')
            ->countAllResults(false);

        $overdueCount = $this->accountsPayableModel
            ->where('payment_status', 'overdue')
            ->countAllResults(false);

        $paidCount = $this->accountsPayableModel
            ->where('payment_status', 'paid')
            ->countAllResults(false);

        return [
            'total_pending' => (float)($totalPending['total'] ?? 0),
            'total_overdue' => (float)($totalOverdue['total'] ?? 0),
            'total_paid' => (float)($totalPaid['total'] ?? 0),
            'pending_count' => $pendingCount,
            'overdue_count' => $overdueCount,
            'paid_count' => $paidCount
        ];
    }
}

