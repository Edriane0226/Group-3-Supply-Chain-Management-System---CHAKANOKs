<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    
    protected $allowedFields = ['id', 'first_Name', 'last_Name', 'middle_Name', 'email', 'role', 'branch_id'];

    protected $returnType    = 'array';
}
