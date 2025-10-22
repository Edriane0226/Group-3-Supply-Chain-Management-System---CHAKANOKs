<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    
    protected $allowedFields = ['id', 'first_Name', 'last_Name', 'middle_Name', 'email', 'password', 'role_id', 'branch_id', 'created_at', 'updated_at'];

    protected $returnType    = 'array';
}
