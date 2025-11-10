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

        // Get pending purchase orders for scheduling
        $data['pendingPurchaseOrders'] = $this->purchaseOrderModel->getPendingForLogistics();

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

        if (!$data || !isset($data['delivery_ids']) || !isset($data['scheduled_date'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid data']);
        }

        $coordinatorId = (int)$session->get('user_id');
        $deliveryIds = $data['delivery_ids'];
        $scheduledDate = $data['scheduled_date'];
        $scheduledTime = $data['scheduled_time'] ?? date('H:i:s');

        try {
            // Optimize routes for selected deliveries
            $schedules = $this->deliveryScheduleModel->optimizeRoutes($deliveryIds, $coordinatorId);

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
                $delivery = $this->db->table('deliveries')
                                    ->select('actual_delivery_time')
                                    ->where('id', $schedule['delivery_id'])
                                    ->get()
                                    ->getRowArray();

                if ($delivery && $delivery['actual_delivery_time']) {
                    $actualTime = strtotime($delivery['actual_delivery_time']);
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
}
