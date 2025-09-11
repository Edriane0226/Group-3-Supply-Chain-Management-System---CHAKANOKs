<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use App\Models\PurchaseRequestModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $inventoryModel = new InventoryModel();
        $purchaseRequestModel = new PurchaseRequestModel();

        // Hardcode branch_id = 2 for your case
        $branchId = 2;

        // Get total inventory value for branch 2
        $inventoryValue = $inventoryModel->getInventoryValue($branchId);


        $data = [
            'inventoryValue' => $inventoryValue,
            // 'inventoryLevels' removed
        ];

        return view('pages/dashboard', $data);
    }
}
