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
    protected $db;

    public function __construct()
    {
        $this->deliveryScheduleModel = new DeliveryScheduleModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->inventoryModel = new InventoryModel();
        $this->notificationModel = new NotificationModel();
        $this->db = \Config\Database::connect();
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

        // Prevent scheduling deliveries in the past (date only)
        $today = date('Y-m-d');
        if (strtotime($scheduledDate) < strtotime($today)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Scheduled date cannot be in the past']);
        }

        try {
            // Create delivery schedules for selected purchase orders (no route optimization)
            $schedules = [];
            $db = \Config\Database::connect();

            // Get current max route sequence for the scheduled date
            $maxSequenceRow = $db->table('delivery_schedules')
                ->selectMax('route_sequence')
                ->where('scheduled_date', $scheduledDate)
                ->get()
                ->getRow();

            $nextSequence = ($maxSequenceRow->route_sequence ?? 0) + 1;

            foreach ($poIds as $poId) {
                // Skip if a schedule already exists for this PO
                $existing = $db->table('delivery_schedules')
                    ->where('po_id', $poId)
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    continue;
                }

                $scheduleData = [
                    'po_id' => $poId,
                    'coordinator_id' => $coordinatorId,
                    'scheduled_date' => $scheduledDate,
                    'scheduled_time' => $scheduledTime,
                    'route_sequence' => $nextSequence,
                    'status' => 'Scheduled',
                ];

                $scheduleId = $this->deliveryScheduleModel->createSchedule($scheduleData);

                if ($scheduleId) {
                    $schedules[] = $this->deliveryScheduleModel->find($scheduleId);

                    // Update PO status to indicate scheduling
                    $this->purchaseOrderModel->update($poId, [
                        'logistics_status' => 'delivery_scheduled',
                        'status' => 'In_Transit',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    $nextSequence++;
                }
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
                // Map external delivery status to schedule status, but do NOT mark as Completed here.
                // Final completion (inventory update / actual delivery confirmation) must be done by the branch via Inventory::confirmDelivery
                $scheduleStatus = match($status) {
                    'Approved' => 'Scheduled',
                    'In Transit' => 'In Progress',
                    'Delivered' => 'Delivered', // delivered at central/supplier side (pending branch receipt)
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
        if (!$po) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'PO not found']);
        }

        $currentLogisticsStatus = $po['logistics_status'] ?? 'pending_review';
        $supplierStatus = $po['status'] ?? 'Pending';

        // Determine next logistics status based on current state
        $nextLogisticsStatus = 'supplier_coordination';
        
        // If supplier has already confirmed or is preparing, move to coordination
        if (in_array($currentLogisticsStatus, ['supplier_confirmed', 'supplier_preparing'])) {
            $nextLogisticsStatus = 'supplier_coordination';
        }
        // If ready for pickup, move directly to delivery scheduling stage
        elseif ($currentLogisticsStatus === 'ready_for_pickup' || $supplierStatus === 'Ready for Pickup') {
            $nextLogisticsStatus = 'supplier_coordinated'; // Skip coordination, ready for scheduling
        }
        // If already in coordination or coordinated, don't change
        elseif (in_array($currentLogisticsStatus, ['supplier_coordination', 'supplier_coordinated'])) {
            $nextLogisticsStatus = $currentLogisticsStatus;
        }
        // Default: pending_review -> supplier_coordination
        elseif ($currentLogisticsStatus === 'pending_review') {
            $nextLogisticsStatus = 'supplier_coordination';
        }
        // If already in later stages, don't change
        else {
            return $this->response->setJSON(['success' => true, 'message' => 'PO is already in logistics workflow']);
        }

        // Update PO logistics status
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => $nextLogisticsStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $message = $nextLogisticsStatus === 'supplier_coordinated' 
            ? 'PO reviewed and ready for delivery scheduling' 
            : 'PO reviewed and ready for supplier coordination';

        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }

    // Step 2: Coordinate with Supplier
    public function coordinateWithSupplier(int $poId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $po = $this->purchaseOrderModel->find($poId);
        if (!$po) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'PO not found']);
        }

        $data = $this->request->getJSON(true);
        $supplierConfirmed = $data['supplier_confirmed'] ?? false;
        $pickupDate = $data['pickup_date'] ?? null;
        $notes = $data['notes'] ?? null;

        // If supplier has already confirmed (status is Confirmed, Preparing, or Ready for Pickup), 
        // we can skip the confirmation check
        $currentStatus = $po['status'] ?? 'Pending';
        $isSupplierAlreadyConfirmed = in_array($currentStatus, ['Confirmed', 'Preparing', 'Ready for Pickup']);

        if (!$isSupplierAlreadyConfirmed && (!$supplierConfirmed || !$pickupDate)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Supplier confirmation and pickup date required']);
        }

        // If supplier already confirmed, use existing expected_delivery_date or require pickup date
        if ($isSupplierAlreadyConfirmed && !$pickupDate) {
            $pickupDate = $po['expected_delivery_date'] ?? date('Y-m-d', strtotime('+3 days'));
        }

        // Update PO with supplier coordination details
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => 'supplier_coordinated',
            'expected_delivery_date' => $pickupDate,
            'delivery_notes' => ($po['delivery_notes'] ?? '') . ($notes ? "\n" . $notes : ''),
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

        try {
            $po = $this->purchaseOrderModel->find($poId);
            if (!$po) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'PO not found']);
            }

            $data = $this->request->getJSON(true);
            if ($data === null) {
                // Try to get data from POST if JSON is null
                $data = $this->request->getPost();
            }
            
            $scheduledDate = $data['scheduled_date'] ?? null;
            $scheduledTime = $data['scheduled_time'] ?? null;

            if (!$scheduledDate || !$scheduledTime) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Scheduled date and time are required']);
            }

            // Prevent scheduling deliveries in the past (date only)
            $today = date('Y-m-d');
            if (strtotime($scheduledDate) < strtotime($today)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Scheduled date cannot be in the past']);
            }

            // Check if PO is ready for scheduling (supplier confirmed or ready for pickup)
            $currentLogisticsStatus = $po['logistics_status'] ?? 'pending_review';
            $supplierStatus = $po['status'] ?? 'Pending';

            $allowedLogisticsStatuses = [
                'pending_review',
                'supplier_coordination',
                'supplier_coordinated',
                'supplier_confirmed',
                'supplier_preparing',
                'ready_for_pickup',
                'delivery_scheduled'
            ];

            $allowedSupplierStatuses = [
                'Approved',
                'Confirmed',
                'Preparing',
                'Ready for Pickup',
                'In_Transit'
            ];

            $canSchedule = in_array($currentLogisticsStatus, $allowedLogisticsStatuses, true)
                        || in_array($supplierStatus, $allowedSupplierStatuses, true);

            if (!$canSchedule) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'PO is not ready for delivery scheduling. Please coordinate with supplier first.']);
            }

            // Get database connection
            $db = \Config\Database::connect();

            // Check if schedule already exists for this PO
            $existingSchedule = $db->table('delivery_schedules')
                ->where('po_id', $poId)
                ->get()
                ->getRowArray();
            
            if ($existingSchedule) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'A delivery schedule already exists for this PO']);
            }

            // Get the next route sequence number
            $maxSequence = $db->table('delivery_schedules')
                ->selectMax('route_sequence')
                ->where('scheduled_date', $scheduledDate)
                ->get()
                ->getRow();
            $nextSequence = ($maxSequence->route_sequence ?? 0) + 1;

            // Create delivery schedule
            $scheduleData = [
                'po_id' => $poId,
                'coordinator_id' => (int)$session->get('user_id'),
                'driver_id' => null,
                'vehicle_id' => null,
                'scheduled_date' => $scheduledDate,
                'scheduled_time' => $scheduledTime,
                'route_sequence' => $nextSequence,
                'status' => 'Scheduled',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('delivery_schedules')->insert($scheduleData);
            $scheduleId = $db->insertID();

            if (!$scheduleId) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to create delivery schedule']);
            }

            // Update PO status
            $this->purchaseOrderModel->update($poId, [
                'logistics_status' => 'delivery_scheduled',
                'status' => 'In_Transit',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Notify logistics coordinators (optional, don't fail if notification fails)
            try {
                $coordinatorIds = [(int)$session->get('user_id')];
                $this->notificationModel->notifyLogisticsCoordinator('delivery_scheduled', $poId, $coordinatorIds);
            } catch (\Exception $e) {
                log_message('error', 'Failed to send notification: ' . $e->getMessage());
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Delivery schedule created', 'schedule_id' => $scheduleId]);
        } catch (\Exception $e) {
            log_message('error', 'Create delivery schedule error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to create schedule: ' . $e->getMessage()]);
        }
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

        // Map statuses but DO NOT finalize the PO or set actual delivery date here.
        // The branch's Inventory::confirmDelivery must perform the final completion and inventory update.
        $logisticsStatus = match($status) {
            'in_transit' => 'delivery_started',
            'delivered' => 'delivered',
            default => 'delivery_scheduled'
        };

        // Update PO logistics status to reflect progress, but do NOT mark as completed or set actual_delivery_date
        $this->purchaseOrderModel->update($poId, [
            'logistics_status' => $logisticsStatus,
            'status' => $status === 'delivered' ? 'Delivered' : 'In_Transit',
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
                'delivered' => 'Delivered', // mark as delivered by logistics side but pending branch confirmation
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

        // Append final notes but DO NOT mark as completed here. Finalization should be done by branch confirming receipt.
        $this->purchaseOrderModel->update($poId, [
            'delivery_notes' => ($this->purchaseOrderModel->find($poId)['delivery_notes'] ?? '') . "\nFinal Notes: {$finalNotes}",
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Notify logistics coordinators about the closing notes
        $coordinatorIds = [(int)$session->get('user_id')];
        $this->notificationModel->notifyLogisticsCoordinator('delivery_record_updated', $poId, $coordinatorIds);

        return $this->response->setJSON(['success' => true, 'message' => 'Delivery record updated (awaiting branch confirmation)']);
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

    // Delivery Schedules Page
    public function deliverySchedules()
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

        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d', strtotime('+30 days'));
        $status = $this->request->getGet('status') ?? null;

        // Get all schedules for this coordinator
        $schedules = $this->deliveryScheduleModel->getSchedulesByDateRange($startDate, $endDate, $coordinatorId);

        // Filter by status if provided
        if ($status) {
            $schedules = array_filter($schedules, function($schedule) use ($status) {
                return $schedule['status'] === $status;
            });
        }

        $data = [
            'role' => $session->get('role'),
            'title' => 'Delivery Schedules',
            'schedules' => $schedules,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
        ];

        return view('reusables/sidenav', $data) . view('logistics_coordinator/delivery_schedules', $data);
    }

    // Get schedule details
    public function getScheduleDetails(int $scheduleId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        // Get schedule with related PO, supplier, and branch info
        $schedule = $this->deliveryScheduleModel->select('delivery_schedules.*, purchase_orders.id as po_id, suppliers.supplier_name, branches.branch_name')
            ->join('purchase_orders', 'purchase_orders.id = delivery_schedules.po_id')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
            ->join('branches', 'branches.id = purchase_orders.branch_id')
            ->where('delivery_schedules.id', $scheduleId)
            ->first();

        if (!$schedule) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Schedule not found']);
        }

        // Get detailed PO information
        $po = $this->purchaseOrderModel->getDetails($schedule['po_id']);
        if ($po) {
            $schedule['po_details'] = $po;
        }

        return $this->response->setJSON($schedule);
    }

    // Update schedule status
    public function updateScheduleStatus(int $scheduleId): ResponseInterface
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Logistics Coordinator') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $data = $this->request->getJSON(true);
        $status = $data['status'] ?? null;
        $notes = $data['notes'] ?? null;
        $routeSequence = $data['route_sequence'] ?? null;

        $updateData = [];
        
        if ($status) {
            $updateData['status'] = $status;
        }
        
        if ($notes !== null) {
            $updateData['notes'] = $notes;
        }
        
        if ($routeSequence !== null) {
            $updateData['route_sequence'] = (int)$routeSequence;
        }

        if (empty($updateData)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'At least one field (status, notes, or route_sequence) is required']);
        }

        try {
            if (isset($updateData['status'])) {
                $this->deliveryScheduleModel->updateScheduleStatus($scheduleId, $updateData['status'], $updateData['notes'] ?? null);
            } else {
                // Update other fields if status is not being updated
                $this->deliveryScheduleModel->update($scheduleId, $updateData);
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Schedule updated successfully']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to update schedule: ' . $e->getMessage()]);
        }
    }

    

    // Active Deliveries Page
    public function activeDeliveries()
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

        // Get active deliveries (In Progress status)
        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $allDeliveries = $this->deliveryScheduleModel->getSchedulesByDateRange($today, $nextWeek, $coordinatorId);
        
        // Filter active deliveries
        $activeDeliveries = array_filter($allDeliveries, function($delivery) {
            return in_array($delivery['status'], ['Scheduled', 'In Progress']);
        });

        $data = [
            'role' => $session->get('role'),
            'title' => 'Active Deliveries',
            'activeDeliveries' => $activeDeliveries,
        ];

        return view('reusables/sidenav', $data) . view('logistics_coordinator/active_deliveries', $data);
    }

    // Performance Reports Page
    public function performanceReports()
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

        // Get date range for reports
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        // Get all schedules for the period
        $allSchedules = $this->deliveryScheduleModel->getSchedulesByDateRange($startDate, $endDate, $coordinatorId);

        // Calculate performance metrics
        $totalSchedules = count($allSchedules);
        $completedSchedules = count(array_filter($allSchedules, fn($s) => $s['status'] === 'Completed'));
        $inProgressSchedules = count(array_filter($allSchedules, fn($s) => $s['status'] === 'In Progress'));
        $cancelledSchedules = count(array_filter($allSchedules, fn($s) => $s['status'] === 'Cancelled'));
        
        // Calculate on-time delivery rate (simplified - checking if completed on scheduled date)
        $onTimeDeliveries = 0;
        foreach ($allSchedules as $schedule) {
            if ($schedule['status'] === 'Completed') {
                $scheduledDate = date('Y-m-d', strtotime($schedule['scheduled_date']));
                $po = $this->purchaseOrderModel->find($schedule['po_id']);
                if ($po && $po['actual_delivery_date']) {
                    $actualDate = date('Y-m-d', strtotime($po['actual_delivery_date']));
                    if ($actualDate <= $scheduledDate) {
                        $onTimeDeliveries++;
                    }
                }
            }
        }

        $completionRate = $totalSchedules > 0 ? round(($completedSchedules / $totalSchedules) * 100, 2) : 0;
        $onTimeRate = $completedSchedules > 0 ? round(($onTimeDeliveries / $completedSchedules) * 100, 2) : 0;

        // Get schedules by status for chart
        $statusBreakdown = [
            'Scheduled' => count(array_filter($allSchedules, fn($s) => $s['status'] === 'Scheduled')),
            'In Progress' => $inProgressSchedules,
            'Completed' => $completedSchedules,
            'Cancelled' => $cancelledSchedules,
        ];

        // Get daily delivery counts
        $dailyCounts = [];
        foreach ($allSchedules as $schedule) {
            $date = $schedule['scheduled_date'];
            if (!isset($dailyCounts[$date])) {
                $dailyCounts[$date] = 0;
            }
            $dailyCounts[$date]++;
        }
        ksort($dailyCounts);

        $data = [
            'role' => $session->get('role'),
            'title' => 'Performance Reports',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSchedules' => $totalSchedules,
            'completedSchedules' => $completedSchedules,
            'inProgressSchedules' => $inProgressSchedules,
            'cancelledSchedules' => $cancelledSchedules,
            'completionRate' => $completionRate,
            'onTimeRate' => $onTimeRate,
            'onTimeDeliveries' => $onTimeDeliveries,
            'statusBreakdown' => $statusBreakdown,
            'dailyCounts' => $dailyCounts,
        ];

        return view('reusables/sidenav', $data) . view('logistics_coordinator/performance_reports', $data);
    }
}
