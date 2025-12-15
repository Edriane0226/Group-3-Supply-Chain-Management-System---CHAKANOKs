<?php

namespace App\Controllers;

use App\Models\SupplierContractModel;
use App\Models\SupplierModel;
class SupplierContract extends BaseController
{
    protected SupplierContractModel $contractModel;
    protected SupplierModel $supplierModel;

    public function __construct()
    {
        $this->contractModel = new SupplierContractModel();
        $this->supplierModel = new SupplierModel();
        helper(['form', 'url']);
    }

    /**
     * List all supplier contracts
     */
    public function index()
    {
        if ($redirect = $this->authorize('supplier_contracts.view')) {
            return $redirect;
        }

        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $builder = $this->contractModel->select('supplier_contracts.*, suppliers.supplier_name, suppliers.contact_info')
                                       ->join('suppliers', 'suppliers.id = supplier_contracts.supplier_id', 'left');

        if ($status) {
            $builder->where('supplier_contracts.status', $status);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('suppliers.supplier_name', $search)
                    ->orLike('supplier_contracts.contract_number', $search)
                    ->groupEnd();
        }

        $contracts = $builder->orderBy('supplier_contracts.created_at', 'DESC')->findAll();

        $data = [
            'role'      => $this->session->get('role'),
            'title'     => 'Supplier Contracts',
            'contracts' => $contracts,
            'statistics' => $this->contractModel->getStatistics(),
            'currentStatus' => $status,
            'search'    => $search,
        ];

        return view('reusables/sidenav', $data) . view('supplier_contracts/index', $data);
    }

    /**
     * Show create contract form
     */
    public function create()
    {
        if ($redirect = $this->authorize('supplier_contracts.create')) {
            return $redirect;
        }

        $data = [
            'role'      => $this->session->get('role'),
            'title'     => 'Create Supplier Contract',
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
        ];

        return view('reusables/sidenav', $data) . view('supplier_contracts/create', $data);
    }

