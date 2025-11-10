<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Logistics Coordinator Dashboard</h1>
      <div class="text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)
      </div>
    </div>

    <!-- Performance Metrics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card border-primary">
          <div class="card-body text-center">
            <h5 class="card-title text-primary">Total Schedules</h5>
            <h3 class="mb-0"><?= esc($performanceMetrics['total_schedules']) ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-success">
          <div class="card-body text-center">
            <h5 class="card-title text-success">Completion Rate</h5>
            <h3 class="mb-0"><?= esc($performanceMetrics['completion_rate']) ?>%</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-info">
          <div class="card-body text-center">
            <h5 class="card-title text-info">On-Time Rate</h5>
            <h3 class="mb-0"><?= esc($performanceMetrics['on_time_rate']) ?>%</h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-warning">
          <div class="card-body text-center">
            <h5 class="card-title text-warning">Notifications</h5>
            <h3 class="mb-0" id="notification-count"><?= esc($unreadNotifications) ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="row">
      <!-- Pending Purchase Orders -->
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-warning text-white">
            <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Pending Purchase Orders</h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table mb-0">
                <thead class="table-light">
                  <tr>
                    <th>PO ID</th>
                    <th>Supplier</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($pendingPurchaseOrders)): ?>
                    <?php foreach ($pendingPurchaseOrders as $po): ?>
                      <tr>
                        <td><?= esc($po['id']) ?></td>
                        <td><?= esc($po['supplier_name']) ?></td>
                        <td><?= esc($po['branch_name']) ?></td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary" onclick="viewPODetails(<?= $po['id'] ?>)">
                            <i class="bi bi-eye"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No pending purchase orders</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Today's Schedules -->
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="bi bi-calendar-day me-2"></i>Today's Schedules</h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Time</th>
                    <th>Supplier</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($todaySchedules)): ?>
                    <?php foreach ($todaySchedules as $schedule): ?>
                      <tr>
                        <td><?= esc(date('H:i', strtotime($schedule['scheduled_time']))) ?></td>
                        <td><?= esc($schedule['supplier_name']) ?></td>
                        <td><?= esc($schedule['branch_name']) ?></td>
                        <td>
                          <span class="badge bg-<?= $schedule['status'] == 'Completed' ? 'success' : ($schedule['status'] == 'In Progress' ? 'warning' : 'secondary') ?>">
                            <?= esc($schedule['status']) ?>
                          </span>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary" onclick="viewScheduleDetails(<?= $schedule['id'] ?>)">
                            <i class="bi bi-eye"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No schedules for today</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Upcoming Deliveries -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-success text-white">
        <h6 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Upcoming Deliveries (Next 7 Days)</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Supplier</th>
                <th>Branch</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($upcomingDeliveries)): ?>
                <?php foreach ($upcomingDeliveries as $delivery): ?>
                  <tr>
                    <td><?= esc(date('M d, Y', strtotime($delivery['scheduled_date']))) ?></td>
                    <td><?= esc(date('H:i', strtotime($delivery['scheduled_time']))) ?></td>
                    <td><?= esc($delivery['supplier_name']) ?></td>
                    <td><?= esc($delivery['branch_name']) ?></td>
                    <td>
                      <span class="badge bg-<?= $delivery['status'] == 'Completed' ? 'success' : ($delivery['status'] == 'In Progress' ? 'warning' : 'secondary') ?>">
                        <?= esc($delivery['status']) ?>
                      </span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewScheduleDetails(<?= $delivery['id'] ?>)">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="updateDeliveryStatus(<?= $delivery['delivery_id'] ?>, 'In Transit')">
                          <i class="bi bi-play"></i>
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="updateDeliveryStatus(<?= $delivery['delivery_id'] ?>, 'Delivered')">
                          <i class="bi bi-check2"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No upcoming deliveries</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow-sm">
      <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Quick Actions</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <button class="btn btn-primary w-100 mb-2" onclick="openScheduleModal()">
              <i class="bi bi-calendar-plus me-2"></i>Schedule Deliveries
            </button>
          </div>
          <div class="col-md-4">
            <button class="btn btn-info w-100 mb-2" onclick="openCalendarView()">
              <i class="bi bi-calendar me-2"></i>Calendar View
            </button>
          </div>
          <div class="col-md-4">
            <button class="btn btn-warning w-100 mb-2" onclick="viewNotifications()">
              <i class="bi bi-bell me-2"></i>View Notifications
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Schedule Deliveries Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Schedule Deliveries</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="scheduleForm">
          <div class="mb-3">
            <label class="form-label">Select Purchase Orders</label>
            <div id="poList" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
              <!-- PO list will be loaded here -->
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label class="form-label">Scheduled Date</label>
              <input type="date" class="form-control" id="scheduledDate" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Scheduled Time</label>
              <input type="time" class="form-control" id="scheduledTime" value="09:00">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="scheduleDeliveries()">Schedule</button>
      </div>
    </div>
  </div>
