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
        'po_id',
        'coordinator_id',
        'driver_id',
        'vehicle_id',
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
            'po_id' => $data['po_id'],
            'coordinator_id' => $data['coordinator_id'],
            'driver_id' => $data['driver_id'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
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

    // Update schedule status (and optional notes)
    public function updateScheduleStatus(int $scheduleId, string $status, ?string $notes = null): bool
    {
        $updateData = ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')];

        if ($notes !== null) {
            $updateData['notes'] = $notes;
        }

        return $this->update($scheduleId, $updateData);
    }

    // Get schedules for a date range
    public function getSchedulesByDateRange(string $startDate, string $endDate, ?int $coordinatorId = null): array
    {
        $builder = $this->select([
                            'delivery_schedules.*',
                            'purchase_orders.id as po_id',
                            'purchase_orders.branch_id',
                            'purchase_orders.expected_delivery_date',
                            'purchase_orders.actual_delivery_date',
                            'suppliers.supplier_name',
                            'branches.branch_name',
                            'coordinators.first_Name as coordinator_first_name',
                            'coordinators.last_Name as coordinator_last_name',
                            'drivers.first_Name as driver_first_name',
                            'drivers.last_Name as driver_last_name',
                        ])
                        ->join('purchase_orders', 'purchase_orders.id = delivery_schedules.po_id')
                        ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                        ->join('branches', 'branches.id = purchase_orders.branch_id')
                        ->join('users as coordinators', 'coordinators.id = delivery_schedules.coordinator_id', 'left')
                        ->join('users as drivers', 'drivers.id = delivery_schedules.driver_id', 'left')
                        ->where('delivery_schedules.scheduled_date >=', $startDate)
                        ->where('delivery_schedules.scheduled_date <=', $endDate)
                        ->orderBy('delivery_schedules.scheduled_date', 'ASC')
                        ->orderBy('delivery_schedules.scheduled_time', 'ASC');

        if ($coordinatorId) {
            $builder->where('delivery_schedules.coordinator_id', $coordinatorId);
        }

        return $builder->findAll();
    }

    // Get schedules for logistics coordinator
    public function getCoordinatorSchedules(int $coordinatorId, ?string $status = null): array
    {
        $builder = $this->select('delivery_schedules.*, purchase_orders.id as po_id, suppliers.supplier_name, branches.branch_name')
                        ->join('purchase_orders', 'purchase_orders.id = delivery_schedules.po_id')
                        ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                        ->join('branches', 'branches.id = purchase_orders.branch_id')
                        ->where('delivery_schedules.coordinator_id', $coordinatorId)
                        ->orderBy('delivery_schedules.scheduled_date', 'ASC')
                        ->orderBy('delivery_schedules.scheduled_time', 'ASC');

        if ($status) {
            $builder->where('delivery_schedules.status', $status);
        }

        return $builder->findAll();
    }

    // Get delivery calendar data
    public function getCalendarData(string $startDate, string $endDate): array
    {
        return $this->select('delivery_schedules.*, purchase_orders.id as po_id, suppliers.supplier_name, branches.branch_name')
                    ->join('purchase_orders', 'purchase_orders.id = delivery_schedules.po_id')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                    ->join('branches', 'branches.id = purchase_orders.branch_id')
                    ->where('delivery_schedules.scheduled_date >=', $startDate)
                    ->where('delivery_schedules.scheduled_date <=', $endDate)
                    ->orderBy('delivery_schedules.scheduled_date', 'ASC')
                    ->findAll();
    }

    public function getBranchUpcomingDeliveries(int $branchId, string $startDate, string $endDate): array
    {
        return $this->select([
                        'delivery_schedules.*',
                        'purchase_orders.branch_id',
                        'purchase_orders.id as po_id',
                        'purchase_orders.expected_delivery_date',
                        'purchase_orders.actual_delivery_date',
                        'suppliers.supplier_name',
                        'branches.branch_name',
                        'coordinators.first_Name as coordinator_first_name',
                        'coordinators.last_Name as coordinator_last_name',
                        'drivers.first_Name as driver_first_name',
                        'drivers.last_Name as driver_last_name',
                    ])
                    ->join('purchase_orders', 'purchase_orders.id = delivery_schedules.po_id')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                    ->join('branches', 'branches.id = purchase_orders.branch_id')
                    ->join('users as coordinators', 'coordinators.id = delivery_schedules.coordinator_id', 'left')
                    ->join('users as drivers', 'drivers.id = delivery_schedules.driver_id', 'left')
                    ->where('purchase_orders.branch_id', $branchId)
                    ->where('delivery_schedules.scheduled_date >=', $startDate)
                    ->where('delivery_schedules.scheduled_date <=', $endDate)
                    ->orderBy('delivery_schedules.scheduled_date', 'ASC')
                    ->orderBy('delivery_schedules.scheduled_time', 'ASC')
                    ->findAll();
    }

    public function getCentralDeliveryOverview(string $startDate, string $endDate): array
    {
        return $this->select([
                        'delivery_schedules.*',
                        'purchase_orders.branch_id',
                        'purchase_orders.id as po_id',
                        'purchase_orders.expected_delivery_date',
                        'purchase_orders.actual_delivery_date',
                        'suppliers.supplier_name',
                        'branches.branch_name',
                        'coordinators.first_Name as coordinator_first_name',
                        'coordinators.last_Name as coordinator_last_name',
                        'drivers.first_Name as driver_first_name',
                        'drivers.last_Name as driver_last_name',
                    ])
                    ->join('purchase_orders', 'purchase_orders.id = delivery_schedules.po_id')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                    ->join('branches', 'branches.id = purchase_orders.branch_id')
                    ->join('users as coordinators', 'coordinators.id = delivery_schedules.coordinator_id', 'left')
                    ->join('users as drivers', 'drivers.id = delivery_schedules.driver_id', 'left')
                    ->where('delivery_schedules.scheduled_date >=', $startDate)
                    ->where('delivery_schedules.scheduled_date <=', $endDate)
                    ->orderBy('delivery_schedules.scheduled_date', 'ASC')
                    ->orderBy('delivery_schedules.scheduled_time', 'ASC')
                    ->findAll();
    }

    public function getScheduleWithRelations(int $scheduleId): ?array
    {
        return $this->select('delivery_schedules.*, purchase_orders.branch_id, purchase_orders.purchase_request_id, purchase_orders.id as po_id, suppliers.supplier_name, branches.branch_name')
                    ->join('purchase_orders', 'purchase_orders.id = delivery_schedules.po_id')
                    ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                    ->join('branches', 'branches.id = purchase_orders.branch_id')
                    ->where('delivery_schedules.id', $scheduleId)
                    ->first();
    }
}
