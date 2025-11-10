<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class Deliveries extends BaseController
{
    protected InventoryModel $inventoryModel;

    public function __construct()
    {
        $this->inventoryModel = new InventoryModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        if (!in_array($session->get('role'), ['Branch Manager', 'Inventory Staff', 'Central Office Admin'])) {
            $session->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(site_url('login'));
        }

        $branchId = (int)($session->get('branch_id') ?? 0);

        $data = [
            'role' => $session->get('role'),
            'title' => 'Deliveries',
            'stockTypes' => $this->inventoryModel->getStockTypes(),
        ];

        // Get deliveries based on role
        if ($session->get('role') === 'Central Office Admin') {
            // Admin sees all deliveries
            $data['pendingDeliveries'] = $this->inventoryModel->getDeliveries(0, 'Pending');
            $data['receivedDeliveries'] = $this->inventoryModel->getDeliveries(0, 'Received');
            $data['cancelledDeliveries'] = $this->inventoryModel->getDeliveries(0, 'Cancelled');
        } else {
            // Branch users see only their branch deliveries
            $data['pendingDeliveries'] = $this->inventoryModel->getDeliveries($branchId, 'Pending');
            $data['receivedDeliveries'] = $this->inventoryModel->getDeliveries($branchId, 'Received');
            $data['cancelledDeliveries'] = $this->inventoryModel->getDeliveries($branchId, 'Cancelled');
        }

        return view('reusables/sidenav', $data) . view('pages/deliveries', $data);
    }



    // Get delivery details
    public function details(int $id): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $delivery = $this->inventoryModel->getDeliveryDetails($id);

        if (!$delivery) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Delivery not found']);
        }

        // Check if user can access this delivery
        $branchId = (int)($session->get('branch_id') ?? 0);
        if ($session->get('role') !== 'Central Office Admin' && $delivery['branch_id'] != $branchId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON($delivery);
    }

    // Mark delivery as received
    public function receive(int $id): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['Inventory Staff', 'Branch Manager'])) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $delivery = $this->inventoryModel->getDeliveryDetails($id);

        if (!$delivery) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Delivery not found']);
        }

        if ($delivery['status'] !== 'Pending') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Delivery is not pending']);
        }

        // Check if user can access this delivery
        $branchId = (int)($session->get('branch_id') ?? 0);
        if ($delivery['branch_id'] != $branchId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        if ($this->inventoryModel->receiveDelivery($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Delivery received and stock updated']);
        }

        return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to receive delivery']);
    }

    // Cancel delivery
    public function cancel(int $id): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['Branch Manager', 'Central Office Admin'])) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $delivery = $this->inventoryModel->getDeliveryDetails($id);

        if (!$delivery) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Delivery not found']);
        }

        if ($delivery['status'] !== 'Pending') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Only pending deliveries can be cancelled']);
        }

        // Check if user can access this delivery
        $branchId = (int)($session->get('branch_id') ?? 0);
        if ($session->get('role') !== 'Central Office Admin' && $delivery['branch_id'] != $branchId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        if ($this->inventoryModel->cancelDelivery($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Delivery cancelled']);
        }

        return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to cancel delivery']);
    }
}
