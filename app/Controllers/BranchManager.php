<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class BranchManager extends Controller
{
    public function __construct()
    {
        helper(['form', 'url']);
    }

    // ✅ Dashboard for Branch Manager
    public function dashboard()
    {
        // Make sure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        // Allow only Branch Manager role
        if (session()->get('role') !== 'Branch Manager') {
            session()->setFlashdata('error', 'Unauthorized: Branch Manager only');
            return redirect()->to('login');
        }

        // Pass session data to the view
        $data = [
            'title'       => 'Branch Manager Dashboard',
            'first_name'  => session()->get('first_Name'),
            'last_name'   => session()->get('last_Name'),
            'branch_id'   => session()->get('branch_id'),
        ];

        return view('branchmanager/dashboard', $data);
    }

    // ✅ Example: View branch reports
    public function reports()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        if (session()->get('role') !== 'Branch Manager') {
            session()->setFlashdata('error', 'Unauthorized: Branch Manager only');
            return redirect()->to('login');
        }

        return view('branchmanager/reports');
    }

    // ✅ Example: Manage staff under this branch
    public function manageStaff()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        if (session()->get('role') !== 'Branch Manager') {
            session()->setFlashdata('error', 'Unauthorized: Branch Manager only');
            return redirect()->to('login');
        }

        return view('branchmanager/manage_staff');
    }
}
