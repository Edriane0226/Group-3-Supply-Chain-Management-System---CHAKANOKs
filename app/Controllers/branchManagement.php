<?php

namespace App\Controllers;

use App\Models\BranchModel;
use CodeIgniter\Controller;

class BranchManagement extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    /**
     * Check if user is logged in and has correct role
     */
    private function authorize()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Please login first.');
        }

        if ($this->session->get('role') !== 'Central Office Admin') {
            return redirect()->to(site_url('login'))
                ->with('error', 'Unauthorized access to Branch Management.');
        }

        return null; // Authorized
    }

    /**
     * Show list of branches
     */
    public function index()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $branchModel = new BranchModel();
        $data['branches'] = $branchModel->findAll();

        return view('pages/branchManagement', $data);
    }

    /**
     * Show form to create branch
     */
    public function create()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        return view('branchManager/createBranch');
    }

    /**
     * Save new branch to database
     */
    public function store()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $branchModel = new BranchModel();

        $data = [
            'branch_name'  => $this->request->getPost('branch_name'),
            'location'     => $this->request->getPost('location'),
            'contact_info' => $this->request->getPost('contact_info'),
            'status'       => $this->request->getPost('status'),
        ];

        $branchModel->save($data);

        return redirect()->to(site_url('branches'))
            ->with('success', 'Branch added successfully.');
    }

    /**
     * Show form to edit a branch
     */
    public function edit($id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $branchModel = new BranchModel();
        $data['branch'] = $branchModel->find($id);

        if (!$data['branch']) {
            return redirect()->to(site_url('branches'))
                ->with('error', 'Branch not found.');
        }

        return view('branchManager/editBranch', $data);
    }

    /**
     * Update branch record
     */
    public function update($id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $branchModel = new BranchModel();

        $data = [
            'branch_name'  => $this->request->getPost('branch_name'),
            'location'     => $this->request->getPost('location'),
            'contact_info' => $this->request->getPost('contact_info'),
        ];

        $branchModel->update($id, $data);

        return redirect()->to(site_url('branches'))
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Delete a branch
     */
    public function delete($id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $branchModel = new BranchModel();
        $branchModel->delete($id);

        return redirect()->to(site_url('branches'))
            ->with('success', 'Branch deleted successfully.');
    }
}
