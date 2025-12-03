<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Accounts Payable</h1>
      <div class="text-muted small">
        <?= esc(session()->get('supplier_name')) ?> (Supplier)
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-muted small mb-1">Total Pending</p>
                <h4 class="mb-0">₱<?= number_format($summary['total_pending'], 2) ?></h4>
              </div>
              <div class="text-warning">
                <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-muted small mb-1">Total Overdue</p>
                <h4 class="mb-0 text-danger">₱<?= number_format($summary['total_overdue'], 2) ?></h4>
              </div>
              <div class="text-danger">
                <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-muted small mb-1">Total Paid</p>
                <h4 class="mb-0 text-success">₱<?= number_format($summary['total_paid'], 2) ?></h4>
              </div>
              <div class="text-success">
                <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <p class="text-muted small mb-1">Pending Invoices</p>
                <h4 class="mb-0"><?= $summary['pending_count'] + $summary['overdue_count'] ?></h4>
              </div>
              <div class="text-info">
                <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form method="GET" action="<?= site_url('supplier/accounts-payable') ?>" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All Status</option>
              <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Overdue</option>
              <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Paid</option>
              <option value="partial" <?= $status === 'partial' ? 'selected' : '' ?>>Partial</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?= esc($startDate ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?= esc($endDate ?? '') ?>">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
              <i class="bi bi-search me-1"></i>Filter
            </button>
            <a href="<?= site_url('supplier/accounts-payable') ?>" class="btn btn-outline-secondary">
              <i class="bi bi-x-circle me-1"></i>Clear
            </a>
          </div>
        </form>
      </div>
    </div>

    <!-- Accounts Payable Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="bi bi-file-earmark-dollar me-2"></i>Accounts Payable Records</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Invoice #</th>
                <th>PO ID</th>
                <th>Branch</th>
                <th>Invoice Amount</th>
                <th>Amount Paid</th>
                <th>Balance Due</th>
                <th>Due Date</th>
                <th>Payment Terms</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($accountsPayable)): ?>
                <?php foreach ($accountsPayable as $ap): ?>
                  <?php
                    $isOverdue = $ap['payment_status'] === 'overdue' || 
                                ($ap['payment_status'] === 'pending' && $ap['due_date'] && strtotime($ap['due_date']) < strtotime(date('Y-m-d')));
                  ?>
                  <tr class="<?= $isOverdue ? 'table-danger' : '' ?>">
                    <td><strong>#<?= esc($ap['id']) ?></strong></td>
                    <td>PO-<?= esc($ap['purchase_order_id'] ?? $ap['po_id']) ?></td>
                    <td><?= esc($ap['branch_name'] ?? 'N/A') ?></td>
                    <td><strong>₱<?= number_format($ap['invoice_amount'], 2) ?></strong></td>
                    <td>₱<?= number_format($ap['amount_paid'] ?? 0, 2) ?></td>
                    <td><strong class="<?= $ap['balance_due'] > 0 ? 'text-danger' : 'text-success' ?>">
                      ₱<?= number_format($ap['balance_due'], 2) ?>
                    </strong></td>
                    <td>
                      <?php if ($ap['due_date']): ?>
                        <?= date('M d, Y', strtotime($ap['due_date'])) ?>
                        <?php if ($isOverdue): ?>
                          <br><small class="text-danger">Overdue</small>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="text-muted">Not set</span>
                      <?php endif; ?>
                    </td>
                    <td><?= esc($ap['payment_terms'] ?? 'N/A') ?></td>
                    <td>
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
                      <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                    </td>
                    <td>
                      <a href="<?= site_url('supplier/accounts-payable/' . $ap['id']) ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>View
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="10" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-2">No accounts payable records found.</p>
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

