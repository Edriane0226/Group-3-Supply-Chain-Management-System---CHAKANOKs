<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Database\Seeds\StockSeeder;

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

        $item = $this->inventoryModel->findByBarcode($barcode, $branchId > 0 ? $branchId : null);
        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
        }

        return $this->response->setJSON($item);
    }

    // 🔒 Access guards
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
    public function overviewPage() { $guard = $this->ensureInventoryAccess(); if ($guard) return $guard; if ((string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); } return view('pages/inventory_overview'); }
    public function scanPage() { $guard = $this->ensureInventoryAccess(); if ($guard) return $guard; if ((string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); } return view('pages/inventory_scan'); }
    public function lowPage() { $guard = $this->ensureInventoryAccess(); if ($guard) return $guard; if ((string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); } return view('pages/inventory_low'); }
    public function expiryPage() { $guard = $this->ensureInventoryAccess(); if ($guard) return $guard; if ((string)session()->get('role') !== 'Inventory Staff') { return redirect()->to('/inventory'); } return view('pages/inventory_expiry'); }

    // ✅ Branch-scoped summary for dashboards
    public function summary(): ResponseInterface
    {
        $guard = $this->ensureInventoryAccess();
        if ($guard) {
            // If guard returns a redirect response, honor it
            return $this->response->setStatusCode(302)->setJSON(['redirect' => (string)$guard->getHeaderLine('Location')]);
        }
        $branchId = (int)(session()->get('branch_id') ?? 0);
        if ($branchId <= 0) {
            return $this->response->setJSON(['totals' => ['total_skus' => 0, 'total_quantity' => 0], 'lowStock' => [], 'expiringSoon' => []]);
        }
        $summary = $this->inventoryModel->getBranchSummary($branchId);
        return $this->response->setJSON($summary);
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

    
}


