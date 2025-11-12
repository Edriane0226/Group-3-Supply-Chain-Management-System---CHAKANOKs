<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Purchase Orders</h1>
      <div class="text-muted small">
        <?= esc(session()->get('supplier_name')) ?> (Supplier)
      </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>My Purchase Orders</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Order ID</th>
                <th>Branch Name</th>
                <th>Date Requested</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                  <tr>
                    <td><strong>#<?= esc($order['id']) ?></strong></td>
                    <td><?= esc($order['branch_name']) ?></td>
                    <td><?= esc(date('M d, Y', strtotime($order['created_at']))) ?></td>
                    <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                    <td>
                      <?php
                      $statusClass = [
                        'Pending' => 'badge bg-warning',
                        'Confirmed' => 'badge bg-info',
                        'Preparing' => 'badge bg-primary',
                        'Ready for Pickup' => 'badge bg-secondary',
                        'Delivered' => 'badge bg-success'
                      ];
                      ?>
                      <span class="badge <?= $statusClass[$order['status']] ?? 'badge bg-secondary' ?>">
                        <?= esc($order['status']) ?>
                      </span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewOrderDetails(<?= $order['id'] ?>)">
                          <i class="bi bi-eye"></i> View
                        </button>
                        <?php if ($order['status'] === 'Pending'): ?>
                          <button class="btn btn-outline-success btn-sm" onclick="updateStatus(<?= $order['id'] ?>, 'Confirmed')">
                            <i class="bi bi-check-circle"></i> Confirm
                          </button>
                        <?php elseif ($order['status'] === 'Confirmed'): ?>
                          <button class="btn btn-outline-info btn-sm" onclick="updateStatus(<?= $order['id'] ?>, 'Preparing')">
                            <i class="bi bi-gear"></i> Prepare
                          </button>
                        <?php elseif ($order['status'] === 'Preparing'): ?>
                          <button class="btn btn-outline-warning btn-sm" onclick="updateStatus(<?= $order['id'] ?>, 'Ready for Pickup')">
                            <i class="bi bi-truck"></i> Ready
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No purchase orders assigned
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

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="orderDetailsContent">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script>
function viewOrderDetails(orderId) {
  console.log('Fetching order details for ID:', orderId);
  fetch(`<?= site_url('supplier/order-details/') ?>${orderId}`)
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        throw new Error('HTTP error! status: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      console.log('Received data:', data);
      if (data.error) {
        alert('Error: ' + data.error);
        return;
      }

      if (data.id) {
        let itemsHtml = '';
        if (data.items && data.items.length > 0) {
          itemsHtml = `
            <div class="row mt-3">
              <div class="col-12">
                <h6>Requested Items</h6>
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
              <h6>Order Information</h6>
              <p><strong>Order ID:</strong> #${data.id}</p>
              <p><strong>Branch:</strong> ${data.branch_name || 'N/A'}</p>
              <p><strong>Status:</strong> ${data.status}</p>
              <p><strong>Total Amount:</strong> ₱${parseFloat(data.total_amount || 0).toLocaleString()}</p>
            </div>
            <div class="col-md-6">
              <h6>Dates</h6>
              <p><strong>Requested:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
              <p><strong>Last Updated:</strong> ${data.updated_at ? new Date(data.updated_at).toLocaleDateString() : 'N/A'}</p>
              <p><strong>Expected Delivery:</strong> ${data.expected_delivery_date || 'N/A'}</p>
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
        document.getElementById('orderDetailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
      } else {
        alert('Failed to load order details: Invalid response format');
      }
    })
    .catch(error => {
      console.error('Error loading order details:', error);
      alert('Failed to load order details: ' + error.message);
    });
}

function updateStatus(orderId, status) {
  if (confirm(`Update order status to "${status}"?`)) {
    const formData = new FormData();
    formData.append('status', status);

    fetch(`<?= site_url('supplier/update-order-status/') ?>${orderId}`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    .then(response => {
      console.log('Response status:', response.status);
      console.log('Response headers:', response.headers);
      if (!response.ok) {
        throw new Error('HTTP error! status: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      console.log('Response data:', data);
      if (data.success) {
        alert('Order status updated successfully');
        location.reload();
      } else {
        alert('Failed to update status: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to update status: ' + error.message);
    });
  }
}
</script>
