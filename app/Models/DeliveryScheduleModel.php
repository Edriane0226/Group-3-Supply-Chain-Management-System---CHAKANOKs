<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryScheduleModel extends Model
{
    protected $table            = 'delivery_schedules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'delivery_id',
        'logistics_coordinator_id',
        'scheduled_date',
        'scheduled_time',
        'route_sequence',
        'estimated_duration',
        'route_coordinates',
        'status',
        'notes',
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

    // Create delivery schedule
    public function createSchedule(array $data): int
    {
        $scheduleData = [
            'delivery_id' => $data['delivery_id'],
            'logistics_coordinator_id' => $data['logistics_coordinator_id'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'route_sequence' => $data['route_sequence'] ?? 1,
            'estimated_duration' => $data['estimated_duration'] ?? null,
            'route_coordinates' => isset($data['route_coordinates']) ? json_encode($data['route_coordinates']) : null,
            'status' => $data['status'] ?? 'Scheduled',
            'notes' => $data['notes'] ?? null,
        ];

        $this->insert($scheduleData);
        return $this->insertID();
    }

    // Get schedules for a date range
    public function getSchedulesByDateRange(string $startDate, string $endDate, ?int $coordinatorId = null): array
    {
        $builder = $this->select('delivery_schedules.*, deliveries.supplier_name, deliveries.delivery_date, branches.branch_name')
                        ->join('deliveries', 'deliveries.id = delivery_schedules.delivery_id')
                        ->join('branches', 'branches.id = deliveries.branch_id')
                        ->where('delivery_schedules.scheduled_date >=', $startDate)
                        ->where('delivery_schedules.scheduled_date <=', $endDate)
                        ->orderBy('delivery_schedules.scheduled_date', 'ASC')
                        ->orderBy('delivery_schedules.scheduled_time', 'ASC');

        if ($coordinatorId) {
            $builder->where('delivery_schedules.logistics_coordinator_id', $coordinatorId);
        }

        return $builder->findAll();
    }

    // Get schedules for logistics coordinator
    public function getCoordinatorSchedules(int $coordinatorId, ?string $status = null): array
    {
        $builder = $this->select('delivery_schedules.*, deliveries.supplier_name, deliveries.delivery_date, branches.branch_name')
                        ->join('deliveries', 'deliveries.id = delivery_schedules.delivery_id')
                        ->join('branches', 'branches.id = deliveries.branch_id')
                        ->where('delivery_schedules.logistics_coordinator_id', $coordinatorId)
                        ->orderBy('delivery_schedules.scheduled_date', 'ASC')
                        ->orderBy('delivery_schedules.scheduled_time', 'ASC');

        if ($status) {
            $builder->where('delivery_schedules.status', $status);
        }

        return $builder->findAll();
    }

    // Update schedule status
    public function updateScheduleStatus(int $scheduleId, string $status, ?string $notes = null): bool
    {
        $updateData = ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')];

        if ($notes) {
            $updateData['notes'] = $notes;
        }

        return $this->update($scheduleId, $updateData);
    }

    // Optimize routes for multiple deliveries (simplified algorithm)
    public function optimizeRoutes(array $deliveryIds, int $coordinatorId): array
    {
        // Simple route optimization - sort by branch location (placeholder)
        // In real implementation, would use Google Maps API for actual optimization

        $deliveries = $this->db->table('deliveries')
                              ->select('deliveries.*, branches.branch_name')
                              ->join('branches', 'branches.id = deliveries.branch_id')
                              ->whereIn('deliveries.id', $deliveryIds)
                              ->orderBy('branches.branch_name', 'ASC') // Simple sorting
                              ->get()
                              ->getResultArray();

        $schedules = [];
        $sequence = 1;

        foreach ($deliveries as $delivery) {
            $scheduleData = [
                'delivery_id' => $delivery['id'],
                'logistics_coordinator_id' => $coordinatorId,
                'scheduled_date' => date('Y-m-d'), // Today
                'scheduled_time' => date('H:i:s', strtotime('+'.($sequence * 2).' hours')), // 2 hours apart
                'route_sequence' => $sequence,
                'estimated_duration' => 60, // 1 hour default
                'status' => 'Scheduled',
            ];

            $scheduleId = $this->createSchedule($scheduleData);
            $schedules[] = $this->find($scheduleId);
            $sequence++;
        }

        return $schedules;
    }

    // Get delivery calendar data
    public function getCalendarData(string $startDate, string $endDate): array
    {
        return $this->select('delivery_schedules.*, deliveries.supplier_name, branches.branch_name')
                    ->join('deliveries', 'deliveries.id = delivery_schedules.delivery_id')
                    ->join('branches', 'branches.id = deliveries.branch_id')
                    ->where('delivery_schedules.scheduled_date >=', $startDate)
                    ->where('delivery_schedules.scheduled_date <=', $endDate)
                    ->orderBy('delivery_schedules.scheduled_date', 'ASC')
                    ->findAll();
    }
}
