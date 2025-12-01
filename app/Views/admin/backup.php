<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-cloud-arrow-down me-2"></i>Backup & Maintenance</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Backup</li>
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
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="<?= site_url('admin/backup/create') ?>" method="post">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-download me-2"></i>Create Database Backup
                            </button>
                        </form>
                        <form action="<?= site_url('admin/cache/clear') ?>" method="post" onsubmit="return confirm('Clear all cache?')">
                            <button type="submit" class="btn btn-outline-warning w-100">
                                <i class="bi bi-trash me-2"></i>Clear System Cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-info-circle me-2"></i>System Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">PHP Version</td>
                            <td class="text-end fw-semibold"><?= $phpInfo['version'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Memory Limit</td>
                            <td class="text-end fw-semibold"><?= $phpInfo['memory_limit'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Max Execution</td>
                            <td class="text-end fw-semibold"><?= $phpInfo['max_execution'] ?? 'N/A' ?>s</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Upload Max Size</td>
                            <td class="text-end fw-semibold"><?= $phpInfo['upload_max'] ?? 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- System Health -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-heart-pulse me-2"></i>System Health</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Database</span>
                        <?php if ($systemHealth['database_connected'] ?? false): ?>
                            <span class="badge bg-success"><i class="bi bi-check"></i> OK</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><i class="bi bi-x"></i> Error</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Cache Directory</span>
                        <?php if ($systemHealth['writable_cache'] ?? false): ?>
                            <span class="badge bg-success"><i class="bi bi-check"></i> Writable</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><i class="bi bi-x"></i> Not Writable</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Logs Directory</span>
                        <?php if ($systemHealth['writable_logs'] ?? false): ?>
                            <span class="badge bg-success"><i class="bi bi-check"></i> Writable</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><i class="bi bi-x"></i> Not Writable</span>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($systemHealth['disk_free']) && isset($systemHealth['disk_total'])): ?>
                        <hr>
                        <div class="small text-muted">
                            <div class="d-flex justify-content-between">
                                <span>Disk Space:</span>
                                <span><?= round($systemHealth['disk_free'] / 1073741824, 2) ?> GB free</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Backup Files -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-file-earmark-zip me-2"></i>Available Backups</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($backups)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Filename</th>
                                        <th>Size</th>
                                        <th>Date Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($backups as $backup): ?>
                                        <tr>
                                            <td>
                                                <i class="bi bi-file-earmark-code text-primary me-2"></i>
                                                <?= esc($backup['filename']) ?>
                                            </td>
                                            <td><?= round($backup['size'] / 1024, 2) ?> KB</td>
                                            <td><?= date('M d, Y h:i A', strtotime($backup['date'])) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= site_url('admin/backup/download/' . urlencode($backup['filename'])) ?>" 
                                                       class="btn btn-outline-primary" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <form action="<?= site_url('admin/backup/delete/' . urlencode($backup['filename'])) ?>" 
                                                          method="post" class="d-inline" 
                                                          onsubmit="return confirm('Delete this backup?')">
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-zip text-muted fs-1"></i>
                            <p class="text-muted mt-2">No backups available</p>
                            <form action="<?= site_url('admin/backup/create') ?>" method="post">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-download me-1"></i> Create First Backup
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Backup Info -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="fw-semibold"><i class="bi bi-info-circle me-2"></i>About Backups</h6>
                    <ul class="small text-muted mb-0">
                        <li>Backups are stored in: <code>writable/backups/</code></li>
                        <li>Each backup contains all database tables and data</li>
                        <li>Recommended to create backups before major updates</li>
                        <li>Download and store backups in a secure location</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

