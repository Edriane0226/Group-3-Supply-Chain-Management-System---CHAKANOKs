<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run()
    {
        $this->call('BranchesSeeder');
        $this->call('UserSeeder');
        $this->call('StockSeeder');
    }
}
