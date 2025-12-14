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

    <!-- Logistics Workflow - Purchase Orders -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Logistics Workflow - Purchase Orders</h6>
        <small>Manage the complete delivery process from approval to completion</small>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>PO ID</th>
                <th>Branch</th>
                <th>Supplier</th>
                <th>Expected Delivery</th>
                <th>Workflow Step</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($pendingPurchaseOrders)): ?>
                <?php foreach ($pendingPurchaseOrders as $po): ?>
                  <tr>
                    <td><strong>#<?= esc($po['id']) ?></strong></td>
                    <td><?= esc($po['branch_name']) ?></td>
                    <td><?= esc($po['supplier_name']) ?></td>
                    <td><?= esc($po['expected_delivery_date']) ?></td>
                    <td>
                      <?php
                      $supplierStatus = $po['status'] ?? 'Pending';
                      $logisticsStatus = $po['logistics_status'] ?? 'pending_review';

                      // Determine workflow step based on logistics status (priority) or supplier status
                      if ($logisticsStatus === 'pending_review') {
                        $stepLabel = 'Step 1: Pending Review';
                        $stepClass = 'badge bg-secondary';
                      } elseif ($logisticsStatus === 'supplier_coordination') {
                        $stepLabel = 'Step 2: Supplier Coordination';
                        $stepClass = 'badge bg-info';
                      } elseif ($logisticsStatus === 'supplier_coordinated' || $logisticsStatus === 'ready_for_pickup') {
                        $stepLabel = 'Step 3: Ready to Schedule';
                        $stepClass = 'badge bg-primary';
                      } elseif ($logisticsStatus === 'delivery_scheduled') {
                        $stepLabel = 'Step 4: Delivery Scheduled';
                        $stepClass = 'badge bg-warning';
                      } elseif ($logisticsStatus === 'delivery_started') {
                        $stepLabel = 'Step 5: In Transit';
                        $stepClass = 'badge bg-info';
                      } elseif ($logisticsStatus === 'branch_notified') {
                        $stepLabel = 'Step 6: Branch Notified';
                        $stepClass = 'badge bg-success';
                      } elseif ($logisticsStatus === 'completed') {
                        $stepLabel = 'Completed';
                        $stepClass = 'badge bg-success';
                      } elseif ($supplierStatus === 'Pending') {
                        $stepLabel = 'Supplier: Pending Confirmation';
                        $stepClass = 'badge bg-warning';
                      } elseif ($supplierStatus === 'Confirmed') {
                        $stepLabel = 'Supplier: Confirmed';
                        $stepClass = 'badge bg-info';
                      } elseif ($supplierStatus === 'Preparing') {
                        $stepLabel = 'Supplier: Preparing';
                        $stepClass = 'badge bg-primary';
                      } elseif ($supplierStatus === 'Ready for Pickup') {
                        $stepLabel = 'Ready for Logistics';
                        $stepClass = 'badge bg-success';
                      } else {
                        $stepLabel = 'Status: ' . $supplierStatus;
                        $stepClass = 'badge bg-secondary';
                      }
                      ?>
                      <span class="badge <?= $stepClass ?>">
                        <?= $stepLabel ?>
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-warning">In Progress</span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewPODetails(<?= $po['id'] ?>)">
                          <i class="bi bi-eye"></i> View
                        </button>
                        <?php
                        // Show actions based on logistics_status (workflow stage)
                        if ($logisticsStatus === 'pending_review' || ($logisticsStatus === 'supplier_confirmed' && $supplierStatus === 'Ready for Pickup')): ?>
                          <button class="btn btn-outline-success btn-sm" onclick="startLogisticsProcess(<?= $po['id'] ?>)">
                            <i class="bi bi-play-circle"></i> Start Logistics
                          </button>
                        <?php elseif ($logisticsStatus === 'supplier_coordination'): ?>
                          <button class="btn btn-outline-info btn-sm" onclick="coordinateSupplier(<?= $po['id'] ?>)">
                            <i class="bi bi-handshake"></i> Coordinate Supplier
                          </button>
                        <?php elseif ($logisticsStatus === 'supplier_coordinated' || $logisticsStatus === 'ready_for_pickup'): ?>
                          <button class="btn btn-outline-success btn-sm" onclick="scheduleDelivery(<?= $po['id'] ?>)">
                            <i class="bi bi-calendar-check"></i> Schedule Delivery
                          </button>
                        <?php elseif ($logisticsStatus === 'delivery_scheduled'): ?>
                          <button class="btn btn-outline-warning btn-sm" onclick="startDelivery(<?= $po['id'] ?>)">
                            <i class="bi bi-truck"></i> Start Delivery
                          </button>
                        <?php elseif ($logisticsStatus === 'delivery_started'): ?>
                          <button class="btn btn-outline-info btn-sm" onclick="notifyBranch(<?= $po['id'] ?>)">
                            <i class="bi bi-bell"></i> Notify Branch
                          </button>
                        <?php elseif ($logisticsStatus === 'branch_notified'): ?>
                          <button class="btn btn-outline-success btn-sm" onclick="updateDeliveryStatus(<?= $po['id'] ?>)">
                            <i class="bi bi-check-circle"></i> Mark Delivered
                          </button>
                        <?php elseif ($supplierStatus === 'Preparing'): ?>
                          <button class="btn btn-outline-info btn-sm" onclick="monitorSupplier(<?= $po['id'] ?>)">
                            <i class="bi bi-eye"></i> Monitor
                          </button>
                        <?php elseif ($supplierStatus === 'Confirmed'): ?>
                          <button class="btn btn-outline-warning btn-sm" onclick="contactSupplier(<?= $po['id'] ?>)">
                            <i class="bi bi-telephone"></i> Contact
                          </button>
                        <?php else: ?>
                          <button class="btn btn-outline-secondary btn-sm" onclick="viewPODetails(<?= $po['id'] ?>)">
                            <i class="bi bi-info-circle"></i> Info
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No pending purchase orders in logistics workflow
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Today's Schedules -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-info text-white">
        <h6 class="mb-0"><i class="bi bi-calendar-day me-2"></i>Today's Schedules</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th>Time</th>
                <th>PO ID</th>
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
                    <td><strong>#<?= esc($schedule['po_id'] ?? 'N/A') ?></strong></td>
                    <td><?= esc($schedule['supplier_name'] ?? 'N/A') ?></td>
                    <td><?= esc($schedule['branch_name'] ?? 'N/A') ?></td>
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
                <tr><td colspan="6" class="text-center text-muted py-4">No schedules for today</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
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
                <th>PO ID</th>
                <th>Supplier</th>
                <th>Branch</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($upcomingDeliveries)): ?>
                <?php foreach ($upcomingDeliveries as $schedule): ?>
                  <?php 
                  $scheduleDate = strtotime($schedule['scheduled_date']);
                  $isToday = date('Y-m-d', $scheduleDate) === date('Y-m-d');
                  ?>
                  <?php if (!$isToday): ?>
                    <tr>
                      <td><?= esc(date('M d, Y', $scheduleDate)) ?></td>
                      <td><?= esc(date('H:i', strtotime($schedule['scheduled_time']))) ?></td>
                      <td><strong>#<?= esc($schedule['po_id'] ?? 'N/A') ?></strong></td>
                      <td><?= esc($schedule['supplier_name'] ?? 'N/A') ?></td>
                      <td><?= esc($schedule['branch_name'] ?? 'N/A') ?></td>
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
                  <?php endif; ?>
                <?php endforeach; ?>
                <?php if (empty(array_filter($upcomingDeliveries, function($s) { return date('Y-m-d', strtotime($s['scheduled_date'])) !== date('Y-m-d'); }))): ?>
                  <tr><td colspan="7" class="text-center text-muted py-4">No upcoming deliveries scheduled</td></tr>
                <?php endif; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No upcoming deliveries scheduled</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PO Details Modal -->
