<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-credit-card me-2"></i><?= esc($title) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item active">Payments</li>
                </ol>
            </nav>
        </div>
        <?php if (!empty($franchise)): ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="bi bi-plus-circle me-1"></i> Record Payment
            </button>
        <?php endif; ?>
    </div>

    <!-- Flash Messages -->
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

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-success mb-0">₱<?= number_format($stats['total'] ?? 0, 0) ?></h5>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-0">₱<?= number_format($stats['franchise_fee'] ?? 0, 0) ?></h5>
                    <small class="text-muted">Franchise Fees</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-info mb-0">₱<?= number_format($stats['royalty'] ?? 0, 0) ?></h5>
                    <small class="text-muted">Royalties</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-0">₱<?= number_format($stats['supply_payment'] ?? 0, 0) ?></h5>
                    <small class="text-muted">Supplies</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-danger mb-0">₱<?= number_format($stats['penalty'] ?? 0, 0) ?></h5>
                    <small class="text-muted">Penalties</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h5 class="text-success mb-0">₱<?= number_format($stats['this_month'] ?? 0, 0) ?></h5>
                    <small class="text-muted">This Month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (!empty($payments)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <?php if (empty($franchise)): ?>
                                    <th>Franchise</th>
                                <?php endif; ?>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Period</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <?php
                                $typeColors = [
                                    'franchise_fee' => 'primary',
                                    'royalty' => 'success',
                                    'supply_payment' => 'info',
                                    'penalty' => 'danger',
                                    'other' => 'secondary',
                                ];
                                $typeColor = $typeColors[$payment['payment_type']] ?? 'secondary';

                                $statusColors = [
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'refunded' => 'info',
                                ];
                                $statusColor = $statusColors[$payment['payment_status'] ?? 'completed'] ?? 'secondary';
                                ?>
                                <tr>
                                    <td><strong>#<?= esc($payment['id']) ?></strong></td>
                                    <?php if (empty($franchise)): ?>
                                        <td><?= esc($payment['applicant_name'] ?? 'N/A') ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <?= date('M d, Y', strtotime($payment['payment_date'])) ?>
                                        <br><small class="text-muted"><?= date('h:i A', strtotime($payment['payment_date'])) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $typeColor ?>">
                                            <?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?>
                                        </span>
                                    </td>
                                    <td><?= ucfirst($payment['payment_method'] ?? 'cash') ?></td>
                                    <td><?= esc($payment['reference_number'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($payment['period_start']) && !empty($payment['period_end'])): ?>
                                            <small>
                                                <?= date('M d', strtotime($payment['period_start'])) ?> - 
                                                <?= date('M d, Y', strtotime($payment['period_end'])) ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold text-success">₱<?= number_format($payment['amount'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $statusColor ?>">
                                            <?= ucfirst($payment['payment_status'] ?? 'completed') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('franchise/payment-receipt/' . $payment['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank"
                                           title="Print Receipt">
                                            <i class="bi bi-printer"></i> Print
                                        </a>
                                    </td>
                                </tr>
                                <?php if (!empty($payment['remarks'])): ?>
                                    <tr class="table-light">
                                        <td colspan="<?= empty($franchise) ? '10' : '9' ?>" class="small text-muted py-1 ps-4">
                                            <i class="bi bi-chat-left-text me-1"></i> <?= esc($payment['remarks']) ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-credit-card display-1 text-muted"></i>
                    <h5 class="mt-3">No Payments Found</h5>
                    <p class="text-muted">No payment records found.</p>
                    <?php if (!empty($franchise)): ?>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                            <i class="bi bi-plus-circle me-1"></i> Record First Payment
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($franchise)): ?>
<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('franchise/payment/' . $franchise['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle text-success me-2"></i>Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                            <select name="payment_type" class="form-select" required>
                                <option value="royalty">Royalty</option>
                                <option value="franchise_fee">Franchise Fee</option>
                                <option value="supply_payment">Supply Payment</option>
                                <option value="penalty">Penalty</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                            <input type="text" name="amount" class="form-control payment-amount-input" placeholder="0.00" required>
                            <input type="hidden" name="amount_raw" class="amount-raw-value">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="e.g., Receipt #, Check #">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Period (for royalties)</label>
                            <div class="input-group">
                                <input type="date" name="period_start" class="form-control" placeholder="Start">
                                <span class="input-group-text">to</span>
                                <input type="date" name="period_end" class="form-control" placeholder="End">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

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
