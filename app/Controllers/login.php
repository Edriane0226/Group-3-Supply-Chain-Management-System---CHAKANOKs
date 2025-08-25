<?php

namespace App\Controllers;

use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function auth()
    {
        $session = session();
        $model = new UserModel();

        $email = $this->request->getVar('email');   // <-- palit sa email
        $password = $this->request->getVar('password');

        $user = $model->where('email', $email)->first(); // <-- palit sa email

        if ($user && password_verify($password, $user['password'])) {
            $session->set([
                'id'       => $user['id'],
                'name'     => $user['name'],   // gamit name imbes username
                'email'    => $user['email'],
                'role'     => $user['role'],
                'isLoggedIn' => true,
            ]);

            if ($user['role'] == 'admin') {
                return redirect()->to('/admin/dashboard');
            } else {
                return redirect()->to('/user/dashboard');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid Login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
