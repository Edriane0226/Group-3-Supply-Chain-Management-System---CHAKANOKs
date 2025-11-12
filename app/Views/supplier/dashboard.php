<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Supplier Dashboard</h1>
      <div class="text-muted small">
        <?= esc(session()->get('supplier_name')) ?> (Supplier)
      </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card border-warning">
          <div class="card-body text-center">
            <h5 class="card-title text-warning">Pending Orders</h5>
            <h3 class="mb-0"><?= esc($pendingOrders) ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-info">
          <div class="card-body text-center">
            <h5 class="card-title text-info">In Progress</h5>
            <h3 class="mb-0"><?= esc($inProgressOrders) ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-success">
          <div class="card-body text-center">
            <h5 class="card-title text-success">Completed</h5>
            <h3 class="mb-0"><?= esc($completedOrders) ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-primary">
          <div class="card-body text-center">
            <h5 class="card-title text-primary">Total Revenue</h5>
            <h3 class="mb-0">₱<?= number_format($totalRevenue, 2) ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Notifications -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-info text-white">
        <h6 class="mb-0"><i class="bi bi-bell me-2"></i>Recent Notifications</h6>
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
                <p class="mb-1 small"><?= esc($notification['message']) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted mb-0">No recent notifications</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-secondary text-white">
            <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Order Statistics</h6>
          </div>
          <div class="card-body">
            <p><strong>Total Orders:</strong> <?= esc($totalOrders) ?></p>
            <p><strong>Pending:</strong> <?= esc($pendingOrders) ?></p>
            <p><strong>In Progress:</strong> <?= esc($inProgressOrders) ?></p>
            <p><strong>Completed:</strong> <?= esc($completedOrders) ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="bi bi-cash me-2"></i>Revenue Summary</h6>
          </div>
          <div class="card-body">
            <p><strong>Total Revenue:</strong> ₱<?= number_format($totalRevenue, 2) ?></p>
            <p><strong>Average per Order:</strong> ₱<?= $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00' ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
