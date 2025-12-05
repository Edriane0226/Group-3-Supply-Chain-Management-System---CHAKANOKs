<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { overflow-x: hidden; background: #f6f8fb; color:#212529; display: flex; }
        .sidebar { width: 220px; background-color: orange; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; min-height: 100vh; }
        .sidebar img { width: 100px; height: 100px; border-radius: 50%; margin-bottom: 15px; }
        .sidebar h5 { margin-bottom: 20px; text-align: center; }
        .sidebar a { width: 100%; padding: 12px 20px; color: #fff; text-decoration: none; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; }
        .content { flex-grow: 1; padding: 80px 24px 24px; }
        .filter-section { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
    </style>
</head>
<body>

<?php echo view('reusables/sidenav'); ?>

<main class="content">
    <div class="container-fluid">
        <h5 class="mb-3">Inventory Reports</h5>

        <!-- Filters -->
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Branch</label>
                    <select id="branchFilter" class="form-control">
                        <option value="">All Branches</option>
                        <?php if (!empty($branches)): ?>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= (session()->get('branch_id') == $branch['id']) ? 'selected' : '' ?>>
                                    <?= esc($branch['branch_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Item Type</label>
                    <select id="typeFilter" class="form-control">
                        <option value="">All Types</option>
                        <?php if (!empty($stockTypes)): ?>
                            <?php foreach ($stockTypes as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= esc($type['type_name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" id="dateFrom" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" id="dateTo" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button id="applyFilters" class="btn btn-primary me-2">
                    <i class="bi bi-funnel me-2"></i>Apply Filters
                </button>
                <button id="exportCSV" class="btn btn-success me-2">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
                </button>
                <button id="exportPDF" class="btn btn-danger me-2">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                </button>
                <button id="exportExcel" class="btn btn-primary">
                    <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                </button>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="card shadow-sm">
            <div class="card-header section-header fw-semibold">
                <i class="bi bi-table me-2"></i>Inventory Report Data
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item Name</th>
                                <th class="text-end">Current Stock</th>
                                <th class="text-end">Unit</th>
                                <th class="text-end">Expiry Date</th>
                                <th class="text-end">Barcode</th>
                                <th class="text-end">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody id="reports_body">
                            <tr><td colspan="6" class="text-center text-muted">Loading report data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const branchId = <?php echo json_encode(session()->get('branch_id') ?? null); ?>;

    async function loadReports(filters = {}) {
        try {
            const url = new URL('<?php echo base_url('inventory/balance'); ?>', window.location.origin);
            if (filters.branch_id) url.searchParams.set('branch_id', filters.branch_id);
            if (filters.item_type_id) url.searchParams.set('item_type_id', filters.item_type_id);
            if (filters.date_from) url.searchParams.set('date_from', filters.date_from);
            if (filters.date_to) url.searchParams.set('date_to', filters.date_to);

            const res = await fetch(url);
            if (!res.ok) return;

            const data = await res.json();
            const tbody = document.getElementById('reports_body');
            tbody.innerHTML = '';

            if (data.length) {
                data.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${item.item_name}</td>
                        <td class="text-end">${item.current_stock}</td>
                        <td class="text-end">${item.unit}</td>
                        <td class="text-end">${item.expiry_date || 'N/A'}</td>
                        <td class="text-end">${item.barcode || 'N/A'}</td>
                        <td class="text-end">${item.updated_at || 'N/A'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No data available for the selected filters</td></tr>';
            }
        } catch (error) {
            console.error('Error loading reports:', error);
        }
    }

    // Apply filters
    document.getElementById('applyFilters').addEventListener('click', () => {
        const filters = {
            branch_id: document.getElementById('branchFilter').value,
            item_type_id: document.getElementById('typeFilter').value,
            date_from: document.getElementById('dateFrom').value,
            date_to: document.getElementById('dateTo').value
        };
        loadReports(filters);
    });

    // Export CSV
    document.getElementById('exportCSV').addEventListener('click', async () => {
        const filters = {
            branch_id: document.getElementById('branchFilter').value,
            item_type_id: document.getElementById('typeFilter').value,
            date_from: document.getElementById('dateFrom').value,
            date_to: document.getElementById('dateTo').value,
            export: 'csv'
        };

        const url = new URL('<?php echo base_url('inventory/export'); ?>', window.location.origin);
        Object.keys(filters).forEach(key => {
            if (filters[key]) url.searchParams.set(key, filters[key]);
        });

        window.open(url, '_blank');
    });

    // Export PDF
    document.getElementById('exportPDF').addEventListener('click', async () => {
        const filters = {
            branch_id: document.getElementById('branchFilter').value,
            item_type_id: document.getElementById('typeFilter').value,
            date_from: document.getElementById('dateFrom').value,
            date_to: document.getElementById('dateTo').value,
            export: 'pdf'
        };

        const url = new URL('<?php echo base_url('inventory/export'); ?>', window.location.origin);
        Object.keys(filters).forEach(key => {
            if (filters[key]) url.searchParams.set(key, filters[key]);
        });

        window.open(url, '_blank');
    });

    // Export Excel
    document.getElementById('exportExcel').addEventListener('click', async () => {
        const filters = {
            branch_id: document.getElementById('branchFilter').value,
            item_type_id: document.getElementById('typeFilter').value,
            date_from: document.getElementById('dateFrom').value,
            date_to: document.getElementById('dateTo').value,
            export: 'excel'
        };

        const url = new URL('<?php echo base_url('inventory/export'); ?>', window.location.origin);
        Object.keys(filters).forEach(key => {
            if (filters[key]) url.searchParams.set(key, filters[key]);
        });

        window.open(url, '_blank');
    });

    // Initial load
    loadReports({ branch_id: branchId });
</script>
</body>
</html>
