<?php

namespace App\Controllers;

class BranchManager extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url']);
    }

    // ✅ Dashboard for Branch Manager
    public function dashboard()
    {
        if ($redirect = $this->authorize('branch_manager.dashboard')) {
            return $redirect;
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
        if ($redirect = $this->authorize('branch_manager.reports')) {
            return $redirect;
        }

        return view('branchmanager/reports');
    }

    // ✅ Example: Manage staff under this branch
    public function manageStaff()
    {
        if ($redirect = $this->authorize('branch_manager.manage_staff')) {
            return $redirect;
        }

        return view('branchmanager/manage_staff');
    }
}
