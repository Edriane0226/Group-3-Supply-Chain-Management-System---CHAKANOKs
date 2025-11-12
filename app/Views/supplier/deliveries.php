<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Delivery Management</h1>
      <div class="text-muted small">
        <?= esc(session()->get('supplier_name')) ?> (Supplier)
      </div>
    </div>

    <!-- Deliveries Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-warning text-white">
        <h6 class="mb-0"><i class="bi bi-truck me-2"></i>My Deliveries</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Order ID</th>
                <th>Branch Name</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($deliveries)): ?>
                <?php foreach ($deliveries as $delivery): ?>
                  <tr>
                    <td><strong>#<?= esc($delivery['id']) ?></strong></td>
                    <td><?= esc($delivery['branch_name']) ?></td>
                    <td>
                      <span class="badge bg-secondary">
                        <?= esc($delivery['status']) ?>
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-outline-primary btn-sm" onclick="viewDeliveryDetails(<?= $delivery['id'] ?>)">
                        <i class="bi bi-eye"></i> View
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-truck fs-1 d-block mb-2"></i>
                    No deliveries found
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

<!-- Delivery Details Modal -->
<div class="modal fade" id="deliveryDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delivery Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="deliveryDetailsContent">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script>
function viewDeliveryDetails(deliveryId) {
  // For now, just show basic info
  fetch(`<?= site_url('supplier/delivery-details/') ?>${deliveryId}`)
    .then(response => response.json())
    .then(data => {
      if (data.id) {
        let content = `
          <div class="row">
            <div class="col-md-6">
              <h6>Delivery Information</h6>
              <p><strong>Delivery ID:</strong> #${data.id}</p>
              <p><strong>Order ID:</strong> #${data.order_id}</p>
              <p><strong>Branch:</strong> ${data.branch_name || 'N/A'}</p>
              <p><strong>Status:</strong> ${data.status}</p>
            </div>
            <div class="col-md-6">
              <h6>Tracking</h6>
              <p><strong>Created:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
              <p><strong>Updated:</strong> ${data.updated_at ? new Date(data.updated_at).toLocaleDateString() : 'N/A'}</p>
            </div>
          </div>
        `;
        document.getElementById('deliveryDetailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('deliveryDetailsModal')).show();
      } else {
        alert('Failed to load delivery details');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to load delivery details');
    });
}
</script>
