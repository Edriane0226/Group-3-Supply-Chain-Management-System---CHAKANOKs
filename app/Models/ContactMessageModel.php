<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactMessageModel extends Model
{
    protected $table            = 'contact_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'read_at',
        'read_by',
    ];

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
    protected $beforeFind      = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get unread messages count
     */
    public function getUnreadCount(): int
    {
        return $this->where('status', 'unread')->countAllResults();
    }

    /**
     * Get all messages with pagination
     */
    public function getMessages($status = null, $limit = 20, $offset = 0)
    {
        $builder = $this->select('contact_messages.*, users.first_Name, users.last_Name as admin_last_name')
                       ->join('users', 'users.id = contact_messages.read_by', 'left');

        if ($status) {
            $builder->where('contact_messages.status', $status);
        }

        return $builder->orderBy('contact_messages.created_at', 'DESC')
                      ->limit($limit, $offset)
                      ->get()
                      ->getResultArray();
    }

    /**
     * Mark message as read
     */
    public function markAsRead(int $messageId, int $userId): bool
    {
        return $this->update($messageId, [
            'status'  => 'read',
            'read_at' => date('Y-m-d H:i:s'),
            'read_by' => $userId,
        ]);
    }

    /**
     * Update message status
     */
    public function updateStatus(int $messageId, string $status): bool
    {
        return $this->update($messageId, ['status' => $status]);
    }

    /**
     * Get message by ID
     */
    public function getMessage(int $id)
    {
        return $this->select('contact_messages.*, users.first_Name, users.last_Name as admin_last_name')
                   ->join('users', 'users.id = contact_messages.read_by', 'left')
                   ->where('contact_messages.id', $id)
                   ->first();
    }
}