    /**
     * Store new contract
     */
    public function store()
    {
        if ($redirect = $this->authorize('supplier_contracts.create')) {
            return $redirect;
        }

        $rules = [
            'supplier_id'       => 'required|integer',
            'contract_type'     => 'required|in_list[Supply Agreement,Service Contract,Exclusive Agreement,Non-Exclusive Agreement]',
            'start_date'        => 'required|valid_date',
            'end_date'          => 'required|valid_date',
            'payment_terms'     => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[100]',
            'minimum_order_value' => 'permit_empty|decimal',
            'discount_rate'     => 'permit_empty|decimal',
            'delivery_terms'    => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'quality_standards' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'penalty_clauses'   => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'status'            => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[50]',
            'notes'             => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'supplier_id'         => $this->request->getPost('supplier_id'),
            'contract_number'     => $this->contractModel->generateContractNumber(),
            'contract_type'       => $this->request->getPost('contract_type'),
            'start_date'          => $this->request->getPost('start_date'),
            'end_date'            => $this->request->getPost('end_date'),
            'renewal_date'        => $this->request->getPost('renewal_date') ?: null,
            'auto_renewal'        => $this->request->getPost('auto_renewal') ? 1 : 0,
            'payment_terms'       => $this->request->getPost('payment_terms'),
            'minimum_order_value' => $this->request->getPost('minimum_order_value') ?: 0.00,
            'discount_rate'       => $this->request->getPost('discount_rate') ?: 0.00,
            'delivery_terms'      => $this->request->getPost('delivery_terms'),
            'quality_standards'   => $this->request->getPost('quality_standards'),
            'penalty_clauses'     => $this->request->getPost('penalty_clauses'),
            'status'              => $this->request->getPost('status') ?: 'draft',
            'notes'               => $this->request->getPost('notes'),
            'created_by'          => $this->session->get('user_id'),
        ];

        if ($this->contractModel->insert($data)) {
            return redirect()->to(site_url('supplier-contracts'))->with('success', 'Contract created successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create contract.');
    }

    /**
     * View contract details
     */
    public function view(int $id)
    {
        if ($redirect = $this->authorize('supplier_contracts.view')) {
            return $redirect;
        }

        $contract = $this->contractModel->getContractDetails($id);

        if (!$contract) {
            return redirect()->to(site_url('supplier-contracts'))->with('error', 'Contract not found.');
        }

        $data = [
            'role'     => $this->session->get('role'),
            'title'    => 'Contract Details',
            'contract' => $contract,
        ];

        return view('reusables/sidenav', $data) . view('supplier_contracts/view', $data);
    }

    /**
     * Show edit contract form
     */
    public function edit(int $id)
    {
        if ($redirect = $this->authorize('supplier_contracts.update')) {
            return $redirect;
        }

        $contract = $this->contractModel->find($id);

        if (!$contract) {
            return redirect()->to(site_url('supplier-contracts'))->with('error', 'Contract not found.');
        }

        $data = [
            'role'      => $this->session->get('role'),
            'title'     => 'Edit Contract',
            'contract'  => $contract,
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
        ];

        return view('reusables/sidenav', $data) . view('supplier_contracts/edit', $data);
    }

    /**
     * Update contract
     */
    public function update(int $id)
    {
        if ($redirect = $this->authorize('supplier_contracts.update')) {
            return $redirect;
        }

        $contract = $this->contractModel->find($id);

        if (!$contract) {
            return redirect()->to(site_url('supplier-contracts'))->with('error', 'Contract not found.');
        }

        $rules = [
            'contract_type'     => 'required|in_list[Supply Agreement,Service Contract,Exclusive Agreement,Non-Exclusive Agreement]',
            'start_date'        => 'required|valid_date',
            'end_date'          => 'required|valid_date',
            'payment_terms'     => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[100]',
            'minimum_order_value' => 'permit_empty|decimal',
            'discount_rate'     => 'permit_empty|decimal',
            'delivery_terms'    => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'quality_standards' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'penalty_clauses'   => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'status'            => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[50]',
            'notes'             => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'contract_type'       => $this->request->getPost('contract_type'),
            'start_date'          => $this->request->getPost('start_date'),
            'end_date'            => $this->request->getPost('end_date'),
            'renewal_date'        => $this->request->getPost('renewal_date') ?: null,
            'auto_renewal'        => $this->request->getPost('auto_renewal') ? 1 : 0,
            'payment_terms'       => $this->request->getPost('payment_terms'),
            'minimum_order_value' => $this->request->getPost('minimum_order_value') ?: 0.00,
            'discount_rate'       => $this->request->getPost('discount_rate') ?: 0.00,
            'delivery_terms'      => $this->request->getPost('delivery_terms'),
            'quality_standards'   => $this->request->getPost('quality_standards'),
            'penalty_clauses'     => $this->request->getPost('penalty_clauses'),
            'status'              => $this->request->getPost('status'),
            'notes'               => $this->request->getPost('notes'),
        ];

        if ($this->contractModel->update($id, $data)) {
            return redirect()->to(site_url('supplier-contracts/view/' . $id))->with('success', 'Contract updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update contract.');
    }

    /**
     * Delete contract
     */
    public function delete(int $id)
    {
        if ($redirect = $this->authorize('supplier_contracts.delete')) {
            return $redirect;
        }

        $contract = $this->contractModel->find($id);

        if (!$contract) {
            return redirect()->to(site_url('supplier-contracts'))->with('error', 'Contract not found.');
        }

        if ($this->contractModel->delete($id)) {
            return redirect()->to(site_url('supplier-contracts'))->with('success', 'Contract deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete contract.');
    }

    /**
     * Activate contract
     */
    public function activate(int $id)
    {
        if ($redirect = $this->authorize('supplier_contracts.update')) {
            return $redirect;
        }

        if ($this->contractModel->update($id, [
            'status' => 'active',
            'signed_by_admin' => 1,
            'signed_date' => date('Y-m-d'),
        ])) {
            return redirect()->back()->with('success', 'Contract activated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to activate contract.');
    }

    /**
     * Renew contract
     */
    public function renew(int $id)
    {
        if ($redirect = $this->authorize('supplier_contracts.update')) {
            return $redirect;
        }

        $contract = $this->contractModel->find($id);

        if (!$contract) {
            return redirect()->to(site_url('supplier-contracts'))->with('error', 'Contract not found.');
        }

        $data = [
            'role'      => $this->session->get('role'),
            'title'     => 'Renew Contract',
            'contract'  => $contract,
            'suppliers' => $this->supplierModel->getActiveSuppliers(),
        ];

        return view('reusables/sidenav', $data) . view('supplier_contracts/renew', $data);
    }

    /**
     * Process contract renewal
     */
    public function processRenewal(int $id)
    {
        if ($redirect = $this->authorize('supplier_contracts.update')) {
            return $redirect;
        }

        $contract = $this->contractModel->find($id);

        if (!$contract) {
            return redirect()->to(site_url('supplier-contracts'))->with('error', 'Contract not found.');
        }

        $rules = [
            'start_date'        => 'required|valid_date',
            'end_date'          => 'required|valid_date',
            'payment_terms'     => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[100]',
            'delivery_terms'    => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'quality_standards' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'penalty_clauses'   => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
            'notes'             => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newContractData = [
            'supplier_id'         => $contract['supplier_id'],
            'contract_type'       => $this->request->getPost('contract_type') ?: $contract['contract_type'],
            'start_date'          => $this->request->getPost('start_date'),
            'end_date'            => $this->request->getPost('end_date'),
            'renewal_date'        => $this->request->getPost('renewal_date') ?: null,
            'auto_renewal'        => $this->request->getPost('auto_renewal') ? 1 : 0,
            'payment_terms'       => $this->request->getPost('payment_terms') ?: $contract['payment_terms'],
            'minimum_order_value' => $this->request->getPost('minimum_order_value') ?: $contract['minimum_order_value'],
            'discount_rate'       => $this->request->getPost('discount_rate') ?: $contract['discount_rate'],
            'delivery_terms'      => $this->request->getPost('delivery_terms') ?: $contract['delivery_terms'],
            'quality_standards'   => $this->request->getPost('quality_standards') ?: $contract['quality_standards'],
            'penalty_clauses'     => $this->request->getPost('penalty_clauses') ?: $contract['penalty_clauses'],
            'notes'               => $this->request->getPost('notes'),
            'created_by'          => $this->session->get('user_id'),
        ];

        $newContractId = $this->contractModel->renewContract($id, $newContractData);

        if ($newContractId) {
            return redirect()->to(site_url('supplier-contracts/view/' . $newContractId))->with('success', 'Contract renewed successfully.');
        }

        return redirect()->back()->with('error', 'Failed to renew contract.');
    }
}

