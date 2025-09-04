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
            return redirect()->to('dashboard');
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
            return view('manage/dashboard'); // Branch manager dashboard
        }

        // Fallback for unauthorized roles
        session()->setFlashdata('error', 'Unauthorized access');
        return redirect()->to('login');
    }
}
