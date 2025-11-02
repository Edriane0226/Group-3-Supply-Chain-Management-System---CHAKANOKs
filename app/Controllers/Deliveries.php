<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Deliveries extends Controller
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        if ($session->get('role') !== 'Branch Manager') {
            $session->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(site_url('login'));
        }

        $data = [
            'role' => $session->get('role'),
            'title' => 'Deliveries',
        ];

        return view('reusables/sidenav', $data) . view('pages/deliveries', $data);
    }
}
