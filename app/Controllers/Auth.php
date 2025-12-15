<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BranchModel;
use App\Models\RoleModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends Controller
{
    /**
     * Check if current user's role has been changed to have no permissions.
     * If so, log them out automatically.
     */
    protected function checkRolePermissions(): ?RedirectResponse
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return null;
        }

        // System Administrator always has access
        if ($session->get('role') === 'System Administrator') {
            return null;
        }

        $roleModel = new RoleModel();
        $roleId = (int) ($session->get('role_id') ?? 0);
        
        if ($roleId > 0) {
            $role = $roleModel->find($roleId);
            
            if ($role) {
                // Check if role has permissions
                $permissions = [];
                if (!empty($role['permissions'])) {
                    $decoded = json_decode($role['permissions'], true);
                    if (is_array($decoded)) {
                        $permissions = $decoded;
                    }
                }
                
                // If role has no permissions, log the user out
                if (empty($permissions)) {
                    $session->destroy();
                    return redirect()->to(site_url('login'))->with('error', 'You don\'t have permission to access this system.');
                }
            }
        }

        return null;
    }

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
        $roleModel   = new RoleModel();

        $rules = [
            'id'       => 'required|integer',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return view('auth/login', ['validation' => $this->validator]);
        }

        // Clear any previous session
        $session->remove([
            'user_id','first_Name','last_Name','middle_Name',
            'email','role','role_id','branch_id','branch_name','full_name','isLoggedIn','permissions'
        ]);

        $id       = $this->request->getVar('id');
        $password = $this->request->getVar('password');

        // Check if supplier login - first check if ID exists in suppliers table
        $supplierModel = new \App\Models\SupplierModel();
        $supplier = $supplierModel->where('id', $id)->first();

        if ($supplier) {
            // Verify password
            if (!password_verify($password, $supplier['password'])) {
                $session->setFlashdata('error', 'Invalid Supplier ID or password');
                return redirect()->back();
            }

            // Set session for supplier
            $session->set([
                'user_id'        => $supplier['id'],
                'supplier_name'  => $supplier['supplier_name'],
                'email'          => $supplier['email'] ?? null,
                'role'           => 'Supplier',
                'role_id'        => $supplier['role_id'] ?? 6,
                'permissions'    => [],
                'isLoggedIn'     => true,
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

        // Resolve role permissions
        $rolePermissions = [];
        if (!empty($user['role_id'])) {
            $roleRecord = $roleModel->find($user['role_id']);
            if ($roleRecord && !empty($roleRecord['permissions'])) {
                $decoded = json_decode($roleRecord['permissions'], true);
                if (is_array($decoded)) {
                    $rolePermissions = $decoded;
                }
            }
        }

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
            'permissions' => $rolePermissions,
            'isLoggedIn'  => true,
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
            case 'Franchise Manager':
                return redirect()->to(site_url('franchise'));
            case 'System Administrator':
                return redirect()->to(site_url('admin'));
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

        // Check if user's role has been changed to have no permissions
        $roleCheck = $this->checkRolePermissions();
        if ($roleCheck !== null) {
            return $roleCheck;
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

        // Check if user's role has been changed to have no permissions
        $roleCheck = $this->checkRolePermissions();
        if ($roleCheck !== null) {
            return $roleCheck;
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

        // Check if user's role has been changed to have no permissions
        $roleCheck = $this->checkRolePermissions();
        if ($roleCheck !== null) {
            return $roleCheck;
        }

        if (session()->get('role') === 'Central Office Admin') {
            return view('pages/branches');
        }

        session()->setFlashdata('error', 'Unauthorized access to branches.');
        return redirect()->to(site_url('login'));
    }
}
