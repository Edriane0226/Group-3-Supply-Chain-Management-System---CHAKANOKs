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

    public function summary(): ResponseInterface
    {
        $branchId = (int)($this->request->getGet('branch_id') ?? session()->get('branch_id') ?? 0);
        if ($branchId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'branch_id required']);
        }
        $data = $this->inventoryModel->getBranchSummary($branchId);
        return $this->response->setJSON($data);
    }

    public function findByBarcode(): ResponseInterface
    {
        $barcode  = (string)$this->request->getGet('barcode');
        $branchId = (int)($this->request->getGet('branch_id') ?? session()->get('branch_id') ?? 0);
        if ($barcode === '') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'barcode required']);
        }
        $item = $this->inventoryModel->findByBarcode($barcode, $branchId > 0 ? $branchId : null);
        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
        }
        return $this->response->setJSON($item);
    }

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

    public function receive(): ResponseInterface
    {
        $id     = (int)$this->request->getPost('id');
        $amount = (int)$this->request->getPost('amount');
        $amount = max(0, $amount);

        if ($id <= 0 || $amount <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'id and positive amount required']);
        }
        $ok = $this->inventoryModel->adjustStock($id, $amount);
        if (!$ok) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
        }
        $item = $this->inventoryModel->find($id);
        return $this->response->setJSON(['success' => true, 'item' => $item]);
    }

    public function reportDamage(): ResponseInterface
    {
        $id     = (int)$this->request->getPost('id');
        $amount = (int)$this->request->getPost('amount');
        $amount = max(0, $amount);

        if ($id <= 0 || $amount <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'id and positive amount required']);
        }
        $ok = $this->inventoryModel->adjustStock($id, -$amount);
        if (!$ok) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Item not found']);
        }
        $item = $this->inventoryModel->find($id);
        return $this->response->setJSON(['success' => true, 'item' => $item]);
    }

    // Render pages (Inventory Staff only)
    private function ensureStaff()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        if (session()->get('role') !== 'Inventory Staff') {
            return redirect()->to('inventory');
        }
        return null;
    }

    public function overviewPage()
    {
        $guard = $this->ensureStaff(); if ($guard) return $guard;
        return view('pages/inventory_overview');
    }

    public function scanPage()
    {
        $guard = $this->ensureStaff(); if ($guard) return $guard;
        return view('pages/inventory_scan');
    }

    public function lowPage()
    {
        $guard = $this->ensureStaff(); if ($guard) return $guard;
        return view('pages/inventory_low');
    }

    public function expiryPage()
    {
        $guard = $this->ensureStaff(); if ($guard) return $guard;
        return view('pages/inventory_expiry');
    }
}


