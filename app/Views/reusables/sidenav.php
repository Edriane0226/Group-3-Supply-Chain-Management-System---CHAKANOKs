<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      min-height: 100vh;
      display: flex;
      margin: 0;
    }
    /* Sidebar */
    .sidebar {
      width: 220px;
      background-color: orange;
      color: #fff;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 20px;
    }
    .sidebar img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      margin-bottom: 15px;
    }
    .sidebar h5 {
      margin-bottom: 20px;
      text-align: center;
    }
    .sidebar a {
      width: 100%;
      padding: 12px 20px;
      color: #fff;
      text-decoration: none;
      display: block;
    }
    .sidebar a:hover,
    .sidebar a.active {
      background-color: #495057;
    }

    /* Content area */
    .content {
      flex-grow: 1;
      background: #f8f9fa;
      display: flex;
      flex-direction: column;
    }

    /* Top Navbar */
    .topbar {
      background-color: #fff;
      padding: 15px 20px;
      border-bottom: 1px solid #ddd;
    }

    .topbar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .topbar h5 {
      margin: 0;
      font-weight: bold;
    }

    /* Searchbar above content */
    .searchbar {
      margin: 15px 20px;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .searchbar input {
      max-width: 300px;
    }

    /* Cards */
    .card-box {
      padding: 20px;
      text-align: center;
      border-radius: 10px;
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .card-box i {
      font-size: 2rem;
      margin-bottom: 10px;
      color: orange;
    }
    .card-value {
      font-size: 0.9rem;
      font-weight: normal;
      color: gray;
    }

    .dashboard-section {
      margin: 20px;
    }

    .dashboard-box {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      height: 300px;
      overflow-y: auto;
      font-size: 0.9rem;
      font-weight: normal;
      color: gray;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .dashboard-box i {
      font-size: 2rem;
      color: orange;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <img src="<?= base_url('public/images/2.jpg') ?>" alt="Logo">
    <h5>
      ChakaNoks<br>
      <?= esc(session()->get('branch_name')) ?>
    </h5>
    <?php $role = session()->get('role'); ?>

    <?php if ($role === 'Central Office Admin'): ?>
        <a href="<?= site_url('dashboard') ?>">
            <i class="bi bi-building-gear me-2"></i> Central Dashboard
        </a>
        <a href="<?= site_url('users') ?>"><i class="bi bi-people me-2"></i> User Management</a>
        <a href="<?= site_url('branches') ?>"><i class="bi bi-building me-2"></i> Branches</a>

    <?php elseif ($role === 'Inventory Staff'): ?>
        <a href="<?= site_url('inventory/overview') ?>" class="<?= (uri_string() == 'inventory/overview') ? 'active' : '' ?>">
            <i class="bi bi-graph-up me-2"></i> Overview
        </a>
        <a href="<?= site_url('inventory/scan') ?>" class="<?= (uri_string() == 'inventory/scan') ? 'active' : '' ?>">
            <i class="bi bi-upc-scan me-2"></i> Scan
        </a>
        <a href="<?= site_url('inventory/low') ?>" class="<?= (uri_string() == 'inventory/low') ? 'active' : '' ?>">
            <i class="bi bi-exclamation-triangle me-2"></i> Low Stock
        </a>
        <a href="<?= site_url('inventory/expiry') ?>" class="<?= (uri_string() == 'inventory/expiry') ? 'active' : '' ?>">
            <i class="bi bi-calendar2-event me-2"></i> Expiry
        </a>

    <?php else: ?>
        <!-- Branch Manager -->
        <a href="<?= site_url('dashboard') ?>" class="<?= (uri_string() == 'dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="<?= site_url('inventory') ?>" class="<?= (uri_string() == 'inventory') ? 'active' : '' ?>">
            <i class="bi bi-box-seam me-2"></i> Inventory
        </a>
        <a href="<?= site_url('orders') ?>"><i class="bi bi-cart-check me-2"></i> Orders</a>
        <a href="<?= site_url('deliveries') ?>"><i class="bi bi-truck me-2"></i> Deliveries</a>
    <?php endif; ?>

    <a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

</body>
</html>