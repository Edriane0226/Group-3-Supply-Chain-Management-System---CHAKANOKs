<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-file-earmark-person me-2"></i>Application Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise/applications') ?>">Applications</a></li>
                    <li class="breadcrumb-item active">#<?= esc($application['id']) ?></li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('franchise/applications') ?>" class="btn btn-outline-secondary">
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
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php
    $statusColors = [
        'pending' => 'warning',
        'under_review' => 'info',
        'approved' => 'success',
        'rejected' => 'danger',
        'active' => 'primary',
        'suspended' => 'secondary',
        'terminated' => 'dark',
    ];
    $color = $statusColors[$application['status']] ?? 'secondary';
    ?>

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <!-- Application Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-person me-2"></i>Applicant Information</h6>
                    <span class="badge bg-<?= $color ?> fs-6">
                        <?= ucfirst(str_replace('_', ' ', esc($application['status']))) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Applicant Name</label>
                            <p class="fw-semibold mb-0"><?= esc($application['applicant_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Contact Number</label>
                            <p class="fw-semibold mb-0"><?= esc($application['contact_info']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Email Address</label>
                            <p class="fw-semibold mb-0"><?= esc($application['email'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Application Date</label>
                            <p class="fw-semibold mb-0"><?= date('F d, Y h:i A', strtotime($application['created_at'])) ?></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Address</label>
                            <p class="fw-semibold mb-0"><?= esc($application['address'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-briefcase me-2"></i>Business Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Proposed Location</label>
                            <p class="fw-semibold mb-0"><?= esc($application['proposed_location'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Investment Capacity</label>
                            <p class="fw-semibold mb-0">
                                <?php if (!empty($application['investment_capacity'])): ?>
                                    ₱<?= number_format($application['investment_capacity'], 2) ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Business Experience</label>
                            <p class="mb-0"><?= nl2br(esc($application['business_experience'] ?? 'No information provided.')) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($application['status'] === 'rejected' && !empty($application['rejection_reason'])): ?>
                <!-- Rejection Reason -->
                <div class="card border-0 shadow-sm mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-x-circle me-2"></i>Rejection Reason</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(esc($application['rejection_reason'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array($application['status'], ['approved', 'active'])): ?>
                <!-- Contract Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Contract Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Royalty Rate</label>
                                <p class="fw-semibold mb-0"><?= esc($application['royalty_rate'] ?? 5.00) ?>%</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Franchise Fee</label>
                                <p class="fw-semibold mb-0">₱<?= number_format($application['franchise_fee'] ?? 0, 2) ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Approved By</label>
                                <p class="fw-semibold mb-0">
                                    <?= esc(($application['approver_first'] ?? '') . ' ' . ($application['approver_last'] ?? '')) ?: 'N/A' ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Contract Start</label>
                                <p class="fw-semibold mb-0">
                                    <?= !empty($application['contract_start']) ? date('F d, Y', strtotime($application['contract_start'])) : 'N/A' ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Contract End</label>
                                <p class="fw-semibold mb-0">
                                    <?= !empty($application['contract_end']) ? date('F d, Y', strtotime($application['contract_end'])) : 'N/A' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($application['notes'])): ?>
                <!-- Notes -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-sticky me-2"></i>Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(esc($application['notes'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-lg-4">
            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-gear me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <?php if (in_array($application['status'], ['pending', 'under_review'])): ?>
                        <!-- Approve Button - Both Central Admin and Franchise Manager can approve -->
                        <?php 
                        // Get role from view variable or session as fallback
                        $currentRole = isset($role) && !empty($role) ? $role : (session()->get('role') ?? '');
                        // Debug: Uncomment to see role value (remove after testing)
                        // echo "<!-- DEBUG: Role = " . htmlspecialchars($currentRole) . ", Status = " . htmlspecialchars($application['status']) . " -->";
                        ?>
                        <?php if ($currentRole === 'Central Office Admin'): ?>
                            <!-- Central Admin Approval -->
                            <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i> Approve Application (Strategic Review)
                            </button>
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-info-circle me-1"></i>
                                You can approve directly or mark for Franchise Manager review.
                            </small>
                        <?php elseif ($currentRole === 'Franchise Manager'): ?>
                            <!-- Franchise Manager Approval -->
                            <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i> Approve Application (Set Terms)
                            </button>
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Set franchise terms (royalty, fees, contract dates) and approve.
                            </small>
                        <?php else: ?>
                            <!-- Fallback: Show approve button for any authorized role (Central Admin or Franchise Manager) -->
                            <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i> Approve Application
                            </button>
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Both Central Admin and Franchise Manager can approve this application.
                            </small>
                        <?php endif; ?>
                        
                        <!-- Reject Button -->
                        <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i> Reject Application
                        </button>

                        <?php if ($application['status'] === 'pending'): ?>
                            <form action="<?= site_url('franchise/review/' . $application['id']) ?>" method="post">
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="bi bi-search me-1"></i> Mark Under Review
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php elseif ($application['status'] === 'approved'): ?>
                        <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#activateModal">
                            <i class="bi bi-shop me-1"></i> Activate Franchise
                        </button>
                    <?php elseif ($application['status'] === 'active'): ?>
                        <a href="<?= site_url('franchise/payments/' . $application['id']) ?>" class="btn btn-info w-100 mb-2">
                            <i class="bi bi-credit-card me-1"></i> Manage Payments
                        </a>
                        <a href="<?= site_url('franchise/allocate/' . $application['id']) ?>" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-box me-1"></i> Allocate Supplies
                        </a>
                        <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#suspendModal">
                            <i class="bi bi-pause-circle me-1"></i> Suspend Franchise
                        </button>
                    <?php elseif ($application['status'] === 'suspended'): ?>
                        <form action="<?= site_url('franchise/reactivate/' . $application['id']) ?>" method="post">
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-play-circle me-1"></i> Reactivate Franchise
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if (in_array($application['status'], ['active', 'suspended'])): ?>
                        <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#terminateModal">
                            <i class="bi bi-slash-circle me-1"></i> Terminate Franchise
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <?php if (in_array($application['status'], ['approved', 'active'])): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-graph-up me-2"></i>Financial Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Payments</span>
                            <span class="fw-bold text-success">₱<?= number_format($application['total_payments'] ?? 0, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Supplies</span>
                            <span class="fw-bold text-primary">₱<?= number_format($application['total_supplies'] ?? 0, 2) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Net Revenue</span>
                            <span class="fw-bold">₱<?= number_format(($application['total_payments'] ?? 0) - ($application['total_supplies'] ?? 0), 2) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('franchise/approve/' . $application['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-check-circle text-success me-2"></i>Approve Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php 
                    // Get role from view variable or session
                    $modalRole = $role ?? session()->get('role') ?? '';
                    ?>
                    <?php if ($modalRole === 'Central Office Admin'): ?>
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Central Admin Approval:</strong> You can approve this application directly. Franchise Manager can set detailed terms later if needed.
                        </div>
                    <?php elseif ($modalRole === 'Franchise Manager'): ?>
                        <div class="alert alert-primary mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Franchise Manager Approval:</strong> Set all franchise terms (royalty, fees, contract dates) before approving.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Approval:</strong> Set franchise terms (royalty, fees, contract dates) and approve this application.
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Royalty Rate (%)</label>
                        <input type="number" step="0.01" name="royalty_rate" class="form-control" value="5.00" required>
                        <small class="text-muted">Default: 5.00%</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Franchise Fee (₱)</label>
                        <input type="number" step="0.01" name="franchise_fee" class="form-control" value="0">
                        <small class="text-muted">One-time franchise fee</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contract Start</label>
                            <input type="date" name="contract_start" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contract End</label>
                            <input type="date" name="contract_end" class="form-control" value="<?= date('Y-m-d', strtotime('+5 years')) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any additional notes or comments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Approve Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('franchise/reject/' . $application['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Reject Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Please provide the reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Activate Modal -->
<div class="modal fade" id="activateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('franchise/activate/' . $application['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-shop text-primary me-2"></i>Activate Franchise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Activating this franchise will create a new branch entry for operations.</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        A new branch will be created with status "franchise" using the applicant's proposed location.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shop me-1"></i> Activate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('franchise/suspend/' . $application['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pause-circle text-warning me-2"></i>Suspend Franchise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Suspension</label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="Please provide the reason for suspension..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-pause-circle me-1"></i> Suspend
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Terminate Modal -->
<div class="modal fade" id="terminateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('franchise/terminate/' . $application['id']) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-slash-circle text-danger me-2"></i>Terminate Franchise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Warning:</strong> This action cannot be undone. The franchise will be permanently terminated.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Termination</label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="Please provide the reason for termination..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-slash-circle me-1"></i> Terminate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

