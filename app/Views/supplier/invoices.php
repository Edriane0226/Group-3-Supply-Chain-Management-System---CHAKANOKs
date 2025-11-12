<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Invoices & Payments</h1>
      <div class="text-muted small">
        <?= esc(session()->get('supplier_name')) ?> (Supplier)
      </div>
    </div>

    <!-- Invoices Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-success text-white">
        <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>My Invoices</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Order ID</th>
                <th>Branch Name</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($invoices)): ?>
                <?php foreach ($invoices as $invoice): ?>
                  <tr>
                    <td><strong>#<?= esc($invoice['id']) ?></strong></td>
                    <td><?= esc($invoice['branch_name']) ?></td>
                    <td>₱<?= number_format($invoice['total_amount'], 2) ?></td>
                    <td>
                      <span class="badge bg-success">Completed</span>
                    </td>
                    <td>
                      <button class="btn btn-outline-primary btn-sm" onclick="viewInvoice(<?= $invoice['id'] ?>)">
                        <i class="bi bi-eye"></i> View
                      </button>
                      <button class="btn btn-outline-secondary btn-sm" onclick="printInvoice(<?= $invoice['id'] ?>)">
                        <i class="bi bi-printer"></i> Print
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                    No completed orders for invoicing
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
function viewInvoice(orderId) {
  // For now, just show basic info
  fetch(`<?= site_url('supplier/order-details/') ?>${orderId}`)
    .then(response => response.json())
    .then(data => {
      if (data.id) {
        let content = `
          <div class="row">
            <div class="col-12">
              <h6>Invoice for Order #${data.id}</h6>
              <p><strong>Branch:</strong> ${data.branch_name || 'N/A'}</p>
              <p><strong>Total Amount:</strong> ₱${parseFloat(data.total_amount).toLocaleString()}</p>
              <p><strong>Status:</strong> ${data.status}</p>
              <p><strong>Completed:</strong> ${data.updated_at ? new Date(data.updated_at).toLocaleDateString() : 'N/A'}</p>
            </div>
          </div>
        `;
        // You could show a modal here
        alert('Invoice details:\n' + content.replace(/<[^>]*>/g, ''));
      } else {
        alert('Failed to load invoice details');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to load invoice details');
    });
}

function printInvoice(orderId) {
  // Simple print functionality
  window.open(`<?= site_url('supplier/print-invoice/') ?>${orderId}`, '_blank');
}
</script>
