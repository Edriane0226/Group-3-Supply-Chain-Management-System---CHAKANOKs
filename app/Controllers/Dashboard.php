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

        if (!$session->get('isLoggedIn')) {
        return redirect()->to('/login');
        }

        $branchId = $session->get('branch_id');

        $inventoryModel = new InventoryModel();

        $inventoryValue = $inventoryModel->getInventoryValue($branchId);
        $stockWarning = $inventoryModel->getLowStockAlerts($branchId);
        
        $data = [
            'inventoryValue' => $inventoryValue,
            'stockWarning' => $stockWarning
        ];
            if($session->get('role') == 'Branch Manager') {
                return view('pages/dashboard', $data);
            }
            elseif($session->get('role') == 'Inventory Staff'){
                return view("pages/inventory_overview", $data);
            }
    }
}
