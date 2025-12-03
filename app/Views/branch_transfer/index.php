<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Branch Transfers</h1>
      <div>
        <a href="<?= site_url('branch-transfers/create') ?>" class="btn btn-primary">
          <i class="bi bi-plus-circle me-2"></i>Create Transfer Request
        </a>
      </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Status Filter -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <div class="btn-group" role="group">
          <a href="<?= site_url('branch-transfers') ?>" 
             class="btn btn-sm <?= !$currentStatus ? 'btn-primary' : 'btn-outline-primary' ?>">
            All
          </a>
          <a href="<?= site_url('branch-transfers?status=pending') ?>" 
             class="btn btn-sm <?= $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
            Pending <?= isset($pendingCount) && $pendingCount > 0 ? '<span class="badge bg-dark">' . $pendingCount . '</span>' : '' ?>
          </a>
          <a href="<?= site_url('branch-transfers?status=approved') ?>" 
             class="btn btn-sm <?= $currentStatus === 'approved' ? 'btn-info' : 'btn-outline-info' ?>">
            Approved
          </a>
          <a href="<?= site_url('branch-transfers?status=completed') ?>" 
             class="btn btn-sm <?= $currentStatus === 'completed' ? 'btn-success' : 'btn-outline-success' ?>">
            Completed
          </a>
          <a href="<?= site_url('branch-transfers?status=rejected') ?>" 
             class="btn btn-sm <?= $currentStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>">
            Rejected
          </a>
        </div>
      </div>
    </div>

    <!-- Transfers Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Transfer Requests</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>From Branch</th>
                <th>To Branch</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Requested By</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($transfers)): ?>
                <?php foreach ($transfers as $transfer): ?>
                  <tr>
                    <td><strong>#<?= esc($transfer['id']) ?></strong></td>
                    <td><?= esc($transfer['from_branch_name'] ?? 'N/A') ?></td>
                    <td><?= esc($transfer['to_branch_name'] ?? 'N/A') ?></td>
                    <td><?= esc($transfer['item_name']) ?></td>
                    <td><?= esc($transfer['quantity']) ?> <?= esc($transfer['unit'] ?? 'pcs') ?></td>
                    <td>
                      <?php
                      $statusClass = 'secondary';
                      switch($transfer['status']) {
                        case 'pending':
                          $statusClass = 'warning';
                          break;
                        case 'approved':
                          $statusClass = 'info';
                          break;
                        case 'completed':
                          $statusClass = 'success';
                          break;
                        case 'rejected':
                          $statusClass = 'danger';
                          break;
                      }
                      ?>
                      <span class="badge bg-<?= $statusClass ?>">
                        <?= ucfirst($transfer['status']) ?>
                      </span>
                    </td>
                    <td>
                      <?= esc($transfer['requester_first_name'] ?? '') ?> 
                      <?= esc($transfer['requester_last_name'] ?? '') ?>
                    </td>
                    <td><?= date('M d, Y', strtotime($transfer['created_at'])) ?></td>
                    <td>
                      <a href="<?= site_url('branch-transfers/view/' . $transfer['id']) ?>" 
                         class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No transfers found
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
