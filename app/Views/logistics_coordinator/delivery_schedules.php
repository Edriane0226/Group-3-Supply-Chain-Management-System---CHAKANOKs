<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Delivery Schedules</h1>
      <div class="text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)
      </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form method="GET" action="<?= site_url('logistics-coordinator/delivery-schedules') ?>" class="row g-3">
          <div class="col-md-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate) ?>">
          </div>
          <div class="col-md-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate) ?>">
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status">
              <option value="">All Statuses</option>
              <option value="Scheduled" <?= $status === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
              <option value="In Progress" <?= $status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
              <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
              <option value="Cancelled" <?= $status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
              <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="<?= site_url('logistics-coordinator/delivery-schedules') ?>" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
          </div>
        </form>
      </div>
    </div>

    <!-- Delivery Schedules Table -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>All Delivery Schedules</h6>
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
              <?php if (!empty($schedules)): ?>
                <?php foreach ($schedules as $schedule): ?>
                  <tr>
                    <td><strong>#<?= esc($schedule['id']) ?></strong></td>
                    <td><?= esc(date('M d, Y', strtotime($schedule['scheduled_date']))) ?></td>
                    <td><?= esc(date('H:i', strtotime($schedule['scheduled_time']))) ?></td>
                    <td><strong>#<?= esc($schedule['po_id'] ?? 'N/A') ?></strong></td>
                    <td><?= esc($schedule['supplier_name'] ?? 'N/A') ?></td>
                    <td><?= esc($schedule['branch_name'] ?? 'N/A') ?></td>
                    <td>
                      <span class="badge bg-<?= 
                        ($schedule['status'] == 'Completed' || $schedule['status'] == 'Delivered') ? 'success' : 
                        ($schedule['status'] == 'In Progress' ? 'warning' : 
                        ($schedule['status'] == 'Cancelled' ? 'danger' : 'secondary')) 
                      ?>">
                        <?= esc($schedule['status']) ?>
                      </span>
                    </td>
                    <td><?= esc($schedule['route_sequence'] ?? 'N/A') ?></td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewScheduleDetails(<?= $schedule['id'] ?>)">
                          <i class="bi bi-eye"></i> View
                        </button>
                        <?php if ($schedule['status'] === 'Scheduled'): ?>
                          <button class="btn btn-outline-warning btn-sm" onclick="updateScheduleStatus(<?= $schedule['id'] ?>, 'In Progress')">
                            <i class="bi bi-play-circle"></i> Start
                          </button>
                        <?php elseif ($schedule['status'] === 'In Progress'): ?>
                          <button class="btn btn-outline-success btn-sm" onclick="updateScheduleStatus(<?= $schedule['id'] ?>, 'Delivered')">
                            <i class="bi bi-check-circle"></i> Mark Delivered
                          </button>
                        <?php endif; ?>
                        <?php if (in_array($schedule['status'], ['Scheduled', 'In Progress'])): ?>
                          <button class="btn btn-outline-danger btn-sm" onclick="cancelSchedule(<?= $schedule['id'] ?>)">
                            <i class="bi bi-x-circle"></i> Cancel
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
                    No delivery schedules found for the selected date range
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

<!-- Schedule Details Modal -->
<div class="modal fade" id="scheduleDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Schedule Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="scheduleDetailsContent">
        <!-- Content will be loaded here -->
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
        let poDetails = '';
        if (data.po_details) {
          // Build items table if available
          let itemsHtml = '';
          if (data.po_details.items && data.po_details.items.length > 0) {
            itemsHtml = `
              <div class="row mt-2">
                <div class="col-12">
                  <h6>Order Items</h6>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead class="table-light">
                        <tr>
                          <th>Item Name</th>
                          <th>Quantity</th>
                          <th>Unit</th>
                          <th>Description</th>
                        </tr>
                      </thead>
                      <tbody>
            `;
            data.po_details.items.forEach(item => {
              itemsHtml += `
                <tr>
                  <td>${item.item_name || 'N/A'}</td>
                  <td>${item.quantity || 0}</td>
                  <td>${item.unit || 'N/A'}</td>
                  <td>${item.description || 'No description'}</td>
                </tr>
              `;
            });
            itemsHtml += `
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            `;
          }

          poDetails = `
            <div class="row mt-3">
              <div class="col-12">
                <h6>Purchase Order Details</h6>
                <p><strong>PO ID:</strong> #${data.po_details.id}</p>
                <p><strong>Total Amount:</strong> ₱${parseFloat(data.po_details.total_amount || 0).toLocaleString()}</p>
                <p><strong>PO Status:</strong> ${data.po_details.status}</p>
                <p><strong>Logistics Status:</strong> ${data.po_details.logistics_status}</p>
              </div>
            </div>
            ${itemsHtml}
          `;
        }

        let content = `
          <div class="row">
            <div class="col-md-6">
              <h6>Schedule Information</h6>
              <p><strong>Schedule ID:</strong> #${data.id}</p>
              <p><strong>Scheduled Date:</strong> ${new Date(data.scheduled_date).toLocaleDateString()}</p>
              <p><strong>Scheduled Time:</strong> ${new Date('1970-01-01T' + data.scheduled_time).toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}</p>
              <p><strong>Status:</strong> <span class="badge bg-${data.status === 'Completed' ? 'success' : (data.status === 'In Progress' ? 'warning' : 'secondary')}">${data.status}</span></p>
              <p><strong>Route Sequence:</strong> ${data.route_sequence || 'N/A'}</p>
            </div>
            <div class="col-md-6">
              <h6>Delivery Information</h6>
              <p><strong>Supplier:</strong> ${data.supplier_name || 'N/A'}</p>
              <p><strong>Branch:</strong> ${data.branch_name || 'N/A'}</p>
              <p><strong>Expected Delivery Date:</strong> ${data.po_details?.expected_delivery_date || 'N/A'}</p>
            </div>
          </div>
          ${poDetails}
          ${data.notes ? `<div class="row mt-3"><div class="col-12"><h6>Notes</h6><p>${data.notes}</p></div></div>` : ''}
        `;
        document.getElementById('scheduleDetailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('scheduleDetailsModal')).show();
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
  if (confirm(`Are you sure you want to ${action} this delivery schedule?`)) {
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
        alert(`Schedule ${action}ed successfully`);
        location.reload();
      } else {
        alert('Failed to update schedule: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to update schedule');
    });
  }
}

function cancelSchedule(scheduleId) {
  if (confirm('Are you sure you want to cancel this delivery schedule?')) {
    fetch(`<?= site_url('logistics-coordinator/update-schedule-status/') ?>${scheduleId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ status: 'Cancelled' })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Schedule cancelled successfully');
        location.reload();
      } else {
        alert('Failed to cancel schedule: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to cancel schedule');
    });
  }
}
</script>

