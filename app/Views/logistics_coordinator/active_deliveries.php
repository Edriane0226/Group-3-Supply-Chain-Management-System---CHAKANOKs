<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Active Deliveries</h1>
      <div class="text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card border-primary">
          <div class="card-body text-center">
            <h5 class="card-title text-primary">Total Active</h5>
            <h3 class="mb-0"><?= count($activeDeliveries) ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-warning">
          <div class="card-body text-center">
            <h5 class="card-title text-warning">Scheduled</h5>
            <h3 class="mb-0"><?= count(array_filter($activeDeliveries, fn($d) => $d['status'] === 'Scheduled')) ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-info">
          <div class="card-body text-center">
            <h5 class="card-title text-info">In Progress</h5>
            <h3 class="mb-0"><?= count(array_filter($activeDeliveries, fn($d) => $d['status'] === 'In Progress')) ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Deliveries Table -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-warning text-white">
        <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Active Delivery Schedules</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Schedule ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>PO ID</th>
                <th>Supplier</th>
                <th>Branch</th>
                <th>Status</th>
                <th>Route Sequence</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($activeDeliveries)): ?>
                <?php 
                // Sort by date and time
                usort($activeDeliveries, function($a, $b) {
                  $dateCompare = strcmp($a['scheduled_date'], $b['scheduled_date']);
                  if ($dateCompare === 0) {
                    return strcmp($a['scheduled_time'], $b['scheduled_time']);
                  }
                  return $dateCompare;
                });
                ?>
                <?php foreach ($activeDeliveries as $delivery): ?>
                  <tr>
                    <td><strong>#<?= esc($delivery['id']) ?></strong></td>
                    <td><?= esc(date('M d, Y', strtotime($delivery['scheduled_date']))) ?></td>
                    <td><?= esc(date('H:i', strtotime($delivery['scheduled_time']))) ?></td>
                    <td><strong>#<?= esc($delivery['po_id'] ?? 'N/A') ?></strong></td>
                    <td><?= esc($delivery['supplier_name'] ?? 'N/A') ?></td>
                    <td><?= esc($delivery['branch_name'] ?? 'N/A') ?></td>
                    <td>
                      <span class="badge bg-<?= 
                        $delivery['status'] === 'In Progress' ? 'warning' : 'secondary'
                      ?>">
                        <?= esc($delivery['status']) ?>
                      </span>
                    </td>
                    <td><?= esc($delivery['route_sequence'] ?? 'N/A') ?></td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewScheduleDetails(<?= $delivery['id'] ?>)">
                          <i class="bi bi-eye"></i> View
                        </button>
                        <?php if ($delivery['status'] === 'Scheduled'): ?>
                          <button class="btn btn-outline-warning btn-sm" onclick="updateScheduleStatus(<?= $delivery['id'] ?>, 'In Progress')">
                            <i class="bi bi-play-circle"></i> Start
                          </button>
                        <?php elseif ($delivery['status'] === 'In Progress'): ?>
                          <button class="btn btn-outline-success btn-sm" onclick="updateScheduleStatus(<?= $delivery['id'] ?>, 'Completed')">
                            <i class="bi bi-check-circle"></i> Complete
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No active deliveries at the moment
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function viewScheduleDetails(scheduleId) {
  fetch(`<?= site_url('logistics-coordinator/schedule-details/') ?>${scheduleId}`)
    .then(response => response.json())
    .then(data => {
      if (data.id) {
        alert(`Schedule #${data.id}\nDate: ${new Date(data.scheduled_date).toLocaleDateString()}\nTime: ${new Date('1970-01-01T' + data.scheduled_time).toLocaleTimeString()}\nStatus: ${data.status}\nPO: #${data.po_id}`);
      } else {
        alert('Failed to load schedule details');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to load schedule details');
    });
}

function updateScheduleStatus(scheduleId, newStatus) {
  const action = newStatus === 'In Progress' ? 'start' : 'complete';
  if (confirm(`Are you sure you want to ${action} this delivery?`)) {
    fetch(`<?= site_url('logistics-coordinator/update-schedule-status/') ?>${scheduleId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(`Delivery ${action}ed successfully`);
        location.reload();
      } else {
        alert('Failed to update delivery: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to update delivery');
    });
  }
}
</script>

