<?php
  include 'app/Views/reusables/sidenav.php';
?>

<div class="content">
  <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <h5 class="fw-bold mb-0">
      <i class="bi bi-pencil-square me-2 text-warning"></i>Edit User
    </h5>
  </div>

  <div class="dashboard-section">
    <div class="card shadow-sm border-0 p-4">

      <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('update/'.$user['id']) ?>" method="post">
        <?= csrf_field() ?>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="first_name" class="form-label fw-semibold">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?= esc(old('first_name', $user['first_Name'])) ?>" class="form-control" autocomplete="given-name" required>
          </div>

          <div class="col-md-4">
            <label for="last_name" class="form-label fw-semibold">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?= esc(old('last_name', $user['last_Name'])) ?>" class="form-control" autocomplete="family-name" required>
          </div>

          <div class="col-md-4">
            <label for="middle_name" class="form-label fw-semibold">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name" value="<?= esc(old('middle_name', $user['middle_Name'])) ?>" class="form-control" autocomplete="additional-name">
          </div>

          <div class="col-md-6">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input type="email" id="email" name="email" value="<?= esc(old('email', $user['email'])) ?>" class="form-control" autocomplete="email" required>
          </div>

          <?php $selectedRole = old('role_id', $user['role_id']); ?>

          <div class="col-md-6">
            <label for="role_id" class="form-label fw-semibold">Role</label>
            <select id="role_id" name="role_id" class="form-select" autocomplete="off" required>
              <option value="">Select Role</option>
              <?php foreach ($roles as $role): ?>
                <option value="<?= esc($role['id']) ?>" <?= ((string) $selectedRole === (string) $role['id']) ? 'selected' : '' ?>>
                  <?= esc($role['role_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <?php $selectedBranch = old('branch_id', $user['branch_id']); ?>

          <div class="col-md-6">
            <label for="branch_id" class="form-label fw-semibold">Branch</label>
            <select id="branch_id" name="branch_id" class="form-select" autocomplete="off">
              <option value="">(No branch assigned)</option>
              <?php foreach ($branches as $branch): ?>
                <option value="<?= esc($branch['id']) ?>" <?= ((string) $selectedBranch === (string) $branch['id']) ? 'selected' : '' ?>>
                  <?= esc($branch['branch_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label for="password" class="form-label fw-semibold">New Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current" autocomplete="new-password">
          </div>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="<?= base_url('users') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Cancel
          </a>
          <button type="submit" class="btn btn-warning text-white">
            <i class="bi bi-check-circle"></i> Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>