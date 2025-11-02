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
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 fw-bold mb-0">Purchase Request</h1>
      <div class="text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)
      </div>
    </div>

    <!-- Search toolbar -->
    <div class="pr-toolbar d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-search"></i>
      <input type="text" class="form-control form-control-sm border-0" placeholder="Search"/>
      <button class="btn btn-sm btn-light ms-auto"><i class="bi bi-list"></i></button>
    </div>

    <!-- Request table -->
    <div class="pr-table mb-4">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Code</th>
            <th>Current Stock</th>
            <th>Supplier</th>
            <th>Unit Price</th>
            <th>Total Price</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="7" class="text-center text-muted py-4">No items added yet</td></tr>
        </tbody>
      </table>
    </div>

    <!-- Wizard cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="wizard-card">
          <h6 class="fw-semibold mb-3">Select Items</h6>
          <div class="input-group input-group-sm mb-2">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input class="form-control" placeholder="Search"/>
            <button class="btn btn-light"><i class="bi bi-list"></i></button>
          </div>
          <div class="table-responsive" style="max-height:180px">
            <table class="table table-sm">
              <thead><tr><th>Item Name</th><th>Category</th><th>Current Stock</th></tr></thead>
              <tbody><tr><td colspan="3" class="text-muted">...</td></tr></tbody>
            </table>
          </div>
          <div class="d-flex justify-content-end"><button class="btn btn-sm btn-secondary">Next</button></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="wizard-card">
          <h6 class="fw-semibold mb-3">Select Quantities</h6>
          <div class="table-responsive" style="max-height:212px">
            <table class="table table-sm">
              <thead><tr><th>Item Name</th><th class="text-end">Quantity</th></tr></thead>
              <tbody><tr><td>...</td><td class="text-end"><input type="number" class="form-control form-control-sm" style="max-width:110px"></td></tr></tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between"><button class="btn btn-sm btn-outline-secondary">Back</button><button class="btn btn-sm btn-secondary">Next</button></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="wizard-card">
          <h6 class="fw-semibold mb-3">Choose Supplier</h6>
          <div class="mb-2">
            <select class="form-select form-select-sm">
              <option>Supplier Name</option>
            </select>
          </div>
          <div class="small text-muted mb-2">Contact Supplier</div>
          <div class="small text-muted mb-3">Delivery Lead Time</div>
          <div class="d-flex justify-content-between"><button class="btn btn-sm btn-outline-secondary">Back</button><button class="btn btn-sm btn-secondary">Next</button></div>
        </div>
      </div>
    </div>

    <div class="text-center">
      <button class="btn btn-secondary btn-pill px-5">Submit to Central</button>
    </div>
  </div>
</body>
</html>
