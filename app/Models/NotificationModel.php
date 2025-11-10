<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'type',
        'title',
        'message',
        'reference_type',
        'reference_id',
        'status',
        'sent_at',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Create notification
    public function createNotification(array $data): int
    {
        $notificationData = [
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'reference_type' => $data['reference_type'],
            'reference_id' => $data['reference_id'],
            'status' => 'pending',
        ];

        $this->insert($notificationData);
        return $this->insertID();
    }

    // Get notifications for user
    public function getUserNotifications(int $userId, ?string $status = null): array
    {
        $builder = $this->where('user_id', $userId)
                        ->orderBy('created_at', 'DESC');

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->findAll();
    }

    // Mark notification as sent
    public function markAsSent(int $notificationId): bool
    {
        return $this->update($notificationId, [
            'status' => 'sent',
            'sent_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // Mark notification as failed
    public function markAsFailed(int $notificationId): bool
    {
        return $this->update($notificationId, [
            'status' => 'failed',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // Send notification (placeholder for actual implementation)
    public function sendNotification(int $notificationId): bool
    {
        $notification = $this->find($notificationId);
        if (!$notification) {
            return false;
        }

        // Placeholder for actual sending logic
        // In real implementation, would integrate with email/SMS APIs

        if ($notification['type'] === 'email') {
            // Send email logic here
            return $this->markAsSent($notificationId);
        } elseif ($notification['type'] === 'sms') {
            // Send SMS logic here
            return $this->markAsSent($notificationId);
        } elseif ($notification['type'] === 'in_app') {
            // In-app notification - just mark as sent
            return $this->markAsSent($notificationId);
        }

        return false;
    }

    // Create notification for status change
    public function notifyStatusChange(string $referenceType, int $referenceId, string $oldStatus, string $newStatus, array $userIds): void
    {
        $messages = [
            'purchase_request' => [
                'approved' => 'Your purchase request has been approved.',
                'cancelled' => 'Your purchase request has been cancelled.',
            ],
            'purchase_order' => [
                'approved' => 'Purchase order has been approved and is ready for delivery.',
                'in_transit' => 'Purchase order is now in transit.',
                'delivered' => 'Purchase order has been delivered.',
                'pending_logistics' => 'Purchase order is pending logistics review.',
            ],
            'delivery' => [
                'Approved' => 'Delivery has been approved and scheduled.',
                'In Transit' => 'Delivery is now in transit.',
                'Delivered' => 'Delivery has been completed.',
            ],
        ];

        $title = ucfirst($referenceType) . ' Status Update';
        $message = $messages[$referenceType][$newStatus] ?? "Status changed from {$oldStatus} to {$newStatus}";

        foreach ($userIds as $userId) {
            $this->createNotification([
                'user_id' => $userId,
                'type' => 'in_app',
                'title' => $title,
                'message' => $message,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        }
    }

    // Notify logistics coordinators about new approved PO
    public function notifyLogisticsCoordinator(string $eventType, int $referenceId, array $coordinatorIds): void
    {
        $messages = [
            'new_po_ready' => 'A new purchase order is ready for logistics coordination.',
            'supplier_coordinated' => 'Supplier coordination completed for purchase order.',
            'delivery_scheduled' => 'Delivery has been scheduled for purchase order.',
            'delivery_started' => 'Delivery has started for purchase order.',
            'branch_notified' => 'Branch has been notified about incoming delivery.',
            'delivery_completed' => 'Delivery has been completed and verified.',
        ];

        $titles = [
            'new_po_ready' => 'New Purchase Order Ready',
            'supplier_coordinated' => 'Supplier Coordination Complete',
            'delivery_scheduled' => 'Delivery Scheduled',
            'delivery_started' => 'Delivery Started',
            'branch_notified' => 'Branch Notified',
            'delivery_completed' => 'Delivery Completed',
        ];

        $title = $titles[$eventType] ?? 'Logistics Update';
        $message = $messages[$eventType] ?? 'Logistics workflow update.';

        foreach ($coordinatorIds as $coordinatorId) {
            $this->createNotification([
                'user_id' => $coordinatorId,
                'type' => 'in_app',
                'title' => $title,
                'message' => $message,
                'reference_type' => 'purchase_order',
                'reference_id' => $referenceId,
            ]);
        }
    }

    // Get unread notifications count
    public function getUnreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'pending')
                    ->countAllResults();
    }

    // Bulk send pending notifications
    public function sendPendingNotifications(): array
    {
        $pendingNotifications = $this->where('status', 'pending')->findAll();
        $results = ['sent' => 0, 'failed' => 0];

        foreach ($pendingNotifications as $notification) {
            if ($this->sendNotification($notification['id'])) {
                $results['sent']++;
            } else {
                $this->markAsFailed($notification['id']);
                $results['failed']++;
            }
        }

        return $results;
    }
}
