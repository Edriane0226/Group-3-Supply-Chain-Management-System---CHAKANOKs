<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <h1 class="h5 fw-bold"><i class="bi bi-shop me-2"></i>Franchise Management Dashboard</h1>
        <div class="d-flex align-items-center">
            <span class="me-2 text-muted small">
                <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?>
            </span>
            <span class="me-2 text-muted small">(<?= esc(session()->get('role')) ?>)</span>
            <div class="user-avatar">
                <?= esc(substr(session()->get('first_Name') ?? 'U', 0, 1)) ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Notifications Section -->
    <?php if (!empty($notifications ?? [])): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-bell me-2"></i>Recent Notifications</h6>
            <?php if (($unreadCount ?? 0) > 0): ?>
                <span class="badge bg-danger"><?= $unreadCount ?> unread</span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                    <div class="list-group-item <?= $notification['status'] === 'pending' ? 'bg-light' : '' ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= esc($notification['title']) ?></h6>
                            <small class="text-muted"><?= date('M d, H:i', strtotime($notification['created_at'])) ?></small>
                        </div>
                        <p class="mb-1 small"><?= esc($notification['message']) ?></p>
                        <?php if ($notification['status'] === 'pending'): ?>
                            <small class="badge bg-warning text-dark">New</small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-3">
                <a href="<?= site_url('notifications') ?>" class="btn btn-sm btn-outline-primary">View All Notifications</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Pending Applications</h6>
                            <h3 class="mb-0"><?= esc($stats['pending'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Active Franchises</h6>
                            <h3 class="mb-0"><?= esc(($stats['approved'] ?? 0) + ($stats['active'] ?? 0)) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-currency-dollar text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Revenue</h6>
                            <h3 class="mb-0">₱<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="bi bi-cash-stack text-info fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">This Month</h6>
                            <h3 class="mb-0">₱<?= number_format($paymentStats['this_month'] ?? 0, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= site_url('franchise/create') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> New Application
                        </a>
                        <a href="<?= site_url('franchise/applications') ?>" class="btn btn-outline-warning">
                            <i class="bi bi-file-earmark-text me-1"></i> View Applications
                        </a>
                        <a href="<?= site_url('franchise/list') ?>" class="btn btn-outline-success">
                            <i class="bi bi-shop me-1"></i> Active Franchises
                        </a>
                        <a href="<?= site_url('franchise/payments') ?>" class="btn btn-outline-info">
                            <i class="bi bi-credit-card me-1"></i> All Payments
                        </a>
                        <a href="<?= site_url('franchise/reports') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-bar-chart me-1"></i> Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Pending Applications -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-2 text-warning"></i>Pending Applications</h6>
                    <a href="<?= site_url('franchise/applications?status=pending') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($pendingApplications)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($pendingApplications, 0, 5) as $app): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($app['applicant_name']) ?></strong>
                                                <br><small class="text-muted"><?= esc($app['contact_info']) ?></small>
                                            </td>
                                            <td><?= esc($app['proposed_location'] ?? 'N/A') ?></td>
                                            <td><small><?= date('M d, Y', strtotime($app['created_at'])) ?></small></td>
                                            <td>
                                                <a href="<?= site_url('franchise/application/' . $app['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">No pending applications</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Active Franchises -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-shop me-2 text-success"></i>Active Franchises</h6>
                    <a href="<?= site_url('franchise/list') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($activeFranchises)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Franchise</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($activeFranchises, 0, 5) as $franchise): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($franchise['applicant_name']) ?></strong>
                                                <br><small class="text-muted"><?= esc($franchise['contact_info']) ?></small>
                                            </td>
                                            <td><?= esc($franchise['branch_name'] ?? 'Not Assigned') ?></td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'active' => 'success',
                                                    'approved' => 'primary',
                                                    'suspended' => 'danger',
                                                ];
                                                $color = $statusColors[$franchise['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= ucfirst(esc($franchise['status'])) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('franchise/view/' . $franchise['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-shop fs-1 d-block mb-2"></i>
                            <p class="mb-0">No active franchises yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-credit-card me-2 text-info"></i>Recent Payments</h6>
                    <a href="<?= site_url('franchise/payments') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentPayments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentPayments as $payment): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= esc($payment['applicant_name'] ?? 'N/A') ?></strong>
                                            <br><small class="text-muted">
                                                <?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?> · 
                                                <?= date('M d, Y', strtotime($payment['payment_date'])) ?>
                                            </small>
                                        </div>
                                        <span class="text-success fw-bold">₱<?= number_format($payment['amount'], 2) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-credit-card fs-1 d-block mb-2"></i>
                            <p class="mb-0">No payments recorded yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Expiring Contracts -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Expiring Contracts (30 days)</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($expiringContracts)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($expiringContracts as $contract): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= esc($contract['applicant_name']) ?></strong>
                                            <br><small class="text-muted">Contract ends: <?= date('M d, Y', strtotime($contract['contract_end'])) ?></small>
                                        </div>
                                        <a href="<?= site_url('franchise/view/' . $contract['id']) ?>" class="btn btn-sm btn-outline-warning">
                                            Renew
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                            <p class="mb-0">No contracts expiring soon</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Summary by Type -->
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-pie-chart me-2"></i>Payment Summary by Type</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1">₱<?= number_format($paymentStats['franchise_fee'] ?? 0, 0) ?></h4>
                                <small class="text-muted">Franchise Fees</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1">₱<?= number_format($paymentStats['royalty'] ?? 0, 0) ?></h4>
                                <small class="text-muted">Royalties</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1">₱<?= number_format($paymentStats['supply_payment'] ?? 0, 0) ?></h4>
                                <small class="text-muted">Supply Payments</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1">₱<?= number_format($paymentStats['penalty'] ?? 0, 0) ?></h4>
                                <small class="text-muted">Penalties</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-1">₱<?= number_format($paymentStats['other'] ?? 0, 0) ?></h4>
                                <small class="text-muted">Other</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="text-center p-3 bg-warning bg-opacity-25 rounded">
                                <h4 class="mb-1">₱<?= number_format($paymentStats['total'] ?? 0, 0) ?></h4>
                                <small class="text-muted fw-bold">Total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

