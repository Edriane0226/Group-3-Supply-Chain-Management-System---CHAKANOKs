<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Account Payable Details</h1>
      <a href="<?= site_url('supplier/accounts-payable') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
      </a>
    </div>

    <div class="row">
      <!-- Main Details -->
      <div class="col-md-8">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Invoice Information</h6>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Account Payable ID:</strong><br>
                <span class="text-muted">#<?= esc($ap['id']) ?></span>
              </div>
              <div class="col-md-6">
                <strong>Purchase Order ID:</strong><br>
                <span class="text-muted">PO-<?= esc($ap['purchase_order_id']) ?></span>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Branch:</strong><br>
                <span class="text-muted"><?= esc($purchaseOrder['branch_name'] ?? 'N/A') ?></span>
              </div>
              <div class="col-md-6">
                <strong>Invoice Date:</strong><br>
                <span class="text-muted">
                  <?= $ap['invoice_date'] ? date('F d, Y', strtotime($ap['invoice_date'])) : 'N/A' ?>
                </span>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Due Date:</strong><br>
                <?php if ($ap['due_date']): ?>
                  <?php
                    $dueDate = strtotime($ap['due_date']);
                    $today = strtotime(date('Y-m-d'));
                    $isOverdue = $dueDate < $today && $ap['payment_status'] !== 'paid';
                  ?>
                  <span class="text-muted <?= $isOverdue ? 'text-danger' : '' ?>">
                    <?= date('F d, Y', $dueDate) ?>
                  </span>
                  <?php if ($isOverdue): ?>
                    <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Overdue</small>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-muted">Not set</span>
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <strong>Payment Terms:</strong><br>
                <span class="text-muted"><?= esc($ap['payment_terms'] ?? 'N/A') ?></span>
              </div>
            </div>

            <hr>

            <div class="row">
              <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                  <p class="text-muted small mb-1">Invoice Amount</p>
                  <h4 class="mb-0">₱<?= number_format($ap['invoice_amount'], 2) ?></h4>
                </div>
              </div>
              <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                  <p class="text-muted small mb-1">Amount Paid</p>
                  <h4 class="mb-0 text-success">₱<?= number_format($ap['amount_paid'] ?? 0, 2) ?></h4>
                </div>
              </div>
              <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                  <p class="text-muted small mb-1">Balance Due</p>
                  <h4 class="mb-0 <?= $ap['balance_due'] > 0 ? 'text-danger' : 'text-success' ?>">
                    ₱<?= number_format($ap['balance_due'], 2) ?>
                  </h4>
                </div>
              </div>
            </div>

            <hr>

            <div class="mb-3">
              <strong>Payment Status:</strong><br>
              <?php
                $statusClass = 'secondary';
                $statusText = ucfirst($ap['payment_status']);
                
                switch($ap['payment_status']) {
                  case 'paid':
                    $statusClass = 'success';
                    break;
                  case 'partial':
                    $statusClass = 'info';
                    break;
                  case 'overdue':
                    $statusClass = 'danger';
                    break;
                  case 'pending':
                    $statusClass = 'warning';
                    break;
                }
              ?>
              <span class="badge bg-<?= $statusClass ?>" style="font-size: 1rem;"><?= $statusText ?></span>
            </div>

            <?php if ($ap['paid_date']): ?>
              <div class="mb-3">
                <strong>Paid Date:</strong><br>
                <span class="text-muted"><?= date('F d, Y', strtotime($ap['paid_date'])) ?></span>
              </div>
            <?php endif; ?>

            <?php if ($ap['payment_method']): ?>
              <div class="mb-3">
                <strong>Payment Method:</strong><br>
                <span class="text-muted"><?= ucfirst(str_replace('_', ' ', $ap['payment_method'])) ?></span>
              </div>
            <?php endif; ?>

            <?php if ($ap['payment_reference']): ?>
              <div class="mb-3">
                <strong>Payment Reference:</strong><br>
                <span class="text-muted"><?= esc($ap['payment_reference']) ?></span>
              </div>
            <?php endif; ?>

            <?php if ($ap['notes']): ?>
              <div class="mb-3">
                <strong>Notes:</strong><br>
                <div class="text-muted"><?= nl2br(esc($ap['notes'])) ?></div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Purchase Order Details -->
      <div class="col-md-4">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="bi bi-cart-check me-2"></i>Purchase Order Details</h6>
          </div>
          <div class="card-body">
            <?php if ($purchaseOrder): ?>
              <div class="mb-3">
                <strong>Order ID:</strong><br>
                <span class="text-muted">PO-<?= esc($purchaseOrder['id']) ?></span>
              </div>

              <div class="mb-3">
                <strong>Status:</strong><br>
                <?php
                  $poStatusClass = 'secondary';
                  switch($purchaseOrder['status']) {
                    case 'Delivered':
                      $poStatusClass = 'success';
                      break;
                    case 'In_Transit':
                      $poStatusClass = 'info';
                      break;
                    case 'Approved':
                      $poStatusClass = 'primary';
                      break;
                    case 'Pending':
                      $poStatusClass = 'warning';
                      break;
                    case 'Rejected':
                      $poStatusClass = 'danger';
                      break;
                  }
                ?>
                <span class="badge bg-<?= $poStatusClass ?>">
                  <?= esc($purchaseOrder['status']) ?>
                </span>
              </div>

              <div class="mb-3">
                <strong>Total Amount:</strong><br>
                <span class="text-muted">₱<?= number_format($purchaseOrder['total_amount'], 2) ?></span>
              </div>

              <?php if ($purchaseOrder['actual_delivery_date']): ?>
                <div class="mb-3">
                  <strong>Delivery Date:</strong><br>
                  <span class="text-muted">
                    <?= date('F d, Y', strtotime($purchaseOrder['actual_delivery_date'])) ?>
                  </span>
                </div>
              <?php endif; ?>

              <?php if ($purchaseOrder['invoice_document_path']): ?>
                <div class="mb-3">
                  <strong>Invoice Document:</strong><br>
                  <a href="<?= site_url('supplier/download-invoice/' . $purchaseOrder['id']) ?>" 
                     class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download me-1"></i>Download Invoice
                  </a>
                </div>
              <?php endif; ?>
            <?php else: ?>
              <p class="text-muted">Purchase order details not available.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

