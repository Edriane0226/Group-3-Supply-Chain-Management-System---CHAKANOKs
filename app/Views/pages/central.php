<?php include 'app/Views/reusables/sidenav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'ChakaNoks Central Office') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

  <style>
    body {
      background-color: #f5f5f5;
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
  </style>
</head>
<body>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h4 fw-bold"><?= esc($page_heading ?? 'Overview') ?></h1>
      <div class="d-flex align-items-center">
        <span class="me-2 text-muted small"><?= esc($user_name ?? 'Juan Dela Cruz') ?></span>
        <span class="me-2 text-muted small">(<?= esc($user_role ?? 'Central Manager') ?>)</span>
        <div class="user-avatar">
          <!-- <?= strtoupper(substr($user_name ?? 'JD', 0, 1)) ?>
          <?= strtoupper(substr(explode(' ', $user_name ?? 'JD')[1] ?? '', 0, 1)) ?> -->
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
          <div class="card-body d-flex">
            <div class="metric-icon"><i class="fa-solid fa-peso-sign"></i></div>
            <div class="flex-grow-1">
              <!-- <?= esc($sales_summary ?? '') ?> -->
            </div>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-body d-flex">
            <div class="metric-icon supply-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="flex-grow-1">
              <!-- <?= esc($supply_summary ?? '') ?> -->
            </div>
          </div>
        </div>

        <div class="card mb-3">
          <div class="card-body">
            <h6 class="fw-semibold mb-3">Branches</h6>
            <!-- <?= $branches_list ?? '' ?> -->
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
          <div class="card-body d-flex">
            <!-- <?= $branch_performance_chart ?? '' ?> -->
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card mb-3">
          <div class="card-body">
            <h6 class="fw-semibold mb-3">Reports</h6>
            <!-- <?= $reports_section ?? '' ?> -->
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h6 class="fw-semibold mb-3">Delivery Status</h6>
            <!-- <?= $delivery_status ?? '' ?> -->
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
