<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Office Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { overflow-x: hidden; background: #f6f8fb; color:#212529; }
        .filter-section { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .report-tab { cursor: pointer; padding: 15px; border-bottom: 3px solid transparent; }
        .report-tab.active { border-bottom-color: orange; background: #fff3e0; }
        .report-content { display: none; }
        .report-content.active { display: block; }
    </style>
</head>
<body>

<main class="content">
    <div class="container-fluid">
        <h5 class="mb-4">Central Office Reports</h5>

        <!-- Report Type Tabs -->
        <div class="card shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="d-flex border-bottom">
                    <div class="report-tab active" data-tab="inventory" onclick="switchTab('inventory')">
                        <i class="bi bi-box-seam me-2"></i>Inventory Reports
                    </div>
                    <div class="report-tab" data-tab="branch" onclick="switchTab('branch')">
                        <i class="bi bi-building me-2"></i>Branch Reports
                    </div>
                    <div class="report-tab" data-tab="logistics" onclick="switchTab('logistics')">
                        <i class="bi bi-truck me-2"></i>Logistics Reports
                    </div>
                    <div class="report-tab" data-tab="franchise" onclick="switchTab('franchise')">
                        <i class="bi bi-shop me-2"></i>Franchise Reports
                    </div>
                    <div class="report-tab" data-tab="accounts-payable" onclick="switchTab('accounts-payable')">
                        <i class="bi bi-cash-coin me-2"></i>Accounts Payable Reports
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Reports Tab -->
        <div id="inventory-tab" class="report-content active">
            <div class="filter-section">
                <h6 class="mb-3"><i class="bi bi-box-seam me-2"></i>Inventory Report Filters</h6>
                
                <!-- Quick Date Range Presets -->
                <div class="mb-3">
                    <label class="form-label">Quick Date Range:</label>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('inventory', 'daily')">
                            <i class="bi bi-calendar-day me-1"></i>Daily
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('inventory', 'weekly')">
                            <i class="bi bi-calendar-week me-1"></i>Weekly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('inventory', 'monthly')">
                            <i class="bi bi-calendar-month me-1"></i>Monthly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('inventory', 'yearly')">
                            <i class="bi bi-calendar-year me-1"></i>Yearly
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Branch</label>
                        <select id="invBranchFilter" class="form-control">
                            <option value="">All Branches</option>
                            <?php if (!empty($branches)): ?>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>"><?= esc($branch['branch_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Item Type</label>
                        <select id="invTypeFilter" class="form-control">
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
                        <input type="date" id="invDateFrom" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" id="invDateTo" class="form-control">
                    </div>
                </div>
                <div class="mt-3">
                    <button onclick="exportReport('inventory', 'csv')" class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
                    </button>
                    <button onclick="exportReport('inventory', 'pdf')" class="btn btn-danger me-2">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                    </button>
                    <button onclick="exportReport('inventory', 'excel')" class="btn btn-primary">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Branch Reports Tab -->
        <div id="branch-tab" class="report-content">
            <div class="filter-section">
                <h6 class="mb-3"><i class="bi bi-building me-2"></i>Branch Performance Report Filters</h6>
                
                <!-- Quick Date Range Presets -->
                <div class="mb-3">
                    <label class="form-label">Quick Date Range:</label>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('branch', 'daily')">
                            <i class="bi bi-calendar-day me-1"></i>Daily
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('branch', 'weekly')">
                            <i class="bi bi-calendar-week me-1"></i>Weekly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('branch', 'monthly')">
                            <i class="bi bi-calendar-month me-1"></i>Monthly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('branch', 'yearly')">
                            <i class="bi bi-calendar-year me-1"></i>Yearly
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date From</label>
                        <input type="date" id="branchDateFrom" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date To</label>
                        <input type="date" id="branchDateTo" class="form-control">
                    </div>
                </div>
                <div class="mt-3">
                    <button onclick="exportReport('branch', 'csv')" class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
                    </button>
                    <button onclick="exportReport('branch', 'pdf')" class="btn btn-danger me-2">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                    </button>
                    <button onclick="exportReport('branch', 'excel')" class="btn btn-primary">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Logistics Reports Tab -->
        <div id="logistics-tab" class="report-content">
            <div class="filter-section">
                <h6 class="mb-3"><i class="bi bi-truck me-2"></i>Logistics Coordinator Report Filters</h6>
                
                <!-- Quick Date Range Presets -->
                <div class="mb-3">
                    <label class="form-label">Quick Date Range:</label>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('logistics', 'daily')">
                            <i class="bi bi-calendar-day me-1"></i>Daily
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('logistics', 'weekly')">
                            <i class="bi bi-calendar-week me-1"></i>Weekly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('logistics', 'monthly')">
                            <i class="bi bi-calendar-month me-1"></i>Monthly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('logistics', 'yearly')">
                            <i class="bi bi-calendar-year me-1"></i>Yearly
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date From</label>
                        <input type="date" id="logDateFrom" class="form-control" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date To</label>
                        <input type="date" id="logDateTo" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="mt-3">
                    <button onclick="exportReport('logistics', 'csv')" class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
                    </button>
                    <button onclick="exportReport('logistics', 'pdf')" class="btn btn-danger me-2">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                    </button>
                    <button onclick="exportReport('logistics', 'excel')" class="btn btn-primary">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Franchise Reports Tab -->
        <div id="franchise-tab" class="report-content">
            <div class="filter-section">
                <h6 class="mb-3"><i class="bi bi-shop me-2"></i>Franchise Performance Report Filters</h6>
                
                <!-- Quick Date Range Presets -->
                <div class="mb-3">
                    <label class="form-label">Quick Date Range:</label>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('franchise', 'daily')">
                            <i class="bi bi-calendar-day me-1"></i>Daily
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('franchise', 'weekly')">
                            <i class="bi bi-calendar-week me-1"></i>Weekly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('franchise', 'monthly')">
                            <i class="bi bi-calendar-month me-1"></i>Monthly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('franchise', 'yearly')">
                            <i class="bi bi-calendar-year me-1"></i>Yearly
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date From</label>
                        <input type="date" id="franchiseDateFrom" class="form-control" value="<?= date('Y-m-d', strtotime('-12 months')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date To</label>
                        <input type="date" id="franchiseDateTo" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="mt-3">
                    <button onclick="exportReport('franchise', 'csv')" class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
                    </button>
                    <button onclick="exportReport('franchise', 'pdf')" class="btn btn-danger me-2">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                    </button>
                    <button onclick="exportReport('franchise', 'excel')" class="btn btn-primary">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Accounts Payable Reports Tab -->
        <div id="accounts-payable-tab" class="report-content">
            <div class="filter-section">
                <h6 class="mb-3"><i class="bi bi-cash-coin me-2"></i>Accounts Payable Report Filters</h6>
                
                <!-- Quick Date Range Presets -->
                <div class="mb-3">
                    <label class="form-label">Quick Date Range:</label>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('accounts-payable', 'daily')">
                            <i class="bi bi-calendar-day me-1"></i>Daily
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('accounts-payable', 'weekly')">
                            <i class="bi bi-calendar-week me-1"></i>Weekly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('accounts-payable', 'monthly')">
                            <i class="bi bi-calendar-month me-1"></i>Monthly
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('accounts-payable', 'yearly')">
                            <i class="bi bi-calendar-year me-1"></i>Yearly
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Supplier</label>
                        <select id="apSupplierFilter" class="form-control">
                            <option value="">All Suppliers</option>
                            <?php if (!empty($suppliers)): ?>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier['id'] ?>"><?= esc($supplier['supplier_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Status</label>
                        <select id="apStatusFilter" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="overdue">Overdue</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" id="apDateFrom" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" id="apDateTo" class="form-control">
                    </div>
                </div>
                <div class="mt-3">
                    <button onclick="exportReport('accounts-payable', 'csv')" class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
                    </button>
                    <button onclick="exportReport('accounts-payable', 'pdf')" class="btn btn-danger me-2">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                    </button>
                    <button onclick="exportReport('accounts-payable', 'excel')" class="btn btn-primary">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function switchTab(tabName) {
        // Hide all tabs and content
        document.querySelectorAll('.report-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.report-content').forEach(content => {
            content.classList.remove('active');
        });

        // Show selected tab and content
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        document.getElementById(`${tabName}-tab`).classList.add('active');
    }

    function setDateRange(reportType, period) {
        const today = new Date();
        let dateFrom, dateTo;

        switch(period) {
            case 'daily':
                // Today's report
                dateFrom = new Date(today);
                dateTo = new Date(today);
                break;
            
            case 'weekly':
                // This week (Monday to Sunday)
                const dayOfWeek = today.getDay();
                const diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1); // Adjust when day is Sunday
                dateFrom = new Date(today.setDate(diff));
                dateTo = new Date(today);
                dateTo.setDate(dateFrom.getDate() + 6);
                break;
            
            case 'monthly':
                // This month
                dateFrom = new Date(today.getFullYear(), today.getMonth(), 1);
                dateTo = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            
            case 'yearly':
                // This year
                dateFrom = new Date(today.getFullYear(), 0, 1);
                dateTo = new Date(today.getFullYear(), 11, 31);
                break;
        }

        // Format dates as YYYY-MM-DD
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        // Set the date inputs based on report type
        if (reportType === 'inventory') {
            document.getElementById('invDateFrom').value = formatDate(dateFrom);
            document.getElementById('invDateTo').value = formatDate(dateTo);
        } else if (reportType === 'branch') {
            document.getElementById('branchDateFrom').value = formatDate(dateFrom);
            document.getElementById('branchDateTo').value = formatDate(dateTo);
        } else if (reportType === 'logistics') {
            document.getElementById('logDateFrom').value = formatDate(dateFrom);
            document.getElementById('logDateTo').value = formatDate(dateTo);
        } else if (reportType === 'franchise') {
            document.getElementById('franchiseDateFrom').value = formatDate(dateFrom);
            document.getElementById('franchiseDateTo').value = formatDate(dateTo);
        } else if (reportType === 'accounts-payable') {
            document.getElementById('apDateFrom').value = formatDate(dateFrom);
            document.getElementById('apDateTo').value = formatDate(dateTo);
        }
    }

    function exportReport(type, format) {
        const url = new URL('<?php echo base_url('dashboard/export-central-report'); ?>', window.location.origin);
        url.searchParams.set('type', type);
        url.searchParams.set('format', format);

        // Get filters based on report type
        if (type === 'inventory') {
            const branchId = document.getElementById('invBranchFilter').value;
            const itemTypeId = document.getElementById('invTypeFilter').value;
            const dateFrom = document.getElementById('invDateFrom').value;
            const dateTo = document.getElementById('invDateTo').value;
            
            if (branchId) url.searchParams.set('branch_id', branchId);
            if (itemTypeId) url.searchParams.set('item_type_id', itemTypeId);
            if (dateFrom) url.searchParams.set('date_from', dateFrom);
            if (dateTo) url.searchParams.set('date_to', dateTo);
        } else if (type === 'branch') {
            const dateFrom = document.getElementById('branchDateFrom').value;
            const dateTo = document.getElementById('branchDateTo').value;
            
            if (dateFrom) url.searchParams.set('date_from', dateFrom);
            if (dateTo) url.searchParams.set('date_to', dateTo);
        } else if (type === 'logistics') {
            const dateFrom = document.getElementById('logDateFrom').value;
            const dateTo = document.getElementById('logDateTo').value;
            
            if (dateFrom) url.searchParams.set('date_from', dateFrom);
            if (dateTo) url.searchParams.set('date_to', dateTo);
        } else if (type === 'franchise') {
            const dateFrom = document.getElementById('franchiseDateFrom').value;
            const dateTo = document.getElementById('franchiseDateTo').value;
            
            if (dateFrom) url.searchParams.set('date_from', dateFrom);
            if (dateTo) url.searchParams.set('date_to', dateTo);
        } else if (type === 'accounts-payable') {
            const supplierId = document.getElementById('apSupplierFilter').value;
            const status = document.getElementById('apStatusFilter').value;
            const dateFrom = document.getElementById('apDateFrom').value;
            const dateTo = document.getElementById('apDateTo').value;
            
            if (supplierId) url.searchParams.set('supplier_id', supplierId);
            if (status) url.searchParams.set('status', status);
            if (dateFrom) url.searchParams.set('date_from', dateFrom);
            if (dateTo) url.searchParams.set('date_to', dateTo);
        }

        window.open(url, '_blank');
    }
</script>
</body>
</html>

