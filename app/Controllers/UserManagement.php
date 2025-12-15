<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BranchModel;
use App\Models\RoleModel;

class UserManagement extends BaseController
{
    public function index()
    {   
        if ($redirect = $this->authorize('users.view')) {
            return $redirect;
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
        if ($redirect = $this->authorize('users.create')) {
            return $redirect;
        }

        helper(['form']);

        $branchModel = new BranchModel();
        $roleModel = new RoleModel();
        // GET niya Current na naa sa database
        $data['branches'] = $branchModel->findAll();
        $data['roles'] = $roleModel->findAll();
        return view('users/create', $data);
    }

    public function store()
    {
        if ($redirect = $this->authorize('users.create')) {
            return $redirect;
        }

        helper(['form']);

        $rules = [
            'first_name' => 'required|' . self::ALPHANUMERIC_SPACE_RULE,
            'middle_name' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE,
            'last_name'  => 'required|' . self::ALPHANUMERIC_SPACE_RULE,
            'email'      => 'required|valid_email|is_unique[users.email]|regex_match[/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/]',
            'role_id'    => 'required|integer',
            'branch_id'  => 'permit_empty|integer',
            'password'   => 'required|min_length[6]'
        ];

        $messages = [
            'first_name' => [
                'required'    => 'First name is required.',
                'regex_match' => 'First name may only contain letters, numbers, and spaces.'
            ],
            'middle_name' => [
                'regex_match' => 'Middle name may only contain letters, numbers, and spaces.'
            ],
            'last_name' => [
                'required'    => 'Last name is required.',
                'regex_match' => 'Last name may only contain letters, numbers, and spaces.'
            ],
            'email' => [
                'required'    => 'Email address is required.',
                'valid_email' => 'Please enter a valid email address.',
                'is_unique'   => 'That email address is already registered.',
                'regex_match' => 'Email address contains invalid characters.'
            ],
            'role_id' => [
                'required' => 'Role selection is required.',
                'integer'  => 'Role selection is invalid.'
            ],
            'branch_id' => [
                'integer' => 'Branch selection is invalid.'
            ],
            'password' => [
                'required'  => 'Password is required.',
                'min_length' => 'Password must be at least 6 characters long.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Unable to save user. Please review the form for specific errors.');
        }

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

        if ($userModel->insert($data) === false) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $userModel->errors())
                ->with('error', 'Unable to save user due to a database error. Please try again.');
        }
        //tapos gi insert sa Users table gamit ang user table
        return redirect()->to('users')->with('success', 'User created successfully');
    }

    public function edit($id)
    {   
        if ($redirect = $this->authorize('users.update')) {
            return $redirect;
        }

        helper(['form']);

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
        if ($redirect = $this->authorize('users.update')) {
            return $redirect;
        }

        $rules = [
            'first_name' => 'required|' . self::ALPHANUMERIC_SPACE_RULE,
            'middle_name' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE,
            'last_name'  => 'required|' . self::ALPHANUMERIC_SPACE_RULE,
            'email'      => 'required|valid_email|is_unique[users.email,id,' . $id . ']|regex_match[/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/]',
            'role_id'    => 'required|integer',
            'branch_id'  => 'permit_empty|integer',
            'password'   => 'permit_empty|min_length[6]'
        ];

        $messages = [
            'first_name' => [
                'required'    => 'First name is required.',
                'regex_match' => 'First name may only contain letters, numbers, and spaces.'
            ],
            'middle_name' => [
                'regex_match' => 'Middle name may only contain letters, numbers, and spaces.'
            ],
            'last_name' => [
                'required'    => 'Last name is required.',
                'regex_match' => 'Last name may only contain letters, numbers, and spaces.'
            ],
            'email' => [
                'required'    => 'Email address is required.',
                'valid_email' => 'Please enter a valid email address.',
                'is_unique'   => 'That email address is already registered by another user.',
                'regex_match' => 'Email address contains invalid characters.'
            ],
            'role_id' => [
                'required' => 'Role selection is required.',
                'integer'  => 'Role selection is invalid.'
            ],
            'branch_id' => [
                'integer' => 'Branch selection is invalid.'
            ],
            'password' => [
                'min_length' => 'Password must be at least 6 characters long.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Unable to update user. Please review the form for specific errors.');
        }
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

        if ($userModel->update($id, $data) === false) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $userModel->errors())
                ->with('error', 'Unable to update user due to a database error. Please try again.');
        }

        return redirect()->to('users')->with('success', 'User updated successfully');
    }

    public function delete($id)
    {
        if ($redirect = $this->authorize('users.delete')) {
            return $redirect;
        }

        $userModel = new UserModel();
        $userModel->delete($id);

        return redirect()->to('users')->with('success', 'User deleted successfully');
    }
}