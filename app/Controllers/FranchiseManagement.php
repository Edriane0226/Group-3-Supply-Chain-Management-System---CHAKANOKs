<?php

namespace App\Controllers;

use App\Models\FranchiseModel;
use App\Models\FranchisePaymentModel;
use App\Models\FranchiseSupplyAllocationModel;
use App\Models\BranchModel;
use CodeIgniter\Controller;

class FranchiseManagement extends Controller
{
    protected FranchiseModel $franchiseModel;
    protected FranchisePaymentModel $paymentModel;
    protected FranchiseSupplyAllocationModel $allocationModel;
    protected BranchModel $branchModel;
    protected $session;

    public function __construct()
    {
        $this->franchiseModel = new FranchiseModel();
        $this->paymentModel = new FranchisePaymentModel();
        $this->allocationModel = new FranchiseSupplyAllocationModel();
        $this->branchModel = new BranchModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Check authorization - only Franchise Manager and Central Office Admin allowed
     */
    private function authorize()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $allowedRoles = ['Franchise Manager', 'Central Office Admin'];
        if (!in_array($this->session->get('role'), $allowedRoles)) {
            return redirect()->to(site_url('login'))->with('error', 'Unauthorized access to Franchise Management.');
        }

        return null;
    }

    /**
     * Dashboard - Main franchise management overview
     */
    public function index()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $data = [
            'role'  => $this->session->get('role'),
            'title' => 'Franchise Management',
            'stats' => $this->franchiseModel->getStatistics(),
            'paymentStats' => $this->paymentModel->getStatistics(),
            'pendingApplications' => $this->franchiseModel->getPendingApplications(),
            'activeFranchises' => $this->franchiseModel->getActiveFranchises(),
            'recentPayments' => $this->paymentModel->getRecentPayments(5),
            'expiringContracts' => $this->franchiseModel->getExpiringContracts(30),
            'pendingAllocations' => $this->allocationModel->getPendingAllocations(),
        ];

