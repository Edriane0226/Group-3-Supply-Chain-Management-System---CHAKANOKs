<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <h1 class="h5 fw-bold">Supplier Contracts</h1>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('supplier-contracts/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> New Contract
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Contracts</h6>
                    <h3 class="mb-0"><?= $statistics['total'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Active</h6>
                    <h3 class="mb-0 text-success"><?= $statistics['active'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Expiring Soon</h6>
                    <h3 class="mb-0 text-warning"><?= $statistics['expiring_soon'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Expired</h6>
                    <h3 class="mb-0 text-danger"><?= $statistics['expired'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search supplier or contract number..." value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft" <?= ($currentStatus ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="active" <?= ($currentStatus ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="expired" <?= ($currentStatus ?? '') === 'expired' ? 'selected' : '' ?>>Expired</option>
                        <option value="terminated" <?= ($currentStatus ?? '') === 'terminated' ? 'selected' : '' ?>>Terminated</option>
                        <option value="renewed" <?= ($currentStatus ?? '') === 'renewed' ? 'selected' : '' ?>>Renewed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="<?= site_url('supplier-contracts') ?>" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Contracts Table -->
    <div class="card">
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($contracts)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No contracts found.</p>
                    <a href="<?= site_url('supplier-contracts/create') ?>" class="btn btn-primary">Create First Contract</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Contract #</th>
                                <th>Supplier</th>
                                <th>Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contracts as $contract): ?>
                                <?php
                                $today = date('Y-m-d');
                                $isExpiring = $contract['status'] === 'active' && $contract['end_date'] >= $today && $contract['end_date'] <= date('Y-m-d', strtotime('+30 days'));
                                $isExpired = $contract['end_date'] < $today && $contract['status'] === 'active';
                                ?>
                                <tr>
                                    <td><strong><?= esc($contract['contract_number']) ?></strong></td>
                                    <td><?= esc($contract['supplier_name']) ?></td>
                                    <td><?= esc($contract['contract_type']) ?></td>
                                    <td><?= date('M d, Y', strtotime($contract['start_date'])) ?></td>
                                    <td>
                                        <?= date('M d, Y', strtotime($contract['end_date'])) ?>
                                        <?php if ($isExpiring): ?>
                                            <span class="badge bg-warning ms-1">Expiring Soon</span>
                                        <?php elseif ($isExpired): ?>
                                            <span class="badge bg-danger ms-1">Expired</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'active' => 'success',
                                            'expired' => 'danger',
                                            'terminated' => 'dark',
                                            'renewed' => 'info'
                                        ];
                                        $color = $statusColors[$contract['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($contract['status']) ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('supplier-contracts/view/' . $contract['id']) ?>" class="btn btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= site_url('supplier-contracts/edit/' . $contract['id']) ?>" class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($contract['status'] === 'draft'): ?>
                                                <form method="post" action="<?= site_url('supplier-contracts/activate/' . $contract['id']) ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-success" title="Activate" onclick="return confirm('Activate this contract?')">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($contract['status'] === 'active'): ?>
                                                <a href="<?= site_url('supplier-contracts/renew/' . $contract['id']) ?>" class="btn btn-outline-info" title="Renew">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </a>
                                            <?php endif; ?>
                                            <form method="post" action="<?= site_url('supplier-contracts/delete/' . $contract['id']) ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this contract?')">
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
            <?php endif; ?>
        </div>
    </div>
</div>

