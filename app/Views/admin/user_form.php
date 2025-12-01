<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0">
                <i class="bi bi-<?= isset($user) ? 'pencil' : 'person-plus' ?> me-2"></i>
                <?= isset($user) ? 'Edit User' : 'Create New User' ?>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/users') ?>">Users</a></li>
                    <li class="breadcrumb-item active"><?= isset($user) ? 'Edit' : 'Create' ?></li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Users
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="<?= isset($user) ? site_url('admin/users/update/' . $user['id']) : site_url('admin/users/store') ?>" method="post">
                <div class="row g-4">
                    <!-- Personal Information -->
                    <div class="col-12">
                        <h6 class="fw-semibold border-bottom pb-2 mb-3">
                            <i class="bi bi-person me-2"></i>Personal Information
                        </h6>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_Name" class="form-control" required
                               value="<?= old('first_Name', $user['first_Name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_Name" class="form-control"
                               value="<?= old('middle_Name', $user['middle_Name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_Name" class="form-control" required
                               value="<?= old('last_Name', $user['last_Name'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required
                               value="<?= old('email', $user['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <?= isset($user) ? '(leave blank to keep current)' : '<span class="text-danger">*</span>' ?></label>
                        <input type="password" name="password" class="form-control" <?= isset($user) ? '' : 'required' ?>
                               placeholder="<?= isset($user) ? '••••••••' : 'Enter password' ?>">
                    </div>

                    <!-- Role & Branch -->
                    <div class="col-12 mt-4">
                        <h6 class="fw-semibold border-bottom pb-2 mb-3">
                            <i class="bi bi-shield-check me-2"></i>Role & Assignment
                        </h6>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= old('role_id', $user['role_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                                    <?= esc($r['role_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-select">
                            <option value="">No Branch Assigned</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= old('branch_id', $user['branch_id'] ?? '') == $branch['id'] ? 'selected' : '' ?>>
                                    <?= esc($branch['branch_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= old('status', $user['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status', $user['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="col-12 mt-4">
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                <?= isset($user) ? 'Update User' : 'Create User' ?>
                            </button>
                            <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

