<?php
    include 'app\Views\reusables\sidenav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --accent:#0d6efd; --muted:#6c757d; --surface:#ffffff; --bg:#f6f8fb; }
        body { overflow-x: hidden; background: var(--bg); color:#212529; }
        .content { margin-left: 0; padding: 80px 24px 24px; }
        #scanner { width: 100%; max-width: 520px; border: 1px dashed #dee2e6; aspect-ratio: 4/3; border-radius: .5rem; background:#fff; }
        .table-sm td, .table-sm th { padding: .45rem .5rem; }
        .metric.card { border: 1px solid #e9ecef; background: var(--surface); }
        .metric .label { color: var(--muted); font-size:.9rem; }
        .metric .value { font-size: 1.6rem; font-weight: 600; }
        .metric .icon { color: var(--accent); opacity:.8; }
        .section-header { background:#fff; border-bottom:1px solid #e9ecef; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>

    <main class="content">
        <div class="container-fluid">
            <div class="row g-3 mb-3">
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
                                    <div class="label">Expiring (30d)</div>
                                    <div class="value" id="exp_count">0</div>
                                </div>
                                <i class="bi bi-calendar2-event fs-1 icon" style="color:#dc3545"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header section-header fw-semibold"><i class="bi bi-bell me-2 text-warning"></i>Low Stock Alerts</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th><th class="text-end">Qty</th><th class="text-end">Reorder</th>
                                        </tr>
                                    </thead>
                                    <tbody id="low_stock"><tr><td colspan="3" class="text-center text-muted">Loading…</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header section-header fw-semibold"><i class="bi bi-calendar2-week me-2 text-danger"></i>Expiring Soon</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th><th>Expiry</th>
                                        </tr>
                                    </thead>
                                    <tbody id="expiring"><tr><td colspan="2" class="text-center text-muted">Loading…</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white"><i class="bi bi-upc-scan me-2"></i>Barcode Scan / Lookup</div>
                        <div class="card-body">
                            <div id="scanner" class="mb-2"></div>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="bi bi-upc"></i></span>
                                <input type="text" class="form-control" id="barcode" placeholder="Scan or enter barcode">
                                <button class="btn btn-primary" id="lookup"><i class="bi bi-search"></i></button>
                            </div>
                            <div id="item_details" class="small text-muted">Scan or search an item to see details.</div>
                            <div class="d-grid gap-2 mt-3">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateModal"><i class="bi bi-pencil-square me-1"></i>Update Stock</button>
                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#receiveModal"><i class="bi bi-box-arrow-in-down me-1"></i>Receive Delivery</button>
                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#damageModal"><i class="bi bi-exclamation-octagon me-1"></i>Report Damage/Expired</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal" id="updateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Update Stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label">Item ID</label><input type="number" id="item_id" class="form-control" placeholder="e.g. 101"></div>
                    <div><label class="form-label">Change (±)</label><input type="number" id="delta" class="form-control" placeholder="e.g. 5 or -2"></div>
                    <div class="small text-muted mt-2">Positive to add, negative to deduct.</div>
                </div>
                <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button class="btn btn-primary" id="update_btn">Apply</button></div>
            </div>
        </div>
    </div>

    <div class="modal" id="receiveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Receive Delivery</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label">Item ID</label><input type="number" id="receive_item_id" class="form-control" placeholder="e.g. 101"></div>
                    <div><label class="form-label">Quantity</label><input type="number" id="receive_amt" class="form-control" placeholder="e.g. 25"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button class="btn btn-success" id="receive_btn">Receive</button></div>
            </div>
        </div>
    </div>

    <div class="modal" id="damageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Report Damage/Expired</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label">Item ID</label><input type="number" id="damage_item_id" class="form-control" placeholder="e.g. 101"></div>
                    <div><label class="form-label">Quantity</label><input type="number" id="damage_amt" class="form-control" placeholder="e.g. 3"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button class="btn btn-danger" id="damage_btn">Report</button></div>
            </div>
        </div>
    </div>

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

        function initScanner() {
            if (!navigator.mediaDevices?.getUserMedia) return;
            Quagga.init({
                inputStream : { name : 'Live', type : 'LiveStream', target: document.querySelector('#scanner') },
                decoder : { readers : ['ean_reader','ean_8_reader','code_128_reader','code_39_reader','upc_reader'] }
            }, function(err) { if (err) { console.log(err); return; } Quagga.start(); });
            Quagga.onDetected(function(result) {
                const code = result?.codeResult?.code; if (code) { document.getElementById('barcode').value = code; lookupBarcode(); }
            });
        }

        async function lookupBarcode() {
            const code = document.getElementById('barcode').value.trim(); if (!code) return;
            const url = new URL('<?php echo base_url('inventory/find'); ?>', window.location.origin);
            url.searchParams.set('barcode', code); if (branchId) url.searchParams.set('branch_id', branchId);
            const res = await fetch(url);
            const box = document.getElementById('item_details');
            if (!res.ok) { box.innerHTML = '<span class="text-danger">Item not found</span>'; return; }
            const i = await res.json();
            box.innerHTML = `<div class="fw-semibold">${i.item_name} <span class="badge text-bg-light">${i.unit}</span></div>
                             <div class="text-muted">ID: ${i.id} · Reorder: ${i.reorder_level} · Qty: <span class="badge text-bg-secondary">${i.quantity}</span></div>
                             <div class="text-muted">Expiry: ${i.expiry_date ?? ''}</div>`;
            document.getElementById('item_id').value = i.id;
            document.getElementById('receive_item_id').value = i.id;
            document.getElementById('damage_item_id').value = i.id;
        }

        async function postForm(url, data) {
            const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams(data) });
            return res;
        }

        async function doAction(kind) {
            let url = ''; let data = {};
            if (kind === 'update') { url = '<?php echo base_url('inventory/update'); ?>'; data = { id: document.getElementById('item_id').value, delta: document.getElementById('delta').value }; }
            if (kind === 'receive') { url = '<?php echo base_url('inventory/receive'); ?>'; data = { id: document.getElementById('receive_item_id').value, amount: document.getElementById('receive_amt').value }; }
            if (kind === 'damage') { url = '<?php echo base_url('inventory/damage'); ?>'; data = { id: document.getElementById('damage_item_id').value, amount: document.getElementById('damage_amt').value }; }
            const res = await postForm(url, data); const json = await res.json();
            if (!res.ok) { alert(json.error || 'Request failed'); return; }
            loadSummary(); lookupBarcode();
        }

        document.getElementById('lookup').addEventListener('click', lookupBarcode);
        document.getElementById('update_btn').addEventListener('click', () => doAction('update'));
        document.getElementById('receive_btn').addEventListener('click', () => doAction('receive'));
        document.getElementById('damage_btn').addEventListener('click', () => doAction('damage'));

        loadSummary();
        initScanner();
        setInterval(loadSummary, 15000);
    </script>
</body>
</html>