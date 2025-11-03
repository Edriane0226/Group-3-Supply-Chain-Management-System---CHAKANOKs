<?php
  include 'app/Views/reusables/sidenav.php';
?>

<div class="content">
  <!-- Topbar -->
  <div class="topbar">
    <h5 class="fw-bold mb-0">
      <i class="bi bi-people-fill me-2 text-warning"></i>User Management
    </h5>
  </div>

  <!-- Filter and Add User -->
  <div class="dashboard-section mb-4">
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <form method="get" action="<?= base_url('users') ?>" class="d-flex align-items-center">
        <select name="branch" class="form-select form-select-sm me-2" onchange="this.form.submit()">
          <option value="">Select Branch</option>
          <?php foreach ($branches as $branch): ?>
            <option value="<?= esc($branch['id']) ?>" <?= (isset($_GET['branch']) && $_GET['branch'] == $branch['id']) ? 'selected' : '' ?>>
              <?= esc($branch['branch_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>

      <a href="<?= base_url('create') ?>" class="btn btn-sm btn-warning text-white shadow-sm rounded">
        <i class="bi bi-person-plus"></i> Add User
      </a>
    </div>
  </div>

  <!-- User Cards -->
  <?php if (!empty($users)): ?>
    <div class="row g-3">
      <?php foreach ($users as $user): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
              <i class="bi bi-person-circle text-warning" style="font-size: 3rem;"></i>
              <h6 class="mt-2 mb-0"><?= esc($user['first_Name'] . ' ' . $user['last_Name']) ?></h6>
              <small class="text-muted d-block mb-2"><?= esc($user['email']) ?></small>

              <?php if ($user['role'] === 'Central Office Admin'): ?>
                <span class="badge bg-primary"><?= esc($user['role']) ?></span>
              <?php else: ?>
                <span class="badge bg-secondary"><?= esc($user['role']) ?></span>
              <?php endif; ?>

              <p class="mt-2 mb-0">
                <i class="bi bi-building text-secondary me-1"></i><?= esc($user['branch_name']) ?>
              </p>
            </div>

            <div class="card-footer bg-light text-center">
              <a href="<?= base_url('edit/'.$user['id']) ?>" class="btn btn-sm btn-outline-warning me-2">
                <i class="bi bi-pencil"></i> Edit
              </a>
              <a href="<?= base_url('delete/'.$user['id']) ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash"></i> Delete
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center py-5 text-muted">
      <i class="bi bi-exclamation-circle me-2"></i>No users found.
    </div>
  <?php endif; ?>
</div>
