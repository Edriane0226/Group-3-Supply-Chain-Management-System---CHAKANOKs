<?php
    include 'app\\Views\\reusables\\sidenav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --accent:#0d6efd; --muted:#6c757d; --surface:#ffffff; --bg:#f6f8fb; }
        body { overflow-x: hidden; background: var(--bg); color:#212529; }
        .content { margin-left: 0; padding: 80px 24px 24px; }
        .table-sm td, .table-sm th { padding: .45rem .5rem; }
        .metric.card { border: 1px solid #e9ecef; background: var(--surface); }
        .metric .label { color: var(--muted); font-size:.9rem; }
        .metric .value { font-size: 1.6rem; font-weight: 600; }
        .metric .icon { color: var(--accent); opacity:.8; }
        .section-header { background:#fff; border-bottom:1px solid #e9ecef; }
    </style>
</head>
<body>

<main class="content">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Inventory Overview</h5>
            <span class="badge rounded-pill text-bg-secondary small">Auto-refreshing</span>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-3"><div class="card shadow-sm h-100 metric"><div class="card-body"><div class="d-flex align-items-center justify-content-between"><div><div class="label">Total SKUs</div><div class="value" id="total_skus">0</div></div><i class="bi bi-collection fs-1 icon"></i></div></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm h-100 metric"><div class="card-body"><div class="d-flex align-items-center justify-content-between"><div><div class="label">Total Quantity</div><div class="value" id="total_quantity">0</div></div><i class="bi bi-basket3 fs-1 icon"></i></div></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm h-100 metric"><div class="card-body"><div class="d-flex align-items-center justify-content-between"><div><div class="label">Low Stock Items</div><div class="value" id="low_count">0</div></div><i class="bi bi-exclamation-triangle fs-1 icon" style="color:#fd7e14"></i></div></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm h-100 metric"><div class="card-body"><div class="d-flex align-items-center justify-content-between"><div><div class="label">Expiring (30d)</div><div class="value" id="exp_count">0</div></div><i class="bi bi-calendar2-event fs-1 icon" style="color:#dc3545"></i></div></div></div></div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header section-header fw-semibold"><i class="bi bi-bell me-2 text-warning"></i>Low Stock Alerts</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <?php if (!empty($stockWarning)) : ?>
                            <table class="table table-sm mb-0 align-middle">
                                <thead>
                      <tr>
                          <th>Item Name</th>
                          <th>Quantity</th>
                          <th>Reorder Level</th>
                          <th>Unit</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($stockWarning as $item) : ?>
                          <tr>
                              <td><?= esc($item['item_name']) ?></td>
                              <td><?= esc($item['quantity']) ?></td>
                              <td><?= esc($item['reorder_level']) ?></td>
                              <td><?= esc($item['unit']) ?></td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
              <?php else : ?>
                <p>No low stock alerts at the moment.</p>
              <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header section-header fw-semibold"><i class="bi bi-calendar2-week me-2 text-danger"></i>Expiring Soon</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light"><tr><th>Item</th><th>Expiry</th></tr></thead>
                                <tbody id="expiring"><tr><td colspan="2" class="text-center text-muted">Loading…</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const branchId = <?php echo json_encode(session()->get('branch_id') ?? null); ?>;

    async function loadSummary() {
        const url = new URL('<?php echo base_url('inventory/summary'); ?>', window.location.origin);
        if (branchId) url.searchParams.set('branch_id', branchId);
        const res = await fetch(url);
        if (!res.ok) return;
        const data = await res.json();
        document.getElementById('total_skus').textContent = data.totals?.total_skus ?? 0;
        document.getElementById('total_quantity').textContent = data.totals?.total_quantity ?? 0;
        const low = data.lowStock || [];
        const exp = data.expiringSoon || [];
        document.getElementById('low_count').textContent = low.length;
        document.getElementById('exp_count').textContent = exp.length;

        const lowBody = document.getElementById('low_stock');
        lowBody.innerHTML = '';
        if (!low.length) lowBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">All good</td></tr>';
        low.forEach(i => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${i.item_name}</td><td class="text-end">${i.quantity}</td><td class="text-end">${i.reorder_level}</td>`;
            lowBody.appendChild(tr);
        });

        const expBody = document.getElementById('expiring');
        expBody.innerHTML = '';
        if (!exp.length) expBody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">No upcoming expiries</td></tr>';
        exp.forEach(i => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${i.item_name}</td><td>${i.expiry_date ?? ''}</td>`;
            expBody.appendChild(tr);
        });
    }

    loadSummary();
    setInterval(loadSummary, 15000);
</script>
</body>
</html>

