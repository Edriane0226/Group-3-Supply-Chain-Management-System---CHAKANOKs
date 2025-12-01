<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-shop me-2"></i>Active Franchises</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item active">Active Franchises</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('franchise/applications') ?>" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-text me-1"></i> View Applications
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

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form action="<?= site_url('franchise/search') ?>" method="get" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Search franchises by name, location, or contact...">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Franchises Grid -->
    <?php if (!empty($franchises)): ?>
        <div class="row g-4">
            <?php foreach ($franchises as $franchise): ?>
                <?php
                $statusColors = [
                    'active' => 'success',
                    'approved' => 'primary',
                    'suspended' => 'warning',
                ];
                $color = $statusColors[$franchise['status']] ?? 'secondary';
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?= $color ?>"><?= ucfirst(esc($franchise['status'])) ?></span>
                            <small class="text-muted">#<?= esc($franchise['id']) ?></small>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title mb-1"><?= esc($franchise['applicant_name']) ?></h5>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-geo-alt me-1"></i><?= esc($franchise['proposed_location'] ?? $franchise['branch_location'] ?? 'Location N/A') ?>
                            </p>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Branch:</span>
                                    <span class="fw-semibold"><?= esc($franchise['branch_name'] ?? 'Not Assigned') ?></span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Royalty Rate:</span>
                                    <span class="fw-semibold"><?= esc($franchise['royalty_rate'] ?? 5.00) ?>%</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Contact:</span>
                                    <span class="fw-semibold"><?= esc($franchise['contact_info']) ?></span>
                                </div>
                            </div>

                            <?php if (!empty($franchise['contract_end'])): ?>
                                <?php
                                $daysUntilExpiry = (strtotime($franchise['contract_end']) - time()) / (60 * 60 * 24);
                                $expiryClass = $daysUntilExpiry <= 30 ? 'text-danger' : ($daysUntilExpiry <= 90 ? 'text-warning' : 'text-muted');
                                ?>
                                <div class="small <?= $expiryClass ?>">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Contract ends: <?= date('M d, Y', strtotime($franchise['contract_end'])) ?>
                                    <?php if ($daysUntilExpiry <= 30): ?>
                                        <span class="badge bg-danger ms-1">Expiring Soon</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <div class="d-flex gap-2">
                                <a href="<?= site_url('franchise/view/' . $franchise['id']) ?>" class="btn btn-sm btn-primary flex-grow-1">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                                <a href="<?= site_url('franchise/payments/' . $franchise['id']) ?>" class="btn btn-sm btn-outline-success" title="Payments">
                                    <i class="bi bi-credit-card"></i>
                                </a>
                                <a href="<?= site_url('franchise/allocate/' . $franchise['id']) ?>" class="btn btn-sm btn-outline-info" title="Allocate Supplies">
                                    <i class="bi bi-box"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-shop display-1 text-muted"></i>
                <h5 class="mt-3">No Active Franchises</h5>
                <p class="text-muted">There are no approved or active franchises at the moment.</p>
                <a href="<?= site_url('franchise/applications') ?>" class="btn btn-primary">
                    <i class="bi bi-file-earmark-text me-1"></i> View Applications
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

