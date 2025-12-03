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
                <th>Invoice Document</th>
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
                      <?php if (!empty($invoice['invoice_document_path'])): ?>
                        <span class="badge bg-success">
                          <i class="bi bi-check-circle me-1"></i>Uploaded
                        </span>
                        <?php if ($invoice['invoice_uploaded_at']): ?>
                          <br><small class="text-muted"><?= date('M d, Y', strtotime($invoice['invoice_uploaded_at'])) ?></small>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="badge bg-warning text-dark">
                          <i class="bi bi-exclamation-triangle me-1"></i>Not Uploaded
                        </span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($invoice['invoice_document_path'])): ?>
                        <a href="<?= site_url('supplier/download-invoice/' . $invoice['id']) ?>" class="btn btn-outline-success btn-sm" title="Download Invoice">
                          <i class="bi bi-download"></i> Download
                        </a>
                      <?php endif; ?>
                      <button class="btn btn-outline-primary btn-sm" onclick="openUploadModal(<?= $invoice['id'] ?>, '<?= esc($invoice['branch_name']) ?>')" title="Upload Invoice">
                        <i class="bi bi-upload"></i> <?= empty($invoice['invoice_document_path']) ? 'Upload' : 'Replace' ?>
                      </button>
                      <button class="btn btn-outline-secondary btn-sm" onclick="viewInvoice(<?= $invoice['id'] ?>)">
                        <i class="bi bi-eye"></i> View
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
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

<!-- Upload Invoice Modal -->
<div class="modal fade" id="uploadInvoiceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Invoice Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="uploadInvoiceForm" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="upload_order_id" name="order_id">
          <p><strong>Branch:</strong> <span id="upload_branch_name"></span></p>
          <p><small class="text-muted">Upload invoice document (PDF, Images, Excel, Word). Max file size: 5MB</small></p>
          
          <div class="mb-3">
            <label for="invoice_file" class="form-label">Invoice Document <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="invoice_file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.doc,.docx" required>
            <small class="form-text text-muted">Supported formats: PDF, JPG, PNG, Excel, Word</small>
          </div>
          
          <div id="upload_error" class="alert alert-danger d-none"></div>
          <div id="upload_success" class="alert alert-success d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-upload me-1"></i>Upload Invoice
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openUploadModal(orderId, branchName) {
  document.getElementById('upload_order_id').value = orderId;
  document.getElementById('upload_branch_name').textContent = branchName;
  document.getElementById('upload_error').classList.add('d-none');
  document.getElementById('upload_success').classList.add('d-none');
  document.getElementById('invoice_file').value = '';
  new bootstrap.Modal(document.getElementById('uploadInvoiceModal')).show();
}

// Handle form submission
document.getElementById('uploadInvoiceForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const orderId = formData.get('order_id');
  const errorDiv = document.getElementById('upload_error');
  const successDiv = document.getElementById('upload_success');
  
  errorDiv.classList.add('d-none');
  successDiv.classList.add('d-none');
  
  try {
    const response = await fetch('<?= site_url('supplier/upload-invoice') ?>', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    
    const data = await response.json();
    
    if (data.success) {
      successDiv.textContent = data.message || 'Invoice uploaded successfully!';
      successDiv.classList.remove('d-none');
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      errorDiv.textContent = data.error || 'Failed to upload invoice';
      errorDiv.classList.remove('d-none');
    }
  } catch (error) {
    errorDiv.textContent = 'An error occurred while uploading the invoice. Please try again.';
    errorDiv.classList.remove('d-none');
    console.error('Upload error:', error);
  }
});

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
