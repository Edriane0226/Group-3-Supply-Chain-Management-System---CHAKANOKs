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
        body { overflow-x: hidden; background: var(--bg); color:#212529; display: flex; }
        .sidebar { width: 220px; background-color: orange; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; min-height: 100vh; }
        .sidebar img { width: 100px; height: 100px; border-radius: 50%; margin-bottom: 15px; }
        .sidebar h5 { margin-bottom: 20px; text-align: center; }
        .sidebar a { width: 100%; padding: 12px 20px; color: #fff; text-decoration: none; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; }
        .content { flex-grow: 1; padding: 80px 24px 24px; }
        .table-sm td, .table-sm th { padding: .45rem .5rem; }
        .metric.card { border: 1px solid #e9ecef; background: var(--surface); }
        .metric .label { color: var(--muted); font-size:.9rem; }
        .metric .value { font-size: 1.6rem; font-weight: 600; }
        .metric .icon { color: var(--accent); opacity:.8; }
        .section-header { background:#fff; border-bottom:1px solid #e9ecef; }
        .manual-entry { background-color: #e6f3ff; border-left: 4px solid #007bff; }
    </style>
</head>
<body>

<?php echo view('reusables/sidenav'); ?>

<main class="content">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Inventory Dashboard</h5>
            <span class="badge rounded-pill text-bg-secondary small">Auto-refreshing</span>
        </div>

        <!-- Current Stock Overview -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100 metric">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="label">Total SKUs</div>
                                <div class="value" id="total_skus">0</div>
                            </div>
                            <i class="bi bi-collection fs-1 icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 metric">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="label">Total Quantity</div>
                                <div class="value" id="total_quantity">0</div>
                            </div>
                            <i class="bi bi-basket3 fs-1 icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 metric">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="label">Low Stock Items</div>
                                <div class="value" id="low_count">0</div>
                            </div>
                            <i class="bi bi-exclamation-triangle fs-1 icon" style="color:#fd7e14"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100 metric">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="label">Expiring Soon</div>
                                <div class="value" id="exp_count">0</div>
                            </div>
                            <i class="bi bi-calendar2-event fs-1 icon" style="color:#dc3545"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="card shadow-sm">
            <div class="card-header section-header fw-semibold">
                <i class="bi bi-box-seam me-2"></i>Current Stock Levels
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item Name</th>
                                <th class="text-end">Current Quantity</th>
                                <th class="text-end">Unit</th>
                                <th class="text-end">Expiry Status</th>
                                <th class="text-end">Low Stock Warning</th>
                                <th class="text-end">Entry Method</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventory_table">
                            <tr><td colspan="7" class="text-center text-muted">Loading inventory data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="row g-3 mt-3">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header section-header fw-semibold">
                        <i class="bi bi-bell me-2 text-warning"></i>Low Stock Alerts (≤10)
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th class="text-end">Available Stock</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="low_stock">
                                    <tr><td colspan="3" class="text-center text-muted">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expiring Soon -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header section-header fw-semibold">
                        <i class="bi bi-calendar2-week me-2 text-danger"></i>Expiring Soon (7 days)
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th class="text-end">Expiry Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="expiring">
                                    <tr><td colspan="3" class="text-center text-muted">Loading...</td></tr>
                                </tbody>
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

    async function loadDashboard() {
        try {
            // Load summary metrics
            const summaryUrl = new URL('<?php echo base_url('inventory/summary'); ?>', window.location.origin);
            if (branchId) summaryUrl.searchParams.set('branch_id', branchId);
            const summaryRes = await fetch(summaryUrl);
            if (summaryRes.ok) {
                const summaryData = await summaryRes.json();
                document.getElementById('total_skus').textContent = summaryData.totals?.total_skus ?? 0;
                document.getElementById('total_quantity').textContent = summaryData.totals?.total_quantity ?? 0;
                document.getElementById('low_count').textContent = summaryData.lowStock?.length ?? 0;
                document.getElementById('exp_count').textContent = summaryData.expiringSoon?.length ?? 0;

                // Populate low stock alerts
                const lowBody = document.getElementById('low_stock');
                lowBody.innerHTML = '';
                if (summaryData.lowStock?.length) {
                    summaryData.lowStock.forEach(item => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${item.item_name}</td>
                            <td class="text-end">${item.available_stock}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-primary" href="<?php echo base_url('inventory/stockin'); ?>">
                                    <i class="bi bi-plus-circle"></i> Stock In
                                </a>
                            </td>
                        `;
                        lowBody.appendChild(tr);
                    });
                } else {
                    lowBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">All stock levels good</td></tr>';
                }

                // Populate expiring soon
                const expBody = document.getElementById('expiring');
                expBody.innerHTML = '';
                if (summaryData.expiringSoon?.length) {
                    summaryData.expiringSoon.forEach(item => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${item.item_name}</td>
                            <td class="text-end">${item.expiry_date}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-danger" href="<?php echo base_url('inventory/stockout'); ?>">
                                    <i class="bi bi-dash-circle"></i> Stock Out
                                </a>
                            </td>
                        `;
                        expBody.appendChild(tr);
                    });
                } else {
                    expBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No upcoming expiries</td></tr>';
                }
            }

            // Load inventory table
            const inventoryUrl = new URL('<?php echo base_url('inventory/balance'); ?>', window.location.origin);
            if (branchId) inventoryUrl.searchParams.set('branch_id', branchId);
            const inventoryRes = await fetch(inventoryUrl);
            if (inventoryRes.ok) {
                const inventoryData = await inventoryRes.json();
                const tableBody = document.getElementById('inventory_table');
                tableBody.innerHTML = '';

                if (inventoryData.length) {
                    inventoryData.forEach(item => {
                        const isLowStock = item.current_stock <= 10;
                        const isExpiringSoon = item.expiry_date && new Date(item.expiry_date) <= new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
                        const isManualEntry = !item.barcode; // Assuming manual entries don't have barcodes

                        const tr = document.createElement('tr');
                        tr.className = isManualEntry ? 'manual-entry' : '';
                        tr.innerHTML = `
                            <td>${item.item_name}</td>
                            <td class="text-end">${item.current_stock}</td>
                            <td class="text-end">${item.unit}</td>
                            <td class="text-end">
                                ${isExpiringSoon ? '<span class="badge bg-danger">Expiring Soon</span>' : '<span class="badge bg-success">OK</span>'}
                            </td>
                            <td class="text-end">
                                ${isLowStock ? '<span class="badge bg-warning">Low Stock</span>' : '<span class="badge bg-success">OK</span>'}
                            </td>
                            <td class="text-end">
                                ${isManualEntry ? '<span class="badge bg-info">Manual</span>' : '<span class="badge bg-primary">Scanned</span>'}
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-primary me-1" href="<?php echo base_url('inventory/stockin'); ?>">
                                    <i class="bi bi-plus-circle"></i>
                                </a>
                                <a class="btn btn-sm btn-danger" href="<?php echo base_url('inventory/stockout'); ?>">
                                    <i class="bi bi-dash-circle"></i>
                                </a>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No inventory items found</td></tr>';
                }
            }
        } catch (error) {
            console.error('Error loading dashboard:', error);
        }
    }

    loadDashboard();
    setInterval(loadDashboard, 15000);
</script>
</body>
</html>
