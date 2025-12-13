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
        <!-- ✅ updated form action -->
        <form action="<?= site_url('branches/update/'.$branch['id']) ?>" method="post">
          <?= csrf_field() ?>

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-warning" role="alert">
              <ul class="mb-0">
                <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php $statusValue = old('status') !== null ? old('status') : $branch['status']; ?>
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-building"></i> Branch Name</label>
              <input type="text" name="branch_name" class="form-control" value="<?= esc(old('branch_name', $branch['branch_name'])) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-geo-alt"></i> Location</label>
              <input type="text" name="location" class="form-control" value="<?= esc(old('location', $branch['location'])) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-telephone"></i> Contact Info</label>
              <input type="text" name="contact_info" class="form-control" value="<?= esc(old('contact_info', $branch['contact_info'])) ?>" pattern="[0-9]{7,15}" title="Enter digits only (7-15 characters)" maxlength="15">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="bi bi-flag"></i> Status</label>
              <select name="status" class="form-select" required>
                <option value="existing" <?= $statusValue === 'existing' ? 'selected' : '' ?>>Existing</option>
                <option value="upcoming" <?= $statusValue === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="franchise" <?= $statusValue === 'franchise' ? 'selected' : '' ?>>Franchise</option>
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
