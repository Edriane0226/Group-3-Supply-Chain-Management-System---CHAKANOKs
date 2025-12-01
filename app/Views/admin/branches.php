<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-building me-2"></i>Branch Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Branches</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Branches Grid -->
    <div class="row g-4">
        <?php if (!empty($branches)): ?>
            <?php foreach ($branches as $branch): ?>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-semibold mb-1">
                                        <i class="bi bi-building text-primary me-2"></i>
                                        <?= esc($branch['branch_name']) ?>
                                    </h5>
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?= esc($branch['location'] ?? 'No location') ?>
                                    </small>
                                </div>
                                <?php
                                $statusColors = [
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    'franchise' => 'info',
                                ];
                                $color = $statusColors[$branch['status'] ?? 'active'] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>">
                                    <?= ucfirst(esc($branch['status'] ?? 'active')) ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($branch['contact_info'])): ?>
                                <p class="small text-muted mb-2">
                                    <i class="bi bi-telephone me-1"></i>
                                    <?= esc($branch['contact_info']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <small class="text-muted">
                                Created: <?= date('M d, Y', strtotime($branch['created_at'] ?? 'now')) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-building text-muted fs-1"></i>
                        <p class="text-muted mt-2">No branches found</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

