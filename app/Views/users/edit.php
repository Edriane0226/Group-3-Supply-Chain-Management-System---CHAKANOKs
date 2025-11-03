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
      <form action="<?= base_url('update/'.$user['id']) ?>" method="post">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">First Name</label>
            <input type="text" name="first_name" value="<?= esc($user['first_Name']) ?>" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Last Name</label>
            <input type="text" name="last_name" value="<?= esc($user['last_Name']) ?>" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Middle Name</label>
            <input type="text" name="middle_name" value="<?= esc($user['middle_Name']) ?>" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" value="<?= esc($user['email']) ?>" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Role</label>
            <select name="role" class="form-select" required>
              <option value="">Select Role</option>
              <?php
                $roles = ['Branch Manager','Inventory Staff','Central Office Admin','Supplier','Logistics Coordinator','Franchise Manager','System Administrator'];
                foreach ($roles as $role): ?>
                <option value="<?= $role ?>" <?= ($user['role_id'] === $role) ? 'selected' : '' ?>>
                  <?= $role ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Branch</label>
            <select name="branch_id" class="form-select">
              <option value="">(No branch assigned)</option>
              <?php foreach ($branches as $branch): ?>
                <option value="<?= $branch['id'] ?>" <?= ($user['branch_id'] == $branch['id']) ? 'selected' : '' ?>>
                  <?= esc($branch['branch_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">New Password</label>
            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
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
