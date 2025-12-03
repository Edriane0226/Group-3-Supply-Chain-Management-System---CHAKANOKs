<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Transfer Details #<?= esc($transfer['id']) ?></h1>
      <a href="<?= site_url('branch-transfers') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
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
      <div class="col-md-8">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Transfer Information</h6>
          </div>
          <div class="card-body">
            <table class="table table-borderless">
              <tr>
                <th width="200">Transfer ID:</th>
                <td><strong>#<?= esc($transfer['id']) ?></strong></td>
              </tr>
              <tr>
                <th>From Branch:</th>
                <td><?= esc($transfer['from_branch_name'] ?? 'N/A') ?></td>
              </tr>
              <tr>
                <th>To Branch:</th>
                <td><?= esc($transfer['to_branch_name'] ?? 'N/A') ?></td>
              </tr>
              <tr>
                <th>Item:</th>
                <td><?= esc($transfer['item_name']) ?></td>
              </tr>
              <tr>
                <th>Quantity:</th>
                <td><?= esc($transfer['quantity']) ?> <?= esc($transfer['unit'] ?? 'pcs') ?></td>
              </tr>
              <tr>
                <th>Status:</th>
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
                  <span class="badge bg-<?= $statusClass ?> fs-6">
                    <?= ucfirst($transfer['status']) ?>
                  </span>
                </td>
              </tr>
              <tr>
                <th>Requested By:</th>
                <td>
                  <?= esc($transfer['requester_first_name'] ?? '') ?> 
                  <?= esc($transfer['requester_last_name'] ?? '') ?>
                </td>
              </tr>
              <?php if ($transfer['approved_by']): ?>
                <tr>
                  <th>Approved By:</th>
                  <td>
                    <?= esc($transfer['approver_first_name'] ?? '') ?> 
                    <?= esc($transfer['approver_last_name'] ?? '') ?>
                  </td>
                </tr>
                <tr>
                  <th>Approved At:</th>
                  <td><?= $transfer['approved_at'] ? date('M d, Y H:i', strtotime($transfer['approved_at'])) : 'N/A' ?></td>
                </tr>
              <?php endif; ?>
              <?php if ($transfer['completed_at']): ?>
                <tr>
                  <th>Completed At:</th>
                  <td><?= date('M d, Y H:i', strtotime($transfer['completed_at'])) ?></td>
                </tr>
              <?php endif; ?>
              <tr>
                <th>Created At:</th>
                <td><?= date('M d, Y H:i', strtotime($transfer['created_at'])) ?></td>
              </tr>
              <?php if ($transfer['notes']): ?>
                <tr>
                  <th>Notes:</th>
                  <td><?= nl2br(esc($transfer['notes'])) ?></td>
                </tr>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-header bg-secondary text-white">
            <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Actions</h6>
          </div>
          <div class="card-body">
            <?php
            $branchId = session()->get('branch_id');
            $role = session()->get('role');
            $isDestinationBranch = ($transfer['to_branch_id'] == $branchId);
            ?>

            <?php if ($transfer['status'] === 'pending' && ($isDestinationBranch || $role === 'Central Office Admin')): ?>
              <form action="<?= site_url('branch-transfers/approve/' . $transfer['id']) ?>" method="POST" class="mb-2">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success w-100" 
                        onclick="return confirm('Approve this transfer request?')">
                  <i class="bi bi-check-circle me-2"></i>Approve
                </button>
              </form>

              <button type="button" class="btn btn-danger w-100 mb-2" 
                      data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-2"></i>Reject
              </button>
            <?php endif; ?>

            <?php if ($transfer['status'] === 'approved' && ($isDestinationBranch || $role === 'Central Office Admin')): ?>
              <form action="<?= site_url('branch-transfers/complete/' . $transfer['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary w-100" 
                        onclick="return confirm('Complete this transfer? This will move the stock between branches.')">
                  <i class="bi bi-check2-all me-2"></i>Complete Transfer
                </button>
              </form>
            <?php endif; ?>

            <?php if (!in_array($transfer['status'], ['pending', 'approved'])): ?>
              <p class="text-muted text-center mb-0">
                No actions available for <?= $transfer['status'] ?> transfers.
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= site_url('branch-transfers/reject/' . $transfer['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Reject Transfer Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="rejection_reason" class="form-label">Rejection Reason</label>
            <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                      rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Reject Transfer</button>
        </div>
      </form>
    </div>
  </div>
</div>
