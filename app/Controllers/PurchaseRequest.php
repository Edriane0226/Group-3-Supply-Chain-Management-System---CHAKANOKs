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
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        if (!in_array($session->get('role'), ['Branch Manager', 'Central Office Admin', 'Inventory Staff'])) {
            $session->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(site_url('login'));
        }

        $purchModel = new PurchaseRequestModel();
        $branchModel = new BranchModel();

        if ($session->get('role') === 'Central Office Admin') {
            $data = [
                'role' => $session->get('role'),
                'title' => 'Purchase Request',
                'branches' => $branchModel->findAll(),
                'requests' => $purchModel->findAllWithRelations()
            ];
        } elseif ($session->get('role') === 'Inventory Staff') {
            $data = [
                'role' => $session->get('role'),
                'title' => 'Purchase Request',
                'requests' => $purchModel->findAllWithRelations()
            ];
        } else { // Branch Manager
            $data = [
                'role' => $session->get('role'),
                'title' => 'Purchase Request',
                'requests' => $purchModel->findByBranchWithRelations((int) $session->get('branch_id'))
            ];
        }

        return view('reusables/sidenav', $data) . view('pages/purchase_request', $data);
    }

    public function create()
    {
        $supplierModel = new SupplierModel();
        $branchModel   = new BranchModel();

        $data['suppliers'] = $supplierModel->getActiveSuppliers();
        $data['branches']  = $branchModel->findAll();

        return view('purchase_requests/create', $data);
    }

    public function store()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $model = new PurchaseRequestModel();

        $rules = [
            'item_name'   => 'required|string|min_length[2]',
            'quantity'    => 'required|integer|greater_than_equal_to[1]',
            'supplier_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please correct the form errors.');
        }

        $data = [
            'branch_id'    => (int) ($session->get('branch_id') ?: $this->request->getPost('branch_id')),
            'supplier_id'  => (int) $this->request->getPost('supplier_id'),
            'item_name'    => $this->request->getPost('item_name'),
            'quantity'     => (int) $this->request->getPost('quantity'),
            'unit'         => $this->request->getPost('unit') ?? 'pcs',
            'description'  => $this->request->getPost('description'),
            'request_date' => date('Y-m-d H:i:s'),
            'status'       => 'pending',
        ];

        if ($model->insert($data)) {
            return redirect()->to(site_url('purchase-requests'))
                             ->with('success', 'Purchase request submitted successfully.');
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
            'item_name'   => $this->request->getPost('item_name'),
            'quantity'    => $this->request->getPost('quantity'),
            'unit'        => $this->request->getPost('unit'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(site_url('purchase-requests'))->with('success', 'Purchase Request updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update Purchase Request');
    }

    public function delete($id)
    {
        $model = new PurchaseRequestModel();
        $model->delete($id);

        return redirect()->to(site_url('purchase-requests'))->with('success', 'Purchase Request deleted successfully');
    }

    public function approve($id)
    {
        $session = session();
        if ($session->get('role') !== 'Central Office Admin') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $model = new PurchaseRequestModel();
        $model->update($id, ['status' => 'approved']);

        return redirect()->back()->with('success', 'Request approved');
    }

    public function cancel($id)
    {
        $session = session();
        if ($session->get('role') !== 'Central Office Admin') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $remarks = $this->request->getPost('remarks');
        $model = new PurchaseRequestModel();
        $model->update($id, ['status' => 'cancelled', 'remarks' => $remarks]);

        return redirect()->back()->with('success', 'Request cancelled');
    }
}
