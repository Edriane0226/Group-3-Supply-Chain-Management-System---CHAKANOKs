<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get unread notification count (AJAX endpoint)
     */
    public function count()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $userId = (int) $this->session->get('user_id');
        $count = $this->notificationModel->getUnreadCount($userId);

        return $this->response->setJSON(['count' => $count]);
    }

    /**
     * Get notifications list (AJAX endpoint)
     */
    public function getNotifications()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $userId = (int) $this->session->get('user_id');
        $status = $this->request->getGet('status'); // 'pending', 'sent', or null for all
        $limit = (int) ($this->request->getGet('limit') ?? 10);

        $notifications = $this->notificationModel->getUserNotifications($userId, $status);
        $notifications = array_slice($notifications, 0, $limit);

        return $this->response->setJSON($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markRead(int $id)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $userId = (int) $this->session->get('user_id');
        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Notification not found']);
        }

        $this->notificationModel->markAsSent($id);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $userId = (int) $this->session->get('user_id');
        $notifications = $this->notificationModel->getUserNotifications($userId, 'pending');

        foreach ($notifications as $notification) {
            $this->notificationModel->markAsSent($notification['id']);
        }

        return $this->response->setJSON(['success' => true, 'count' => count($notifications)]);
    }

    /**
     * View all notifications page
     */
    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $userId = (int) $this->session->get('user_id');
        $status = $this->request->getGet('status'); // Filter by status

        $notifications = $this->notificationModel->getUserNotifications($userId, $status);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        $data = [
            'role' => $this->session->get('role'),
            'title' => 'Notifications',
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'status' => $status,
        ];

        return view('reusables/sidenav', $data) . view('notifications/index', $data);
    }

    /**
     * View single notification
     */
    public function view(int $id)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $userId = (int) $this->session->get('user_id');
        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] != $userId) {
            return redirect()->to(site_url('notifications'))->with('error', 'Notification not found.');
        }

        // Mark as read
        if ($notification['status'] === 'pending') {
            $this->notificationModel->markAsSent($id);
        }

        $data = [
            'role' => $this->session->get('role'),
            'title' => 'Notification Details',
            'notification' => $notification,
        ];

        return view('reusables/sidenav', $data) . view('notifications/view', $data);
    }
}

