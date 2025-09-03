<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{   
    // login form loader ni gi comment nlng nako tung sa home
    public function login()
    {
        helper(['form']);
        return view('auth/login');
    }
    
    public function attemptLogin()
    {
        helper(['form']);
        $session = session();
        $userModel = new UserModel();

        $rules = [
            'id' => 'required|integer', // ID ra akong gi req
        ];

        if (!$this->validate($rules)) {
            return view('auth/login', ['validation' => $this->validator]);
        }

        $user = $userModel->where('id', $this->request->getVar('id'))->first();

        if ($user) {
            $session->set([
                'id'        => $user['id'],
                'first_Name'      => $user['first_Name'],
                'last_Name'      => $user['last_Name'],
                'Middle_Name'      => $user['middle_Name'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'branch_id'    => $user['branch_id'],
                'isLoggedIn'=> true
            ]);
            $session->setFlashdata('success', 'Welcome ' . $user['first_Name']);
            return redirect()->to('dashboard'); // blud will go to dashboard()
        }

        $session->setFlashdata('error', 'Invalid ID');
        return redirect()->back();
    }
    // session DESTROYER
    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }
        return view('pages/dashboard'); // load dashboard
    }
}
