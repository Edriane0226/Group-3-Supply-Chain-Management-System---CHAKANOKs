<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class Inventory extends BaseController
{
    protected InventoryModel $inventoryModel;

    public function __construct()
    {
        $this->inventoryModel = new InventoryModel();
        helper(['form']);
    }

    // ✅ Main Inventory Page (Branch Manager)
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
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
        $branchId = (int)(session()->get('branch_id') ?? 0);
        $inventory = $branchId > 0
            ? $this->inventoryModel->where('branch_id', $branchId)->findAll()
            : $this->inventoryModel->findAll();

        return $this->response->setJSON($inventory);
    }

    // ✅ Update stock
    public function updateStock(): ResponseInterface
    {
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
        $branchId = (int)$this->request->getGet('branch_id') ?? (int)(session()->get('branch_id') ?? 0);

        $balance = $this->inventoryModel->getStockBalance($branchId);

        return $this->response->setJSON($balance);
    }

    // ✅ Stock In (add to inventory)
    public function stockin(): ResponseInterface
    {
        $guard = $this->ensureInventoryAccess();
        if ($guard) {
            return $this->response->setStatusCode(302)->setJSON(['redirect' => (string)$guard->getHeaderLine('Location')]);
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
        $guard = $this->ensureInventoryAccess();
        if ($guard) {
            return $this->response->setStatusCode(302)->setJSON(['redirect' => (string)$guard->getHeaderLine('Location')]);
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
        $format = $this->request->getGet('export') ?? 'csv';
        $branchId = (int)$this->request->getGet('branch_id') ?? (int)(session()->get('branch_id') ?? 0);
        $itemTypeId = $this->request->getGet('item_type_id');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $data = $this->inventoryModel->getExportData($branchId, $itemTypeId, $dateFrom, $dateTo);

        if ($format === 'csv') {
            $filename = 'inventory_report_' . date('Y-m-d') . '.csv';
            $csv = $this->generateCSV($data);
            return $this->response
                ->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csv);
        } elseif ($format === 'pdf') {
            // For PDF, we'd need a library like TCPDF or similar
            // For now, return CSV as fallback
            $filename = 'inventory_report_' . date('Y-m-d') . '.csv';
            $csv = $this->generateCSV($data);
            return $this->response
                ->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($csv);
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid export format']);
    }

    private function generateCSV($data): string
    {
        $csv = "Item Name,Current Stock,Unit,Expiry Date,Barcode,Last Updated\n";

        foreach ($data as $item) {
            $csv .= '"' . str_replace('"', '""', $item['item_name']) . '",';
            $csv .= '"' . $item['current_stock'] . '",';
            $csv .= '"' . $item['unit'] . '",';
            $csv .= '"' . ($item['expiry_date'] ?? '') . '",';
            $csv .= '"' . ($item['barcode'] ?? '') . '",';
            $csv .= '"' . ($item['updated_at'] ?? '') . '"';
            $csv .= "\n";
        }

        return $csv;
    }

    // ✅ Receive stock (increase)
    public function receive(): ResponseInterface
    {
        $guard = $this->ensureInventoryAccess();
        if ($guard) {
            return $this->response->setStatusCode(302)->setJSON(['redirect' => (string)$guard->getHeaderLine('Location')]);
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
        $guard = $this->ensureInventoryAccess();
        if ($guard) {
            return $this->response->setStatusCode(302)->setJSON(['redirect' => (string)$guard->getHeaderLine('Location')]);
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
        $guard = $this->ensureInventoryAccess();
        if ($guard) {
            return $this->response->setStatusCode(302)->setJSON(['redirect' => (string)$guard->getHeaderLine('Location')]);
        }
        $branchId = (int)(session()->get('branch_id') ?? 0);
        if ($branchId <= 0) {
            return $this->response->setJSON(['totals' => ['total_skus' => 0, 'total_quantity' => 0], 'lowStock' => [], 'expiringSoon' => []]);
        }
        $summary = $this->inventoryModel->getBranchSummary($branchId);
        return $this->response->setJSON($summary);
    }

    // Access guards
    private function ensureInventoryAccess()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        $role = (string) (session()->get('role') ?? '');
        if ($role !== 'Inventory Staff' && $role !== 'Branch Manager' && $role !== 'Central Office Admin') {
            return redirect()->to('/login');
        }
        return null;
    }

    // Staff Pages (Inventory Staff only)
    public function overviewPage() {
        $guard = $this->ensureInventoryAccess();
        if ($guard) return $guard;
        if ((string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); }
        $branchId = (int)(session()->get('branch_id') ?? 0);
        $data['inventory'] = $this->inventoryModel->getStockBalance($branchId);
        return view('pages/inventory_overview', $data);
    }
    public function scanPage() { $guard = $this->ensureInventoryAccess(); if ($guard) return $guard; if ((string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); } return view('pages/inventory_scan'); }


    // Stock In Page
    public function stockInPage()
    {
        $guard = $this->ensureInventoryAccess();
        if ($guard) return $guard;
        if ((string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); }
        $data['stockTypes'] = $this->inventoryModel->getStockTypes();
        return view('pages/inventory_stockin', $data);
    }

    // Stock Out Page
    public function stockOutPage()
    {
        $guard = $this->ensureInventoryAccess();
        if ($guard) return $guard;
        if ((string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); }
        $data['stockTypes'] = $this->inventoryModel->getStockTypes();
        return view('pages/inventory_stockout', $data);
    }

    // Reports Page
    public function reportsPage()
    {
        $guard = $this->ensureInventoryAccess();
        if ($guard) return $guard;
        if ((string)session()->get('role') !== 'Branch Manager' && (string)session()->get('role') !== 'Central Office Admin' && (string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); }
        $branchId = (int)(session()->get('branch_id') ?? 0);
        $data['balance'] = $this->inventoryModel->getBalance($branchId);
        return view('pages/inventory_reports', $data);
    }
}