        return view('reusables/sidenav', $data) . view('franchise/dashboard', $data);
    }

    /**
     * List all franchise applications
     */
    public function applications()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $status = $this->request->getGet('status');
        
        if ($status) {
            $applications = $this->franchiseModel->getByStatus($status);
        } else {
            $applications = $this->franchiseModel->orderBy('created_at', 'DESC')->findAll();
        }

        $data = [
            'role'         => $this->session->get('role'),
            'title'        => 'Franchise Applications',
            'applications' => $applications,
            'currentStatus' => $status,
        ];

        return view('reusables/sidenav', $data) . view('franchise/applications', $data);
    }

    /**
     * View single application details
     */
    public function viewApplication(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $application = $this->franchiseModel->getFranchiseDetails($id);

        if (!$application) {
            return redirect()->to(site_url('franchise/applications'))->with('error', 'Application not found.');
        }

        $data = [
            'role'        => $this->session->get('role'),
            'title'       => 'Application Details',
            'application' => $application,
        ];

        return view('reusables/sidenav', $data) . view('franchise/view_application', $data);
    }

    /**
     * Create new franchise application (form)
     */
    public function create()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $data = [
            'role'  => $this->session->get('role'),
            'title' => 'New Franchise Application',
        ];

        return view('reusables/sidenav', $data) . view('franchise/create', $data);
    }

    /**
     * Store new franchise application
     */
    public function store()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $rules = [
            'applicant_name' => 'required|min_length[3]|max_length[150]',
            'contact_info'   => 'required|max_length[150]',
            'email'          => 'permit_empty|valid_email|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'applicant_name'      => $this->request->getPost('applicant_name'),
            'contact_info'        => $this->request->getPost('contact_info'),
            'email'               => $this->request->getPost('email'),
            'address'             => $this->request->getPost('address'),
            'proposed_location'   => $this->request->getPost('proposed_location'),
            'business_experience' => $this->request->getPost('business_experience'),
            'investment_capacity' => $this->request->getPost('investment_capacity'),
        ];

        $id = $this->franchiseModel->createApplication($data);

        if ($id) {
            return redirect()->to(site_url('franchise/applications'))->with('success', 'Franchise application submitted successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to submit application.');
    }

    /**
     * Approve franchise application
     */
    public function approve(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $application = $this->franchiseModel->find($id);

        if (!$application) {
            return redirect()->back()->with('error', 'Application not found.');
        }

        if (!in_array($application['status'], ['pending', 'under_review'])) {
            return redirect()->back()->with('error', 'Only pending or under review applications can be approved.');
        }

        $approvalData = [
            'royalty_rate'   => $this->request->getPost('royalty_rate') ?: 5.00,
            'franchise_fee'  => $this->request->getPost('franchise_fee') ?: 0,
            'contract_start' => $this->request->getPost('contract_start'),
            'contract_end'   => $this->request->getPost('contract_end'),
            'notes'          => $this->request->getPost('notes'),
        ];

        $approvedBy = (int) $this->session->get('user_id');

        if ($this->franchiseModel->approveApplication($id, $approvedBy, $approvalData)) {
            return redirect()->to(site_url('franchise/application/' . $id))->with('success', 'Application approved successfully.');
        }

        return redirect()->back()->with('error', 'Failed to approve application.');
    }

    /**
     * Reject franchise application
     */
    public function reject(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $application = $this->franchiseModel->find($id);

        if (!$application) {
            return redirect()->back()->with('error', 'Application not found.');
        }

        if (!in_array($application['status'], ['pending', 'under_review'])) {
            return redirect()->back()->with('error', 'Only pending or under review applications can be rejected.');
        }

        $reason = $this->request->getPost('rejection_reason') ?? '';

        if ($this->franchiseModel->rejectApplication($id, $reason)) {
            return redirect()->to(site_url('franchise/applications'))->with('success', 'Application rejected.');
        }

        return redirect()->back()->with('error', 'Failed to reject application.');
    }

    /**
     * Mark application as under review
     */
    public function markUnderReview(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $application = $this->franchiseModel->find($id);

        if (!$application || $application['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Invalid application status.');
        }

        $this->franchiseModel->update($id, ['status' => 'under_review', 'updated_at' => date('Y-m-d H:i:s')]);

        return redirect()->back()->with('success', 'Application marked as under review.');
    }

    /**
     * List all active franchises
     */
    public function franchises()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $data = [
            'role'       => $this->session->get('role'),
            'title'      => 'Active Franchises',
            'franchises' => $this->franchiseModel->getActiveFranchises(),
        ];

        return view('reusables/sidenav', $data) . view('franchise/franchises', $data);
    }

    /**
     * View franchise details
     */
    public function viewFranchise(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = $this->franchiseModel->getFranchiseDetails($id);

        if (!$franchise) {
            return redirect()->to(site_url('franchise/list'))->with('error', 'Franchise not found.');
        }

        $data = [
            'role'      => $this->session->get('role'),
            'title'     => 'Franchise Details',
            'franchise' => $franchise,
            'payments'  => $this->paymentModel->getByFranchise($id),
            'allocations' => $this->allocationModel->getByFranchise($id),
        ];

        return view('reusables/sidenav', $data) . view('franchise/view_franchise', $data);
    }

    /**
     * Activate franchise (link to branch)
     */
    public function activate(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = $this->franchiseModel->find($id);

        if (!$franchise || $franchise['status'] !== 'approved') {
            return redirect()->back()->with('error', 'Only approved franchises can be activated.');
        }

        $branchId = $this->request->getPost('branch_id');

        if (!$branchId) {
            // Create new branch for this franchise
            $branchData = [
                'branch_name'  => $franchise['applicant_name'] . ' Franchise',
                'location'     => $franchise['proposed_location'],
                'contact_info' => $franchise['contact_info'],
                'status'       => 'franchise',
            ];

            $this->branchModel->insert($branchData);
            $branchId = $this->branchModel->insertID();
        }

        if ($this->franchiseModel->activateFranchise($id, $branchId)) {
            return redirect()->to(site_url('franchise/view/' . $id))->with('success', 'Franchise activated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to activate franchise.');
    }

    /**
     * Suspend franchise
     */
    public function suspend(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = $this->franchiseModel->find($id);

        if (!$franchise || $franchise['status'] !== 'active') {
            return redirect()->back()->with('error', 'Only active franchises can be suspended.');
        }

        $reason = $this->request->getPost('reason') ?? '';

        if ($this->franchiseModel->suspendFranchise($id, $reason)) {
            return redirect()->back()->with('success', 'Franchise suspended.');
        }

        return redirect()->back()->with('error', 'Failed to suspend franchise.');
    }

    /**
     * Reactivate suspended franchise
     */
    public function reactivate(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = $this->franchiseModel->find($id);

        if (!$franchise || $franchise['status'] !== 'suspended') {
            return redirect()->back()->with('error', 'Only suspended franchises can be reactivated.');
        }

        if ($this->franchiseModel->reactivateFranchise($id)) {
            return redirect()->back()->with('success', 'Franchise reactivated.');
        }

        return redirect()->back()->with('error', 'Failed to reactivate franchise.');
    }

    /**
     * Terminate franchise
     */
    public function terminate(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = $this->franchiseModel->find($id);

        if (!$franchise) {
            return redirect()->back()->with('error', 'Franchise not found.');
        }

        $reason = $this->request->getPost('reason') ?? '';

        if ($this->franchiseModel->terminateFranchise($id, $reason)) {
            return redirect()->to(site_url('franchise/list'))->with('success', 'Franchise terminated.');
        }

        return redirect()->back()->with('error', 'Failed to terminate franchise.');
    }

    /**
     * Payments management page
     */
    public function payments(?int $franchiseId = null)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = null;
        $payments = [];

        if ($franchiseId) {
            $franchise = $this->franchiseModel->find($franchiseId);
            $payments = $this->paymentModel->getByFranchise($franchiseId);
        } else {
            $payments = $this->paymentModel->getRecentPayments(50);
        }

        $data = [
            'role'      => $this->session->get('role'),
            'title'     => $franchise ? 'Payments - ' . $franchise['applicant_name'] : 'All Payments',
            'franchise' => $franchise,
            'payments'  => $payments,
            'stats'     => $this->paymentModel->getStatistics($franchiseId),
        ];

        return view('reusables/sidenav', $data) . view('franchise/payments', $data);
    }

    /**
     * Record new payment
     */
    public function recordPayment(int $franchiseId)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = $this->franchiseModel->find($franchiseId);

        if (!$franchise) {
            return redirect()->back()->with('error', 'Franchise not found.');
        }

        $rules = [
            'amount'       => 'required|decimal|greater_than[0]',
            'payment_date' => 'required|valid_date',
            'payment_type' => 'required|in_list[franchise_fee,royalty,supply_payment,penalty,other]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'franchise_id'     => $franchiseId,
            'payment_type'     => $this->request->getPost('payment_type'),
            'amount'           => $this->request->getPost('amount'),
            'reference_number' => $this->request->getPost('reference_number'),
            'payment_method'   => $this->request->getPost('payment_method') ?: 'cash',
            'payment_date'     => $this->request->getPost('payment_date'),
            'period_start'     => $this->request->getPost('period_start'),
            'period_end'       => $this->request->getPost('period_end'),
            'remarks'          => $this->request->getPost('remarks'),
            'recorded_by'      => (int) $this->session->get('user_id'),
        ];

        $paymentId = $this->paymentModel->recordPayment($data);

        if ($paymentId) {
            return redirect()->to(site_url('franchise/payments/' . $franchiseId))->with('success', 'Payment recorded successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to record payment.');
    }

    /**
     * Supply allocations page
     */
    public function allocations(?int $franchiseId = null)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = null;
        $allocations = [];

        if ($franchiseId) {
            $franchise = $this->franchiseModel->find($franchiseId);
            $allocations = $this->allocationModel->getByFranchise($franchiseId);
        } else {
            $allocations = $this->allocationModel->getRecentAllocations(50);
        }

        $data = [
            'role'        => $this->session->get('role'),
            'title'       => $franchise ? 'Supply Allocations - ' . $franchise['applicant_name'] : 'All Supply Allocations',
            'franchise'   => $franchise,
            'allocations' => $allocations,
            'stats'       => $this->allocationModel->getStatistics($franchiseId),
        ];

        return view('reusables/sidenav', $data) . view('franchise/allocations', $data);
    }

    /**
     * Allocate supply form
     */
    public function allocateSupply(int $franchiseId)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = $this->franchiseModel->find($franchiseId);

        if (!$franchise || !in_array($franchise['status'], ['approved', 'active'])) {
            return redirect()->back()->with('error', 'Can only allocate supplies to approved or active franchises.');
        }

        $data = [
            'role'      => $this->session->get('role'),
            'title'     => 'Allocate Supply - ' . $franchise['applicant_name'],
            'franchise' => $franchise,
        ];

        return view('reusables/sidenav', $data) . view('franchise/allocate_supply', $data);
    }

    /**
     * Process supply allocation
     */
    public function processAllocation(int $franchiseId)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $franchise = $this->franchiseModel->find($franchiseId);

        if (!$franchise) {
            return redirect()->back()->with('error', 'Franchise not found.');
        }

        $itemNames = $this->request->getPost('item_name');
        $quantities = $this->request->getPost('quantity');
        $units = $this->request->getPost('unit');
        $unitPrices = $this->request->getPost('unit_price');
        $deliveryDate = $this->request->getPost('delivery_date');
        $notes = $this->request->getPost('notes');

        if (!is_array($itemNames) || count($itemNames) === 0) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one item.');
        }

        $items = [];
        for ($i = 0; $i < count($itemNames); $i++) {
            if (empty($itemNames[$i]) || empty($quantities[$i])) {
                continue;
            }

            $items[] = [
                'item_name'     => $itemNames[$i],
                'quantity'      => (int) $quantities[$i],
                'unit'          => $units[$i] ?? 'pcs',
                'unit_price'    => (float) ($unitPrices[$i] ?? 0),
                'delivery_date' => $deliveryDate,
                'notes'         => $notes,
            ];
        }

        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'No valid items to allocate.');
        }

        $allocatedBy = (int) $this->session->get('user_id');
        $insertedIds = $this->allocationModel->createBatchAllocation($franchiseId, $items, $allocatedBy);

        if (!empty($insertedIds)) {
            return redirect()->to(site_url('franchise/allocations/' . $franchiseId))->with('success', count($insertedIds) . ' items allocated successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to allocate supplies.');
    }

    /**
     * Update allocation status
     */
    public function updateAllocationStatus(int $id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $status = $this->request->getPost('status');
        $validStatuses = ['pending', 'approved', 'preparing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        if ($this->allocationModel->updateStatus($id, $status)) {
            return redirect()->back()->with('success', 'Allocation status updated.');
        }

        return redirect()->back()->with('error', 'Failed to update status.');
    }

    /**
     * Reports page
     */
    public function reports()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $year = $this->request->getGet('year') ?: date('Y');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-d', strtotime('-12 months'));
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');

        // Get franchise performance data
        $performanceData = $this->franchiseModel->getAllFranchisesPerformance($startDate, $endDate);
        $overdueFranchises = $this->franchiseModel->getFranchisesWithOverduePayments(30);

        $data = [
            'role'              => $this->session->get('role'),
            'title'             => 'Franchise Reports',
            'stats'             => $this->franchiseModel->getStatistics(),
            'paymentStats'      => $this->paymentModel->getStatistics(),
            'monthlyReport'     => $this->paymentModel->getMonthlyReport($year),
            'performanceData'   => $performanceData,
            'overdueFranchises' => $overdueFranchises,
            'year'              => $year,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
        ];

        return view('reusables/sidenav', $data) . view('franchise/reports', $data);
    }

    /**
     * View individual franchise performance report
     */
    public function performanceReport(int $franchiseId)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-d', strtotime('-12 months'));
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');

        $franchise = $this->franchiseModel->find($franchiseId);
        if (!$franchise) {
            return redirect()->to(site_url('franchise/list'))->with('error', 'Franchise not found.');
        }

        $performance = $this->franchiseModel->getPerformanceMetrics($franchiseId, $startDate, $endDate);
        $payments = $this->paymentModel->getByFranchise($franchiseId);
        $allocations = $this->allocationModel->getByFranchise($franchiseId);

        $data = [
            'role'        => $this->session->get('role'),
            'title'       => 'Performance Report - ' . $franchise['applicant_name'],
            'franchise'   => $franchise,
            'performance' => $performance,
            'payments'    => $payments,
            'allocations' => $allocations,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
        ];

        return view('reusables/sidenav', $data) . view('franchise/performance_report', $data);
    }

    /**
     * Send payment reminders to franchises with overdue payments
     */
    public function sendPaymentReminders()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $daysOverdue = (int) ($this->request->getPost('days_overdue') ?? 30);
        $overdueFranchises = $this->franchiseModel->getFranchisesWithOverduePayments($daysOverdue);

        $reminderCount = 0;
        $notificationModel = new \App\Models\NotificationModel();

        foreach ($overdueFranchises as $franchise) {
            // Create notification for franchise manager
            // Note: In a real system, you would also send email/SMS here
            $notificationModel->createNotification([
                'user_id' => $franchise['approved_by'] ?? null, // Notify the approver/franchise manager
                'type' => 'in_app',
                'title' => 'Payment Reminder - ' . $franchise['applicant_name'],
                'message' => "Franchise '{$franchise['applicant_name']}' has overdue payments. Days overdue: {$franchise['days_overdue']}",
                'reference_type' => 'franchise',
                'reference_id' => $franchise['id'],
            ]);

            $reminderCount++;
        }

        return redirect()->back()->with('success', "Payment reminders sent to {$reminderCount} franchise(s).");
    }

    /**
     * Automated payment reminder check (can be called via cron job)
     */
    public function checkPaymentReminders()
    {
        // This can be called via cron job for automated reminders
        $overdueFranchises = $this->franchiseModel->getFranchisesWithOverduePayments(30);
        
        $notificationModel = new \App\Models\NotificationModel();
        $reminderCount = 0;

        foreach ($overdueFranchises as $franchise) {
            // Check if reminder was already sent today
            $today = date('Y-m-d');
            $existingReminder = $notificationModel->where('reference_type', 'franchise')
                ->where('reference_id', $franchise['id'])
                ->where('title LIKE', '%Payment Reminder%')
                ->where('DATE(created_at)', $today)
                ->first();

            if (!$existingReminder) {
                $notificationModel->createNotification([
                    'user_id' => $franchise['approved_by'] ?? null,
                    'type' => 'in_app',
                    'title' => 'Payment Reminder - ' . $franchise['applicant_name'],
                    'message' => "Franchise '{$franchise['applicant_name']}' has overdue payments. Days overdue: {$franchise['days_overdue']}",
                    'reference_type' => 'franchise',
                    'reference_id' => $franchise['id'],
                ]);

                $reminderCount++;
            }
        }

        return $reminderCount;
    }

    /**
     * Search franchises
     */
    public function search()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $keyword = $this->request->getGet('q') ?? '';
        $results = [];

        if (strlen($keyword) >= 2) {
            $results = $this->franchiseModel->search($keyword);
        }

        $data = [
            'role'    => $this->session->get('role'),
            'title'   => 'Search Results',
            'keyword' => $keyword,
            'results' => $results,
        ];

        return view('reusables/sidenav', $data) . view('franchise/search_results', $data);
    }
}

