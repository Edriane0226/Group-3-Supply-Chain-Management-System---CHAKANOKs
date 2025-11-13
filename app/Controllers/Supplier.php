<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PurchaseOrderModel;
use App\Models\InventoryModel;
use App\Models\NotificationModel;
use App\Models\SupplierModel;

class Supplier extends BaseController
{
    protected $purchaseOrderModel;
    protected $inventoryModel;
    protected $notificationModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->inventoryModel = new InventoryModel();
        $this->notificationModel = new NotificationModel();
        $this->supplierModel = new SupplierModel();
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return redirect()->to(site_url('login'))->with('error', 'Please login as supplier.');
        }

        $supplierId = session()->get('user_id');

        try {
            // Get purchase orders for this supplier
            $pendingOrders = $this->purchaseOrderModel->where('supplier_id', $supplierId)->where('status', 'Pending')->countAllResults();
            $inProgressOrders = $this->purchaseOrderModel->where('supplier_id', $supplierId)->whereIn('status', ['Confirmed', 'Preparing'])->countAllResults();
            $completedOrders = $this->purchaseOrderModel->where('supplier_id', $supplierId)->where('status', 'Delivered')->countAllResults();

            // Get notifications
            $notifications = $this->notificationModel->where('user_id', $supplierId)->orderBy('created_at', 'DESC')->findAll(5);

            // Quick stats
            $totalOrders = $this->purchaseOrderModel->where('supplier_id', $supplierId)->countAllResults();
            $totalRevenue = $this->purchaseOrderModel->selectSum('total_amount')->where('supplier_id', $supplierId)->where('status', 'Delivered')->get()->getRow()->total_amount ?? 0;

            $data = [
                'pendingOrders' => $pendingOrders,
                'inProgressOrders' => $inProgressOrders,
                'completedOrders' => $completedOrders,
                'notifications' => $notifications,
                'totalOrders' => $totalOrders,
                'totalRevenue' => $totalRevenue,
            ];
        } catch (\Exception $e) {
            // If database queries fail, provide default data
            $data = [
                'pendingOrders' => 0,
                'inProgressOrders' => 0,
                'completedOrders' => 0,
                'notifications' => [],
                'totalOrders' => 0,
                'totalRevenue' => 0,
            ];
        }

        return view('reusables/sidenav', ['title' => 'Supplier Dashboard']) . view('supplier/dashboard', $data);
    }

    public function orders()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return redirect()->to(site_url('login'))->with('error', 'Please login as supplier.');
        }

        $supplierId = session()->get('user_id');

        $orders = $this->purchaseOrderModel->select('purchase_orders.*, branches.branch_name')
                                           ->join('branches', 'branches.id = purchase_orders.branch_id')
                                           ->where('supplier_id', $supplierId)
                                           ->orderBy('created_at', 'DESC')
                                           ->findAll();

        return view('reusables/sidenav', ['title' => 'Purchase Orders']) . view('supplier/orders', ['orders' => $orders]);
    }

    public function orderDetails($orderId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $supplierId = session()->get('user_id');

        try {
            // Check if order belongs to supplier and get branch_name
            $orderCheck = $this->purchaseOrderModel->select('purchase_orders.*, branches.branch_name')
                                                   ->join('branches', 'branches.id = purchase_orders.branch_id')
                                                   ->where('purchase_orders.id', $orderId)
                                                   ->where('purchase_orders.supplier_id', $supplierId)
                                                   ->first();
            if (!$orderCheck) {
                return $this->response->setJSON(['error' => 'Order not found or does not belong to this supplier']);
            }

            $order = $this->purchaseOrderModel->getDetails($orderId);

            if (!$order) {
                return $this->response->setJSON(['error' => 'Order not found']);
            }

            $order['branch_name'] = $orderCheck['branch_name'];

            return $this->response->setJSON($order);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function updateOrderStatus($orderId)
    {
        // Always return JSON for this AJAX endpoint
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $supplierId = session()->get('user_id');
        $status = $this->request->getPost('status');

        $order = $this->purchaseOrderModel->where('id', $orderId)->where('supplier_id', $supplierId)->first();
        if (!$order) {
            return $this->response->setJSON(['success' => false, 'error' => 'Order not found or does not belong to this supplier.']);
        }

        try {
            // Update status and logistics_status based on supplier workflow
            $updateData = ['status' => $status];

            // Update logistics_status based on supplier status
            if ($status === 'Confirmed') {
                $updateData['logistics_status'] = 'supplier_confirmed';
                // Also update purchase request status to 'In Progress'
                if (!empty($order['purchase_request_id'])) {
                    $db = \Config\Database::connect();
                    $db->table('purchase_requests')->update(['status' => 'In Progress'], ['id' => $order['purchase_request_id']]);
                }
                // Notify logistics coordinator
                try {
                    $this->notificationModel->createNotification([
                        'user_id' => 1, // Assuming logistics coordinator id is 1
                        'type' => 'in_app',
                        'title' => 'Order Confirmed by Supplier',
                        'message' => 'Order ' . $orderId . ' has been confirmed by the supplier.',
                        'reference_type' => 'purchase_order',
                        'reference_id' => $orderId,
                    ]);
                } catch (\Exception $e) {
                    log_message('error', 'Failed to create confirmation notification: ' . $e->getMessage());
                }
            } elseif ($status === 'Preparing') {
                $updateData['logistics_status'] = 'supplier_preparing';
                // Notify logistics coordinator
                try {
                    $this->notificationModel->createNotification([
                        'user_id' => 1, // Assuming logistics coordinator id is 1
                        'type' => 'in_app',
                        'title' => 'Supplier Started Preparing Order',
                        'message' => 'Order ' . $orderId . ' is now being prepared by the supplier.',
                        'reference_type' => 'purchase_order',
                        'reference_id' => $orderId,
                    ]);
                } catch (\Exception $e) {
                    log_message('error', 'Failed to create preparation notification: ' . $e->getMessage());
                }
            } elseif ($status === 'Ready for Pickup') {
                $updateData['logistics_status'] = 'ready_for_pickup';
                // Notify logistics coordinator
                try {
                    $this->notificationModel->createNotification([
                        'user_id' => 1, // Assuming logistics coordinator id is 1
                        'type' => 'in_app',
                        'title' => 'Order Ready for Pickup',
                        'message' => 'Order ' . $orderId . ' is ready for pickup.',
                        'reference_type' => 'purchase_order',
                        'reference_id' => $orderId,
                    ]);
                } catch (\Exception $e) {
                    log_message('error', 'Failed to create pickup notification: ' . $e->getMessage());
                }
            }

            $this->purchaseOrderModel->update($orderId, $updateData);

            return $this->response->setJSON(['success' => true, 'message' => 'Order status updated successfully.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }

    public function deliveries()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return redirect()->to(site_url('login'))->with('error', 'Please login as supplier.');
        }

        $supplierId = session()->get('user_id');

        // Show orders that are in transit or delivered
        $orders = $this->purchaseOrderModel->select('purchase_orders.*, branches.branch_name')
                                           ->join('branches', 'branches.id = purchase_orders.branch_id')
                                           ->where('supplier_id', $supplierId)
                                           ->whereIn('purchase_orders.status', ['in_transit', 'delivered', 'approved'])
                                           ->orderBy('purchase_orders.created_at', 'DESC')
                                           ->findAll();

        return view('reusables/sidenav', ['title' => 'Delivery Management']) . view('supplier/deliveries', ['deliveries' => $orders]);
    }

    public function deliveryDetails($orderId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $supplierId = session()->get('user_id');
        $order = $this->purchaseOrderModel->select('purchase_orders.*, branches.branch_name')
                                          ->join('branches', 'branches.id = purchase_orders.branch_id')
                                          ->where('purchase_orders.id', $orderId)
                                          ->where('purchase_orders.supplier_id', $supplierId)
                                          ->first();

        if (!$order) {
            return $this->response->setJSON(['error' => 'Order not found']);
        }

        return $this->response->setJSON($order);
    }



    public function invoices()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return redirect()->to(site_url('login'))->with('error', 'Please login as supplier.');
        }

        $supplierId = session()->get('user_id');

        // Assuming invoices are linked to purchase_orders
        $invoices = $this->purchaseOrderModel->select('purchase_orders.*, branches.branch_name')
                                             ->join('branches', 'branches.id = purchase_orders.branch_id')
                                             ->where('supplier_id', $supplierId)
                                             ->where('purchase_orders.status', 'Delivered')
                                             ->findAll();

        return view('reusables/sidenav', ['title' => 'Invoices & Payments']) . view('supplier/invoices', ['invoices' => $invoices]);
    }

    public function notifications()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return redirect()->to(site_url('login'))->with('error', 'Please login as supplier.');
        }

        $supplierId = session()->get('user_id');

        $notifications = $this->notificationModel->where('user_id', $supplierId)
                                                 ->orderBy('created_at', 'DESC')
                                                 ->findAll();

        return view('reusables/sidenav', ['title' => 'Notifications']) . view('supplier/notifications', ['notifications' => $notifications]);
    }

    public function profile()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return redirect()->to(site_url('login'))->with('error', 'Please login as supplier.');
        }

        $supplierId = session()->get('user_id');
        $supplier = $this->supplierModel->find($supplierId);

        return view('reusables/sidenav', ['title' => 'Profile & Settings']) . view('supplier/profile', ['supplier' => $supplier]);
    }

    public function updateProfile()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return redirect()->to(site_url('login'))->with('error', 'Please login as supplier.');
        }

        $supplierId = session()->get('user_id');

        $data = [
            'contact_info' => $this->request->getPost('contact_info'),
            'address' => $this->request->getPost('address'),
        ];

        $this->supplierModel->update($supplierId, $data);

        return redirect()->back()->with('success', 'Profile updated.');
    }

    public function changePassword()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Supplier') {
            return redirect()->to(site_url('login'))->with('error', 'Please login as supplier.');
        }

        $supplierId = session()->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        $supplier = $this->supplierModel->find($supplierId);

        if (!password_verify($currentPassword, $supplier['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $this->supplierModel->update($supplierId, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);

        return redirect()->back()->with('success', 'Password changed.');
    }
}
