<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Route Optimization</h1>
      <div class="text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)
      </div>
    </div>

    <!-- Route Optimization Info -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Optimize Delivery Routes</h6>
      </div>
      <div class="card-body">
        <p class="text-muted">Select multiple scheduled deliveries to optimize routes and reduce travel time.</p>
        
        <?php if (empty($scheduledDeliveries)): ?>
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>No scheduled deliveries available for optimization.
          </div>
        <?php else: ?>
          <form id="optimizeRouteForm">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>
                      <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                    </th>
                    <th>PO ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Supplier</th>
                    <th>Branch</th>
                    <th>Route Sequence</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($scheduledDeliveries as $delivery): ?>
                    <tr>
                      <td>
                        <input type="checkbox" class="delivery-checkbox" name="po_ids[]" value="<?= esc($delivery['po_id']) ?>" data-schedule-id="<?= esc($delivery['id']) ?>">
                      </td>
                      <td><strong>#<?= esc($delivery['po_id']) ?></strong></td>
                      <td><?= esc(date('M d, Y', strtotime($delivery['scheduled_date']))) ?></td>
                      <td><?= esc(date('H:i', strtotime($delivery['scheduled_time']))) ?></td>
                      <td><?= esc($delivery['supplier_name']) ?></td>
                      <td><?= esc($delivery['branch_name']) ?></td>
                      <td><?= esc($delivery['route_sequence'] ?? 'Not set') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <div class="mt-3">
              <button type="button" class="btn btn-primary" onclick="optimizeRoutes()">
                <i class="bi bi-geo-alt me-2"></i>Optimize Selected Routes
              </button>
              <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                <i class="bi bi-x-circle me-2"></i>Clear Selection
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- Optimization Results -->
    <div class="card shadow-sm mb-4" id="optimizationResults" style="display: none;">
      <div class="card-header bg-success text-white">
        <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Optimized Route</h6>
      </div>
      <div class="card-body">
        <div id="optimizedRouteContent">
          <!-- Optimized route will be displayed here -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function toggleAll(checkbox) {
  const checkboxes = document.querySelectorAll('.delivery-checkbox');
  checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function clearSelection() {
  document.querySelectorAll('.delivery-checkbox').forEach(cb => cb.checked = false);
  document.getElementById('selectAll').checked = false;
  document.getElementById('optimizationResults').style.display = 'none';
}

function optimizeRoutes() {
  const selectedCheckboxes = document.querySelectorAll('.delivery-checkbox:checked');
  
  if (selectedCheckboxes.length < 2) {
    alert('Please select at least 2 deliveries to optimize routes.');
    return;
  }

  const scheduleIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.scheduleId);
  const poIds = Array.from(selectedCheckboxes).map(cb => cb.value);

  // Simple optimization: sort by branch name (in real implementation, would use Google Maps API)
  const rows = Array.from(selectedCheckboxes).map(cb => {
    const row = cb.closest('tr');
    return {
      scheduleId: cb.dataset.scheduleId,
      poId: cb.value,
      branch: row.cells[5].textContent.trim(),
      supplier: row.cells[4].textContent.trim(),
      date: row.cells[2].textContent.trim(),
      time: row.cells[3].textContent.trim()
    };
  });

  // Sort by branch name (simple optimization)
  rows.sort((a, b) => a.branch.localeCompare(b.branch));

  // Display optimized route
  let optimizedHtml = '<h6>Optimized Route Sequence:</h6><ol class="list-group list-group-numbered">';
  rows.forEach((row, index) => {
    optimizedHtml += `
      <li class="list-group-item d-flex justify-content-between align-items-start">
        <div class="ms-2 me-auto">
          <div class="fw-bold">Stop ${index + 1}: ${row.branch}</div>
          <small>PO #${row.poId} - ${row.supplier}</small><br>
          <small class="text-muted">${row.date} at ${row.time}</small>
        </div>
        <span class="badge bg-primary rounded-pill">${index + 1}</span>
      </li>
    `;
  });
  optimizedHtml += '</ol>';

  optimizedHtml += `
    <div class="mt-3">
      <button class="btn btn-success" onclick="applyOptimization([${scheduleIds.join(',')}], [${rows.map((r, i) => i + 1).join(',')}])">
        <i class="bi bi-check-circle me-2"></i>Apply Optimization
      </button>
      <button class="btn btn-outline-secondary" onclick="document.getElementById('optimizationResults').style.display='none'">
        <i class="bi bi-x-circle me-2"></i>Cancel
      </button>
    </div>
  `;

  document.getElementById('optimizedRouteContent').innerHTML = optimizedHtml;
  document.getElementById('optimizationResults').style.display = 'block';
}

function applyOptimization(scheduleIds, sequences) {
  if (confirm('Apply this route optimization? This will update the route sequence for selected deliveries.')) {
    // Update route sequences
    const updates = scheduleIds.map((scheduleId, index) => {
      return fetch(`<?= site_url('logistics-coordinator/update-schedule-status/') ?>${scheduleId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
          route_sequence: sequences[index]
        })
      });
    });

    Promise.all(updates)
      .then(responses => Promise.all(responses.map(r => r.json())))
      .then(results => {
        if (results.every(r => r.success)) {
          alert('Route optimization applied successfully!');
          location.reload();
        } else {
          alert('Some updates failed. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to apply optimization');
      });
  }
}
</script>

