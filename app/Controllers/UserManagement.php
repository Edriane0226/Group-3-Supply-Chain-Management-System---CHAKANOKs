<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BranchModel;
use App\Models\RoleModel;
use CodeIgniter\Controller;

class UserManagement extends Controller
{
    public function index()
    {   
        // Check Sesssion kung dli Central Office Admin ang role auto back to login
        if (session()->get('role') !== 'Central Office Admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        $userModel = new UserModel();
        $branchModel = new BranchModel();

        //kwaon niya ang branch id sa current na naka login
        $branchId = $this->request->getGet('branch');
        // GET niya all users and ilang branch name
        $data['users'] = $userModel
            ->select('users.*, branches.branch_name, roles.role_name')
            ->join('branches', 'branches.id = users.branch_id', 'left')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.branch_id', $branchId)
            ->findAll();

        // GET all data kung unsa naa sa branch model
        $data['branches'] = $branchModel->findAll();

        return view('pages/usersManage', $data);
    }

    public function create()
    {
        $branchModel = new BranchModel();
        $roleModel = new RoleModel();
        // GET niya Current na naa sa database
        $data['branches'] = $branchModel->findAll();
        $data['roles'] = $roleModel->findAll();
        return view('users/create', $data);
    }

    public function store()
    {
        $userModel = new UserModel();
        // gikuha niya ang data galing sa form gisulod sa array
        $data = [
            'first_Name'  => $this->request->getPost('first_name'),
            'last_Name'   => $this->request->getPost('last_name'),
            'middle_Name' => $this->request->getPost('middle_name'),
            'email'       => $this->request->getPost('email'),
            'role_id'        => $this->request->getPost('role_id'),
            'branch_id'   => $this->request->getPost('branch_id') ?: null,
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'created_at'  => date('Y-m-d H:i:s')
        ];
        //tapos gi insert sa Users table gamit ang user table
        $userModel->insert($data);
        return redirect()->to('create')->with('success', 'User created successfully');
    }

    public function edit($id)
    {   
        //bale nag create ni siya ug instance sa models
        $userModel = new UserModel();
        $branchModel = new BranchModel();
        $roleModel = new RoleModel();

        //gigamit dayun para mag kuha ug data pasulod sa array
        $data['user'] = $userModel->find($id);
        $data['branches'] = $branchModel->findAll();
        $data['roles'] = $roleModel->findAll();

        return view('users/edit', $data);
    }

    public function update($id)
    {
        $userModel = new UserModel();
        $data = [
            'first_Name'  => $this->request->getPost('first_name'),
            'last_Name'   => $this->request->getPost('last_name'),
            'middle_Name' => $this->request->getPost('middle_name'),
            'email'       => $this->request->getPost('email'),
            'role_id'     => $this->request->getPost('role_id'),
            'branch_id'   => $this->request->getPost('branch_id') ?: null,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);
        return redirect()->to('users')->with('success', 'User updated successfully');
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id);

        return redirect()->to('users')->with('success', 'User deleted successfully');
    }
}
