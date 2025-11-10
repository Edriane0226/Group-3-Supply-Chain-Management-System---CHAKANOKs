<div class="content">
  <!-- Header -->
  <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <h1 class="h5 fw-bold">Central Office Dashboard</h1>
    <div class="d-flex align-items-center">
      <span class="me-2 text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?>
      </span>
      <span class="me-2 text-muted small">(<?= esc(session()->get('role')) ?>)</span>
      <div class="user-avatar">
        <?= esc(substr(session()->get('first_Name'), 0, 1)) ?>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
          <span class="fw-semibold">Sales</span>
          <div>
            <button class="btn btn-sm btn-outline-primary active">7d</button>
            <button class="btn btn-sm btn-outline-secondary">30d</button>
          </div>
        </div>
        <div class="card-body d-flex align-items-center">
          <div class="metric-icon"><i class="fa-solid fa-peso-sign"></i></div>
          <div class="flex-grow-1"><?= esc($sales_summary ?? 'No data available') ?></div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-body d-flex">
          <div class="metric-icon supply-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
          <div class="flex-grow-1">
            <?= esc(session()->get('branch_name') ?? 'All Branches') ?>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <h6 class="fw-semibold mb-3">Branches</h6>
          <?= $branches_list ?? '<p>No branch data available.</p>' ?>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
