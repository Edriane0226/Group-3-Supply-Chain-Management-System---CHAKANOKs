<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseRequestModel;
use App\Models\InventoryModel;
use App\Models\BranchModel;
use App\Models\DeliveryScheduleModel;
use App\Models\DemandAnalysisModel;
use App\Models\FranchiseModel;
use App\Models\FranchisePaymentModel;
use App\Models\BranchTransferModel;
use App\Models\AccountsPayableModel;
use App\Models\SupplierModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        if ($redirect = $this->authorize('dashboard.view')) {
            return $redirect;
        }

        $session = session();

        $role = $session->get('role');
        $branchName = $session->get('branch_name');
        $branchId = (int)($session->get('branch_id') ?? 0);

        $userModel = new UserModel();
        $branchModel = new BranchModel();
        $deliveryScheduleModel = new DeliveryScheduleModel();
        $inventoryModel = new InventoryModel();
        $AllBranches = $branchModel->findAll();

        if ($role === 'Branch Manager') {
            try {
                $allUsers = $userModel->getUserByBranch($branchId);
            } catch (\Exception $e) {
                $allUsers = [];
                log_message('error', 'Dashboard getUserByBranch error: ' . $e->getMessage());
            }

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

            try {
                $incomingDeliveries = $branchId ? $inventoryModel->getDeliveries($branchId, 'Pending') : [];
            } catch (\Exception $e) {
                $incomingDeliveries = [];
            }
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

            // Get additional dashboard data
            $formattedStockWarning = [];
            $inventoryValue = 0;
            
            if ($branchId > 0) {
                try {
                    $stockWarning = $inventoryModel->getLowStockAlerts($branchId);
                    // Format stockWarning to match view expectations
                    foreach ($stockWarning as $item) {
                        $formattedStockWarning[] = [
                            'item_name' => $item['item_name'] ?? 'N/A',
                            'quantity' => $item['available_stock'] ?? 0,
                            'reorder_level' => 10, // Default reorder level
                            'unit' => 'pcs' // Default unit
                        ];
                    }
                    
                    // Calculate inventory value (need to get price from stock_in)
                    $db = \Config\Database::connect();
                    $inventoryWithPrice = $db->table('stock_in')
                        ->select('item_name, branch_id, SUM(quantity) as total_qty, AVG(price) as avg_price, unit')
                        ->where('branch_id', $branchId)
                        ->groupBy('item_name', 'branch_id', 'unit')
                        ->get()
                        ->getResultArray();
                    
                    foreach ($inventoryWithPrice as $item) {
                        $inventoryValue += ($item['total_qty'] ?? 0) * ($item['avg_price'] ?? 0);
                    }
                } catch (\Exception $e) {
                    // If there's an error, just use empty arrays
                    $formattedStockWarning = [];
                    $inventoryValue = 0;
                }
            }

            $data = [
                'branchName' => $branchName,
                'allUsers' => $allUsers,
                'role' => $role,
                'upcomingDeliveries' => $upcomingDeliveries,
                'branchDeliveryStatus' => $branchDeliveryStatus,
                'incomingDeliveries' => array_slice($incomingDeliveries, 0, 5),
                'stockWarning' => $formattedStockWarning,
                'inventoryValue' => $inventoryValue,
                'totalSalesToday' => 'N/A', // Placeholder - can be implemented later
                'topSellingItems' => 'N/A', // Placeholder - can be implemented later
                'pendingPRs' => 0, // Placeholder - can be implemented later
                'dailySalesSummary' => 'N/A', // Placeholder - can be implemented later
                'salesBreakdown' => 'N/A', // Placeholder - can be implemented later
                'inventoryLevels' => 'N/A', // Placeholder - can be implemented later
                'recentActivity' => 'N/A', // Placeholder - can be implemented later
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

            // Get Demand Analysis Data (Based on Purchase Patterns)
            $demandAnalysisModel = new DemandAnalysisModel();
            $demandSummary = $demandAnalysisModel->getDemandSummary(); // Overall demand summary
            $demandByBranch = $demandAnalysisModel->getDemandByBranch(); // Demand by branch
            $fastSlowMoving = array_slice($demandAnalysisModel->getFastSlowMovingItems(15), 0, 15); // Top 15 fast/slow moving items
            $demandTrends = $demandAnalysisModel->getDemandTrends(30); // Last 30 days demand trends
            $demandByItem = array_slice($demandAnalysisModel->getDemandByItem(10), 0, 10); // Top 10 items by demand
            $reorderPointAnalysis = array_slice($demandAnalysisModel->getReorderPointAnalysis(), 0, 10); // Top 10 items needing reorder
            $demandVsSupply = array_slice($demandAnalysisModel->getDemandVsSupply(), 0, 10); // Top 10 items with demand vs supply gaps
            $seasonalPatterns = $demandAnalysisModel->getSeasonalPatterns(12); // Last 12 months seasonal patterns

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
                // Demand Analysis Data
                'demandSummary' => $demandSummary,
                'demandByBranch' => $demandByBranch,
                'fastSlowMoving' => $fastSlowMoving,
                'demandTrends' => $demandTrends,
                'demandByItem' => $demandByItem,
                'reorderPointAnalysis' => $reorderPointAnalysis,
                'demandVsSupply' => $demandVsSupply,
                'seasonalPatterns' => $seasonalPatterns,
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

    /**
     * Export Central Office Dashboard Reports
     */
    public function exportReport()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Central Office Admin') {
            return redirect()->to(site_url('login'))->with('error', 'Unauthorized access.');
        }

        $reportType = $this->request->getGet('type') ?? 'cost';
        $format = $this->request->getGet('format') ?? 'pdf';
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $reportExport = new \App\Libraries\ReportExport();
        $data = [];
        $headers = [];
        $title = '';

        $purchaseOrderModel = new PurchaseOrderModel();
        $inventoryModel = new InventoryModel();
        $purchaseRequestModel = new PurchaseRequestModel();
        $demandAnalysisModel = new DemandAnalysisModel();

        switch ($reportType) {
            case 'cost':
                $title = 'Cost Analysis Report - ' . date('F d, Y');
                $costByBranch = $purchaseOrderModel->getCostBreakdownByBranch($dateFrom, $dateTo);
                $headers = ['Branch', 'Total Cost', 'Order Count', 'Average Order Value'];
                foreach ($costByBranch as $branch) {
                    $data[] = [
                        $branch['branch_name'] ?? 'N/A',
                        '₱' . number_format($branch['total_cost'] ?? 0, 2),
                        $branch['order_count'] ?? 0,
                        '₱' . number_format($branch['avg_order_value'] ?? 0, 2)
                    ];
                }
                break;

            case 'wastage':
                $title = 'Wastage Analysis Report - ' . date('F d, Y');
                $wastageByBranch = $inventoryModel->getWastageByBranch();
                $headers = ['Branch', 'Total Wastage Value', 'Expired Value', 'Damaged Value', 'Item Count'];
                foreach ($wastageByBranch as $branch) {
                    $data[] = [
                        $branch['branch_name'] ?? 'N/A',
                        '₱' . number_format($branch['total_wastage_value'] ?? 0, 2),
                        '₱' . number_format($branch['expired_value'] ?? 0, 2),
                        '₱' . number_format($branch['damaged_value'] ?? 0, 2),
                        $branch['item_count'] ?? 0
                    ];
                }
                break;

            case 'demand':
                $title = 'Demand Analysis Report - ' . date('F d, Y');
                $demandByBranch = $demandAnalysisModel->getDemandByBranch();
                $headers = ['Branch', 'Total Requests', 'Total Items', 'Unique Items', 'Avg Frequency'];
                foreach ($demandByBranch as $branch) {
                    $data[] = [
                        $branch['branch_name'] ?? 'N/A',
                        $branch['total_requests'] ?? 0,
                        $branch['total_items_requested'] ?? 0,
                        $branch['unique_items'] ?? 0,
                        number_format($branch['avg_request_frequency'] ?? 0, 2) . ' requests/day'
                    ];
                }
                break;

            case 'purchase_requests':
                $title = 'Purchase Request Report - ' . date('F d, Y');
                $prByBranch = $purchaseRequestModel->getStatisticsByBranch();
                $headers = ['Branch', 'Total', 'Pending', 'Approved', 'Rejected', 'Approval Rate'];
                foreach ($prByBranch as $branch) {
                    $data[] = [
                        $branch['branch_name'] ?? 'N/A',
                        $branch['total'] ?? 0,
                        $branch['pending'] ?? 0,
                        $branch['approved'] ?? 0,
                        $branch['rejected'] ?? 0,
                        number_format($branch['approval_rate'] ?? 0, 1) . '%'
                    ];
                }
                break;

            default:
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid report type']);
        }

        if (empty($data)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'No data available for export']);
        }

        // Generate export based on format
        if ($format === 'pdf') {
            $filename = str_replace(' ', '_', strtolower($title)) . '.pdf';
            $pdfContent = $reportExport->generatePDF($data, $title, $headers);
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($pdfContent);
        } elseif ($format === 'excel' || $format === 'xlsx') {
            $filename = str_replace(' ', '_', strtolower($title)) . '.xlsx';
            $excelFile = $reportExport->generateExcel($data, $title, $headers);
            return $this->response->download($excelFile, null)->setFileName($filename);
        } elseif ($format === 'csv') {
            $filename = str_replace(' ', '_', strtolower($title)) . '.csv';
            $csv = $reportExport->generateCSV($data, $headers);
            return $this->response
                ->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csv);
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid export format']);
    }

    /**
     * Central Office Reports Page - Comprehensive reports for all modules
     */
    public function centralReports()
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Central Office Admin') {
            return redirect()->to(site_url('login'))->with('error', 'Unauthorized access.');
        }

        $inventoryModel = new InventoryModel();
        $branchModel = new BranchModel();
        $deliveryScheduleModel = new DeliveryScheduleModel();
        $franchiseModel = new FranchiseModel();
        $branchTransferModel = new BranchTransferModel();
        $supplierModel = new SupplierModel();

        $data = [
            'role' => $session->get('role'),
            'title' => 'Central Office Reports',
            'branches' => $branchModel->findAll(),
            'stockTypes' => $inventoryModel->getStockTypes(),
            'suppliers' => $supplierModel->findAll(),
        ];

        return view('reusables/sidenav', $data) . view('pages/central_reports', $data);
    }

    /**
     * Export Central Office Reports
     */
    public function exportCentralReport(): ResponseInterface
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Central Office Admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized access.']);
        }

        try {
            $reportType = $this->request->getGet('type') ?? 'inventory';
            $format = $this->request->getGet('format') ?? 'pdf';
            $branchId = (int)$this->request->getGet('branch_id') ?? 0;
            $itemTypeId = $this->request->getGet('item_type_id');
            $dateFrom = $this->request->getGet('date_from');
            $dateTo = $this->request->getGet('date_to');

            $reportExport = new \App\Libraries\ReportExport();
            $data = [];
            $headers = [];
            $title = '';

            $inventoryModel = new InventoryModel();
            $branchModel = new BranchModel();
            $deliveryScheduleModel = new DeliveryScheduleModel();
            $franchiseModel = new FranchiseModel();
            $franchisePaymentModel = new FranchisePaymentModel();
            $branchTransferModel = new BranchTransferModel();
            $accountsPayableModel = new AccountsPayableModel();
            $supplierModel = new SupplierModel();

            switch ($reportType) {
                case 'inventory':
                    $title = 'Inventory Report - ' . date('F d, Y');
                    $exportData = $inventoryModel->getExportData($branchId, $itemTypeId, $dateFrom, $dateTo);
                    $headers = ['Item Name', 'Current Stock', 'Unit', 'Expiry Date', 'Barcode', 'Last Updated'];
                    foreach ($exportData as $item) {
                        $data[] = [
                            $item['item_name'] ?? '',
                            $item['current_stock'] ?? 0,
                            $item['unit'] ?? '',
                            $item['expiry_date'] ?? 'N/A',
                            $item['barcode'] ?? 'N/A',
                            $item['updated_at'] ?? 'N/A'
                        ];
                    }
                    break;

                case 'branch':
                    $title = 'Branch Performance Report - ' . date('F d, Y');
                    $branches = $branchModel->findAll();
                    $headers = ['Branch Name', 'Location', 'Status', 'Total Inventory Items', 'Total Stock Value'];
                    foreach ($branches as $branch) {
                        $branchInventory = $inventoryModel->getBalance($branch['id']);
                        $totalItems = count($branchInventory);
                        $totalValue = 0;
                        foreach ($branchInventory as $item) {
                            $totalValue += ($item['current_stock'] ?? 0) * ($item['price'] ?? 0);
                        }
                        $data[] = [
                            $branch['branch_name'] ?? 'N/A',
                            $branch['location'] ?? 'N/A',
                            ucfirst($branch['status'] ?? 'N/A'),
                            $totalItems,
                            '₱' . number_format($totalValue, 2)
                        ];
                    }
                    break;

                case 'logistics':
                    $title = 'Logistics Coordinator Report - ' . date('F d, Y');
                    $startDate = $dateFrom ?: date('Y-m-d', strtotime('-30 days'));
                    $endDate = $dateTo ?: date('Y-m-d');
                    $schedules = $deliveryScheduleModel->getSchedulesByDateRange($startDate, $endDate);
                    $headers = ['Schedule ID', 'Branch', 'Supplier', 'Scheduled Date', 'Status', 'Delivery Status'];
                    foreach ($schedules as $schedule) {
                        $po = (new PurchaseOrderModel())->find($schedule['po_id'] ?? 0);
                        $branch = $branchModel->find($schedule['branch_id'] ?? 0);
                        $data[] = [
                            $schedule['id'] ?? 'N/A',
                            $branch['branch_name'] ?? 'N/A',
                            $po['supplier_name'] ?? 'N/A',
                            date('M d, Y', strtotime($schedule['scheduled_date'] ?? '')),
                            ucfirst($schedule['status'] ?? 'N/A'),
                            ucfirst($po['logistics_status'] ?? 'N/A')
                        ];
                    }
                    break;

                case 'franchise':
                    $title = 'Franchise Performance Report - ' . date('F d, Y');
                    $startDate = $dateFrom ?: date('Y-m-d', strtotime('-12 months'));
                    $endDate = $dateTo ?: date('Y-m-d');
                    $performanceData = $franchiseModel->getAllFranchisesPerformance($startDate, $endDate);
                    $headers = ['Franchise', 'Status', 'Total Payments', 'Avg Monthly Revenue', 'Overall Score'];
                    foreach ($performanceData as $perf) {
                        $data[] = [
                            $perf['franchise_name'] ?? 'N/A',
                            ucfirst($perf['status'] ?? 'N/A'),
                            '₱' . number_format($perf['total_payments'] ?? 0, 2),
                            '₱' . number_format($perf['avg_monthly_revenue'] ?? 0, 2),
                            number_format($perf['overall_score'] ?? 0, 1)
                        ];
                    }
                    break;

                case 'accounts-payable':
                    $title = 'Accounts Payable Report - ' . date('F d, Y');
                    $supplierId = $this->request->getGet('supplier_id') ? (int)$this->request->getGet('supplier_id') : null;
                    $status = $this->request->getGet('status');
                    
                    $accountsPayable = $accountsPayableModel->getAccountsPayableWithRelations($supplierId, $status);
                    
                    // Filter by date range if provided
                    if ($dateFrom && $dateTo) {
                        $accountsPayable = array_filter($accountsPayable, function($ap) use ($dateFrom, $dateTo) {
                            $invoiceDate = $ap['invoice_date'] ?? $ap['created_at'];
                            return $invoiceDate >= $dateFrom && $invoiceDate <= $dateTo;
                        });
                    }
                    
                    $headers = ['Invoice #', 'Supplier', 'Branch', 'Invoice Amount', 'Amount Paid', 'Balance Due', 'Due Date', 'Payment Status'];
                    foreach ($accountsPayable as $ap) {
                        $data[] = [
                            'AP-' . str_pad($ap['id'], 6, '0', STR_PAD_LEFT),
                            $ap['supplier_name'] ?? 'N/A',
                            $ap['branch_name'] ?? 'N/A',
                            '₱' . number_format($ap['invoice_amount'] ?? 0, 2),
                            '₱' . number_format($ap['amount_paid'] ?? 0, 2),
                            '₱' . number_format($ap['balance_due'] ?? 0, 2),
                            $ap['due_date'] ? date('M d, Y', strtotime($ap['due_date'])) : 'N/A',
                            ucfirst($ap['payment_status'] ?? 'N/A')
                        ];
                    }
                    break;

                default:
                    return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid report type']);
            }

            if (empty($data)) {
                $data[] = ['No data available for the selected filters', '', '', '', '', ''];
            }

            if ($format === 'csv') {
                $filename = str_replace(' ', '_', strtolower($title)) . '.csv';
                $csv = $reportExport->generateCSV($data, $headers);
                return $this->response
                    ->setHeader('Content-Type', 'text/csv; charset=utf-8')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($csv);
            } elseif ($format === 'pdf') {
                $filename = str_replace(' ', '_', strtolower($title)) . '.pdf';
                $pdfContent = $reportExport->generatePDF($data, $title, $headers);
                return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($pdfContent);
            } elseif ($format === 'excel' || $format === 'xlsx') {
                $filename = str_replace(' ', '_', strtolower($title)) . '.xlsx';
                $excelFile = $reportExport->generateExcel($data, $title, $headers);
                
                if (!file_exists($excelFile)) {
                    throw new \Exception('Excel file was not created successfully');
                }
                
                $excelContent = file_get_contents($excelFile);
                @unlink($excelFile);
                
                return $this->response
                    ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setHeader('Content-Length', strlen($excelContent))
                    ->setBody($excelContent);
            }

            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid export format']);
        } catch (\Exception $e) {
            log_message('error', 'Central Report Export error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'error' => 'Export failed: ' . $e->getMessage()
                ]);
        }
    }
}