</div>

<!-- Calendar Modal -->
<div class="modal fade" id="calendarModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delivery Calendar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>

<script>
// Load pending POs for scheduling
function loadPendingPOs() {
  fetch('<?= site_url('logistics-coordinator/get-pending-pos') ?>')
    .then(response => response.json())
    .then(data => {
      const poList = document.getElementById('poList');
      poList.innerHTML = '';

      if (data.length === 0) {
        poList.innerHTML = '<p class="text-muted">No pending purchase orders available</p>';
        return;
      }

      data.forEach(po => {
        const checkbox = document.createElement('div');
        checkbox.className = 'form-check';
        checkbox.innerHTML = `
          <input class="form-check-input" type="checkbox" value="${po.id}" id="po${po.id}">
          <label class="form-check-label" for="po${po.id}">
            PO #${po.id} - ${po.supplier_name} → ${po.branch_name}
          </label>
        `;
        poList.appendChild(checkbox);
      });
    });
}

// Open schedule modal
function openScheduleModal() {
  loadPendingPOs();
  new bootstrap.Modal(document.getElementById('scheduleModal')).show();
}

// Schedule deliveries
function scheduleDeliveries() {
  const selectedPOs = Array.from(document.querySelectorAll('#poList input:checked')).map(cb => parseInt(cb.value));
  const scheduledDate = document.getElementById('scheduledDate').value;
  const scheduledTime = document.getElementById('scheduledTime').value;

  if (selectedPOs.length === 0) {
    alert('Please select at least one purchase order');
    return;
  }

  if (!scheduledDate) {
    alert('Please select a scheduled date');
    return;
  }

  fetch('<?= site_url('logistics-coordinator/schedule-deliveries') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
      delivery_ids: selectedPOs,
      scheduled_date: scheduledDate,
      scheduled_time: scheduledTime
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Deliveries scheduled successfully!');
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  });
}

// Update delivery status
function updateDeliveryStatus(deliveryId, status) {
  if (!confirm(`Are you sure you want to mark this delivery as ${status}?`)) {
    return;
  }

  fetch(`<?= site_url('logistics-coordinator/update-delivery-status/') ?>${deliveryId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ status: status })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Delivery status updated successfully!');
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  });
}

// View schedule details
function viewScheduleDetails(scheduleId) {
  // Implementation for viewing schedule details
  alert('Schedule details view - to be implemented');
}

// View PO details
function viewPODetails(poId) {
  // Implementation for viewing PO details
  alert('PO details view - to be implemented');
}

// Open calendar view
function openCalendarView() {
  new bootstrap.Modal(document.getElementById('calendarModal')).show();
  // Initialize calendar - requires FullCalendar library
  // For now, just show the modal
}

// View notifications
function viewNotifications() {
  fetch('<?= site_url('logistics-coordinator/get-notifications') ?>')
    .then(response => response.json())
    .then(notifications => {
      let notificationHtml = '<div class="list-group">';
      if (notifications.length === 0) {
        notificationHtml += '<p class="text-muted p-3">No notifications</p>';
      } else {
        notifications.forEach(notification => {
          notificationHtml += `
            <div class="list-group-item">
              <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1">${notification.title}</h6>
                <small>${new Date(notification.created_at).toLocaleDateString()}</small>
              </div>
              <p class="mb-1">${notification.message}</p>
            </div>
          `;
        });
      }
      notificationHtml += '</div>';

      // Show in a modal or update a section
      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Notifications</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">${notificationHtml}</div>
          </div>
        </div>
      `;
      document.body.appendChild(modal);
      new bootstrap.Modal(modal).show();
    });
}

// Update notification count periodically
setInterval(() => {
  fetch('<?= site_url('logistics-coordinator/get-notifications') ?>')
    .then(response => response.json())
    .then(notifications => {
      const unreadCount = notifications.filter(n => n.status === 'pending').length;
      document.getElementById('notification-count').textContent = unreadCount;
    });
}, 30000); // Update every 30 seconds
</script>
