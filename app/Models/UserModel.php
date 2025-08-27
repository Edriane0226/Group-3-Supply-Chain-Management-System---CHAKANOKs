<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    
    protected $allowedFields = ['id', 'name', 'email', 'role', 'branch'];

    protected $returnType    = 'array';
}
