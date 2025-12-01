<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'old_data',
        'new_data',
        'created_at',
    ];
    protected $useTimestamps = false;

    /**
     * Log an activity
     */
    public function logActivity(string $action, string $module = null, string $description = null, array $oldData = null, array $newData = null): int
    {
        $session = session();
        $request = service('request');

        $data = [
            'user_id'     => $session->get('user_id'),
            'user_name'   => $session->get('full_name') ?? $session->get('supplier_name'),
            'user_role'   => $session->get('role'),
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => $request->getUserAgent()->getAgentString(),
            'old_data'    => $oldData ? json_encode($oldData) : null,
            'new_data'    => $newData ? json_encode($newData) : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $this->insert($data);
        return $this->insertID();
    }

    /**
     * Get logs with filters
     */
    public function getLogs(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $builder = $this->builder();

        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $builder->where('action', $filters['action']);
        }

        if (!empty($filters['module'])) {
            $builder->where('module', $filters['module']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('user_name', $filters['search'])
                    ->orLike('description', $filters['search'])
                    ->orLike('action', $filters['search'])
                    ->groupEnd();
        }

        return $builder->orderBy('created_at', 'DESC')
                       ->limit($limit, $offset)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get total logs count with filters
     */
    public function getLogsCount(array $filters = []): int
    {
        $builder = $this->builder();

        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $builder->where('action', $filters['action']);
        }

        if (!empty($filters['module'])) {
            $builder->where('module', $filters['module']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        return $builder->countAllResults();
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get activities by user
     */
    public function getByUser(int $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get unique actions for filter dropdown
     */
    public function getUniqueActions(): array
    {
        return $this->select('action')
                    ->distinct()
                    ->orderBy('action', 'ASC')
                    ->findAll();
    }

    /**
     * Get unique modules for filter dropdown
     */
    public function getUniqueModules(): array
    {
        return $this->select('module')
                    ->distinct()
                    ->where('module IS NOT NULL')
                    ->orderBy('module', 'ASC')
                    ->findAll();
    }

    /**
     * Get activity statistics
     */
    public function getStatistics(): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        return [
            'total'       => $this->countAll(),
            'today'       => $this->where('DATE(created_at)', $today)->countAllResults(),
            'this_month'  => $this->like('created_at', $thisMonth, 'after')->countAllResults(),
            'logins'      => $this->where('action', 'login')->countAllResults(),
            'today_logins' => $this->where('action', 'login')->where('DATE(created_at)', $today)->countAllResults(),
        ];
    }

    /**
     * Clear old logs
     */
    public function clearOldLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        
        return $this->where('created_at <', $cutoffDate)->delete();
    }
}

