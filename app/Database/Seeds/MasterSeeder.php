<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run()
    {
        // Basic data
        $this->call('BranchesSeeder');
        $this->call('UserSeeder');
        $this->call('StockTypeSeeder');
        $this->call('StockSeeder');
        $this->call('SupplierSeeder');
        $this->call('SupplierItemSeeder');
        
        // Purchase flow
        $this->call('PurchaseRequestsSeeder');
        $this->call('PurchaseOrdersSeeder');
        $this->call('PurchaseOrderItemsSeeder');
        
        // Delivery flow
        $this->call('DeliveriesSeeder');
        $this->call('DeliveryItemsSeeder');
        $this->call('DeliverySchedulesSeeder');
        
        // Financial & Operations
        $this->call('AccountsPayableSeeder');
        $this->call('BranchTransfersSeeder');
        $this->call('SupplierContractsSeeder');
    }
}
