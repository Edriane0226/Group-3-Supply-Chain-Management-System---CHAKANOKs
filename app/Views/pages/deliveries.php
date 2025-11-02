<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Delivery Management') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color:#f8f9fa; min-height:100vh; display:flex; margin:0; }
    .sidebar { width:220px; background-color:orange; color:#fff; flex-shrink:0; display:flex; flex-direction:column; align-items:center; padding-top:20px; position:fixed; top:0; bottom:0; }
    .sidebar img{ width:72px; height:72px; border-radius:50%; object-fit:cover; margin-bottom:10px; }
    .sidebar h5{ font-weight:600; text-align:center; margin:10px 0 20px; line-height:1.2; }
    .sidebar a{ color:#fff; text-decoration:none; width:100%; padding:10px 16px; display:flex; align-items:center; gap:8px; border-radius:6px; margin:2px 8px; }
    .sidebar a.active, .sidebar a:hover{ background:rgba(0,0,0,.25); }
    .main-content { margin-left:220px; padding:20px; width:100%; }

    .tab-bar { background:#fff; border:1px solid #dee2e6; border-radius:14px; padding:8px; display:flex; gap:8px; }
    .tab-btn { border-radius:999px; padding:6px 16px; border:1px solid #ced4da; background:#f8f9fa; font-weight:500; }
    .tab-btn.active { background:#dee2e6; }

    .section-card { background:#fff; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,.06); padding:16px; height:100%; }
    .right-panel { background:#fff; border:1px solid #dee2e6; border-radius:12px; padding:16px; }
    .pill-btn { border-radius:999px; }
  </style>
</head>
<body>
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 fw-bold mb-0">Delivery Management</h1>
      <div class="text-muted small"><?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)</div>
    </div>

    <!-- Top segmented buttons -->
    <div class="tab-bar mb-3">
      <button class="tab-btn active" data-target="#sec-schedule">Scheduling Receiving</button>
      <button class="tab-btn" data-target="#sec-quality">Quality Check</button>
      <button class="tab-btn" data-target="#sec-update">Update Inventory</button>
      <button class="tab-btn" data-target="#sec-discrepancies">Report Discrepancies</button>
    </div>

    <!-- Sections -->
    <div id="sec-schedule" class="section">
      <div class="row g-3">
        <div class="col-md-3">
          <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-2"><span class="small text-muted">Date:</span><span class="badge bg-secondary">Scheduled</span></div>
            <div class="small text-muted">Supplier: __________</div>
            <div class="small text-muted mb-3">Items: __________</div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary">View Details</button>
              <button class="btn btn-sm btn-outline-secondary">Reschedule</button>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-2"><span class="small text-muted">Expected date:</span><span class="badge bg-secondary">In Transit</span></div>
            <div class="small text-muted">Supplier: __________</div>
            <div class="small text-muted mb-3">Items: __________</div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary">Track</button>
              <button class="btn btn-sm btn-outline-secondary">Contact Driver</button>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="section-card d-flex align-items-center justify-content-center">
            <button class="btn btn-outline-secondary">Add Schedule</button>
          </div>
        </div>
        <div class="col-md-3">
          <div class="right-panel">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong class="small">Report Delivery Discrepancies</strong>
              <span class="badge bg-secondary">Open Issue</span>
            </div>
            <div class="mb-2"><select class="form-select form-select-sm"><option>Select Delivery</option></select></div>
            <div class="mb-2"><select class="form-select form-select-sm"><option>Select Type</option></select></div>
            <div class="small text-muted mb-2">__________________________</div>
            <div class="d-flex flex-wrap gap-2 mb-3">
              <button class="btn btn-sm btn-outline-secondary">Contact Supplier</button>
              <button class="btn btn-sm btn-outline-secondary">Add Photos</button>
              <button class="btn btn-sm btn-outline-secondary">Update Status</button>
            </div>
            <div class="row g-2">
              <div class="col"><div class="small text-muted">Investigating</div></div>
              <div class="col"><div class="small text-muted">Resolve</div></div>
            </div>
            <div class="d-grid mt-3"><button class="btn btn-outline-secondary">Download Report</button></div>
          </div>
        </div>
      </div>
    </div>

    <div id="sec-quality" class="section d-none">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="section-card">
            <div class="d-flex justify-content-between mb-2"><span class="badge bg-warning text-dark">Pending inspect</span></div>
            <div class="small text-muted mb-3">Supplier: ______ | Items Received: ______</div>
            <button class="btn btn-outline-secondary">Start Inspection</button>
          </div>
        </div>
        <div class="col-md-6">
          <div class="section-card d-flex flex-column align-items-center justify-content-center">
            <div class="display-6 text-muted">PASSED</div>
            <button class="btn btn-outline-secondary mt-3">View Report</button>
          </div>
        </div>
      </div>
    </div>

    <div id="sec-update" class="section d-none">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="section-card">
            <div class="small text-muted mb-2">Supplier: ________</div>
            <div class="mb-3"><button class="btn btn-sm btn-outline-secondary">Select Item</button></div>
            <button class="btn btn-outline-secondary">Update Inventory</button>
          </div>
        </div>
        <div class="col-md-6">
          <div class="section-card">
            <div class="d-flex justify-content-between mb-2"><span class="small text-muted">Processing</span><span class="small text-muted">Ready</span></div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary flex-fill">View Progress</button>
              <button class="btn btn-outline-secondary flex-fill">Upload</button>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="section-card d-flex align-items-center justify-content-center">
            <button class="btn btn-outline-secondary">Manual Update</button>
          </div>
        </div>
      </div>
    </div>

    <div id="sec-discrepancies" class="section d-none">
      <div class="section-card">
        <p class="mb-0 text-muted">Discrepancy reporting workspace.</p>
      </div>
    </div>
  </div>

  <script>
    // Simple tab switcher
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.section').forEach(s => s.classList.add('d-none'));
        btn.classList.add('active');
        const target = document.querySelector(btn.getAttribute('data-target'));
        if (target) target.classList.remove('d-none');
      });
    });
  </script>
</body>
</html>
