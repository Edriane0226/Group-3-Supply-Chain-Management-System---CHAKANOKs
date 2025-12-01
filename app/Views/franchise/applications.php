<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Franchise Applications</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item active">Applications</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('franchise/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> New Application
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

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link <?= empty($currentStatus) ? 'active' : '' ?>" href="<?= site_url('franchise/applications') ?>">
                        All
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentStatus ?? '') === 'pending' ? 'active' : '' ?>" href="<?= site_url('franchise/applications?status=pending') ?>">
                        <i class="bi bi-hourglass-split me-1"></i> Pending
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentStatus ?? '') === 'under_review' ? 'active' : '' ?>" href="<?= site_url('franchise/applications?status=under_review') ?>">
                        <i class="bi bi-search me-1"></i> Under Review
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentStatus ?? '') === 'approved' ? 'active' : '' ?>" href="<?= site_url('franchise/applications?status=approved') ?>">
                        <i class="bi bi-check-circle me-1"></i> Approved
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentStatus ?? '') === 'rejected' ? 'active' : '' ?>" href="<?= site_url('franchise/applications?status=rejected') ?>">
                        <i class="bi bi-x-circle me-1"></i> Rejected
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (!empty($applications)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Applicant</th>
                                <th>Contact</th>
                                <th>Proposed Location</th>
                                <th>Investment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><strong>#<?= esc($app['id']) ?></strong></td>
                                    <td>
                                        <strong><?= esc($app['applicant_name']) ?></strong>
                                        <?php if (!empty($app['email'])): ?>
                                            <br><small class="text-muted"><?= esc($app['email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($app['contact_info']) ?></td>
                                    <td><?= esc($app['proposed_location'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if (!empty($app['investment_capacity'])): ?>
                                            ₱<?= number_format($app['investment_capacity'], 2) ?>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
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
                                        $statusIcons = [
                                            'pending' => 'hourglass-split',
                                            'under_review' => 'search',
                                            'approved' => 'check-circle',
                                            'rejected' => 'x-circle',
                                            'active' => 'shop',
                                            'suspended' => 'pause-circle',
                                            'terminated' => 'slash-circle',
                                        ];
                                        $color = $statusColors[$app['status']] ?? 'secondary';
                                        $icon = $statusIcons[$app['status']] ?? 'circle';
                                        ?>
                                        <span class="badge bg-<?= $color ?>">
                                            <i class="bi bi-<?= $icon ?> me-1"></i>
                                            <?= ucfirst(str_replace('_', ' ', esc($app['status']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('M d, Y', strtotime($app['created_at'])) ?></small>
                                        <br><small class="text-muted"><?= date('h:i A', strtotime($app['created_at'])) ?></small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="<?= site_url('franchise/application/' . $app['id']) ?>" class="btn btn-sm btn-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($app['status'] === 'pending'): ?>
                                                <form action="<?= site_url('franchise/review/' . $app['id']) ?>" method="post" class="d-inline">
                                                    <button type="submit" class="btn btn-sm btn-info" title="Mark Under Review">
                                                        <i class="bi bi-search"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="mt-3">No Applications Found</h5>
                    <p class="text-muted">
                        <?php if (!empty($currentStatus)): ?>
                            There are no <?= str_replace('_', ' ', $currentStatus) ?> applications at the moment.
                        <?php else: ?>
                            There are no franchise applications yet.
                        <?php endif; ?>
                    </p>
                    <a href="<?= site_url('franchise/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Create New Application
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

