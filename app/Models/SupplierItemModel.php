<?php

namespace App\Models;
use CodeIgniter\Model;

class SupplierItemModel extends Model
{
    protected $table            = 'supplier_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'supplier_id',
        'stock_type_id',
        'item_name',
        'unit_price',
        'price_type',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}