<div class="modal fade" id="poDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Purchase Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="poDetailsContent">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- Supplier Coordination Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Coordinate with Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="supplierForm">
          <input type="hidden" id="supplierPoId" name="po_id">
          <div class="mb-3">
            <label class="form-label">Supplier Confirmed</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="supplierConfirmed" name="supplier_confirmed">
              <label class="form-check-label">Supplier has confirmed availability</label>
            </div>
          </div>
          <div class="mb-3">
            <label for="pickupDate" class="form-label">Pickup Date</label>
            <input type="date" class="form-control" id="pickupDate" name="pickup_date" required>
          </div>
          <div class="mb-3">
            <label for="supplierNotes" class="form-label">Notes</label>
            <textarea class="form-control" id="supplierNotes" name="notes" rows="3"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Confirm Supplier Coordination</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delivery Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Schedule Delivery</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="scheduleForm">
          <input type="hidden" id="schedulePoId" name="po_id">
          <div class="mb-3">
            <label for="scheduledDate" class="form-label">Scheduled Date</label>
            <input type="date" class="form-control" id="scheduledDate" name="scheduled_date" required>
          </div>
          <div class="mb-3">
            <label for="scheduledTime" class="form-label">Scheduled Time</label>
            <input type="time" class="form-control" id="scheduledTime" name="scheduled_time" required>
          </div>
          <button type="submit" class="btn btn-primary">Create Delivery Schedule</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Branch Notification Modal -->
