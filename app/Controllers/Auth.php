<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BranchModel;
use CodeIgniter\Controller;

class Auth extends Controller
{

    // Show Login Form
    public function login()
    {
        helper(['form']);
        return view('auth/login');
    }

    
    // Process Login Attempt
    public function attemptLogin()
    {
        helper(['form']);
        $session     = session();
        $userModel   = new UserModel();
        $branchModel = new BranchModel();

        $rules = [
            'id'       => 'required|integer',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return view('auth/login', ['validation' => $this->validator]);
        }

        // Clear any previous session
        $session->remove([
            'user_id','first_Name','last_Name','middle_Name',
            'email','role','branch_id','branch_name','full_name','isLoggedIn'
        ]);

        $id       = $this->request->getVar('id');
        $password = $this->request->getVar('password');

        // Check if supplier login (id >= 1001)
        if ($id >= 1001 && $id <= 1008) {
            $supplierModel = new \App\Models\SupplierModel();
            $supplier = $supplierModel->where('id', $id)->first();

            if (!$supplier) {
                $session->setFlashdata('error', 'Invalid Supplier ID or password');
                return redirect()->back();
            }

            // Verify password
            if (!password_verify($password, $supplier['password'])) {
                $session->setFlashdata('error', 'Invalid Supplier ID or password');
                return redirect()->back();
            }

            // Set session for supplier
            $session->set([
                'user_id'     => $supplier['id'],
                'supplier_name' => $supplier['supplier_name'],
                'role'        => 'Supplier',
                'role_id'     => 6, // Assuming Supplier role id is 6
                'isLoggedIn'  => true
            ]);

            $session->setFlashdata('success', 'Welcome ' . $supplier['supplier_name'] . '!');

            // Redirect to supplier dashboard
            return redirect()->to(site_url('supplier/dashboard'));
        }

        // Regular user login
        // Get user with role info
        $user = $userModel->select('users.*, roles.role_name')
                         ->join('roles', 'roles.id = users.role_id')
                         ->where('users.id', $id)
                         ->first();

        if (!$user) {
            $session->setFlashdata('error', 'Invalid ID or password');
            return redirect()->back();
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $session->setFlashdata('error', 'Invalid ID or password');
            return redirect()->back();
        }

        // Get branch info safely
        $branchId   = $user['branch_id'] ?? null;
        $branchData = $branchId ? $branchModel->find($branchId) : null;

        // Prepare full name
        $fullName = trim($user['first_Name'] . ' ' . $user['last_Name']);

        // Set session data
        $session->set([
            'user_id'     => $user['id'],
            'first_Name'  => $user['first_Name'],
            'last_Name'   => $user['last_Name'],
            'middle_Name' => $user['middle_Name'],
            'email'       => $user['email'],
            'role'        => $user['role_name'],
            'role_id'     => $user['role_id'],
            'branch_id'   => $branchId,
            'branch_name' => $branchData['branch_name'] ?? 'No Assigned Branch',
            'full_name'   => $fullName,
            'isLoggedIn'  => true
        ]);

        $session->setFlashdata('success', 'Welcome ' . $user['first_Name'] . '!');

        // Redirect by role
        switch ($user['role_name']) {
            case 'Central Office Admin':
                return redirect()->to(site_url('dashboard'));
            case 'Branch Manager':
                return redirect()->to(site_url('dashboard'));
            case 'Inventory Staff':
                return redirect()->to(site_url('inventory/overview'));
            case 'Logistics Coordinator':
                return redirect()->to(site_url('logistics-coordinator'));
            default:
                $session->setFlashdata('error', 'Unauthorized role.');
                return redirect()->to(site_url('login'));
        }
    }

    // // Redirect User by Role
    //     private function redirectByRole(string $role)
    //     {
    //         switch ($role) {
    //             case 'Central Office Admin':
    //                 return redirect()->to(site_url('central'));
    //             case 'Branch Manager':
    //                 return redirect()->to(site_url('dashboard'));
    //             case 'Inventory Staff':
    //                 return redirect()->to(site_url('inventory/overview'));
    //             default:
    //                 session()->setFlashdata('error', 'Unauthorized role.');
    //                 return redirect()->to(site_url('login'));
    //         }
    //     }

    
    // Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))->with('success', 'You have been logged out.');
    }

    // Central Office Admin Dashboard
    public function centralDashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        if (session()->get('role') === 'Central Office Admin') {
            // Include sidenav for Central Office Admin
            return view('reusables/sidenav') . view('pages/central');
        }

        session()->setFlashdata('error', 'Unauthorized access.');
        return redirect()->to(site_url('login'));
    }

    // Inventory Access
    public function inventory()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $role = session()->get('role');

        if ($role === 'Inventory Staff') {
            return redirect()->to(site_url('inventory/overview'));
        }

        if ($role === 'Branch Manager') {
            return view('pages/InventoryBranch');
        }

        session()->setFlashdata('error', 'Unauthorized access.');
        return redirect()->to(site_url('login'));
    }
    
    // Branches Access
    public function branches()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        if (session()->get('role') === 'Central Office Admin') {
            return view('pages/branches');
        }

        session()->setFlashdata('error', 'Unauthorized access to branches.');
        return redirect()->to(site_url('login'));
    }
}
