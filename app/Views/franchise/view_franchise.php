<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-shop me-2"></i><?= esc($franchise['applicant_name']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise/list') ?>">Active Franchises</a></li>
                    <li class="breadcrumb-item active"><?= esc($franchise['applicant_name']) ?></li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('franchise/list') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php
    $statusColors = [
        'active' => 'success',
        'approved' => 'primary',
        'suspended' => 'warning',
        'terminated' => 'danger',
    ];
    $color = $statusColors[$franchise['status']] ?? 'secondary';
    ?>

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-credit-card fs-2 text-success"></i>
                    <h4 class="mt-2 mb-0">₱<?= number_format($franchise['total_payments'] ?? 0, 2) ?></h4>
                    <small class="text-muted">Total Payments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam fs-2 text-info"></i>
                    <h4 class="mt-2 mb-0">₱<?= number_format($franchise['total_supplies'] ?? 0, 2) ?></h4>
                    <small class="text-muted">Total Supplies</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-percent fs-2 text-warning"></i>
                    <h4 class="mt-2 mb-0"><?= esc($franchise['royalty_rate'] ?? 5.00) ?>%</h4>
                    <small class="text-muted">Royalty Rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <span class="badge bg-<?= $color ?> fs-6 mb-2"><?= ucfirst(esc($franchise['status'])) ?></span>
                    <h6 class="mb-0"><?= esc($franchise['branch_name'] ?? 'No Branch') ?></h6>
                    <small class="text-muted">Assigned Branch</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Franchise Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-info-circle me-2"></i>Franchise Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Contact Person</label>
                            <p class="fw-semibold mb-0"><?= esc($franchise['applicant_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Contact Number</label>
                            <p class="fw-semibold mb-0"><?= esc($franchise['contact_info']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Email</label>
                            <p class="fw-semibold mb-0"><?= esc($franchise['email'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Location</label>
                            <p class="fw-semibold mb-0"><?= esc($franchise['proposed_location'] ?? $franchise['branch_location'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Contract Start</label>
                            <p class="fw-semibold mb-0">
                                <?= !empty($franchise['contract_start']) ? date('F d, Y', strtotime($franchise['contract_start'])) : 'N/A' ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Contract End</label>
                            <p class="fw-semibold mb-0">
                                <?= !empty($franchise['contract_end']) ? date('F d, Y', strtotime($franchise['contract_end'])) : 'N/A' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-credit-card me-2"></i>Recent Payments</h6>
                    <a href="<?= site_url('franchise/payments/' . $franchise['id']) ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($payments)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Method</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($payments, 0, 5) as $payment): ?>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?>
                                                </span>
                                            </td>
                                            <td><?= ucfirst($payment['payment_method'] ?? 'cash') ?></td>
                                            <td class="text-end fw-bold text-success">₱<?= number_format($payment['amount'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-credit-card fs-1 d-block mb-2"></i>
                            <p class="mb-0">No payments recorded yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Supply Allocations -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-box-seam me-2"></i>Supply Allocations</h6>
                    <a href="<?= site_url('franchise/allocate/' . $franchise['id']) ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus me-1"></i> Allocate
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($allocations)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($allocations, 0, 5) as $alloc): ?>
                                        <?php
                                        $allocStatusColors = [
                                            'pending' => 'warning',
                                            'approved' => 'info',
                                            'preparing' => 'primary',
                                            'shipped' => 'info',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                        ];
                                        $allocColor = $allocStatusColors[$alloc['status']] ?? 'secondary';
                                        ?>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime($alloc['allocation_date'])) ?></td>
                                            <td><?= esc($alloc['item_name']) ?></td>
                                            <td><?= esc($alloc['quantity']) ?> <?= esc($alloc['unit']) ?></td>
                                            <td><span class="badge bg-<?= $allocColor ?>"><?= ucfirst($alloc['status']) ?></span></td>
                                            <td class="text-end">₱<?= number_format($alloc['total_amount'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-box fs-1 d-block mb-2"></i>
                            <p class="mb-0">No supply allocations yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="<?= site_url('franchise/payments/' . $franchise['id']) ?>" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-plus-circle me-1"></i> Record Payment
                    </a>
                    <a href="<?= site_url('franchise/allocate/' . $franchise['id']) ?>" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-box me-1"></i> Allocate Supplies
                    </a>
                    <a href="<?= site_url('franchise/allocations/' . $franchise['id']) ?>" class="btn btn-outline-info w-100 mb-2">
                        <i class="bi bi-list-check me-1"></i> View All Allocations
                    </a>
                    <hr>
                    <?php if ($franchise['status'] === 'active'): ?>
                        <button type="button" class="btn btn-outline-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#suspendModal">
                            <i class="bi bi-pause-circle me-1"></i> Suspend Franchise
                        </button>
                    <?php elseif ($franchise['status'] === 'suspended'): ?>
                        <form action="<?= site_url('franchise/reactivate/' . $franchise['id']) ?>" method="post">
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-play-circle me-1"></i> Reactivate
                            </button>
                        </form>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#terminateModal">
                        <i class="bi bi-slash-circle me-1"></i> Terminate
                    </button>
                </div>
            </div>

            <!-- Contract Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-file-text me-2"></i>Contract Summary</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Franchise Fee</span>
                            <strong>₱<?= number_format($franchise['franchise_fee'] ?? 0, 2) ?></strong>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Royalty Rate</span>
                            <strong><?= esc($franchise['royalty_rate'] ?? 5.00) ?>%</strong>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Contract Duration</span>
                            <?php if (!empty($franchise['contract_start']) && !empty($franchise['contract_end'])): ?>
                                <?php
                                $start = new DateTime($franchise['contract_start']);
                                $end = new DateTime($franchise['contract_end']);
                                $diff = $start->diff($end);
                                ?>
                                <strong><?= $diff->y ?> years <?= $diff->m ?> months</strong>
                            <?php else: ?>
                                <strong>N/A</strong>
                            <?php endif; ?>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Days Remaining</span>
                            <?php if (!empty($franchise['contract_end'])): ?>
                                <?php
                                $daysLeft = (strtotime($franchise['contract_end']) - time()) / (60 * 60 * 24);
                                $daysClass = $daysLeft <= 30 ? 'text-danger' : ($daysLeft <= 90 ? 'text-warning' : 'text-success');
                                ?>
                                <strong class="<?= $daysClass ?>"><?= max(0, round($daysLeft)) ?> days</strong>
                            <?php else: ?>
                                <strong>N/A</strong>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('franchise/suspend/' . $franchise['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pause-circle text-warning me-2"></i>Suspend Franchise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Suspension</label>
                        <textarea name="reason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Suspend</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Terminate Modal -->
<div class="modal fade" id="terminateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('franchise/terminate/' . $franchise['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-slash-circle text-danger me-2"></i>Terminate Franchise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Termination</label>
                        <textarea name="reason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Terminate</button>
                </div>
            </form>
        </div>
    </div>
</div>

