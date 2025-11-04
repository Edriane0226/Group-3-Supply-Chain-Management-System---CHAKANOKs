<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Purchase Request') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; min-height: 100vh; display: flex; margin: 0; }
    .sidebar { width: 220px; background-color: orange; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; position: fixed; top: 0; bottom: 0; }
    .sidebar img { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; }
    .sidebar h5 { font-weight: 600; text-align: center; margin: 10px 0 20px; line-height: 1.2; }
    .sidebar a { color: #fff; text-decoration: none; width: 100%; padding: 10px 16px; display: flex; align-items: center; gap: 8px; border-radius: 6px; margin: 2px 8px; }
    .sidebar a.active, .sidebar a:hover { background: rgba(0, 0, 0, 0.25); }
    .main-content { margin-left: 220px; padding: 20px; width: 100%; }
    .pr-toolbar { border: 1px solid #dee2e6; border-radius: 12px; padding: 8px 12px; background: #fff; }
    .pr-card { background:#fff; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.06); padding:16px; }
    .pr-table { border:1px solid #dee2e6; border-radius:12px; overflow:hidden; }
    .pr-table thead th { background:#f8f9fa; }
    .wizard-card { border:1px solid #dee2e6; border-radius:12px; padding:16px; background:#fff; }
    .btn-pill { border-radius: 999px; padding: 10px 24px; }
  </style>
</head>
<body>
<?php if ($role == 'Branch Manager'): ?>
  <div class="main-content">
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
                  <td><span class="badge bg-<?= $req['status'] == 'Approved' ? 'success' : ($req['status'] == 'Pending' ? 'warning' : 'danger') ?>">
                    <?= esc($req['status']) ?></span>
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

    <div class="text-center">
      <a href="<?= site_url('purchase-requests/create') ?>" class="btn btn-secondary px-4">
        <i class="bi bi-plus-circle me-1"></i>New Request
      </a>
    </div>
  </div>

<?php elseif ($role == 'Central Office Admin'): ?>
  <!-- 🟣 Central Office Admin View -->
  <div class="main-content container">
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
                    <span class="badge bg-<?= $req['status'] == 'Approved' ? 'success' : ($req['status'] == 'Pending' ? 'warning' : 'danger') ?>">
                      <?= esc($req['status']) ?>
                    </span>
                  </td>
                  <td><?= esc($req['request_date']) ?></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <form action="<?= site_url('purchase-requests/approve/' . $req['id']) ?>" method="post" class="d-inline">
                        <button class="btn btn-success btn-sm"><i class="bi bi-check2-circle"></i></button>
                      </form>
                      <form action="<?= site_url('purchase-requests/cancel/' . $req['id']) ?>" method="post" class="d-inline">
                        <button class="btn btn-danger btn-sm"><i class="bi bi-x-circle"></i></button>
                      </form>
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

<?php endif; ?>

</body>
</html>
