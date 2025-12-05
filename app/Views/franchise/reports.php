<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i>Franchise Reports</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" onchange="window.location.href='<?= site_url('franchise/reports') ?>?year=' + this.value">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 text-primary d-block mb-2"></i>
                    <h3 class="mb-0"><?= esc($stats['total_applications'] ?? 0) ?></h3>
                    <small class="text-muted">Total Applications</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-shop fs-2 text-success d-block mb-2"></i>
                    <h3 class="mb-0"><?= esc(($stats['approved'] ?? 0) + ($stats['active'] ?? 0)) ?></h3>
                    <small class="text-muted">Active Franchises</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar fs-2 text-info d-block mb-2"></i>
                    <h3 class="mb-0">₱<?= number_format($stats['total_revenue'] ?? 0, 0) ?></h3>
                    <small class="text-muted">Total Revenue</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up-arrow fs-2 text-warning d-block mb-2"></i>
                    <h3 class="mb-0">₱<?= number_format($paymentStats['this_month'] ?? 0, 0) ?></h3>
                    <small class="text-muted">This Month</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Application Status Breakdown -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-pie-chart me-2"></i>Application Status Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center p-3 bg-warning bg-opacity-10 rounded">
                                <div class="me-3">
                                    <span class="badge bg-warning fs-6"><?= esc($stats['pending'] ?? 0) ?></span>
                                </div>
                                <div>
                                    <div class="fw-semibold">Pending</div>
                                    <small class="text-muted">Awaiting review</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-3 bg-info bg-opacity-10 rounded">
                                <div class="me-3">
                                    <span class="badge bg-info fs-6"><?= esc($stats['under_review'] ?? 0) ?></span>
                                </div>
                                <div>
                                    <div class="fw-semibold">Under Review</div>
                                    <small class="text-muted">Being evaluated</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-3 bg-success bg-opacity-10 rounded">
                                <div class="me-3">
                                    <span class="badge bg-success fs-6"><?= esc($stats['approved'] ?? 0) ?></span>
                                </div>
                                <div>
                                    <div class="fw-semibold">Approved</div>
                                    <small class="text-muted">Ready to activate</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-3 bg-primary bg-opacity-10 rounded">
                                <div class="me-3">
                                    <span class="badge bg-primary fs-6"><?= esc($stats['active'] ?? 0) ?></span>
                                </div>
                                <div>
                                    <div class="fw-semibold">Active</div>
                                    <small class="text-muted">Operating</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-3 bg-secondary bg-opacity-10 rounded">
                                <div class="me-3">
                                    <span class="badge bg-secondary fs-6"><?= esc($stats['suspended'] ?? 0) ?></span>
                                </div>
                                <div>
                                    <div class="fw-semibold">Suspended</div>
                                    <small class="text-muted">Temporarily halted</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-3 bg-danger bg-opacity-10 rounded">
                                <div class="me-3">
                                    <span class="badge bg-danger fs-6"><?= esc($stats['rejected'] ?? 0) ?></span>
                                </div>
                                <div>
                                    <div class="fw-semibold">Rejected</div>
                                    <small class="text-muted">Not approved</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Type -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-cash-stack me-2"></i>Revenue by Payment Type</h6>
                </div>
                <div class="card-body">
                    <?php
                    $paymentTypes = [
                        'franchise_fee' => ['label' => 'Franchise Fees', 'color' => 'primary', 'icon' => 'building'],
                        'royalty' => ['label' => 'Royalties', 'color' => 'success', 'icon' => 'percent'],
                        'supply_payment' => ['label' => 'Supply Payments', 'color' => 'info', 'icon' => 'box'],
                        'penalty' => ['label' => 'Penalties', 'color' => 'danger', 'icon' => 'exclamation-triangle'],
                        'other' => ['label' => 'Other', 'color' => 'secondary', 'icon' => 'three-dots'],
                    ];
                    $total = $paymentStats['total'] ?? 1;
                    ?>
                    <div class="mb-4">
                        <?php foreach ($paymentTypes as $key => $type): ?>
                            <?php
                            $amount = $paymentStats[$key] ?? 0;
                            $percent = $total > 0 ? ($amount / $total) * 100 : 0;
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><i class="bi bi-<?= $type['icon'] ?> me-1"></i> <?= $type['label'] ?></span>
                                    <span class="fw-semibold">₱<?= number_format($amount, 2) ?></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $type['color'] ?>" style="width: <?= $percent ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total Revenue</span>
                        <span class="fw-bold text-success fs-5">₱<?= number_format($paymentStats['total'] ?? 0, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-graph-up me-2"></i>Monthly Revenue - <?= esc($year) ?></h6>
                </div>
                <div class="card-body">
                    <?php
                    // Process monthly data
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $monthlyTotals = array_fill(1, 12, 0);
                    foreach ($monthlyReport as $row) {
                        $monthlyTotals[(int)$row['month']] += (float)$row['total'];
                    }
                    $maxValue = max($monthlyTotals) ?: 1;
                    ?>
                    <div class="row g-2">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <?php $heightPercent = ($monthlyTotals[$m] / $maxValue) * 100; ?>
                            <div class="col text-center">
                                <div class="d-flex flex-column align-items-center" style="height: 200px;">
                                    <div class="flex-grow-1 d-flex align-items-end w-100" style="max-width: 40px;">
                                        <div class="bg-primary rounded-top w-100" style="height: <?= max($heightPercent, 2) ?>%; min-height: 4px;" 
                                             title="₱<?= number_format($monthlyTotals[$m], 2) ?>"></div>
                                    </div>
                                    <small class="text-muted mt-2"><?= $months[$m - 1] ?></small>
                                    <small class="fw-semibold">₱<?= number_format($monthlyTotals[$m] / 1000, 0) ?>k</small>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Franchise Performance Reports -->
        <?php if (!empty($performanceData)): ?>
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-trophy me-2"></i>Franchise Performance Rankings</h6>
                    <small class="text-muted">Period: <?= date('M d, Y', strtotime($startDate)) ?> - <?= date('M d, Y', strtotime($endDate)) ?></small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Franchise</th>
                                    <th>Status</th>
                                    <th>Total Payments</th>
                                    <th>Avg Monthly Revenue</th>
                                    <th>Supply Utilization</th>
                                    <th>Overall Score</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($performanceData as $index => $perf): ?>
                                    <?php
                                    $scoreColor = 'success';
                                    if ($perf['overall_score'] < 50) $scoreColor = 'danger';
                                    elseif ($perf['overall_score'] < 70) $scoreColor = 'warning';
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?= $index < 3 ? 'warning text-dark' : 'secondary' ?>">
                                                #<?= $index + 1 ?>
                                            </span>
                                        </td>
                                        <td><?= esc($perf['franchise_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $perf['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= esc(ucfirst($perf['status'])) ?>
                                            </span>
                                        </td>
                                        <td>₱<?= number_format($perf['total_payments'], 2) ?></td>
                                        <td>₱<?= number_format($perf['avg_monthly_revenue'], 2) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 20px; width: 100px;">
                                                    <div class="progress-bar bg-info" style="width: <?= $perf['supply_utilization'] ?>%">
                                                        <?= number_format($perf['supply_utilization'], 1) ?>%
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $scoreColor ?> fs-6">
                                                <?= number_format($perf['overall_score'], 1) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('franchise/performance/' . $perf['franchise_id']) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Overdue Payments -->
        <?php if (!empty($overdueFranchises)): ?>
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm border-warning">
                <div class="card-header bg-warning text-dark border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Franchises with Overdue Payments</h6>
                    <form method="post" action="<?= site_url('franchise/send-reminders') ?>" class="d-inline">
                        <input type="hidden" name="days_overdue" value="30">
                        <button type="submit" class="btn btn-sm btn-dark">
                            <i class="bi bi-send me-1"></i>Send Reminders
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Franchise</th>
                                    <th>Contact</th>
                                    <th>Days Overdue</th>
                                    <th>Last Payment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueFranchises as $franchise): ?>
                                    <tr>
                                        <td><?= esc($franchise['applicant_name']) ?></td>
                                        <td><?= esc($franchise['contact_info']) ?></td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <?= esc($franchise['days_overdue']) ?> days
                                            </span>
                                        </td>
                                        <td>
                                            <?= $franchise['last_payment_date'] 
                                                ? date('M d, Y', strtotime($franchise['last_payment_date'])) 
                                                : '<span class="text-muted">No payments yet</span>' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $franchise['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= esc(ucfirst($franchise['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('franchise/view/' . $franchise['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

