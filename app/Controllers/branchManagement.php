<?php

namespace App\Controllers;

use App\Models\BranchModel;
use CodeIgniter\Controller;

class BranchManagement extends Controller
{
    //Pang access ni siya sa branchManagement page at the same time gikuha niya ang branches store to $data['branches']
    public function index()
    {
        $branchModel = new BranchModel();
        $data['branches'] = $branchModel->findAll();

        return view('pages/branchManagement', $data);
    }
    //Paadto sa createBranch form
    public function create()
    {
        return view('branchManager/createBranch');
    }

    
    public function store()
    {
        $branchModel = new BranchModel();
        //Gikuha niya data from the createBranch form POST 
        $data = [
            'branch_name'  => $this->request->getPost('branch_name'),
            'location'     => $this->request->getPost('location'),
            'contact_info' => $this->request->getPost('contact_info'),
            'status' => $this->request->getPost('status'),
        ];
        //Save dayun 
        $branchModel->save($data);

        return redirect()->to(base_url('branches'))->with('success', 'Branch added successfully.');
    }

    //Show form the same time kuha branch id
    public function edit($id)
    {
        $branchModel = new BranchModel();
        $data['branch'] = $branchModel->find($id);

        if (!$data['branch']) {
            return redirect()->to(base_url('branches'))->with('error', 'Branch not found.');
        }

        return view('branchManager/editBranch', $data);
    }

    // Update dayun from edit form POST tapos update data
    public function update($id)
    {
        $branchModel = new BranchModel();

        $data = [
            'branch_name'  => $this->request->getPost('branch_name'),
            'location'     => $this->request->getPost('location'),
            'contact_info' => $this->request->getPost('contact_info'),
        ];

        $branchModel->update($id, $data);

        return redirect()->to(base_url('branches'))->with('success', 'Branch updated successfully.');
    }

    public function delete($id)
    {
        $branchModel = new BranchModel();
        $branchModel->delete($id);

        return redirect()->to(base_url('branches'))->with('success', 'Branch deleted successfully.');
    }
}
