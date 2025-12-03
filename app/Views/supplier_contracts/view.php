<div class="content">
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <h1 class="h5 fw-bold">Contract Details</h1>
        <div class="d-flex gap-2">
            <a href="<?= site_url('supplier-contracts/edit/' . $contract['id']) ?>" class="btn btn-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="<?= site_url('supplier-contracts') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Contract Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Contract Number:</strong><br>
                            <span class="text-primary"><?= esc($contract['contract_number']) ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
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
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Supplier:</strong><br>
                            <?= esc($contract['supplier_name']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Contract Type:</strong><br>
                            <?= esc($contract['contract_type']) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Start Date:</strong><br>
                            <?= date('F d, Y', strtotime($contract['start_date'])) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>End Date:</strong><br>
                            <?= date('F d, Y', strtotime($contract['end_date'])) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Renewal Date:</strong><br>
                            <?= $contract['renewal_date'] ? date('F d, Y', strtotime($contract['renewal_date'])) : 'N/A' ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Payment Terms:</strong><br>
                            <?= esc($contract['payment_terms'] ?: 'N/A') ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Minimum Order Value:</strong><br>
                            ₱<?= number_format($contract['minimum_order_value'], 2) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Discount Rate:</strong><br>
                            <?= number_format($contract['discount_rate'], 2) ?>%
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Auto Renewal:</strong><br>
                        <?= $contract['auto_renewal'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                    </div>
                </div>
            </div>

            <?php if ($contract['delivery_terms']): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Delivery Terms</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(esc($contract['delivery_terms'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($contract['quality_standards']): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Quality Standards</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(esc($contract['quality_standards'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($contract['penalty_clauses']): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Penalty Clauses</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(esc($contract['penalty_clauses'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($contract['notes']): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(esc($contract['notes'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <?php if ($contract['status'] === 'draft'): ?>
                        <form method="post" action="<?= site_url('supplier-contracts/activate/' . $contract['id']) ?>" class="mb-2">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Activate this contract?')">
                                <i class="bi bi-check-circle me-1"></i> Activate Contract
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($contract['status'] === 'active'): ?>
                        <a href="<?= site_url('supplier-contracts/renew/' . $contract['id']) ?>" class="btn btn-info w-100 mb-2">
                            <i class="bi bi-arrow-repeat me-1"></i> Renew Contract
                        </a>
                    <?php endif; ?>

                    <a href="<?= site_url('supplier-contracts/edit/' . $contract['id']) ?>" class="btn btn-secondary w-100 mb-2">
                        <i class="bi bi-pencil me-1"></i> Edit Contract
                    </a>

                    <form method="post" action="<?= site_url('supplier-contracts/delete/' . $contract['id']) ?>" onsubmit="return confirm('Are you sure you want to delete this contract?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-1"></i> Delete Contract
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Contract Details</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Created By:</strong><br>
                    <?= esc($contract['first_Name'] . ' ' . $contract['last_Name']) ?></p>
                    <p class="mb-2"><strong>Created:</strong><br>
                    <?= date('F d, Y g:i A', strtotime($contract['created_at'])) ?></p>
                    <?php if ($contract['signed_date']): ?>
                        <p class="mb-0"><strong>Signed Date:</strong><br>
                        <?= date('F d, Y', strtotime($contract['signed_date'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

