<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-gear me-2"></i>System Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Settings</li>
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

    <form action="<?= site_url('admin/settings/update') ?>" method="post">
        <div class="row g-4">
            <?php 
            $groupIcons = [
                'company' => 'building',
                'inventory' => 'box-seam',
                'franchise' => 'shop',
                'system' => 'gear',
            ];
            $groupTitles = [
                'company' => 'Company Information',
                'inventory' => 'Inventory Settings',
                'franchise' => 'Franchise Settings',
                'system' => 'System Settings',
            ];
            ?>

            <?php foreach ($settings as $group => $groupSettings): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0">
                            <h6 class="fw-semibold mb-0">
                                <i class="bi bi-<?= $groupIcons[$group] ?? 'sliders' ?> me-2"></i>
                                <?= esc($groupTitles[$group] ?? ucfirst($group)) ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($groupSettings as $setting): ?>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">
                                        <?= esc(ucwords(str_replace('_', ' ', $setting['setting_key']))) ?>
                                        <?php if (!empty($setting['description'])): ?>
                                            <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip" 
                                               title="<?= esc($setting['description']) ?>"></i>
                                        <?php endif; ?>
                                    </label>
                                    
                                    <?php if ($setting['setting_type'] === 'boolean'): ?>
                                        <select name="<?= esc($setting['setting_key']) ?>" class="form-select">
                                            <option value="1" <?= $setting['setting_value'] == '1' ? 'selected' : '' ?>>Enabled</option>
                                            <option value="0" <?= $setting['setting_value'] == '0' ? 'selected' : '' ?>>Disabled</option>
                                        </select>
                                    <?php elseif ($setting['setting_type'] === 'number'): ?>
                                        <input type="number" step="0.01" name="<?= esc($setting['setting_key']) ?>" 
                                               class="form-control" value="<?= esc($setting['setting_value']) ?>">
                                    <?php elseif ($setting['setting_type'] === 'email'): ?>
                                        <input type="email" name="<?= esc($setting['setting_key']) ?>" 
                                               class="form-control" value="<?= esc($setting['setting_value']) ?>">
                                    <?php elseif ($setting['setting_type'] === 'url'): ?>
                                        <input type="url" name="<?= esc($setting['setting_key']) ?>" 
                                               class="form-control" value="<?= esc($setting['setting_value']) ?>">
                                    <?php else: ?>
                                        <input type="text" name="<?= esc($setting['setting_key']) ?>" 
                                               class="form-control" value="<?= esc($setting['setting_value']) ?>">
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($setting['description'])): ?>
                                        <small class="text-muted"><?= esc($setting['description']) ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Submit Button -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Save All Settings
                        </button>
                        <a href="<?= site_url('admin') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

