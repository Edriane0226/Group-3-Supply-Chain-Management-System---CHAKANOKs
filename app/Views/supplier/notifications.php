<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Notifications</h1>
      <div class="text-muted small">
        <?= esc(session()->get('supplier_name')) ?> (Supplier)
      </div>
    </div>

    <!-- Notifications List -->
    <div class="card shadow-sm">
      <div class="card-header bg-info text-white">
        <h6 class="mb-0"><i class="bi bi-bell me-2"></i>All Notifications</h6>
      </div>
      <div class="card-body">
        <?php if (!empty($notifications)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($notifications as $notification): ?>
              <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1"><?= esc($notification['title']) ?></h6>
                  <small class="text-muted"><?= esc(date('M d, Y H:i', strtotime($notification['created_at']))) ?></small>
                </div>
                <p class="mb-1"><?= esc($notification['message']) ?></p>
                <small class="text-muted">Type: <?= esc($notification['type']) ?> | Status: <?= esc($notification['status']) ?></small>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center text-muted py-4">
            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
            No notifications available
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
