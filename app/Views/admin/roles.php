<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-shield-check me-2"></i>Role Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-plus-circle me-1"></i> Add New Role
        </button>
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

    <!-- Roles Grid -->
    <div class="row g-4">
        <?php foreach ($roles as $r): ?>
            <?php $rolePermissions = $r['permissions'] ?? []; ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-semibold mb-1"><?= esc($r['role_name']) ?></h5>
                                <small class="text-muted"><?= esc($r['description'] ?? 'No description') ?></small>
                            </div>
                            <span class="badge bg-primary"><?= $r['user_count'] ?? 0 ?> users</span>
                        </div>
                        <?php if (!empty($rolePermissions)): ?>
                            <div class="mb-3">
                                <div class="fw-semibold small text-uppercase text-muted">Permissions</div>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <?php foreach ($rolePermissions as $permission): ?>
                                        <?php
                                            $label = $permission;
                                            [$groupKey, $permissionKey] = array_pad(explode('.', $permission, 2), 2, null);
                                            if ($groupKey && $permissionKey && isset($permissionGroups[$groupKey]['permissions'][$permissionKey])) {
                                                $label = $permissionGroups[$groupKey]['permissions'][$permissionKey];
                                            }
                                        ?>
                                        <span class="badge bg-light text-dark border"><?= esc($label) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small">No permissions assigned.</p>
                        <?php endif; ?>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editRoleModal<?= $r['id'] ?>">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <?php if (($r['user_count'] ?? 0) == 0): ?>
                                <form action="<?= site_url('admin/roles/delete/' . $r['id']) ?>" method="post" 
                                      onsubmit="return confirm('Delete this role?')">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Role Modal -->
            <div class="modal fade" id="editRoleModal<?= $r['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?= site_url('admin/roles/update/' . $r['id']) ?>" method="post">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Role</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" name="role_name" class="form-control" required 
                                           value="<?= esc($r['role_name']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"><?= esc($r['description'] ?? '') ?></textarea>
                                </div>
                                <?php if (!empty($permissionGroups)): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Permissions</label>
                                        <div class="accordion" id="editPermissionsAccordion<?= $r['id'] ?>">
                                            <?php $accordionIndex = 0; ?>
                                            <?php foreach ($permissionGroups as $groupKey => $group): ?>
                                                <?php $accordionIndex++; ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-edit-<?= $r['id'] ?>-<?= esc($groupKey) ?>">
                                                        <button class="accordion-button <?php if ($accordionIndex > 1): ?>collapsed<?php endif; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-edit-<?= $r['id'] ?>-<?= esc($groupKey) ?>">
                                                            <?= esc($group['label']) ?>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-edit-<?= $r['id'] ?>-<?= esc($groupKey) ?>" class="accordion-collapse collapse <?php if ($accordionIndex === 1): ?>show<?php endif; ?>" data-bs-parent="#editPermissionsAccordion<?= $r['id'] ?>">
                                                        <div class="accordion-body">
                                                            <div class="row g-2">
                                                                <?php foreach ($group['permissions'] as $permissionKey => $permissionLabel): ?>
                                                                    <?php $fullKey = $groupKey . '.' . $permissionKey; ?>
                                                                    <?php $inputId = 'edit-' . $r['id'] . '-' . $groupKey . '-' . $permissionKey; ?>
                                                                    <div class="col-12 col-sm-6">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= esc($fullKey) ?>" id="<?= esc($inputId) ?>" <?php if (in_array($fullKey, $rolePermissions, true)): ?>checked<?php endif; ?>>
                                                                            <label class="form-check-label" for="<?= esc($inputId) ?>">
                                                                                <?= esc($permissionLabel) ?>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Role</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/roles/create') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="role_name" class="form-control" required placeholder="e.g., Warehouse Staff">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this role..."></textarea>
                    </div>
                    <?php if (!empty($permissionGroups)): ?>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="accordion" id="createPermissionsAccordion">
                                <?php $createAccordionIndex = 0; ?>
                                <?php foreach ($permissionGroups as $groupKey => $group): ?>
                                    <?php $createAccordionIndex++; ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-create-<?= esc($groupKey) ?>">
                                            <button class="accordion-button <?php if ($createAccordionIndex > 1): ?>collapsed<?php endif; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-create-<?= esc($groupKey) ?>">
                                                <?= esc($group['label']) ?>
                                            </button>
                                        </h2>
                                        <div id="collapse-create-<?= esc($groupKey) ?>" class="accordion-collapse collapse <?php if ($createAccordionIndex === 1): ?>show<?php endif; ?>" data-bs-parent="#createPermissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row g-2">
                                                    <?php foreach ($group['permissions'] as $permissionKey => $permissionLabel): ?>
                                                        <?php $fullKey = $groupKey . '.' . $permissionKey; ?>
                                                        <?php $inputId = 'create-' . $groupKey . '-' . $permissionKey; ?>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= esc($fullKey) ?>" id="<?= esc($inputId) ?>">
                                                                <label class="form-check-label" for="<?= esc($inputId) ?>">
                                                                    <?= esc($permissionLabel) ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

