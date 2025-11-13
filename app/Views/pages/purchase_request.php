<div class="content">
<?php if ($role == 'Branch Manager') { ?>
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 fw-bold mb-0">Purchase Request</h1>
      <div class="text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)
      </div>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-header bg-warning text-white">
        <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Your Purchase Requests</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>PR No.</th>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Supplier</th>
              <th>Status</th>
              <th>Date Requested</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($requests)): ?>
              <?php foreach ($requests as $req): ?>
                <tr>
                  <td><?= esc($req['id']) ?></td>
                  <td><?= esc($req['item_name']) ?></td>
                  <td><?= esc($req['quantity']) ?></td>
                  <td><?= esc($req['supplier_name']) ?></td>
                  <td><span class="badge bg-<?= $req['status'] == 'approved' ? 'success' : ($req['status'] == 'pending' ? 'warning' : 'danger') ?>">
                    <?= esc(ucfirst($req['status'])) ?></span>
                  </td>
                  <td><?= esc($req['request_date']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center text-muted py-4">No purchase requests yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>

    <div class="text-center">
      <a href="<?= site_url('purchase-requests/create') ?>" class="btn btn-secondary px-4">
        <i class="bi bi-plus-circle me-1"></i>New Request
      </a>
    </div>
  </div>

<?php } elseif ($role == 'Inventory Staff') { ?>
  <!-- 🟡 Inventory Staff View (read-only) -->
  <div class="content container">
    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-clipboard-data me-2 text-warning"></i>All Purchase Requests</h5>
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Request ID</th>
              <th>Branch</th>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Status</th>
              <th>Request Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($requests)): ?>
              <?php foreach ($requests as $req): ?>
                <tr>
                  <td><?= esc($req['id']) ?></td>
                  <td><?= esc($req['branch_name']) ?></td>
                  <td><?= esc($req['item_name']) ?></td>
                  <td><?= esc($req['quantity']) ?></td>
                  <td><?= esc($req['unit']) ?></td>
                  <td>
                    <span class="badge bg-<?= $req['status'] == 'approved' ? 'success' : ($req['status'] == 'pending' ? 'warning' : 'danger') ?>">
                      <?= esc(ucfirst($req['status'])) ?>
                    </span>
                  </td>
                  <td><?= esc($req['request_date']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-muted py-4">No requests found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php } elseif ($role == 'Central Office Admin') { ?>
  <!-- 🟣 Central Office Admin View -->
  <div class="content container">
    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-bag-check-fill me-2 text-warning"></i>Branch Purchase Requests</h5>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Request ID</th>
              <th>Branch</th>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Status</th>
              <th>Request Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($requests)): ?>
              <?php foreach ($requests as $req): ?>
                <tr>
                  <td><?= esc($req['id']) ?></td>
                  <td><?= esc($req['branch_name']) ?></td>
                  <td><?= esc($req['item_name']) ?></td>
                  <td><?= esc($req['quantity']) ?></td>
                  <td><?= esc($req['unit']) ?></td>
                  <td>
                    <span class="badge bg-<?= $req['status'] == 'approved' ? 'success' : ($req['status'] == 'pending' ? 'warning' : 
                                                                                         ($req['status'] == 'rejected' ? 'danger' : 
                                                                                         ($req['status'] == 'ordered' ? 'info' : 
                                                                                         ($req['status'] == 'in_transit' ? 'primary' : 'secondary')))) ?>">
                      <?= esc(ucfirst($req['status'])) ?>
                    </span>
                  </td>
                  <td><?= esc($req['request_date']) ?></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                     <?php if ($req['status'] == 'pending'): ?>
                        <form method="post" action="<?= site_url('purchase-requests/approve/' . $req['id']) ?>" style="display:inline;">
                        <button type="submit" class="btn btn-success me-1" title="Approve Request">
                          <i class="bi bi-check-lg"></i>
                        </button>
                      </form>

                      <form method="post" action="<?= site_url('purchase-requests/reject/' . $req['id']) ?>" style="display:inline;">
                        <button type="submit" class="btn btn-danger" title="Reject Request">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </form>

                      <?php else:; ?>
                        <span class="text-muted">No actions available</span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center text-muted py-4">No branch requests found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php } ?>
</div>
