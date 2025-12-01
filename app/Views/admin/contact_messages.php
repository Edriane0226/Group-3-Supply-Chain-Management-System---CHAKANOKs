<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-envelope me-2"></i>Contact Messages</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Contact Messages</li>
                </ol>
            </nav>
        </div>
        <?php if (($unreadCount ?? 0) > 0): ?>
            <span class="badge bg-danger fs-6">
                <?= $unreadCount ?> Unread
            </span>
        <?php endif; ?>
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

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="<?= site_url('admin/contact-messages?status=all') ?>" 
                   class="btn <?= $status === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    All (<?= $totalMessages ?>)
                </a>
                <a href="<?= site_url('admin/contact-messages?status=unread') ?>" 
                   class="btn <?= $status === 'unread' ? 'btn-danger' : 'btn-outline-danger' ?> position-relative">
                    Unread
                    <?php if ($unreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="<?= site_url('admin/contact-messages?status=read') ?>" 
                   class="btn <?= $status === 'read' ? 'btn-info' : 'btn-outline-info' ?>">
                    Read
                </a>
                <a href="<?= site_url('admin/contact-messages?status=replied') ?>" 
                   class="btn <?= $status === 'replied' ? 'btn-success' : 'btn-outline-success' ?>">
                    Replied
                </a>
                <a href="<?= site_url('admin/contact-messages?status=archived') ?>" 
                   class="btn <?= $status === 'archived' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                    Archived
                </a>
            </div>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($messages)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="<?= $msg['status'] === 'unread' ? 'table-warning' : '' ?>">
                                    <td>
                                        <div>
                                            <strong><?= esc($msg['name']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($msg['email']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= esc($msg['subject']) ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php 
                                            $message = esc($msg['message']);
                                            echo strlen($message) > 80 ? substr($message, 0, 80) . '...' : $message;
                                            ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'unread' => 'danger',
                                            'read' => 'info',
                                            'replied' => 'success',
                                            'archived' => 'secondary',
                                        ];
                                        $color = $statusColors[$msg['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>">
                                            <?= ucfirst($msg['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('M d, Y h:i A', strtotime($msg['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="<?= site_url('admin/contact-messages/view/' . $msg['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <?php if ($msg['status'] !== 'read'): ?>
                                                <form action="<?= site_url('admin/contact-messages/status/' . $msg['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="read">
                                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Mark as Read">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($msg['status'] !== 'replied'): ?>
                                                <form action="<?= site_url('admin/contact-messages/status/' . $msg['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="replied">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Replied">
                                                        <i class="bi bi-reply"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($msg['status'] !== 'archived'): ?>
                                                <form action="<?= site_url('admin/contact-messages/status/' . $msg['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="archived">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Archive">
                                                        <i class="bi bi-archive"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <form action="<?= site_url('admin/contact-messages/delete/' . $msg['id']) ?>" 
                                                  method="post" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this message?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="card-footer bg-white">
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0 justify-content-center">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?status=<?= $status ?>&page=<?= $currentPage - 1 ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?status=<?= $status ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?status=<?= $status ?>&page=<?= $currentPage + 1 ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-envelope text-muted fs-1"></i>
                    <p class="text-muted mt-2">No messages found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Close dropdown after form submission
document.addEventListener('DOMContentLoaded', function() {
    const dropdownForms = document.querySelectorAll('.dropdown-menu form');
    dropdownForms.forEach(form => {
        form.addEventListener('submit', function() {
            // Close the dropdown
            const dropdown = this.closest('.dropdown');
            if (dropdown) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.querySelector('[data-bs-toggle="dropdown"]'));
                if (dropdownInstance) {
                    dropdownInstance.hide();
                }
            }
        });
    });
});
</script>