<div class="modal fade" id="branchModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Notify Branch</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="branchForm">
          <input type="hidden" id="branchPoId" name="po_id">
          <div class="mb-3">
            <label class="form-label">Branch Notified</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="branchNotified" name="branch_notified">
              <label class="form-check-label">Branch has been notified about incoming delivery</label>
            </div>
          </div>
          <div class="mb-3">
            <label for="estimatedArrival" class="form-label">Estimated Arrival</label>
            <input type="datetime-local" class="form-control" id="estimatedArrival" name="estimated_arrival" required>
          </div>
          <div class="mb-3">
            <label for="contactPerson" class="form-label">Contact Person</label>
            <input type="text" class="form-control" id="contactPerson" name="contact_person" required>
          </div>
          <button type="submit" class="btn btn-primary">Confirm Branch Notification</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Close Delivery Modal -->
<div class="modal fade" id="closeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Close Delivery Record</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="closeForm">
          <input type="hidden" id="closePoId" name="po_id">
          <div class="mb-3">
            <label class="form-label">Branch Confirmation</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="branchConfirmation" name="branch_confirmation">
              <label class="form-check-label">Branch has confirmed receipt of delivery</label>
            </div>
          </div>
          <div class="mb-3">
            <label for="finalNotes" class="form-label">Final Notes</label>
            <textarea class="form-control" id="finalNotes" name="final_notes" rows="3" placeholder="Any final notes about the delivery..."></textarea>
          </div>
          <button type="submit" class="btn btn-success">Close Delivery Record</button>
        </form>
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
        <!-- Content will be loaded dynamically -->
      </div>
    </div>
  </div>
</div>

