<?php
    include 'app\Views\reusables\sidenav.php';
?>

<div class="content">
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold text-warning mb-0"><i class="bi bi-person-plus me-2"></i>Add User</h4>
      <a href="<?= base_url('users') ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-4">

        <?php if ($errors = session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
              <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('store') ?>" method="post">
          <?= csrf_field() ?>
          <div class="row g-3">

            <div class="col-md-4">
              <label for="first_name" class="form-label fw-semibold"><i class="bi bi-person"></i> First Name</label>
              <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter first name" value="<?= esc(old('first_name')) ?>" autocomplete="given-name" required>
            </div>

            <div class="col-md-4">
              <label for="last_name" class="form-label fw-semibold"><i class="bi bi-person"></i> Last Name</label>
              <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter last name" value="<?= esc(old('last_name')) ?>" autocomplete="family-name" required>
            </div>

            <div class="col-md-4">
              <label for="middle_name" class="form-label fw-semibold"><i class="bi bi-person"></i> Middle Name</label>
              <input type="text" id="middle_name" name="middle_name" class="form-control" placeholder="Enter middle name" value="<?= esc(old('middle_name')) ?>" autocomplete="additional-name">
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label fw-semibold"><i class="bi bi-envelope"></i> Email</label>
              <input type="email" id="email" name="email" class="form-control" placeholder="example@email.com" value="<?= esc(old('email')) ?>" autocomplete="email" required>
            </div>

            <div class="col-md-6">
              <label for="role_id" class="form-label fw-semibold"><i class="bi bi-person-badge"></i> Role</label>
              <select id="role_id" name="role_id" class="form-select" autocomplete="off" required>
                <option value="">Select Role</option>
                <?php foreach ($roles as $role): ?>
                  <option value="<?= esc($role['id']) ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>><?= esc($role['role_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label for="branch_id" class="form-label fw-semibold"><i class="bi bi-building"></i> Branch</label>
              <select id="branch_id" name="branch_id" class="form-select" autocomplete="off">
                <option value="">(No branch assigned)</option>
                <?php foreach ($branches as $branch): ?>
                  <option value="<?= esc($branch['id']) ?>" <?= old('branch_id') == $branch['id'] ? 'selected' : '' ?>><?= esc($branch['branch_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label for="password" class="form-label fw-semibold"><i class="bi bi-lock"></i> Password</label>
              <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" autocomplete="new-password" required>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="reset" class="btn btn-outline-secondary rounded-pill me-2">
              <i class="bi bi-x-circle"></i> Reset
            </button>
            <button type="submit" class="btn btn-warning text-white rounded-pill shadow-sm">
              <i class="bi bi-check-circle"></i> Create User
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>