<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-gear-wide-connected me-2"></i>System Administration</h1>
            <small class="text-muted">Manage system settings, users, and maintenance</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <?php if (($unreadMessages ?? 0) > 0): ?>
                <a href="<?= site_url('admin/contact-messages') ?>" class="btn btn-danger position-relative">
                    <i class="bi bi-envelope me-1"></i> Contact Messages
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                        <?= $unreadMessages ?>
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </a>
            <?php else: ?>
                <a href="<?= site_url('admin/contact-messages') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-envelope me-1"></i> Contact Messages
                </a>
            <?php endif; ?>
            <span class="text-muted small"><?= date('l, F d, Y') ?></span>
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

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Users</p>
                            <h3 class="fw-bold mb-0"><?= $stats['total_users'] ?? 0 ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-primary fs-4"></i>
                        </div>
                    </div>
                    <small class="text-success"><i class="bi bi-check-circle"></i> <?= $stats['active_users'] ?? 0 ?> active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Branches</p>
                            <h3 class="fw-bold mb-0"><?= $stats['total_branches'] ?? 0 ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-building text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">User Roles</p>
                            <h3 class="fw-bold mb-0"><?= $stats['total_roles'] ?? 0 ?></h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-shield-check text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Today's Logins</p>
                            <h3 class="fw-bold mb-0"><?= $activityStats['today_logins'] ?? 0 ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-box-arrow-in-right text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary">
                            <i class="bi bi-person-plus me-2"></i>Add New User
                        </a>
                        <a href="<?= site_url('admin/contact-messages') ?>" class="btn btn-outline-danger position-relative">
                            <i class="bi bi-envelope me-2"></i>Contact Messages
                            <?php if (($unreadMessages ?? 0) > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $unreadMessages ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <a href="<?= site_url('admin/backup') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-cloud-arrow-down me-2"></i>Create Backup
                        </a>
                        <a href="<?= site_url('admin/activity-logs') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-2"></i>View Activity Logs
                        </a>
                        <a href="<?= site_url('admin/settings') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-gear me-2"></i>System Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-heart-pulse me-2"></i>System Health</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small">Database</span>
                            <?php if ($systemHealth['database_connected'] ?? false): ?>
                                <span class="badge bg-success">Connected</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Disconnected</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small">Cache Directory</span>
                            <?php if ($systemHealth['writable_cache'] ?? false): ?>
                                <span class="badge bg-success">Writable</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Not Writable</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small">Logs Directory</span>
                            <?php if ($systemHealth['writable_logs'] ?? false): ?>
                                <span class="badge bg-success">Writable</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Not Writable</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <div class="d-flex justify-content-between">
                            <span>PHP Version:</span>
                            <span><?= $systemHealth['php_version'] ?? 'N/A' ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>CI Version:</span>
                            <span><?= $systemHealth['ci_version'] ?? 'N/A' ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Server Time:</span>
                            <span><?= $systemHealth['server_time'] ?? 'N/A' ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-2"></i>Recent Activity</h6>
                    <a href="<?= site_url('admin/activity-logs') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentActivities)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Module</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentActivities as $activity): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-semibold"><?= esc($activity['user_name'] ?? 'System') ?></span>
                                                <?php if (!empty($activity['user_role'])): ?>
                                                    <br><small class="text-muted"><?= esc($activity['user_role']) ?></small>
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
                                                ];
                                                $color = $actionColors[$activity['action']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= esc($activity['action']) ?></span>
                                            </td>
                                            <td><?= esc($activity['module'] ?? '-') ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M d, h:i A', strtotime($activity['created_at'])) ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-clock-history text-muted fs-1"></i>
                            <p class="text-muted mt-2">No recent activity</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

