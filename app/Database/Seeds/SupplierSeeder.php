<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'supplier_name' => 'San Miguel Foods Inc.',
                'contact_info'  => 'orders@smfoods.com | +63 917 555 1001',
                'address'       => 'Ortigas Center, Pasig City',
                'terms'         => 'Net 30',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'supplier_name' => 'Bounty Fresh Chicken Supply',
                'contact_info'  => 'sales@bountyfresh.com | +63 918 333 2002',
                'address'       => 'San Fernando, Pampanga',
                'terms'         => 'Net 15',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'supplier_name' => 'NutriAsia Condiments Distributor',
                'contact_info'  => 'support@nutriasia.com | +63 917 222 3456',
                'address'       => 'Taguig City, Metro Manila',
                'terms'         => 'Net 30',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'supplier_name' => 'Mega Packaging Solutions',
                'contact_info'  => 'info@megapack.com | +63 925 667 4589',
                'address'       => 'Mandaue City, Cebu',
                'terms'         => 'COD',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'supplier_name' => 'PureOil Philippines',
                'contact_info'  => 'orders@pureoil.ph | +63 927 452 6879',
                'address'       => 'Calamba, Laguna',
                'terms'         => 'Net 15',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'supplier_name' => 'FastServe Kitchen Equipment Corp.',
                'contact_info'  => 'sales@fastserve.com | +63 928 122 9988',
                'address'       => 'Quezon City, Metro Manila',
                'terms'         => 'Net 45',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'supplier_name' => 'CleanPro Janitorial Supplies',
                'contact_info'  => 'sales@cleanpro.com.ph | +63 926 778 1203',
                'address'       => 'Iloilo City',
                'terms'         => 'COD',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'supplier_name' => 'FreshVeg Produce Supplier',
                'contact_info'  => 'freshveg@produce.ph | +63 915 444 7890',
                'address'       => 'Baguio City',
                'terms'         => 'Net 7',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
        ];

        $this->db->table('suppliers')->insertBatch($data);
    }
}
