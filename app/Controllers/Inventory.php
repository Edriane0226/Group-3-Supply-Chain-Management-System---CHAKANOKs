<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use App\Models\DeliveryScheduleModel;
use App\Models\PurchaseOrderModel;
use App\Models\BranchModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\ResponseInterface;

class Inventory extends BaseController
{
    protected InventoryModel $inventoryModel;
    protected DeliveryScheduleModel $deliveryScheduleModel;
    protected PurchaseOrderModel $purchaseOrderModel;

    public function __construct()
    {
        $this->inventoryModel = new InventoryModel();
        $this->deliveryScheduleModel = new DeliveryScheduleModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        helper(['form']);
    }

    // ✅ Main Inventory Page (Branch Manager)
    public function index()
    {
        if ($redirect = $this->authorize('inventory.view')) {
            return $redirect;
        }

        $branchId = (int)(session()->get('branch_id') ?? 0);

        // 🔹 Auto-seed if inventory table is empty
        if ($this->inventoryModel->countAllResults(false) === 0) {
            $seeder = new StockSeeder();
            $seeder->run();
        }

        $data['inventory'] = $branchId > 0
            ? $this->inventoryModel->where('branch_id', $branchId)->findAll()
            : $this->inventoryModel->findAll();

        return view('pages/InventoryBranch', $data);
    }

    // ✅ Live inventory JSON (for frontend auto-refresh)
    public function liveInventory(): ResponseInterface
    {
        if (!$this->canAccess('inventory.view')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $branchId = (int)(session()->get('branch_id') ?? 0);
        $inventory = $branchId > 0
            ? $this->inventoryModel->where('branch_id', $branchId)->findAll()
            : $this->inventoryModel->findAll();

        return $this->response->setJSON($inventory);
    }

    // ✅ Update stock
    public function updateStock(): ResponseInterface
    {
        if (!$this->canAccess('inventory.adjust')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $id    = (int)$this->request->getPost('id');
        $delta = (int)$this->request->getPost('delta');

        if ($id <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'id required']);
        }

        if ($this->inventoryModel->adjustStock($id, $delta)) {
            $item = $this->inventoryModel->find($id);
            return $this->response->setJSON(['success' => true, 'item' => $item]);
        }

        return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
    }

