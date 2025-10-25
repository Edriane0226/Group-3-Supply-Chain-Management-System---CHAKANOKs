<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;


class Dashboard extends Controller
{
    public function index()
    {
        $session = session();

        //  Check if logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $branchName = $session->get('branch_name');
        $userModel = new UserModel();

        if ($session->get('role') == 'Branch Manager') {

            //Add dashboard data kung naa i dagdag

            // Get all user in the same branch sa branch manager na naka login
            $allUsers = $userModel->getUserByBranch($session->get('branch_id'));
            $data = [
                'branchName' => $branchName,
                'allUsers' => $allUsers,
            ];

            return view('reusables/sidenav', $data) . view('pages/dashboard');
        }

        else if ($session->get('role') == 'Central Office Admin') {

             //Add dashboard data kung naa i dagdag

            // Get all users kay Central Office
            $allUsers = $userModel->findAll();
            $data = [
                'branchName' => $branchName,
                'allUsers' => $allUsers,
            ];
            
            return view('reusables/sidenav', $data) . view('pages/dashboard');
        }

        else if ($session->get('role') == 'Inventory Staff') {
           
            $role = $session->get('role');
            $data = [
                'role' => $role,
            ];

            // Access Lng niya is Inventory Overview
            return view('reusables/sidenav', $data) . view('pages/inventory_overview');
        }


        // //  Only Branch Managers can access
        // if ($session->get('role') !== 'Branch Manager') {
        //     return redirect()->to(site_url('login'))->with('error', 'Unauthorized access.');
        // }
        
        // //  Load dashboard view
        // return view('pages/branchdashboard', [
        //     'full_name'   => $session->get('full_name'),
        //     'branch_name' => $session->get('branch_name'),
            
        // ]);
    }
}
