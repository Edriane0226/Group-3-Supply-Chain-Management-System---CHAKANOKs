<?php

namespace App\Controllers;

use App\Models\PurchaseRequestModel;
use App\Models\SupplierModel;
use App\Models\BranchModel;
use CodeIgniter\Controller;

class PurchaseRequest extends Controller
{
    public function index()
    {
        $model = new PurchaseRequestModel();
        $data['requests'] = $model->withRelations()->findAll();

        return view('purchase_requests/index', $data);
    }

    public function create()
    {
        $supplierModel = new SupplierModel();
        $branchModel   = new BranchModel();

        $data['suppliers'] = $supplierModel->findAll();
        $data['branches']  = $branchModel->findAll();

        return view('purchase_requests/create', $data);
    }

    public function store()
    {
        $model = new PurchaseRequestModel();

        $data = [
            'branch_id'   => $this->request->getPost('branch_id'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'request_date'=> date('Y-m-d H:i:s'),
            'status'      => 'pending'
        ];

        if ($model->insert($data)) {
            return redirect()->to('/purchase-requests')->with('success', 'Purchase Request created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create Purchase Request');
    }

    public function edit($id)
    {
        $model         = new PurchaseRequestModel();
        $supplierModel = new SupplierModel();
        $branchModel   = new BranchModel();

        $data['request']   = $model->find($id);
        $data['suppliers'] = $supplierModel->findAll();
        $data['branches']  = $branchModel->findAll();

        return view('purchase_requests/edit', $data);
    }

    public function update($id)
    {
        $model = new PurchaseRequestModel();

        $data = [
            'branch_id'   => $this->request->getPost('branch_id'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'status'      => $this->request->getPost('status'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to('/purchase-requests')->with('success', 'Purchase Request updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update Purchase Request');
    }

    public function delete($id)
    {
        $model = new PurchaseRequestModel();
        $model->delete($id);

        return redirect()->to('/purchase-requests')->with('success', 'Purchase Request deleted successfully');
    }
}
