<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeliverySchedulesSeeder extends Seeder
{
    public function run()
    {
        // Get purchase orders that have deliveries or are ready for scheduling
        $purchaseOrders = $this->db->table('purchase_orders')
            ->whereIn('status', ['Delivered', 'In_Transit', 'Approved'])
            ->orderBy('id', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();
        
        // Get logistics coordinator user
        $logisticsCoordinator = $this->db->table('users')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.role_name', 'Logistics Coordinator')
            ->get()
            ->getRow();
        
        if (!$logisticsCoordinator) {
            // Fallback to user ID 23116003 (from UserSeeder)
            $coordinatorId = 23116003;
        } else {
            $coordinatorId = $logisticsCoordinator->id;
        }
        
        $data = [];
        $statuses = ['Scheduled', 'In Progress', 'Completed', 'Scheduled'];
        $times = ['09:00:00', '10:30:00', '14:00:00', '11:00:00'];
        
        foreach ($purchaseOrders as $index => $po) {
            $scheduledDate = $po['expected_delivery_date'] ?? $po['actual_delivery_date'] ?? date('Y-m-d', strtotime('+' . ($index + 1) . ' days'));
            $scheduledTime = $times[min($index, count($times) - 1)];
            $status = $statuses[min($index, count($statuses) - 1)];
            
            $data[] = [
                'po_id' => $po['id'],
                'coordinator_id' => $coordinatorId,
                'driver_id' => null, // Can be filled later
                'vehicle_id' => null, // Can be filled later
                'scheduled_date' => $scheduledDate,
                'scheduled_time' => $scheduledTime,
                'route_sequence' => $index + 1,
                'estimated_duration' => 30 + ($index * 15), // 30, 45, 60, 75 minutes
                'route_coordinates' => null, // Can be filled with actual coordinates if needed
                'status' => $status,
                'notes' => $status === 'Completed' ? 'Delivery completed successfully' : 'Scheduled for delivery',
                'created_at' => $po['created_at'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($data)) {
            $this->db->table('delivery_schedules')->insertBatch($data);
        }
    }
}

