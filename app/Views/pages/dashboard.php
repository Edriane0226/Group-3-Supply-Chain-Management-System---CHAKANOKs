<?php
  include 'app/Views/reusables/sidenav.php';
?>

<div class="content">
  <!-- Header -->
  <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <?php if (($role ?? '') === 'Branch Manager'): ?>
      <h1 class="h5 fw-bold">Branch Dashboard</h1>
    <?php else: ?>
      <h1 class="h5 fw-bold">Dashboard</h1>
    <?php endif; ?>

    <div class="d-flex align-items-center">
      <span class="me-2 text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?>
      </span>
      <span class="me-2 text-muted small">(<?= esc($role) ?>)</span>
      <div class="user-avatar">
        <?= esc(substr(session()->get('first_Name'), 0, 1)) ?>
      </div>
    </div>
  </div>

  <!-- Branch Manager Dashboard -->
  <?php if ($role == 'Branch Manager'): ?>
    <div class="dashboard-section mb-4">
      <div class="d-flex align-items-center gap-2">
        <input type="text" class="form-control" placeholder="Search">
        <button class="btn btn-light"><i class="bi bi-list"></i></button>
        <button class="btn btn-light"><i class="bi bi-gear"></i></button>
      </div>
    </div>

    <div class="dashboard-section">
      <div class="row g-3">
        <div class="col-md-3">
          <div class="card-box">
            <i class="bi bi-cash-stack"></i>
            <h6>Total Sales Today</h6>
            <div class="card-value"><?= esc($totalSalesToday ?? 'N/A') ?></div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-box">
            <i class="bi bi-star-fill"></i>
            <h6>Top-Selling Items</h6>
            <div class="card-value"><?= esc($topSellingItems ?? 'N/A') ?></div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-box">
            <i class="bi bi-archive"></i>
            <h6>Inventory Value</h6>
            <div class="card-value">₱<?= esc(number_format($inventoryValue ?? 0, 2)) ?></div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-box">
            <i class="bi bi-file-earmark-text"></i>
            <h6>Pending PRs</h6>
            <div class="card-value"><?= esc($pendingPRs ?? '0') ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="dashboard-section">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="dashboard-box">
            <i class="bi bi-graph-up"></i>
            <h6>Daily Sales Summary</h6>
            <p><?= esc($dailySalesSummary ?? 'N/A') ?></p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="dashboard-box">
            <i class="bi bi-pie-chart-fill"></i>
            <h6>Sales Breakdown</h6>
            <p><?= esc($salesBreakdown ?? 'N/A') ?></p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="dashboard-box">
            <i class="bi bi-boxes"></i>
            <h6>Inventory Levels</h6>
            <p><?= esc($inventoryLevels ?? 'N/A') ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="dashboard-section">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="dashboard-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h6>Low Stock Alerts</h6>
            <?php if (!empty($stockWarning)) : ?>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Item Name</th>
                    <th>Qty</th>
                    <th>Reorder</th>
                    <th>Unit</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($stockWarning as $item): ?>
                    <tr>
                      <td><?= esc($item['item_name']) ?></td>
                      <td><?= esc($item['quantity']) ?></td>
                      <td><?= esc($item['reorder_level']) ?></td>
                      <td><?= esc($item['unit']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p>No low stock alerts at the moment.</p>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="dashboard-box">
            <i class="bi bi-clock-history"></i>
            <h6>Recent Activity</h6>
            <p><?= esc($recentActivity ?? 'N/A') ?></p>
          </div>
        </div>
      </div>
    </div>
    

  <!-- Central Office Admin Dashboard -->
  <?php elseif ($role == 'Central Office Admin'): ?>
    <div class="row">
      <div class="col-lg-8">
        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between">
            <span class="fw-semibold">Total Inventory Value</span>
          </div>
          <div class="card-body d-flex align-items-center">
            <div class="metric-icon p-2"><i class="fa-solid fa-peso-sign"></i></div>
            <div class="flex-grow-1 p-2"><?= esc($invValues) ?></div>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between">
            <span class="fw-semibold">Total Wastage</span>
          </div>
          <div class="card-body d-flex">
            <div class="metric-icon p-2"><i class="fa-solid fa-peso-sign"></i></div>
            <div class="flex-grow-1 p-2">
              <?= esc($expiredValue) ?>
            </div>
          </div>
        </div>

        <div class="card mb-3 shadow-sm border-0">
          <div class="card-body">
            <h6 class="fw-semibold mb-3 text-primary">
              <i class="bi bi-building me-2"></i>Branches
            </h6>

            <?php if (!empty($AllBranches)): ?>
              <div class="row g-2">
                <?php foreach ($AllBranches as $branch): ?>
                  <div class="col-12 col-md-6">
                    <div class="p-2 border rounded d-flex align-items-center">
                      <i class="bi bi-geo-alt text-warning me-2"></i>
                      <span><?= esc($branch['branch_name']) ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="text-muted fst-italic mb-0">No branches available.</p>
            <?php endif; ?>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between">
            <span class="fw-semibold">Branches Performance</span>
            <div>
              <button class="btn btn-sm btn-outline-primary active">7d</button>
              <button class="btn btn-sm btn-outline-secondary">30d</button>
            </div>
          </div>
          <div class="card-body">
            <?= $branch_performance_chart ?? '<p>No performance data available.</p>' ?>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="dashboard-box mb-3">
          <i class="bi bi-pie-chart-fill"></i>
          <h6>Sales Breakdown</h6>
          <p><?= esc($salesBreakdown ?? 'N/A') ?></p>
        </div>

        <div class="dashboard-box mb-3">
          <i class="bi bi-file-earmark-bar-graph"></i>
          <h6>Reports</h6>
          <?= $reports_section ?? '<p>No reports available.</p>' ?>
        </div>

        <div class="dashboard-box">
          <i class="bi bi-truck"></i>
          <h6>Delivery Status</h6>
          <?= $delivery_status ?? '<p>No deliveries at this time.</p>' ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
