<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Models\BranchModel;

class Auth extends Controller
{   
    // login form
    public function login()
    {
        helper(['form']);
        return view('auth/login');
    }
    
    public function attemptLogin()
    {
        helper(['form']);
        $session   = session();
        $userModel = new UserModel();
        $branchModel = new BranchModel();

        $rules = ['id' => 'required|integer', 'password' => 'required|min_length[8]'];

        if (!$this->validate($rules)) {
            return view('auth/login', ['validation' => $this->validator]);
        }

        // Remove old session if exists
        $session->remove([
            'id','first_Name','last_Name','Middle_Name',
            'email','role','branch_id','isLoggedIn'
        ]);

        $id   = $this->request->getVar('id');
        $user = $userModel->where('id', $id)->first();

        if ($user) {

            $branch = $branchModel->find($user['branch_id']);

            $session->set([
                'id'          => $user['id'],
                'first_Name'  => $user['first_Name'],
                'last_Name'   => $user['last_Name'],
                'Middle_Name' => $user['middle_Name'],
                'email'       => $user['email'],
                'role'        => $user['role'],
                'branch_id'   => $user['branch_id'],
                'branch_name' => $branch ? $branch['branch_name'] : 'Unknown Branch',
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

        $session->setFlashdata('error', 'Invalid ID');
        return redirect()->back();
    }

    // logout / destroy session
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        // Only Branch Manager should access this dashboard
        if (session()->get('role') === 'Branch Manager') {
            return view('pages/dashboard'); // Branch manager dashboard
        }

        // Inventory Staff should go to Inventory dashboard
        if (session()->get('role') === 'Inventory Staff') {
            return redirect()->to('inventory');
        }

        // Fallback for unauthorized roles
        session()->setFlashdata('error', 'Unauthorized access');
        return redirect()->to('login');
    }

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

    public function inventory()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        // Inventory Staff go to their Overview; Branch Manager sees combined inventory dashboard
        if (session()->get('role') === 'Inventory Staff') {
            return redirect()->to('inventory/overview');
        }
        if (session()->get('role') === 'Branch Manager') {
            return view('pages/InventoryBranch'); 
        }

        // Redirect to Login if Unauthrized User
        session()->setFlashdata('error', 'Unauthorized access');
        return redirect()->to('login');
    }
}
