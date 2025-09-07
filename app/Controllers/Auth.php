<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

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

        $rules = ['id' => 'required|integer'];

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
            $session->set([
                'id'          => $user['id'],
                'first_Name'  => $user['first_Name'],
                'last_Name'   => $user['last_Name'],
                'Middle_Name' => $user['middle_Name'],
                'email'       => $user['email'],
                'role'        => $user['role'],
                'branch_id'   => $user['branch_id'],
                'isLoggedIn'  => true
            ]);
            $session->setFlashdata('success', 'Welcome ' . $user['first_Name']);
            if ($user['role'] === 'Central Office Admin') {
                return redirect()->to('central');
            } elseif ($user['role'] === 'Branch Manager') {
                return redirect()->to('dashboard');
            } else {
                return redirect()->to('dashboard');
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

        // ✅ Allow both Inventory Staff and Branch Manager
        if (session()->get('role') === 'Inventory Staff') {
            return view('pages/dashboard'); // Inventory staff dashboard
        }

        if (session()->get('role') === 'Branch Manager') {
            return view('pages/dashboard'); // Branch manager dashboard
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

        // Allows Inv Staff to Access
        if (session()->get('role') === 'Inventory Staff') {
            return view('pages/InventoryBranch'); 
        }
        // Allows Branch Manager to Access
        if (session()->get('role') === 'Branch Manager') {
            return view('pages/InventoryBranch'); 
        }

        // Redirect to Login if Unauthrized User
        session()->setFlashdata('error', 'Unauthorized access');
        return redirect()->to('login');
    }
}
