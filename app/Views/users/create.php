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
        <form action="<?= base_url('store') ?>" method="post">
          <div class="row g-3">

            <div class="col-md-4">
              <label class="form-label fw-semibold"><i class="bi bi-person"></i> First Name</label>
              <input type="text" name="first_name" class="form-control" placeholder="Enter first name" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold"><i class="bi bi-person"></i> Last Name</label>
              <input type="text" name="last_name" class="form-control" placeholder="Enter last name" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold"><i class="bi bi-person"></i> Middle Name</label>
              <input type="text" name="middle_name" class="form-control" placeholder="Enter middle name">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-envelope"></i> Email</label>
              <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-person-badge"></i> Role</label>
              <select name="role" class="form-select" required>
                <option value="">Select Role</option>
                <option>Branch Manager</option>
                <option>Inventory Staff</option>
                <option>Central Office Admin</option>
                <option>Supplier</option>
                <option>Logistics Coordinator</option>
                <option>Franchise Manager</option>
                <option>System Administrator</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-building"></i> Branch</label>
              <select name="branch_id" class="form-select">
                <option value="">(No branch assigned)</option>
                <?php foreach ($branches as $branch): ?>
                  <option value="<?= $branch['id'] ?>"><?= esc($branch['branch_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-lock"></i> Password</label>
              <input type="password" name="password" class="form-control" placeholder="Enter password" required>
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
