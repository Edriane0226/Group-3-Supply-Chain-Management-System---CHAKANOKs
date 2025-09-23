<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BranchModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    // Show login form
    public function login()
    {
        helper(['form']);
        return view('auth/login');
    }

    // Process login attempt
    public function attemptLogin()
    {
        helper(['form']);
        $session     = session();
        $userModel   = new UserModel();
        $branchModel = new BranchModel();

        $rules = [
            'id'       => 'required|integer',
            'password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return view('auth/login', ['validation' => $this->validator]);
        }

        // Clear old session data
        $session->remove([
            'user_id','first_Name','last_Name','middle_Name',
            'email','role','branch_id','branch_name','full_name','isLoggedIn'
        ]);

        $id   = $this->request->getVar('id');
        $pass = $this->request->getVar('password');
        $user = $userModel->where('id', $id)->first();

        if ($user) {
            $branch = $branchModel->find($user['branch_id']);

            if (!password_verify($pass, $user['password'])) {
            $session->setFlashdata('error', 'Invalid ID or password');
            return redirect()->back();
            }

            // Build full name
            $fullName = trim($user['first_Name'] . ' ' . $user['last_Name']);

            // Store session
            $session->set([
                'user_id'     => $user['id'],
                'first_Name'  => $user['first_Name'],
                'last_Name'   => $user['last_Name'],
                'middle_Name' => $user['middle_Name'],
                'email'       => $user['email'],
                'role'        => $user['role'],
                'branch_id'   => $user['branch_id'],
                'branch_name' => $branch ? $branch['branch_name'] : 'Unknown Branch',
                'full_name'   => $fullName,
                'isLoggedIn'  => true
            ]);

            $session->setFlashdata('success', 'Welcome ' . $user['first_Name']);

            if ($user['role'] === 'Central Office Admin') {
                return redirect()->to('central');
            } elseif ($user['role'] === 'Branch Manager') {
                return redirect()->to('dashboard');
            } elseif ($user['role'] === 'Inventory Staff') {
                return redirect()->to('inventory/overview');
            } else {
                return redirect()->to('login');
            }
        }

        $session->setFlashdata('error', 'Invalid ID or password');
        return redirect()->back();
    }

    // Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

    // Central Office Admin Dashboard
    public function centralDashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        if (session()->get('role') === 'Central Office Admin') {
            return view('pages/central');
        }

        session()->setFlashdata('error', 'Unauthorized access');
        return redirect()->to('login');
    }

    // Inventory Access
    public function inventory()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        if (session()->get('role') === 'Inventory Staff') {
            return redirect()->to('inventory/overview');
        }

        if (session()->get('role') === 'Branch Manager') {
            return view('pages/InventoryBranch');
        }

        session()->setFlashdata('error', 'Unauthorized access');
        return redirect()->to('login');
    }
}
