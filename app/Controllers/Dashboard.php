<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use App\Models\PurchaseRequestModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $session = session();

        // ✅ Check kung naka-login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $branchId = $session->get('branch_id');

        $inventoryModel = new InventoryModel();
        $inventoryValue = $inventoryModel->getInventoryValue($branchId);
        $stockWarning   = $inventoryModel->getLowStockAlerts($branchId);

        $data = [
            'inventoryValue' => $inventoryValue,
            'stockWarning'   => $stockWarning,
        ];

        // ✅ Role-based view
        if ($session->get('role') === 'Branch Manager') {
            return view('pages/dashboard', $data);
        } elseif ($session->get('role') === 'Inventory Staff') {
            return view('pages/inventory_overview', $data);
        } else {
            // Kung ibang role or walang role match
            $session->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(site_url('login'));
        }
    }
}
