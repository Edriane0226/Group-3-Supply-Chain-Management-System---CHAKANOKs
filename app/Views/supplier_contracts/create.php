<div class="content">
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <h1 class="h5 fw-bold">Create Supplier Contract</h1>
        <a href="<?= site_url('supplier-contracts') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if ($errors = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('supplier-contracts/store') ?>">
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>" <?= old('supplier_id') == $supplier['id'] ? 'selected' : '' ?>>
                                    <?= esc($supplier['supplier_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contract Type <span class="text-danger">*</span></label>
                        <select name="contract_type" class="form-select" required>
                            <option value="Supply Agreement" <?= old('contract_type') === 'Supply Agreement' ? 'selected' : '' ?>>Supply Agreement</option>
                            <option value="Service Contract" <?= old('contract_type') === 'Service Contract' ? 'selected' : '' ?>>Service Contract</option>
                            <option value="Exclusive Agreement" <?= old('contract_type') === 'Exclusive Agreement' ? 'selected' : '' ?>>Exclusive Agreement</option>
                            <option value="Non-Exclusive Agreement" <?= old('contract_type') === 'Non-Exclusive Agreement' ? 'selected' : '' ?>>Non-Exclusive Agreement</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="<?= old('start_date') ?: date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" value="<?= old('end_date') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Renewal Date</label>
                        <input type="date" name="renewal_date" class="form-control" value="<?= old('renewal_date') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Payment Terms</label>
                        <input type="text" name="payment_terms" class="form-control" placeholder="e.g., Net 30, Net 15, COD" value="<?= old('payment_terms') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Minimum Order Value</label>
                        <input type="number" name="minimum_order_value" class="form-control" step="0.01" value="<?= old('minimum_order_value') ?: '0.00' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Discount Rate (%)</label>
                        <input type="number" name="discount_rate" class="form-control" step="0.01" value="<?= old('discount_rate') ?: '0.00' ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="auto_renewal" value="1" id="auto_renewal" <?= old('auto_renewal') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="auto_renewal">
                            Auto Renewal
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Delivery Terms</label>
                    <textarea name="delivery_terms" class="form-control" rows="3" placeholder="Delivery terms and conditions..."><?= old('delivery_terms') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quality Standards</label>
                    <textarea name="quality_standards" class="form-control" rows="3" placeholder="Quality standards and requirements..."><?= old('quality_standards') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Penalty Clauses</label>
                    <textarea name="penalty_clauses" class="form-control" rows="3" placeholder="Penalty clauses and consequences..."><?= old('penalty_clauses') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?= old('status') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."><?= old('notes') ?></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= site_url('supplier-contracts') ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Contract</button>
                </div>
            </form>
        </div>
    </div>
</div>

