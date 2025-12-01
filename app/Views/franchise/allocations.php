<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-box-seam me-2"></i><?= esc($title) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item active">Supply Allocations</li>
                </ol>
            </nav>
        </div>
        <?php if (!empty($franchise)): ?>
            <a href="<?= site_url('franchise/allocate/' . $franchise['id']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> New Allocation
            </a>
        <?php endif; ?>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-0"><?= esc($stats['total_allocations'] ?? 0) ?></h5>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-warning mb-0"><?= esc($stats['pending'] ?? 0) ?></h5>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-info mb-0"><?= esc($stats['shipped'] ?? 0) ?></h5>
                    <small class="text-muted">Shipped</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="text-success mb-0"><?= esc($stats['delivered'] ?? 0) ?></h5>
                    <small class="text-muted">Delivered</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h5 class="text-success mb-0">₱<?= number_format($stats['total_value'] ?? 0, 2) ?></h5>
                    <small class="text-muted">Total Value</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Allocations Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (!empty($allocations)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <?php if (empty($franchise)): ?>
                                    <th>Franchise</th>
                                <?php endif; ?>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th class="text-end">Total</th>
                                <th>Delivery</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allocations as $alloc): ?>
                                <?php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'approved' => 'info',
                                    'preparing' => 'primary',
                                    'shipped' => 'info',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                ];
                                $color = $statusColors[$alloc['status']] ?? 'secondary';
                                ?>
                                <tr>
                                    <td><strong>#<?= esc($alloc['id']) ?></strong></td>
                                    <?php if (empty($franchise)): ?>
                                        <td><?= esc($alloc['applicant_name'] ?? 'N/A') ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <?= date('M d, Y', strtotime($alloc['allocation_date'])) ?>
                                    </td>
                                    <td><?= esc($alloc['item_name']) ?></td>
                                    <td><?= esc($alloc['quantity']) ?> <?= esc($alloc['unit']) ?></td>
                                    <td>₱<?= number_format($alloc['unit_price'], 2) ?></td>
                                    <td class="text-end fw-bold">₱<?= number_format($alloc['total_amount'], 2) ?></td>
                                    <td>
                                        <?php if (!empty($alloc['delivery_date'])): ?>
                                            <?= date('M d, Y', strtotime($alloc['delivery_date'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">TBD</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($alloc['status']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($alloc['status'] !== 'delivered' && $alloc['status'] !== 'cancelled'): ?>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Update
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($alloc['status'] === 'pending'): ?>
                                                        <li>
                                                            <form action="<?= site_url('franchise/allocation-status/' . $alloc['id']) ?>" method="post">
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" class="dropdown-item">Approve</button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if (in_array($alloc['status'], ['pending', 'approved'])): ?>
                                                        <li>
                                                            <form action="<?= site_url('franchise/allocation-status/' . $alloc['id']) ?>" method="post">
                                                                <input type="hidden" name="status" value="preparing">
                                                                <button type="submit" class="dropdown-item">Mark Preparing</button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if (in_array($alloc['status'], ['preparing', 'approved'])): ?>
                                                        <li>
                                                            <form action="<?= site_url('franchise/allocation-status/' . $alloc['id']) ?>" method="post">
                                                                <input type="hidden" name="status" value="shipped">
                                                                <button type="submit" class="dropdown-item">Mark Shipped</button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <?php if ($alloc['status'] === 'shipped'): ?>
                                                        <li>
                                                            <form action="<?= site_url('franchise/allocation-status/' . $alloc['id']) ?>" method="post">
                                                                <input type="hidden" name="status" value="delivered">
                                                                <button type="submit" class="dropdown-item">Mark Delivered</button>
                                                            </form>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="<?= site_url('franchise/allocation-status/' . $alloc['id']) ?>" method="post">
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger">Cancel</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-box display-1 text-muted"></i>
                    <h5 class="mt-3">No Allocations Found</h5>
                    <p class="text-muted">No supply allocations have been made yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

