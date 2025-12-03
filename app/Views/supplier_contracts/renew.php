<div class="content">
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <h1 class="h5 fw-bold">Renew Contract: <?= esc($contract['contract_number']) ?></h1>
        <a href="<?= site_url('supplier-contracts/view/' . $contract['id']) ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        This will create a new contract and mark the current contract as "Renewed". The new contract will have a new contract number.
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= site_url('supplier-contracts/renew/' . $contract['id']) ?>">
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Original Contract</label>
                        <input type="text" class="form-control" value="<?= esc($contract['contract_number']) ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Supplier</label>
                        <input type="text" class="form-control" value="<?= esc($contract['supplier_name'] ?? 'N/A') ?>" disabled>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Contract Type</label>
                        <select name="contract_type" class="form-select">
                            <option value="<?= esc($contract['contract_type']) ?>" selected><?= esc($contract['contract_type']) ?></option>
                            <option value="Supply Agreement">Supply Agreement</option>
                            <option value="Service Contract">Service Contract</option>
                            <option value="Exclusive Agreement">Exclusive Agreement</option>
                            <option value="Non-Exclusive Agreement">Non-Exclusive Agreement</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Auto Renewal</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="auto_renewal" value="1" id="auto_renewal" <?= $contract['auto_renewal'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="auto_renewal">
                                Enable Auto Renewal
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">New Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d', strtotime($contract['end_date'] . ' +1 day')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">New End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Renewal Date</label>
                        <input type="date" name="renewal_date" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Payment Terms</label>
                        <input type="text" name="payment_terms" class="form-control" value="<?= esc($contract['payment_terms']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Minimum Order Value</label>
                        <input type="number" name="minimum_order_value" class="form-control" step="0.01" value="<?= $contract['minimum_order_value'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Discount Rate (%)</label>
                        <input type="number" name="discount_rate" class="form-control" step="0.01" value="<?= $contract['discount_rate'] ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Delivery Terms</label>
                    <textarea name="delivery_terms" class="form-control" rows="3"><?= esc($contract['delivery_terms']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quality Standards</label>
                    <textarea name="quality_standards" class="form-control" rows="3"><?= esc($contract['quality_standards']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Penalty Clauses</label>
                    <textarea name="penalty_clauses" class="form-control" rows="3"><?= esc($contract['penalty_clauses']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Notes about this renewal..."><?= esc($contract['notes']) ?></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= site_url('supplier-contracts/view/' . $contract['id']) ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Renew Contract</button>
                </div>
            </form>
        </div>
    </div>
</div>

