<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class BranchDashboard extends Controller
{
    public function index()
    {
        $session = session();

        //  Check if logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        //  Only Branch Managers can access
        if ($session->get('role') !== 'Branch Manager') {
            return redirect()->to(site_url('login'))->with('error', 'Unauthorized access.');
        }
        
        //  Load dashboard view
        return view('pages/branchdashboard', [
            'full_name'   => $session->get('full_name'),
            'branch_name' => $session->get('branch_name'),
            
        ]);
    }
}
