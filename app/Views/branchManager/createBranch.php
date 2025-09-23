<?php
    include 'app\Views\reusables\sidenav.php';
?>

<div class="content">
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold text-warning mb-0"><i class="bi bi-plus-circle me-2"></i>Create Branch</h4>
      <a href="<?= base_url('branches') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <!-- ✅ updated form action -->
        <form action="<?= site_url('branches/store') ?>" method="post">
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-building"></i> Branch Name</label>
              <input type="text" name="branch_name" class="form-control" placeholder="Enter branch name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-geo-alt"></i> Location</label>
              <input type="text" name="location" class="form-control" placeholder="Enter location" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-telephone"></i> Contact Info</label>
              <input type="text" name="contact_info" class="form-control" placeholder="Enter phone or email">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-flag"></i> Status</label>
              <select name="status" class="form-select" required>
                <option value="existing">Existing</option>
                <option value="upcoming">Upcoming</option>
                <option value="franchise">Franchise</option>
              </select>
            </div>
          </div>

          <div class="mt-4 text-end">
            <button type="submit" class="btn btn-warning text-white rounded-pill shadow-sm">
              <i class="bi bi-save"></i> Save Branch
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
