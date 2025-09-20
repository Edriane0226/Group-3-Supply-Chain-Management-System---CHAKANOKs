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

    // 🔒 Staff page guards
    private function ensureStaff()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        if (session()->get('role') !== 'Inventory Staff') {
            return redirect()->to('/inventory');
        }
        return null;
    }

    // Staff Pages
    public function overviewPage() { $guard = $this->ensureStaff(); if ($guard) return $guard; return view('pages/inventory_overview'); }
    public function scanPage() { $guard = $this->ensureStaff(); if ($guard) return $guard; return view('pages/inventory_scan'); }
    public function lowPage() { $guard = $this->ensureStaff(); if ($guard) return $guard; return view('pages/inventory_low'); }
    public function expiryPage() { $guard = $this->ensureStaff(); if ($guard) return $guard; return view('pages/inventory_expiry'); }
}


