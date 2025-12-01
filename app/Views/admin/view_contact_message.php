<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-envelope me-2"></i>View Message</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/contact-messages') ?>">Contact Messages</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/contact-messages') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Messages
        </a>
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

    <!-- Message Details -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0"><?= esc($message['subject']) ?></h6>
                    <?php
                    $statusColors = [
                        'unread' => 'danger',
                        'read' => 'info',
                        'replied' => 'success',
                        'archived' => 'secondary',
                    ];
                    $color = $statusColors[$message['status']] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $color ?>"><?= ucfirst($message['status']) ?></span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-person-fill text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0"><?= esc($message['name']) ?></h6>
                                <small class="text-muted"><?= esc($message['email']) ?></small>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted">Sent on:</small>
                            <strong><?= date('F d, Y \a\t h:i A', strtotime($message['created_at'])) ?></strong>
                        </div>
                        <?php if ($message['read_at']): ?>
                            <div class="mb-2">
                                <small class="text-muted">Read on:</small>
                                <strong><?= date('F d, Y \a\t h:i A', strtotime($message['read_at'])) ?></strong>
                                <?php if ($message['first_Name']): ?>
                                    <span class="text-muted">by <?= esc($message['first_Name'] . ' ' . ($message['admin_last_name'] ?? '')) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <div>
                        <h6 class="mb-3">Message:</h6>
                        <div class="bg-light p-4 rounded" style="white-space: pre-wrap; word-wrap: break-word;">
                            <?= esc($message['message']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($message['status'] !== 'read'): ?>
                            <form action="<?= site_url('admin/contact-messages/status/' . $message['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="read">
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="bi bi-check-circle me-1"></i> Mark as Read
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($message['status'] !== 'replied'): ?>
                            <form action="<?= site_url('admin/contact-messages/status/' . $message['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="replied">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-reply me-1"></i> Mark as Replied
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($message['status'] !== 'archived'): ?>
                            <form action="<?= site_url('admin/contact-messages/status/' . $message['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="archived">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="bi bi-archive me-1"></i> Archive
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <a href="mailto:<?= esc($message['email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>" 
                           class="btn btn-primary w-100">
                            <i class="bi bi-envelope me-1"></i> Reply via Email
                        </a>
                        
                        <hr>
                        
                        <form action="<?= site_url('admin/contact-messages/delete/' . $message['id']) ?>" 
                              method="post" 
                              onsubmit="return confirm('Are you sure you want to delete this message?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i> Delete Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Message Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0">Message Information</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-2">
                            <strong>IP Address:</strong><br>
                            <code><?= esc($message['ip_address'] ?? 'N/A') ?></code>
                        </div>
                        <div class="mb-2">
                            <strong>User Agent:</strong><br>
                            <small class="text-muted"><?= esc($message['user_agent'] ?? 'N/A') ?></small>
                        </div>
                        <div>
                            <strong>Message ID:</strong><br>
                            <code>#<?= $message['id'] ?></code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