    // ✅ Find by barcode
    public function findByBarcode(): ResponseInterface
    {
        if (!$this->canAccess('inventory.view')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $barcode  = (string)$this->request->getGet('barcode');
        $branchId = (int)(session()->get('branch_id') ?? 0);

        if ($barcode === '') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'barcode required']);
        }

        $item = $this->inventoryModel->findByBarcode($barcode, $branchId);

        if ($item) {
            return $this->response->setJSON($item);
        }

        return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
    }

    // ✅ Get current stock balance
    public function balance(): ResponseInterface
    {
        if (!$this->canAccess('inventory.view')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $branchId = (int)$this->request->getGet('branch_id') ?? (int)(session()->get('branch_id') ?? 0);

        $balance = $this->inventoryModel->getStockBalance($branchId);

        return $this->response->setJSON($balance);
    }

    // ✅ Stock In (add to inventory)
    public function stockin(): ResponseInterface
    {
        if (!$this->canAccess('inventory.stock_in')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid JSON data']);
        }

        $required = ['item_type_id', 'branch_id', 'item_name', 'quantity', 'unit', 'price'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return $this->response->setStatusCode(400)->setJSON(['error' => "Field '$field' is required"]);
            }
        }

        if ($this->inventoryModel->addStockIn($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Stock added successfully']);
        }

        return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to add stock']);
    }

    // ✅ Stock Out (remove from inventory)
    public function stockout(): ResponseInterface
    {
        if (!$this->canAccess('inventory.stock_out')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid JSON data']);
        }

        $required = ['branch_id', 'item_type_id', 'item_name', 'quantity', 'unit'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return $this->response->setStatusCode(400)->setJSON(['error' => "Field '$field' is required"]);
            }
        }

        if ($this->inventoryModel->addStockOut($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Stock removed successfully']);
        }

        return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to remove stock']);
    }

    // ✅ Export reports
    public function export(): ResponseInterface
    {
        if ($redirect = $this->authorize('inventory.export')) {
            return $redirect;
        }

        try {
            $format = $this->request->getGet('export') ?? 'csv';
            $branchId = (int)$this->request->getGet('branch_id') ?? (int)(session()->get('branch_id') ?? 0);
            $itemTypeId = $this->request->getGet('item_type_id');
            $dateFrom = $this->request->getGet('date_from');
            $dateTo = $this->request->getGet('date_to');

            $data = $this->inventoryModel->getExportData($branchId, $itemTypeId, $dateFrom, $dateTo);
            
            // Prepare data for export
            $exportData = [];
            if (!empty($data)) {
                foreach ($data as $item) {
                    $exportData[] = [
                        $item['item_name'] ?? '',
                        $item['current_stock'] ?? 0,
                        $item['unit'] ?? '',
                        $item['expiry_date'] ?? 'N/A',
                        $item['barcode'] ?? '',
                        $item['updated_at'] ?? ''
                    ];
                }
            } else {
                // If no data, add a message row
                $exportData[] = ['No data available for the selected filters', '', '', '', '', ''];
            }
            
            $headers = ['Item Name', 'Current Stock', 'Unit', 'Expiry Date', 'Barcode', 'Last Updated'];
            $title = 'Inventory Report - ' . date('F d, Y');
            $reportExport = new \App\Libraries\ReportExport();

            if ($format === 'csv') {
                $filename = 'inventory_report_' . date('Y-m-d') . '.csv';
                $csv = $reportExport->generateCSV($exportData, $headers);
                return $this->response
                    ->setHeader('Content-Type', 'text/csv; charset=utf-8')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($csv);
            } elseif ($format === 'pdf') {
                $filename = 'inventory_report_' . date('Y-m-d') . '.pdf';
                $pdfContent = $reportExport->generatePDF($exportData, $title, $headers);
                return $this->response
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($pdfContent);
            } elseif ($format === 'excel' || $format === 'xlsx') {
                $filename = 'inventory_report_' . date('Y-m-d') . '.xlsx';
                $excelFile = $reportExport->generateExcel($exportData, $title, $headers);
                
                if (!file_exists($excelFile)) {
                    throw new \Exception('Excel file was not created successfully');
                }
                
                $excelContent = file_get_contents($excelFile);
                // Clean up temp file after reading
                @unlink($excelFile);
                
                return $this->response
                    ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setHeader('Content-Length', strlen($excelContent))
                    ->setBody($excelContent);
            }

            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid export format']);
        } catch (\Exception $e) {
            log_message('error', 'Export error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'error' => 'Export failed: ' . $e->getMessage(),
                    'message' => 'Please check if required libraries (TCPDF, PhpSpreadsheet) are installed. Run: composer install'
                ]);
        }
    }

    // ✅ Receive stock (increase)
    public function receive(): ResponseInterface
    {
        if (!$this->canAccess('inventory.adjust')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }
        $id     = (int)$this->request->getPost('id');
        $amount = (int)$this->request->getPost('amount');
        if ($id <= 0 || $amount <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'id and positive amount required']);
        }
        if ($this->inventoryModel->increaseStock($id, $amount)) {
            $item = $this->inventoryModel->find($id);
            return $this->response->setJSON(['success' => true, 'item' => $item]);
        }
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
    }

    // ✅ Report damage (decrease)
    public function reportDamage(): ResponseInterface
    {
        if (!$this->canAccess('inventory.adjust')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }
        $id     = (int)$this->request->getPost('id');
        $amount = (int)$this->request->getPost('amount');
        if ($id <= 0 || $amount <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'id and positive amount required']);
        }
        if ($this->inventoryModel->decreaseStock($id, $amount)) {
            $item = $this->inventoryModel->find($id);
            return $this->response->setJSON(['success' => true, 'item' => $item]);
        }
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
    }

    // ✅ Branch-scoped summary for dashboards
    public function summary(): ResponseInterface
    {
        if (!$this->canAccess('inventory.view')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }
        $branchId = (int)(session()->get('branch_id') ?? 0);
        if ($branchId <= 0) {
            return $this->response->setJSON(['totals' => ['total_skus' => 0, 'total_quantity' => 0], 'lowStock' => [], 'expiringSoon' => []]);
        }
        $summary = $this->inventoryModel->getBranchSummary($branchId);
        return $this->response->setJSON($summary);
    }

    // Staff Pages (Inventory Staff only)
    public function overviewPage() {
        if ($redirect = $this->authorize('inventory.staff_portal')) {
            return $redirect;
        }
        $branchId = (int)(session()->get('branch_id') ?? 0);
        $data['inventory'] = $this->inventoryModel->getStockBalance($branchId);
        return view('pages/inventory_overview', $data);
    }
    public function scanPage() {
        if ($redirect = $this->authorize('inventory.staff_portal')) {
            return $redirect;
        }
        return view('pages/inventory_scan');
    }


    // Stock In Page
    public function stockInPage()
    {
        if ($redirect = $this->authorize('inventory.staff_portal')) {
            return $redirect;
        }
        $data['stockTypes'] = $this->inventoryModel->getStockTypes();
        return view('pages/inventory_stockin', $data);
    }

    // Stock Out Page
    public function stockOutPage()
    {
        if ($redirect = $this->authorize('inventory.staff_portal')) {
            return $redirect;
        }
        $data['stockTypes'] = $this->inventoryModel->getStockTypes();
        return view('pages/inventory_stockout', $data);
    }

    // Reports Page
    public function reportsPage()
    {
        if ($redirect = $this->authorize('inventory.reports')) {
            return $redirect;
        }
        $branchId = (int)(session()->get('branch_id') ?? 0);
        $branchModel = new BranchModel();
        $data['balance'] = $this->inventoryModel->getBalance($branchId);
        $data['stockTypes'] = $this->inventoryModel->getStockTypes();
        $data['branches'] = $branchModel->findAll();
        return view('pages/inventory_reports', $data);
    }

    public function confirmDelivery(int $scheduleId): ResponseInterface
    {
        if (!$this->canAccess('inventory.confirm_delivery')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $role = (string) (session()->get('role') ?? '');

        $schedule = $this->deliveryScheduleModel->getScheduleWithRelations($scheduleId);
        if (!$schedule) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Delivery schedule not found']);
        }

        $branchId = (int)($schedule['branch_id'] ?? 0);
        $userBranchId = (int)(session()->get('branch_id') ?? 0);

        if ($role !== 'Central Office Admin' && $branchId !== $userBranchId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'You cannot confirm deliveries for other branches']);
        }

        if (($schedule['status'] ?? '') === 'Completed') {
            return $this->response->setJSON(['success' => true, 'message' => 'Delivery already confirmed']);
        }

        $poId = (int)($schedule['po_id'] ?? 0);
        if ($poId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid purchase order reference']);
        }

        $po = $this->purchaseOrderModel->find($poId);
        if (!$po) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Purchase order not found']);
        }

        if (($po['logistics_status'] ?? '') === 'completed') {
            return $this->response->setJSON(['success' => true, 'message' => 'Delivery already completed']);
        }

        // Only allow confirming delivery on the scheduled date
        $today = date('Y-m-d');
        $scheduledDate = isset($schedule['scheduled_date']) ? date('Y-m-d', strtotime($schedule['scheduled_date'])) : null;
        if ($scheduledDate === null || $scheduledDate !== $today) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Cannot confirm delivery: scheduled date does not match today']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $stockInserted = $this->recordStockFromPurchaseOrder($po, $db);

        if (!$stockInserted) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to update inventory for this purchase order']);
        }

        $this->purchaseOrderModel->update($poId, [
            'status' => 'Delivered',
            'logistics_status' => 'completed',
            'actual_delivery_date' => date('Y-m-d'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->deliveryScheduleModel->update($scheduleId, [
            'status' => 'Completed',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to confirm delivery']);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Delivery confirmed and inventory updated',
        ]);
    }

    private function recordStockFromPurchaseOrder(array $po, BaseConnection $db): bool
    {
        if (empty($po['purchase_request_id'])) {
            return false;
        }

        $request = $db->table('purchase_requests')
                      ->where('id', $po['purchase_request_id'])
                      ->get()
                      ->getRowArray();

        if (!$request) {
            return false;
        }

        $stockType = $db->table('stock_types')->orderBy('id', 'ASC')->get()->getRowArray();

        if (!$stockType) {
            $db->table('stock_types')->insert(['type_name' => 'General']);
            $itemTypeId = $db->insertID();
        } else {
            $itemTypeId = $stockType['id'];
        }

        $quantity = (int)($request['quantity'] ?? 0);
        if ($quantity <= 0) {
            $quantity = 1;
        }

        $insertData = [
            'item_type_id' => $itemTypeId,
            'branch_id' => $po['branch_id'],
            'item_name' => $request['item_name'] ?? ('PO #' . $po['id']),
            'category' => null,
            'quantity' => $quantity,
            'unit' => $request['unit'] ?? 'pcs',
            'price' => $request['price'] ?? ($po['total_amount'] ?? 0),
            'expiry_date' => null,
            'barcode' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $db->table('stock_in')->insert($insertData);
    }
}
