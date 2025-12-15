<?php

namespace App\Controllers;

use App\Models\PurchaseRequestModel;
use App\Models\PurchaseOrderModel;
use App\Models\SupplierItemModel;
use App\Models\SupplierModel;
use App\Models\BranchModel;
class PurchaseRequest extends BaseController
{
    public function index()
    {
        if ($redirect = $this->authorize('purchase_requests.view')) {
            return $redirect;
        }

        $session = $this->session;

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
        if ($redirect = $this->authorize('purchase_requests.create')) {
            return $redirect;
        }

        $supplierModel = new SupplierModel();
        $branchModel   = new BranchModel();
        $supplierItemModel = new SupplierItemModel();

        $data = [
            'suppliers' => $supplierModel->getActiveSuppliers(),
            'supplier_items' => $supplierItemModel->findAll(),
            'role' => $this->session->get('role'),
            'title' => 'New Purchase Request',
        ];

        return view('reusables/sidenav', ['role' => $this->session->get('role')])
            . view('purchase_requests/create', $data);
    }

    public function store()
    {
        if ($redirect = $this->authorize('purchase_requests.create')) {
            return $redirect;
        }

        $model = new PurchaseRequestModel();

        $itemNames   = $this->request->getPost('item_name');
        $quantities  = $this->request->getPost('quantity');
        $suppliers   = $this->request->getPost('supplier_id');
        $units       = $this->request->getPost('unit');
        $prices      = $this->request->getPost('price');
        $descriptions = $this->request->getPost('description');

        if (!is_array($itemNames) || count($itemNames) === 0) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one item.');
        }

        $records = [];
        $branchId = (int) ($this->session->get('branch_id') ?: $this->request->getPost('branch_id'));

        $totalItems = count($itemNames);
        for ($i = 0; $i < $totalItems; $i++) {
            if (empty($itemNames[$i]) || empty($quantities[$i]) || empty($suppliers[$i])) {
                continue;
            }

            $records[] = [
                'branch_id'    => $branchId,
                'supplier_id'  => (int) $suppliers[$i],
                'item_name'    => $itemNames[$i],
                'quantity'     => (int) $quantities[$i],
                'unit'         => $units[$i] ?? 'pcs',
                'price'        => $quantities[$i] * $prices[$i],
                'description'  => $descriptions[$i] ?? null,
                'request_date' => date('Y-m-d H:i:s'),
                'status'       => 'pending',
            ];
        }

        if (empty($records)) {
            return redirect()->back()->withInput()->with('error', 'No valid items to submit.');
        }

        if ($model->insertBatch($records)) {
            return redirect()->to(site_url('purchase-requests'))
                             ->with('success', 'Purchase request submitted successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create Purchase Request');
    }


    public function edit($id)
    {
        if ($redirect = $this->authorize('purchase_requests.update')) {
            return $redirect;
        }

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
        if ($redirect = $this->authorize('purchase_requests.update')) {
            return $redirect;
        }

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
        if ($redirect = $this->authorize('purchase_requests.delete')) {
            return $redirect;
        }

        $model = new PurchaseRequestModel();
        $model->delete($id);

        return redirect()->to(site_url('purchase-requests'))->with('success', 'Purchase Request deleted successfully');
    }

    public function approve($id)
    {
        if ($redirect = $this->authorize('purchase_requests.approve')) {
            return $redirect;
        }

        $requestModel = new \App\Models\PurchaseRequestModel();
        $purchaseOrderModel = new \App\Models\PurchaseOrderModel();

        $approvedBy = (int) $this->session->get('user_id');

        try {
            // 1️⃣ Find the request first
            $request = $requestModel->find($id);

            if (!$request) {
                return redirect()->back()->with('error', 'Purchase request not found.');
            }

            // 2️⃣ Prevent double approval
            if ($request['status'] === 'approved') {
                return redirect()->back()->with('info', 'This request has already been approved.');
            }

            // 3️⃣ Update the request to approved
            $requestModel->update($id, [
                'status'       => 'approved',
                'approved_by'  => $approvedBy,
                'approved_at'  => date('Y-m-d H:i:s'),
            ]);

            // 4️⃣ Create purchase order from this request
            $poId = $purchaseOrderModel->createFromPurchaseRequest($id, $approvedBy, $request['price']);

            if (!$poId) {
                // rollback request status if PO creation failed
                $requestModel->update($id, ['status' => 'pending']);
                return redirect()->back()->with('error', 'Failed to create purchase order. Request reverted to pending.');
            }

            return redirect()->back()
                            ->with('success', "Request approved successfully. Purchase Order #{$poId} created.");
        } catch (Exception $e) {
            log_message('error', 'Error approving purchase request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred while approving the request.');
        }
    }

    public function cancel($id)
    {
        if ($redirect = $this->authorize('purchase_requests.cancel')) {
            return $redirect;
        }

        $remarks = $this->request->getPost('remarks');
        $model = new PurchaseRequestModel();

        try {
            $model->update($id, ['status' => 'cancelled', 'remarks' => $remarks]);
            return redirect()->back()->with('success', 'Request cancelled');
        } catch (\Exception $e) {
            log_message('error', 'Error cancelling purchase request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while cancelling the request');
        }
    }

    public function reject($id)
    {
        if ($redirect = $this->authorize('purchase_requests.reject')) {
            return $redirect;
        }

        $model = new PurchaseRequestModel();

        try {
            $success = $model->rejectRequest($id);
            if ($success) {
                return redirect()->back()->with('success', 'Purchase request rejected successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to reject request.');
            }
        } catch (Exception $e) {
            log_message('error', 'Error rejecting purchase request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An unexpected error occurred.');
        }
    }
}