<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseRequestModel;
use App\Models\InventoryModel;
use App\Models\BranchModel;
use App\Models\DeliveryScheduleModel;


class Dashboard extends Controller
{
    public function index()
    {
        $session = session();

        //  Check if logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $role = $session->get('role');
        $branchName = $session->get('branch_name');
        $branchId = (int)($session->get('branch_id') ?? 0);

        $userModel = new UserModel();
        $branchModel = new BranchModel();
        $deliveryScheduleModel = new DeliveryScheduleModel();
        $inventoryModel = new InventoryModel();
        $AllBranches = $branchModel->findAll();

        if ($role === 'Branch Manager') {
            $allUsers = $userModel->getUserByBranch($branchId);

            $windowStart = date('Y-m-d');
            $windowEnd = date('Y-m-d', strtotime('+14 days'));
            $upcomingDeliveries = $branchId ? $deliveryScheduleModel->getBranchUpcomingDeliveries($branchId, $windowStart, $windowEnd) : [];

            $branchDeliveryStatus = [
                'Scheduled' => 0,
                'In Progress' => 0,
                'Completed' => 0,
            ];
            foreach ($upcomingDeliveries as $delivery) {
                $statusLabel = $delivery['status'] ?? 'Scheduled';
                if (!array_key_exists($statusLabel, $branchDeliveryStatus)) {
                    $branchDeliveryStatus[$statusLabel] = 0;
                }
                $branchDeliveryStatus[$statusLabel]++;
            }

            $incomingDeliveries = $branchId ? $inventoryModel->getDeliveries($branchId, 'Pending') : [];
            $incomingDeliveries = array_map(static function ($delivery) {
                $delivery['source'] = 'delivery_record';
                return $delivery;
            }, $incomingDeliveries);

            $scheduleIncoming = array_filter($upcomingDeliveries, static function ($delivery) {
                return in_array($delivery['status'] ?? 'Scheduled', ['Scheduled', 'In Progress'], true);
            });

            $scheduleIncoming = array_map(static function ($delivery) {
                return [
                    'id' => $delivery['id'],
                    'supplier_name' => $delivery['supplier_name'] ?? 'N/A',
                    'delivery_date' => $delivery['scheduled_date'] ?? null,
                    'delivery_time' => $delivery['scheduled_time'] ?? null,
                    'total_items' => null,
                    'remarks' => $delivery['notes'] ?? null,
                    'status' => $delivery['status'] ?? 'Scheduled',
                    'source' => 'schedule',
                ];
            }, $scheduleIncoming);

            $incomingDeliveries = array_merge($incomingDeliveries, $scheduleIncoming);

            $data = [
                'branchName' => $branchName,
                'allUsers' => $allUsers,
                'role' => $role,
                'upcomingDeliveries' => $upcomingDeliveries,
                'branchDeliveryStatus' => $branchDeliveryStatus,
                'incomingDeliveries' => array_slice($incomingDeliveries, 0, 5),
            ];

            return view('reusables/sidenav', $data) . view('pages/dashboard');
        } elseif ($role === 'Central Office Admin') {
            $allUsers = $userModel->findAll();
            $Inv = new InventoryModel();
            $purchaseRequestModel = new PurchaseRequestModel();
            $purchaseOrderModel = new PurchaseOrderModel();

            // Get Purchase Request Statistics
            $prStatistics = $purchaseRequestModel->getStatisticsSummary();
            $prByBranch = $purchaseRequestModel->getStatisticsByBranch();
            $prBySupplier = array_slice($purchaseRequestModel->getStatisticsBySupplier(), 0, 5); // Top 5 suppliers
            $prAvgProcessingTime = $purchaseRequestModel->getAverageProcessingTime();
            $prTrends = $purchaseRequestModel->getRequestTrends(30); // Last 30 days

            // Get Cost Analysis Data
            $costSummary = $purchaseOrderModel->getCostSummary(); // Overall summary
            $costByBranch = $purchaseOrderModel->getCostBreakdownByBranch(); // Cost by branch
            $costBySupplier = array_slice($purchaseOrderModel->getCostBreakdownBySupplier(), 0, 5); // Top 5 suppliers by cost
            $costTrends = $purchaseOrderModel->getCostTrends(30); // Last 30 days cost trends
            $apSummary = $purchaseOrderModel->getAccountsPayableSummary(); // Accounts payable summary

            // Get Wastage Analysis Data
            $wastageSummary = $Inv->getWastageSummary(); // Overall wastage summary
            $wastageByBranch = $Inv->getWastageByBranch(); // Wastage by branch
            $wastageByItem = array_slice($Inv->getWastageByItem(null, 10), 0, 10); // Top 10 items with wastage
            $wastageByReason = $Inv->getWastageByReason(); // Wastage by reason (expired, damaged)
            $wastageTrends = $Inv->getWastageTrends(6); // Last 6 months wastage trends

            $windowStart = date('Y-m-d');
            $windowEnd = date('Y-m-d', strtotime('+14 days'));
            $centralDeliveryOverview = $deliveryScheduleModel->getCentralDeliveryOverview($windowStart, $windowEnd);

            $centralDeliveryStatusSummary = [
                'Scheduled' => 0,
                'In Progress' => 0,
                'Completed' => 0,
                'Cancelled' => 0,
            ];
            $centralDelayedDeliveries = [];
            $supplierPerformance = [];
            $now = time();

            foreach ($centralDeliveryOverview as $entry) {
                $status = $entry['status'] ?? 'Scheduled';
                if (!array_key_exists($status, $centralDeliveryStatusSummary)) {
                    $centralDeliveryStatusSummary[$status] = 0;
                }
                $centralDeliveryStatusSummary[$status]++;

                $scheduledAt = strtotime(($entry['scheduled_date'] ?? $windowStart) . ' ' . ($entry['scheduled_time'] ?? '00:00:00'));
                if ($scheduledAt !== false && $scheduledAt < $now && ($entry['status'] ?? 'Scheduled') !== 'Completed') {
                    $centralDelayedDeliveries[] = $entry;
                }

                $supplierKey = $entry['supplier_name'] ?? 'Unknown Supplier';
                if (!isset($supplierPerformance[$supplierKey])) {
                    $supplierPerformance[$supplierKey] = [
                        'supplier' => $supplierKey,
                        'total' => 0,
                        'completed' => 0,
                        'on_time' => 0,
                    ];
                }

                $supplierPerformance[$supplierKey]['total']++;
                if (($entry['status'] ?? '') === 'Completed') {
                    $supplierPerformance[$supplierKey]['completed']++;
                    $expectedDate = $entry['expected_delivery_date'] ?? $entry['scheduled_date'] ?? null;
                    $actualDate = $entry['actual_delivery_date'] ?? null;
                    if ($expectedDate && $actualDate && strtotime($actualDate) <= strtotime($expectedDate)) {
                        $supplierPerformance[$supplierKey]['on_time']++;
                    }
                }
            }

            $supplierPerformance = array_map(static function ($metrics) {
                $completionRate = $metrics['total'] > 0 ? round(($metrics['completed'] / $metrics['total']) * 100, 1) : 0;
                $onTimeRate = $metrics['completed'] > 0 ? round(($metrics['on_time'] / $metrics['completed']) * 100, 1) : 0;

                return [
                    'supplier' => $metrics['supplier'],
                    'total' => $metrics['total'],
                    'completion_rate' => $completionRate,
                    'on_time_rate' => $onTimeRate,
                ];
            }, array_values($supplierPerformance));

            usort($supplierPerformance, static function ($a, $b) {
                return $b['completion_rate'] <=> $a['completion_rate'];
            });

            $data = [
                'branchName' => $branchName,
                'allUsers' => $allUsers,
                'role' => $role,
                'AllBranches' => $AllBranches,
                'invValues' => $Inv->getOverallInventoryValue(),
                'expiredValue' => $Inv->getOverallExpiredValue(),
                'centralDeliveryOverview' => $centralDeliveryOverview,
                'centralDeliveryStatusSummary' => $centralDeliveryStatusSummary,
                'centralDelayedDeliveries' => array_slice($centralDelayedDeliveries, 0, 5),
                'supplierPerformance' => array_slice($supplierPerformance, 0, 5),
                // Purchase Request Statistics
                'prStatistics' => $prStatistics,
                'prByBranch' => $prByBranch,
                'prBySupplier' => $prBySupplier,
                'prAvgProcessingTime' => $prAvgProcessingTime,
                'prTrends' => $prTrends,
                // Cost Analysis Data
                'costSummary' => $costSummary,
                'costByBranch' => $costByBranch,
                'costBySupplier' => $costBySupplier,
                'costTrends' => $costTrends,
                'apSummary' => $apSummary,
                // Wastage Analysis Data
                'wastageSummary' => $wastageSummary,
                'wastageByBranch' => $wastageByBranch,
                'wastageByItem' => $wastageByItem,
                'wastageByReason' => $wastageByReason,
                'wastageTrends' => $wastageTrends,
            ];

            return view('reusables/sidenav', $data) . view('pages/dashboard');
        } elseif ($role === 'Inventory Staff') {
            $data = [
                'role' => $role,
            ];

            return view('reusables/sidenav', $data) . view('pages/inventory_overview');
        }


        // //  Only Branch Managers can access
        // if ($session->get('role') !== 'Branch Manager') {
        //     return redirect()->to(site_url('login'))->with('error', 'Unauthorized access.');
        // }
        
        // //  Load dashboard view
        // return view('pages/branchdashboard', [
        //     'full_name'   => $session->get('full_name'),
        //     'branch_name' => $session->get('branch_name'),
            
        // ]);
    }

