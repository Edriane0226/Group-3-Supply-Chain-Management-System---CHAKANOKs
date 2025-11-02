<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'ChakaNoks Dashboard') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

  <style>
    body {
      background-color: #f8f9fa;
      min-height: 100vh;
      display: flex;
      margin: 0;
    }

    .sidebar {
      width: 220px;
      background-color: orange;
      color: #fff;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 20px;
      position: fixed;
      top: 0;
      bottom: 0;
    }

    .main-content {
      margin-left: 220px;
      padding: 20px;
      flex: 1;
      width: 100%;
    }

    .card-box, .dashboard-box {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .card-value {
      font-size: 1.4rem;
      font-weight: bold;
      margin-top: 5px;
    }

    .dashboard-section { margin-top: 20px; }

    .user-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, #4285f4, #0066cc);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 14px;
      font-weight: 500;
    }

    .metric-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: #333;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 18px;
      margin-right: 15px;
    }

    .supply-icon {
      background: transparent;
      border: 2px solid #333;
      color: #333;
    }

    .searchbar {
      display: flex;
      align-items: center;
      margin: 15px 0;
      gap: 10px;
    }

    table {
      width: 100%;
    }

    th, td {
      padding: 8px;
      text-align: left;
    }
  </style>
</head>
<body>
  <!-- MAIN CONTENT -->
  <div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
      <h1 class="h5 fw-bold">Dashboard</h1>
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

    <!-- Branch Manager -->
    <?php if ($role == 'Branch Manager'): ?>
      <div class="searchbar">
        <input type="text" class="form-control" placeholder="Search">
        <button class="btn btn-light"><i class="bi bi-list"></i></button>
        <button class="btn btn-light"><i class="bi bi-gear"></i></button>
      </div>

      <div class="dashboard-section">
        <div class="row g-3">
          <div class="col-md-3">
            <div class="card-box">
              <i class="bi bi-cash-stack"></i>
              <h6>Total Sales Today</h6>
              <!-- Add Data Here kung available na data -->
              <div class="card-value"></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card-box">
              <i class="bi bi-star-fill"></i>
              <h6>Top-Selling Items</h6>
              <!-- Add Data Here kung available na data -->
              <div class="card-value"></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card-box">
              <i class="bi bi-archive"></i>
              <h6>Inventory Value</h6>
              <!-- Add Data Here kung available na data -->
              <div class="card-value"></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card-box">
              <i class="bi bi-file-earmark-text"></i>
              <h6>Pending PRs</h6>
              <!-- Add Data Here kung available na data -->
              <div class="card-value">></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-lg-8">
          <div class="dashboard-box mb-3">
            <i class="bi bi-graph-up"></i>
            <h6>Daily Sales Summary</h6>
            <p><?= esc($dailySalesSummary ?? 'N/A') ?></p>
          </div>
          <div class="dashboard-box mb-3">
            <i class="bi bi-clock-history"></i>
            <h6>Recent Activity</h6>
            <p><?= esc($recentActivity ?? 'N/A') ?></p>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="dashboard-box mb-3">
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
      </div>
    
    <!-- Central Office Admin -->
    <?php elseif ($role == 'Central Office Admin'): ?>

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
              <div class="flex-grow-1">
                <?= esc($sales_summary ?? 'No data available') ?>
              </div>
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
    <?php endif; ?>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>