<script>
// Workflow action functions
function viewPODetails(poId) {
  fetch(`<?= site_url('logistics-coordinator/po-details/') ?>${poId}`)
    .then(response => response.json())
    .then(data => {
      if (data.id) {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
          itemsHtml = `
            <div class="row mt-3">
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
          data.items.forEach(item => {
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

        let content = `
          <div class="row">
            <div class="col-md-6">
              <h6>PO Information</h6>
              <p><strong>PO ID:</strong> #${data.id}</p>
              <p><strong>Branch:</strong> ${data.branch_name || 'N/A'}</p>
              <p><strong>Supplier:</strong> ${data.supplier_name || 'N/A'}</p>
              <p><strong>Status:</strong> ${data.status}</p>
              <p><strong>Total Amount:</strong> ₱${parseFloat(data.total_amount || 0).toLocaleString()}</p>
            </div>
            <div class="col-md-6">
              <h6>Workflow Status</h6>
              <p><strong>Current Step:</strong> ${data.workflow_status ? data.workflow_status.name : 'Unknown'}</p>
              <p><strong>Step Number:</strong> ${data.workflow_status ? data.workflow_status.step : 'N/A'}</p>
              <p><strong>Expected Delivery:</strong> ${data.expected_delivery_date || 'N/A'}</p>
              <p><strong>Logistics Status:</strong> ${data.logistics_status || 'N/A'}</p>
            </div>
          </div>
          ${itemsHtml}
          <div class="row mt-3">
            <div class="col-12">
              <h6>Delivery Notes</h6>
              <p>${data.delivery_notes || 'No notes available'}</p>
            </div>
          </div>
        `;
        document.getElementById('poDetailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('poDetailsModal')).show();
      } else {
        alert('Failed to load PO details');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to load PO details');
    });
}

function reviewPO(poId) {
  if (confirm('Mark this PO as reviewed and ready for supplier coordination?')) {
    fetch(`<?= site_url('logistics-coordinator/review-po/') ?>${poId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('PO reviewed successfully');
        location.reload();
      } else {
        alert('Failed to review PO: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to review PO');
    });
  }
}

function coordinateSupplier(poId) {
  document.getElementById('supplierPoId').value = poId;
  new bootstrap.Modal(document.getElementById('supplierModal')).show();
}

function scheduleDelivery(poId) {
  document.getElementById('schedulePoId').value = poId;
  new bootstrap.Modal(document.getElementById('scheduleModal')).show();
}

function startDelivery(poId) {
  if (confirm('Mark delivery as started?')) {
    fetch(`<?= site_url('logistics-coordinator/update-delivery-status/') ?>${poId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ status: 'in_transit' })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Delivery started successfully');
        location.reload();
      } else {
        alert('Failed to start delivery: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to start delivery');
    });
  }
}

function notifyBranch(poId) {
  document.getElementById('branchPoId').value = poId;
  new bootstrap.Modal(document.getElementById('branchModal')).show();
}

function closeDelivery(poId) {
  document.getElementById('closePoId').value = poId;
  new bootstrap.Modal(document.getElementById('closeModal')).show();
}

function createDelivery(poId) {
  window.location.href = `<?= site_url('logistics-coordinator/create-delivery/') ?>${poId}`;
}

function startLogisticsProcess(poId) {
  if (confirm('Start the logistics process for this order? This will begin delivery coordination.')) {
    fetch(`<?= site_url('logistics-coordinator/review-po/') ?>${poId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Logistics process started successfully');
        location.reload();
      } else {
        alert('Failed to start logistics process: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to start logistics process');
    });
  }
}

function monitorSupplier(poId) {
  alert('Monitoring supplier progress. The supplier is currently preparing the order.');
}

function contactSupplier(poId) {
  alert('Contact supplier functionality - to be implemented. You can reach out to the supplier directly.');
}

// Form submissions
document.getElementById('supplierForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const poId = formData.get('po_id');
  
  // Get form values
  const supplierConfirmed = document.getElementById('supplierConfirmed').checked;
  const pickupDate = document.getElementById('pickupDate').value;
  const notes = document.getElementById('supplierNotes').value || '';

  // Prepare data object
  const data = {
    supplier_confirmed: supplierConfirmed,
    pickup_date: pickupDate,
    notes: notes
  };

  fetch(`<?= site_url('logistics-coordinator/coordinate-supplier/') ?>${poId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(data)
  })
  .then(response => {
    // Check if response is ok
    if (!response.ok) {
      return response.text().then(text => {
        try {
          return JSON.parse(text);
        } catch (e) {
          throw new Error(text || 'Server error');
        }
      });
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      alert('Supplier coordination completed');
      bootstrap.Modal.getInstance(document.getElementById('supplierModal')).hide();
      location.reload();
    } else {
      alert('Failed to coordinate supplier: ' + (data.error || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to coordinate supplier: ' + (error.message || 'Unknown error'));
  });
});

document.getElementById('scheduleForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const poId = document.getElementById('schedulePoId').value;
  const scheduledDate = document.getElementById('scheduledDate').value;
  const scheduledTime = document.getElementById('scheduledTime').value;

  fetch(`<?= site_url('logistics-coordinator/create-delivery-schedule/') ?>${poId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
      scheduled_date: scheduledDate,
      scheduled_time: scheduledTime
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Delivery schedule created');
      bootstrap.Modal.getInstance(document.getElementById('scheduleModal')).hide();
      location.reload();
    } else {
      alert('Failed to create schedule: ' + (data.error || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to create schedule');
  });
});

document.getElementById('branchForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const poId = formData.get('po_id');

  fetch(`<?= site_url('logistics-coordinator/coordinate-branch/') ?>${poId}`, {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Branch notification completed');
      bootstrap.Modal.getInstance(document.getElementById('branchModal')).hide();
      location.reload();
    } else {
      alert('Failed to notify branch: ' + (data.error || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to notify branch');
  });
});

document.getElementById('closeForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const poId = formData.get('po_id');

  fetch(`<?= site_url('logistics-coordinator/close-delivery/') ?>${poId}`, {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Delivery record closed successfully');
      bootstrap.Modal.getInstance(document.getElementById('closeModal')).hide();
      location.reload();
    } else {
      alert('Failed to close delivery: ' + (data.error || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to close delivery');
  });
});

function updateDeliveryStatus(poId) {
  if (confirm('Mark this delivery as completed?')) {
    fetch(`<?= site_url('logistics-coordinator/update-delivery-status/') ?>${poId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ status: 'delivered' })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Delivery marked as completed successfully');
        location.reload();
      } else {
        alert('Failed to update delivery status: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to update delivery status');
    });
  }
}

// View schedule details
function viewScheduleDetails(scheduleId) {
  fetch(`<?= site_url('logistics-coordinator/schedule-details/') ?>${scheduleId}`)
    .then(response => response.json())
    .then(data => {
      if (data.id) {
        let poDetails = '';
        if (data.po_details) {
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
