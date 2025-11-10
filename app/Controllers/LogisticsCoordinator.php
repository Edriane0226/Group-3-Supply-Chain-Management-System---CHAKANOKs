<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DeliveryScheduleModel;
use App\Models\PurchaseOrderModel;
use App\Models\InventoryModel;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class LogisticsCoordinator extends BaseController
{
    protected DeliveryScheduleModel $deliveryScheduleModel;
    protected PurchaseOrderModel $purchaseOrderModel;
    protected InventoryModel $inventoryModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->deliveryScheduleModel = new DeliveryScheduleModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->inventoryModel = new InventoryModel();
        $this->notificationModel = new NotificationModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        if ($session->get('role') !== 'Logistics Coordinator') {
            $session->setFlashdata('error', 'Unauthorized access.');
            return redirect()->to(site_url('login'));
        }

        $coordinatorId = (int)$session->get('user_id');

        $data = [
            'role' => $session->get('role'),
            'title' => 'Logistics Coordinator Dashboard',
            'coordinator_id' => $coordinatorId,
        ];

    // Get pending purchase orders for logistics workflow
        $data['pendingPurchaseOrders'] = $this->purchaseOrderModel->getPendingForLogisticsWorkflow();

        // Get today's schedules
        $today = date('Y-m-d');
        $data['todaySchedules'] = $this->deliveryScheduleModel->getSchedulesByDateRange($today, $today, $coordinatorId);

        // Get upcoming deliveries
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $data['upcomingDeliveries'] = $this->deliveryScheduleModel->getSchedulesByDateRange($today, $nextWeek, $coordinatorId);

        // Get delivery performance metrics
        $data['performanceMetrics'] = $this->getCoordinatorPerformanceMetrics($coordinatorId);

        // Get unread notifications
        $data['unreadNotifications'] = $this->notificationModel->getUnreadCount($coordinatorId);

        return view('reusables/sidenav', $data) . view('logistics_coordinator/dashboard', $data);
    }

    // Schedule deliveries
    public function scheduleDeliveries()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);

        if (!$data || !isset($data['po_ids']) || !isset($data['scheduled_date'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid data']);
        }

        $coordinatorId = (int)$session->get('user_id');
        $poIds = $data['po_ids'];
        $scheduledDate = $data['scheduled_date'];
        $scheduledTime = $data['scheduled_time'] ?? date('H:i:s');

        try {
            // Optimize routes for selected purchase orders
            $schedules = $this->deliveryScheduleModel->optimizeRoutes($poIds, $coordinatorId);

            // Update scheduled date/time for created schedules
            foreach ($schedules as $schedule) {
                $this->deliveryScheduleModel->update($schedule['id'], [
                    'scheduled_date' => $scheduledDate,
                    'scheduled_time' => $scheduledTime,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Deliveries scheduled successfully',
                'schedules' => $schedules
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to schedule deliveries']);
        }
    }

    // Update delivery status
    public function updateDeliveryStatus(int $deliveryId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);

        if (!$data || !isset($data['status'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid data']);
        }

        $status = $data['status'];
        $notes = $data['notes'] ?? null;

        // Update delivery status
        $result = $this->inventoryModel->updateDeliveryStatus($deliveryId, $status);

        if ($result) {
            // Update schedule status if exists
            $schedule = $this->db->table('delivery_schedules')
                                ->where('delivery_id', $deliveryId)
                                ->get()
                                ->getRowArray();

            if ($schedule) {
                $scheduleStatus = match($status) {
                    'Approved' => 'Scheduled',
                    'In Transit' => 'In Progress',
                    'Delivered' => 'Completed',
                    default => 'Scheduled'
                };

                $this->deliveryScheduleModel->updateScheduleStatus($schedule['id'], $scheduleStatus, $notes);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Delivery status updated']);
        }

        return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to update delivery status']);
    }

    // Get delivery calendar data
    public function getCalendarData(): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $startDate = $this->request->getGet('start');
        $endDate = $this->request->getGet('end');

        if (!$startDate || !$endDate) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Start and end dates required']);
        }

        $coordinatorId = (int)$session->get('user_id');
        $calendarData = $this->deliveryScheduleModel->getSchedulesByDateRange($startDate, $endDate, $coordinatorId);

        return $this->response->setJSON($calendarData);
    }

    // Get delivery details for scheduling
    public function getDeliveryDetails(int $deliveryId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $delivery = $this->inventoryModel->getDeliveryDetails($deliveryId);

        if (!$delivery) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Delivery not found']);
        }

        return $this->response->setJSON($delivery);
    }

    // Get coordinator performance metrics
    private function getCoordinatorPerformanceMetrics(int $coordinatorId): array
    {
        // Get schedules for this coordinator
        $schedules = $this->deliveryScheduleModel->getCoordinatorSchedules($coordinatorId);

        $totalSchedules = count($schedules);
        $completedSchedules = count(array_filter($schedules, fn($s) => $s['status'] === 'Completed'));
        $onTimeDeliveries = 0;

        foreach ($schedules as $schedule) {
            if ($schedule['status'] === 'Completed') {
                // Check if delivery was on time (simplified logic)
                $scheduledTime = strtotime($schedule['scheduled_date'] . ' ' . $schedule['scheduled_time']);
                $po = $this->db->table('purchase_orders')
                              ->select('actual_delivery_date')
                              ->where('id', $schedule['po_id'])
                              ->get()
                              ->getRowArray();

                if ($po && $po['actual_delivery_date']) {
                    $actualTime = strtotime($po['actual_delivery_date']);
                    if ($actualTime <= $scheduledTime + 3600) { // Within 1 hour
                        $onTimeDeliveries++;
                    }
                }
            }
        }

        return [
            'total_schedules' => $totalSchedules,
            'completed_schedules' => $completedSchedules,
            'completion_rate' => $totalSchedules > 0 ? round(($completedSchedules / $totalSchedules) * 100, 2) : 0,
            'on_time_rate' => $completedSchedules > 0 ? round(($onTimeDeliveries / $completedSchedules) * 100, 2) : 0,
        ];
    }

    // Get notifications
    public function getNotifications(): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $userId = (int)$session->get('user_id');
        $notifications = $this->notificationModel->getUserNotifications($userId);

        return $this->response->setJSON($notifications);
    }

    // Mark notification as read
    public function markNotificationRead(int $notificationId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $result = $this->notificationModel->markAsSent($notificationId);

        return $this->response->setJSON(['success' => $result]);
    }

    // Step 1: Review Approved Purchase Order
    public function reviewApprovedPO(int $poId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $po = $this->purchaseOrderModel->find($poId);
        if (!$po || $po['logistics_status'] !== 'pending_review') {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'PO not found or not ready for review']);
        }

        // Mark as reviewed and move to supplier coordination
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => 'supplier_coordination',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'PO reviewed and ready for supplier coordination']);
    }

    // Step 2: Coordinate with Supplier
    public function coordinateWithSupplier(int $poId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);
        $supplierConfirmed = $data['supplier_confirmed'] ?? false;
        $pickupDate = $data['pickup_date'] ?? null;
        $notes = $data['notes'] ?? null;

        if (!$supplierConfirmed || !$pickupDate) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Supplier confirmation and pickup date required']);
        }

        // Update PO with supplier coordination details
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => 'supplier_coordinated',
            'expected_delivery_date' => $pickupDate,
            'delivery_notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Notify logistics coordinators
        $coordinatorIds = [(int)$session->get('user_id')];
        $this->notificationModel->notifyLogisticsCoordinator('supplier_coordinated', $poId, $coordinatorIds);

        return $this->response->setJSON(['success' => true, 'message' => 'Supplier coordination completed']);
    }

    // Step 3: Create Delivery Schedule
    public function createDeliverySchedule(int $poId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);
        $scheduledDate = $data['scheduled_date'] ?? null;
        $scheduledTime = $data['scheduled_time'] ?? null;
        $driverId = $data['driver_id'] ?? null;
        $vehicleId = $data['vehicle_id'] ?? null;

        if (!$scheduledDate || !$scheduledTime || !$driverId || !$vehicleId) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'All schedule details required']);
        }

        // Create delivery schedule
        $scheduleData = [
            'po_id' => $poId,
            'coordinator_id' => (int)$session->get('user_id'),
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'scheduled_date' => $scheduledDate,
            'scheduled_time' => $scheduledTime,
            'status' => 'Scheduled',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('delivery_schedules')->insert($scheduleData);
        $scheduleId = $this->db->insertID();

        // Update PO status
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => 'delivery_scheduled',
            'status' => 'in_transit',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Notify logistics coordinators
        $coordinatorIds = [(int)$session->get('user_id')];
        $this->notificationModel->notifyLogisticsCoordinator('delivery_scheduled', $poId, $coordinatorIds);

        return $this->response->setJSON(['success' => true, 'message' => 'Delivery schedule created', 'schedule_id' => $scheduleId]);
    }

    // Step 4: Update Delivery Status (Enhanced)
    public function updateLogisticsDeliveryStatus(int $poId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);
        $status = $data['status'] ?? null; // 'in_transit', 'delivered'
        $notes = $data['notes'] ?? null;

        if (!$status) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Status required']);
        }

        $logisticsStatus = match($status) {
            'in_transit' => 'delivery_started',
            'delivered' => 'delivered',
            default => 'delivery_scheduled'
        };

        // Update PO logistics status
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => $logisticsStatus,
            'status' => $status === 'delivered' ? 'delivered' : 'in_transit',
            'actual_delivery_date' => $status === 'delivered' ? date('Y-m-d') : null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Update delivery schedule if exists
        $schedule = $this->db->table('delivery_schedules')
                            ->where('po_id', $poId)
                            ->get()
                            ->getRowArray();

        if ($schedule) {
            $scheduleStatus = match($status) {
                'in_transit' => 'In Progress',
                'delivered' => 'Completed',
                default => 'Scheduled'
            };

            $this->db->table('delivery_schedules')
                    ->where('id', $schedule['id'])
                    ->update([
                        'status' => $scheduleStatus,
                        'notes' => $notes,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
        }

        // Notify coordinators
        $coordinatorIds = [(int)$session->get('user_id')];
        $eventType = $status === 'in_transit' ? 'delivery_started' : 'delivery_completed';
        $this->notificationModel->notifyLogisticsCoordinator($eventType, $poId, $coordinatorIds);

        return $this->response->setJSON(['success' => true, 'message' => 'Delivery status updated']);
    }

    // Step 5: Coordinate with Branch
    public function coordinateWithBranch(int $poId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);
        $branchNotified = $data['branch_notified'] ?? false;
        $estimatedArrival = $data['estimated_arrival'] ?? null;
        $contactPerson = $data['contact_person'] ?? null;

        if (!$branchNotified || !$estimatedArrival) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Branch notification and estimated arrival required']);
        }

        // Update PO with branch coordination details
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => 'branch_notified',
            'delivery_notes' => ($this->purchaseOrderModel->find($poId)['delivery_notes'] ?? '') . "\nBranch notified. Contact: {$contactPerson}. ETA: {$estimatedArrival}",
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Notify branch users
        $po = $this->purchaseOrderModel->find($poId);
        $branchUsers = $this->db->table('users')
                               ->where('branch_id', $po['branch_id'])
                               ->get()
                               ->getResultArray();

        $branchUserIds = array_column($branchUsers, 'id');
        $this->notificationModel->notifyStatusChange('purchase_order', $poId, 'in_transit', 'branch_notified', $branchUserIds);

        // Notify logistics coordinators
        $coordinatorIds = [(int)$session->get('user_id')];
        $this->notificationModel->notifyLogisticsCoordinator('branch_notified', $poId, $coordinatorIds);

        return $this->response->setJSON(['success' => true, 'message' => 'Branch coordination completed']);
    }

    // Step 6: Close Delivery Record
    public function closeDeliveryRecord(int $poId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);
        $branchConfirmation = $data['branch_confirmation'] ?? false;
        $finalNotes = $data['final_notes'] ?? null;

        if (!$branchConfirmation) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Branch confirmation required']);
        }

        // Update PO to completed
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => 'completed',
            'status' => 'delivered',
            'delivery_notes' => ($this->purchaseOrderModel->find($poId)['delivery_notes'] ?? '') . "\nFinal Notes: {$finalNotes}",
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Update delivery schedule to completed
        $this->db->table('delivery_schedules')
                ->where('po_id', $poId)
                ->update([
                    'status' => 'Completed',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

        // Notify logistics coordinators
        $coordinatorIds = [(int)$session->get('user_id')];
        $this->notificationModel->notifyLogisticsCoordinator('delivery_completed', $poId, $coordinatorIds);

        return $this->response->setJSON(['success' => true, 'message' => 'Delivery record closed successfully']);
    }

    // Get PO details for logistics workflow
    public function getPODetails(int $poId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $po = $this->purchaseOrderModel->getDetails($poId);
        if (!$po) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'PO not found']);
        }

        // Add logistics workflow status
        $po['workflow_status'] = $this->getWorkflowStatus($po);

        return $this->response->setJSON($po);
    }

    // Helper method to get workflow status
    private function getWorkflowStatus(array $po): array
    {
        $status = $po['logistics_status'] ?? 'pending_review';

        $steps = [
            'pending_review' => ['step' => 1, 'name' => 'Review Approved PO', 'completed' => false],
            'supplier_coordination' => ['step' => 2, 'name' => 'Coordinate with Supplier', 'completed' => false],
            'supplier_coordinated' => ['step' => 2, 'name' => 'Supplier Coordinated', 'completed' => true],
            'delivery_scheduled' => ['step' => 3, 'name' => 'Delivery Scheduled', 'completed' => true],
            'delivery_started' => ['step' => 4, 'name' => 'Delivery Started', 'completed' => true],
            'branch_notified' => ['step' => 5, 'name' => 'Branch Notified', 'completed' => true],
            'completed' => ['step' => 6, 'name' => 'Delivery Completed', 'completed' => true],
        ];

        return $steps[$status] ?? ['step' => 1, 'name' => 'Unknown Status', 'completed' => false];
    }
}
