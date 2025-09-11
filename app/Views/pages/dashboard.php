<?php
  include APPPATH . 'Views/reusables/sidenav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Branch Manager Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
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
  <div class="content p-3">
    <!-- Top Bar -->
    <div class="top-bar d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
        <h5 class="mb-0">Branch Dashboard</h5>
        <div class="d-flex align-items-center">
            <span class="me-2">
                <?= esc(session()->get('first_Name') . ' ' . session()->get('last_Name')) ?>
            </span>
            <img src="/assets/images/profile-icon.png" alt="Profile" width="30" height="30" class="rounded-circle">
        </div>
    </div>

    <!-- Search Bar -->
    <div class="searchbar">
      <input type="text" class="form-control" placeholder="Search">
      <button class="btn btn-light"><i class="bi bi-list"></i></button>
      <button class="btn btn-light"><i class="bi bi-gear"></i></button>
    </div>

    <!-- Top Cards -->
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
            <div class="card-value">₱<?= number_format($inventoryValue ?? 0, 2) ?></div>
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

    <!-- Middle Graphs -->
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
            <p>N/A</p> <!-- Inventory Levels content removed -->
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Alerts -->
    <div class="dashboard-section">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="dashboard-box">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h6>Low Stock Alerts</h6>
            <?php if (!empty($lowStockAlerts) && $lowStockAlerts != 'N/A'): ?>
                <table class="table table-sm">
                    <?= $lowStockAlerts ?>
                </table>
            <?php else: ?>
                <p>N/A</p>
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
  </div>
</body>
</html>