    /**
     * Test endpoint to verify new data is available
     * Access: /dashboard/test-data
     */
    public function testData()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Central Office Admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized. Please login as Central Office Admin.']);
        }

        $purchaseRequestModel = new PurchaseRequestModel();
        $purchaseOrderModel = new PurchaseOrderModel();
        $inventoryModel = new InventoryModel();

        $data = [
            'status' => 'success',
            'message' => 'All new methods are working!',
            'data' => [
                'purchase_request_statistics' => $purchaseRequestModel->getStatisticsSummary(),
                'purchase_request_by_branch' => $purchaseRequestModel->getStatisticsByBranch(),
                'purchase_request_by_supplier' => array_slice($purchaseRequestModel->getStatisticsBySupplier(), 0, 3),
                'purchase_request_avg_processing_time' => $purchaseRequestModel->getAverageProcessingTime(),
                'cost_summary' => $purchaseOrderModel->getCostSummary(),
                'cost_by_branch' => $purchaseOrderModel->getCostBreakdownByBranch(),
                'cost_by_supplier' => array_slice($purchaseOrderModel->getCostBreakdownBySupplier(), 0, 3),
                'accounts_payable_summary' => $purchaseOrderModel->getAccountsPayableSummary(),
                'wastage_summary' => $inventoryModel->getWastageSummary(),
                'wastage_by_branch' => $inventoryModel->getWastageByBranch(),
                'wastage_by_reason' => $inventoryModel->getWastageByReason(),
            ]
        ];

        return $this->response->setJSON($data);
    }
}
