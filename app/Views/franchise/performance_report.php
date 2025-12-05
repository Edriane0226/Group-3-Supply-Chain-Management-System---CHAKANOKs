<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Performance Report</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise/reports') ?>">Reports</a></li>
                    <li class="breadcrumb-item active"><?= esc($franchise['applicant_name']) ?></li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <form method="get" class="d-flex gap-2">
                <input type="date" name="start_date" value="<?= esc($startDate) ?>" class="form-control form-control-sm">
                <input type="date" name="end_date" value="<?= esc($endDate) ?>" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <!-- Franchise Info -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="fw-semibold mb-0"><i class="bi bi-shop me-2"></i><?= esc($franchise['applicant_name']) ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Status:</strong> 
                        <span class="badge bg-<?= $franchise['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <?= esc(ucfirst($franchise['status'])) ?>
                        </span>
                    </p>
                    <p class="mb-2"><strong>Contact:</strong> <?= esc($franchise['contact_info']) ?></p>
                    <p class="mb-2"><strong>Email:</strong> <?= esc($franchise['email'] ?? 'N/A') ?></p>
                    <p class="mb-2"><strong>Location:</strong> <?= esc($franchise['proposed_location'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Contract Start:</strong> 
                        <?= $franchise['contract_start'] ? date('M d, Y', strtotime($franchise['contract_start'])) : 'N/A' ?>
                    </p>
                    <p class="mb-2"><strong>Contract End:</strong> 
                        <?= $franchise['contract_end'] ? date('M d, Y', strtotime($franchise['contract_end'])) : 'N/A' ?>
                    </p>
                    <p class="mb-2"><strong>Royalty Rate:</strong> <?= esc($franchise['royalty_rate'] ?? 0) ?>%</p>
                    <p class="mb-2"><strong>Days Active:</strong> <?= esc($performance['days_active'] ?? 0) ?> days</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <?php if (!empty($performance)): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fs-2 text-success d-block mb-2"></i>
                    <h4 class="mb-0">₱<?= number_format($performance['total_payments'], 2) ?></h4>
                    <small class="text-muted">Total Payments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up fs-2 text-info d-block mb-2"></i>
                    <h4 class="mb-0">₱<?= number_format($performance['avg_monthly_revenue'], 2) ?></h4>
                    <small class="text-muted">Avg Monthly Revenue</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam fs-2 text-primary d-block mb-2"></i>
                    <h4 class="mb-0"><?= number_format($performance['supply_utilization'], 1) ?>%</h4>
                    <small class="text-muted">Supply Utilization</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <?php
                    $scoreColor = 'success';
                    if ($performance['overall_score'] < 50) $scoreColor = 'danger';
                    elseif ($performance['overall_score'] < 70) $scoreColor = 'warning';
                    ?>
                    <i class="bi bi-trophy fs-2 text-<?= $scoreColor ?> d-block mb-2"></i>
                    <h4 class="mb-0 text-<?= $scoreColor ?>"><?= number_format($performance['overall_score'], 1) ?></h4>
                    <small class="text-muted">Overall Score</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Breakdown -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-cash-coin me-2"></i>Payment Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-percent me-1"></i> Royalties</span>
                            <span class="fw-semibold">₱<?= number_format($performance['royalty_payments'], 2) ?></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 style="width: <?= $performance['total_payments'] > 0 ? ($performance['royalty_payments'] / $performance['total_payments']) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-building me-1"></i> Franchise Fees</span>
                            <span class="fw-semibold">₱<?= number_format($performance['franchise_fee_payments'], 2) ?></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: <?= $performance['total_payments'] > 0 ? ($performance['franchise_fee_payments'] / $performance['total_payments']) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-box me-1"></i> Supply Payments</span>
                            <span class="fw-semibold">₱<?= number_format($performance['supply_payments'], 2) ?></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" 
                                 style="width: <?= $performance['total_payments'] > 0 ? ($performance['supply_payments'] / $performance['total_payments']) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total Payments</span>
                        <span class="fw-bold text-success fs-5">₱<?= number_format($performance['total_payments'], 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted">Payment Count</span>
                        <span class="text-muted"><?= esc($performance['payment_count']) ?> transactions</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supply Allocation Metrics -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-box-seam me-2"></i>Supply Allocation Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Allocations</span>
                            <span class="fw-semibold"><?= esc($performance['total_allocations']) ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivered Allocations</span>
                            <span class="fw-semibold text-success"><?= esc($performance['delivered_allocations']) ?></span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" 
                                 style="width: <?= $performance['supply_utilization'] ?>%">
                                <?= number_format($performance['supply_utilization'], 1) ?>%
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Supply Value</span>
                            <span class="fw-semibold">₱<?= number_format($performance['total_supply_value'], 2) ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Average Allocation Value</span>
                            <span class="fw-semibold">₱<?= number_format($performance['avg_allocation_value'], 2) ?></span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Utilization Rate</span>
                        <span class="fw-bold text-<?= $performance['supply_utilization'] >= 80 ? 'success' : ($performance['supply_utilization'] >= 50 ? 'warning' : 'danger') ?>">
                            <?= number_format($performance['supply_utilization'], 1) ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Payments -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h6 class="fw-semibold mb-0"><i class="bi bi-receipt me-2"></i>Recent Payments</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($payments)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($payments, 0, 10) as $payment): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= esc(ucfirst(str_replace('_', ' ', $payment['payment_type']))) ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold">₱<?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= esc(ucfirst($payment['payment_method'] ?? 'N/A')) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $payment['payment_status'] === 'completed' ? 'success' : 'warning' ?>">
                                            <?= esc(ucfirst($payment['payment_status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($payment['reference_number'] ?? 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= site_url('franchise/payments/' . $franchise['id']) ?>" class="btn btn-sm btn-outline-primary">
                        View All Payments
                    </a>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No payments recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Allocations -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h6 class="fw-semibold mb-0"><i class="bi bi-box-seam me-2"></i>Recent Supply Allocations</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($allocations)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($allocations, 0, 10) as $allocation): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($allocation['allocation_date'])) ?></td>
                                    <td><?= esc($allocation['item_name']) ?></td>
                                    <td><?= esc($allocation['quantity']) ?> <?= esc($allocation['unit']) ?></td>
                                    <td>₱<?= number_format($allocation['unit_price'], 2) ?></td>
                                    <td class="fw-semibold">₱<?= number_format($allocation['total_amount'], 2) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'approved' => 'info',
                                            'preparing' => 'primary',
                                            'shipped' => 'secondary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $color = $statusColors[$allocation['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>">
                                            <?= esc(ucfirst($allocation['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= site_url('franchise/allocations/' . $franchise['id']) ?>" class="btn btn-sm btn-outline-primary">
                        View All Allocations
                    </a>
                </div>
            <?php else: ?>
                <p class="text-muted text-center mb-0">No supply allocations recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

