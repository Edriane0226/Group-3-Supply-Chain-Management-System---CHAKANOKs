<?php
    include 'app\Views\reusables\sidenav.php';
?>

<div class="content">
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold text-warning mb-0"><i class="bi bi-pencil me-2"></i>Edit Branch</h4>
      <a href="<?= base_url('branches') ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <form action="<?= base_url('updateBranch'.$branch['id']) ?>" method="post">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-building"></i> Branch Name</label>
              <input type="text" name="branch_name" class="form-control" value="<?= esc($branch['branch_name']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-geo-alt"></i> Location</label>
              <input type="text" name="location" class="form-control" value="<?= esc($branch['location']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-telephone"></i> Contact Info</label>
              <input type="text" name="contact_info" class="form-control" value="<?= esc($branch['contact_info']) ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-flag"></i> Status</label>
              <select name="status" class="form-select" required>
                <option value="existing" <?= $branch['status'] === 'existing' ? 'selected' : '' ?>>Existing</option>
                <option value="upcoming" <?= $branch['status'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="franchise" <?= $branch['status'] === 'franchise' ? 'selected' : '' ?>>Franchise</option>
              </select>
            </div>
          </div>

          <div class="mt-4 text-end">
            <button type="submit" class="btn btn-warning text-white rounded-pill shadow-sm">
              <i class="bi bi-check-circle"></i> Update Branch
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
