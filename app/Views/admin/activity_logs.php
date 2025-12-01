<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Activity Logs</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Activity Logs</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
            <i class="bi bi-trash me-1"></i> Clear Old Logs
        </button>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="<?= site_url('admin/activity-logs') ?>" method="get" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search..." 
                           value="<?= esc($filters['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        <?php foreach ($actions as $a): ?>
                            <option value="<?= esc($a['action']) ?>" <?= ($filters['action'] ?? '') == $a['action'] ? 'selected' : '' ?>>
                                <?= esc(ucfirst($a['action'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="module" class="form-select">
                        <option value="">All Modules</option>
                        <?php foreach ($modules as $m): ?>
                            <option value="<?= esc($m['module']) ?>" <?= ($filters['module'] ?? '') == $m['module'] ? 'selected' : '' ?>>
                                <?= esc(ucfirst($m['module'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" placeholder="From" 
                           value="<?= esc($filters['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" placeholder="To" 
                           value="<?= esc($filters['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Showing <?= count($logs) ?> of <?= $totalLogs ?> logs</span>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($logs)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date/Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Description</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td>
                                        <small>
                                            <?= date('M d, Y', strtotime($log['created_at'])) ?><br>
                                            <span class="text-muted"><?= date('h:i A', strtotime($log['created_at'])) ?></span>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold"><?= esc($log['user_name'] ?? 'System') ?></span>
                                        <?php if (!empty($log['user_role'])): ?>
                                            <br><small class="text-muted"><?= esc($log['user_role']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $actionColors = [
                                            'login' => 'success',
                                            'logout' => 'secondary',
                                            'create' => 'primary',
                                            'update' => 'info',
                                            'delete' => 'danger',
                                            'backup' => 'warning',
                                            'password_reset' => 'warning',
                                            'clear_logs' => 'dark',
                                            'clear_cache' => 'dark',
                                        ];
                                        $color = $actionColors[$log['action']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= esc($log['action']) ?></span>
                                    </td>
                                    <td><span class="badge bg-light text-dark"><?= esc($log['module'] ?? '-') ?></span></td>
                                    <td>
                                        <small><?= esc($log['description'] ?? '-') ?></small>
                                    </td>
                                    <td><small class="text-muted"><?= esc($log['ip_address'] ?? '-') ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="card-footer bg-white">
                        <nav>
                            <ul class="pagination pagination-sm mb-0 justify-content-center">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= site_url('admin/activity-logs') ?>?page=<?= $i ?>&<?= http_build_query($filters) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-list-ul text-muted fs-1"></i>
                    <p class="text-muted mt-2">No activity logs found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/activity-logs/clear') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-trash text-danger me-2"></i>Clear Old Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        This will permanently delete old activity logs. This action cannot be undone.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delete logs older than:</label>
                        <select name="days" class="form-select">
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90" selected>90 days</option>
                            <option value="180">180 days</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Old Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>

