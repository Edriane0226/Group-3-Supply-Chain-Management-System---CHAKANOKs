<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">
        <i class="bi bi-file-earmark-dollar me-2"></i>Account Payable Details
      </h1>
      <a href="<?= site_url('accounts-payable') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to List
      </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="row">
      <!-- Account Payable Details -->
      <div class="col-md-8">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Invoice Information</h6>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Invoice #:</strong> #<?= esc($ap['id']) ?>
              </div>
              <div class="col-md-6">
                <strong>PO ID:</strong> PO-<?= esc($ap['purchase_order_id']) ?>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Supplier:</strong> <?= esc($supplier['supplier_name'] ?? 'N/A') ?>
              </div>
              <div class="col-md-6">
                <strong>Branch:</strong> <?= esc($purchaseOrder['branch_name'] ?? 'N/A') ?>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Invoice Amount:</strong> 
                <span class="text-primary fw-bold">₱<?= number_format($ap['invoice_amount'], 2) ?></span>
              </div>
              <div class="col-md-6">
                <strong>Amount Paid:</strong> 
                <span class="text-success">₱<?= number_format($ap['amount_paid'] ?? 0, 2) ?></span>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Balance Due:</strong> 
                <span class="text-danger fw-bold">₱<?= number_format($ap['balance_due'], 2) ?></span>
              </div>
              <div class="col-md-6">
                <strong>Status:</strong>
                <?php
                  $statusClass = 'secondary';
                  switch($ap['payment_status']) {
                    case 'paid': $statusClass = 'success'; break;
                    case 'partial': $statusClass = 'info'; break;
                    case 'overdue': $statusClass = 'danger'; break;
                    case 'pending': $statusClass = 'warning'; break;
                  }
                ?>
                <span class="badge bg-<?= $statusClass ?>"><?= ucfirst($ap['payment_status']) ?></span>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Invoice Date:</strong> 
                <?= $ap['invoice_date'] ? date('M d, Y', strtotime($ap['invoice_date'])) : 'N/A' ?>
              </div>
              <div class="col-md-6">
                <strong>Due Date:</strong> 
                <?php if ($ap['due_date']): ?>
                  <?= date('M d, Y', strtotime($ap['due_date'])) ?>
                  <?php if (strtotime($ap['due_date']) < strtotime(date('Y-m-d')) && $ap['payment_status'] !== 'paid'): ?>
                    <span class="badge bg-danger ms-2">Overdue</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-muted">Not set</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Payment Terms:</strong> <?= esc($ap['payment_terms'] ?? 'N/A') ?>
              </div>
              <div class="col-md-6">
                <strong>Paid Date:</strong> 
                <?= $ap['paid_date'] ? date('M d, Y', strtotime($ap['paid_date'])) : 'Not paid yet' ?>
              </div>
            </div>
            <?php if ($ap['payment_method']): ?>
              <div class="row mb-3">
                <div class="col-md-6">
                  <strong>Payment Method:</strong> <?= ucfirst(str_replace('_', ' ', $ap['payment_method'])) ?>
                </div>
                <div class="col-md-6">
                  <strong>Payment Reference:</strong> <?= esc($ap['payment_reference'] ?? 'N/A') ?>
                </div>
              </div>
            <?php endif; ?>
            <?php if ($ap['notes']): ?>
              <div class="row">
                <div class="col-12">
                  <strong>Notes:</strong>
                  <p class="text-muted"><?= nl2br(esc($ap['notes'])) ?></p>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Purchase Order Details -->
        <?php if ($purchaseOrder): ?>
          <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
              <h6 class="mb-0"><i class="bi bi-cart-check me-2"></i>Purchase Order Details</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <strong>PO Total:</strong> ₱<?= number_format($purchaseOrder['total_amount'] ?? 0, 2) ?>
                </div>
                <div class="col-md-6">
                  <strong>Delivery Date:</strong> 
                  <?= $purchaseOrder['actual_delivery_date'] ? date('M d, Y', strtotime($purchaseOrder['actual_delivery_date'])) : 'N/A' ?>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Payment Actions -->
      <div class="col-md-4">
        <?php if ($ap['balance_due'] > 0): ?>
          <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Record Payment</h6>
            </div>
            <div class="card-body">
              <form action="<?= site_url('accounts-payable/record-payment/' . $ap['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                  <label class="form-label">Balance Due</label>
                  <input type="text" class="form-control" value="₱<?= number_format($ap['balance_due'], 2) ?>" readonly>
                </div>
                <div class="mb-3">
                  <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                  <input type="text" name="amount" class="form-control payment-amount-input" placeholder="0.00" required>
                  <input type="hidden" name="amount_raw" class="amount-raw-value">
                </div>
                <div class="mb-3">
                  <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                  <select name="payment_method" class="form-select" required>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Payment Reference</label>
                  <input type="text" name="payment_reference" class="form-control" placeholder="Check #, Transaction ID, etc.">
                </div>
                <div class="mb-3">
                  <label class="form-label">Notes</label>
                  <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100">
                  <i class="bi bi-check-circle me-1"></i>Record Payment
                </button>
              </form>
            </div>
          </div>

          <!-- Quick Mark as Paid -->
          <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0"><i class="bi bi-check-all me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
              <form action="<?= site_url('accounts-payable/mark-paid/' . $ap['id']) ?>" method="POST" onsubmit="return confirm('Mark this invoice as fully paid?');">
                <?= csrf_field() ?>
                <div class="mb-3">
                  <label class="form-label">Payment Method</label>
                  <select name="payment_method" class="form-select" required>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Payment Reference</label>
                  <input type="text" name="payment_reference" class="form-control" placeholder="Optional">
                </div>
                <div class="mb-3">
                  <label class="form-label">Notes</label>
                  <textarea name="notes" class="form-control" rows="2" placeholder="Optional"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-check-all me-1"></i>Mark as Fully Paid
                </button>
              </form>
            </div>
          </div>
        <?php else: ?>
          <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Payment Status</h6>
            </div>
            <div class="card-body text-center">
              <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
              <p class="mt-3 mb-0"><strong>Fully Paid</strong></p>
              <p class="text-muted small">This invoice has been fully paid.</p>
              <hr>
              <a href="<?= site_url('accounts-payable/receipt/' . $ap['id']) ?>" 
                 class="btn btn-primary w-100" 
                 target="_blank">
                <i class="bi bi-printer me-1"></i>Print Receipt
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Format number with local-specific formatting (Philippines: comma for thousands, period for decimals)
  function formatNumber(value) {
    if (!value) return '';
    
    // Remove all non-numeric characters except decimal point
    let num = value.toString().replace(/[^\d.]/g, '');
    
    // Split by decimal point
    let parts = num.split('.');
    let integerPart = parts[0];
    let decimalPart = parts.length > 1 ? parts[1].substring(0, 2) : '';
    
    // Add thousands separator
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    
    // Combine
    return decimalPart ? integerPart + '.' + decimalPart : integerPart;
  }
  
  // Parse formatted number back to raw number
  function parseNumber(formattedValue) {
    if (!formattedValue) return '';
    return formattedValue.toString().replace(/,/g, '');
  }
  
  // Handle all payment amount inputs
  const paymentInputs = document.querySelectorAll('.payment-amount-input');
  
  paymentInputs.forEach(function(input) {
    const rawInput = input.closest('form').querySelector('.amount-raw-value');
    
    // Format on input
    input.addEventListener('input', function(e) {
      const cursorPosition = e.target.selectionStart;
      const oldValue = e.target.value;
      const formatted = formatNumber(e.target.value);
      
      e.target.value = formatted;
      
      // Update raw value for form submission
      if (rawInput) {
        rawInput.value = parseNumber(formatted);
      }
      
      // Restore cursor position
      const diff = formatted.length - oldValue.length;
      e.target.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
    });
    
    // Format on blur
    input.addEventListener('blur', function(e) {
      const parsed = parseNumber(e.target.value);
      if (parsed && !isNaN(parsed) && parseFloat(parsed) > 0) {
        e.target.value = formatNumber(parseFloat(parsed).toFixed(2));
        if (rawInput) {
          rawInput.value = parseFloat(parsed).toFixed(2);
        }
      }
    });
    
    // Before form submit, ensure raw value is set and update visible input
    const form = input.closest('form');
    if (form) {
      form.addEventListener('submit', function(e) {
        const parsed = parseNumber(input.value);
        if (parsed && !isNaN(parsed)) {
          const rawValue = parseFloat(parsed).toFixed(2);
          if (rawInput) {
            rawInput.value = rawValue;
            // Also update the visible input to raw value for submission
            input.value = rawValue;
          } else {
            // If no hidden input, replace the visible input value with raw number
            input.value = rawValue;
          }
        }
      });
    }
  });
});
</script>
