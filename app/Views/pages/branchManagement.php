<?php
    include 'app\Views\reusables\sidenav.php';
?>

<div class="content">

  <div class="topbar">
    <div class="topbar-header">
      <h5 class="fw-bold mb-0">
        <i class="bi bi-building me-2 text-warning"></i> Branch Management
      </h5>
      <!-- ✅ Update Add Branch button -->
      <a href="<?= site_url('branches/create') ?>" class="btn btn-sm btn-warning text-white shadow-sm">
        <i class="bi bi-plus-circle"></i> Add Branch
      </a>
    </div>
  </div>

  <div class="dashboard-section">
    <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-check-circle me-1 text-success"></i> Existing Branches</h6>
    <div class="row g-3 mb-4">
      <?php foreach ($branches as $branch): ?>
        <?php if ($branch['status'] === 'existing'): ?>
          <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
              <div class="card-body text-center">
                <i class="bi bi-building text-warning" style="font-size: 3rem;"></i>
                <h6 class="mt-2 mb-0"><?= esc($branch['branch_name']) ?></h6>
                <small class="text-muted d-block mb-2">
                  <i class="bi bi-geo-alt me-1"></i><?= esc($branch['location']) ?>
                </small>
                <small class="text-muted d-block">
                  <i class="bi bi-telephone me-1"></i><?= esc($branch['contact_info'] ?? 'N/A') ?>
                </small>
              </div>
              <div class="card-footer bg-light text-center">
                <!-- ✅ Update Edit -->
                <a href="<?= site_url('branches/edit/'.$branch['id']) ?>" class="btn btn-sm btn-outline-warning me-2">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <!-- ✅ Update Delete -->
                <a href="<?= site_url('branches/delete/'.$branch['id']) ?>" 
                   onclick="return confirm('Are you sure you want to delete this branch?')" 
                   class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i> Delete
                </a>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-hourglass-split me-1 text-warning"></i> Upcoming Branch</h6>
    <div class="row g-3 mb-4">
      <?php foreach ($branches as $branch): ?>
        <?php if ($branch['status'] === 'upcoming'): ?>
          <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-warning h-100">
              <div class="card-body text-center">
                <i class="bi bi-building text-secondary" style="font-size: 3rem;"></i>
                <h6 class="mt-2 mb-0"><?= esc($branch['branch_name']) ?> <span class="badge bg-warning text-dark">Upcoming</span></h6>
                <small class="text-muted d-block mb-2">
                  <i class="bi bi-geo-alt me-1"></i><?= esc($branch['location']) ?>
                </small>
                <small class="text-muted d-block fst-italic">Opening Soon...</small>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>    
    </div>

    <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-people me-1 text-primary"></i> Franchise Partners</h6>
    <div class="row g-3">
      <?php foreach ($branches as $branch): ?>
        <?php if ($branch['status'] === 'franchise'): ?>
          <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
              <div class="card-body text-center">
                <i class="bi bi-shop text-primary" style="font-size: 3rem;"></i>
                <h6 class="mt-2 mb-0"><?= esc($branch['branch_name']) ?></h6>
                <small class="text-muted d-block mb-2">
                  <i class="bi bi-geo-alt me-1"></i><?= esc($branch['location']) ?>
                </small>
                <small class="text-muted d-block">
                  <i class="bi bi-envelope me-1"></i> Partner Branch
                </small>
              </div>
              <div class="card-footer bg-light text-center">
                <!-- ✅ Update Edit -->
                <a href="<?= site_url('branches/edit/'.$branch['id']) ?>" class="btn btn-sm btn-outline-warning me-2">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <!-- ✅ Update Delete -->
                <a href="<?= site_url('branches/delete/'.$branch['id']) ?>" 
                   onclick="return confirm('Are you sure you want to delete this branch?')" 
                   class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i> Delete
                </a>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

  </div>
</div>
