<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'ChakaNoks Dashboard') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
  body {
    background-color: #f8f9fa;
    margin: 0;
    min-height: 100vh;
    display: flex;
  }

  /* Sidebar */
  .sidebar {
    width: 220px;
    background-color: orange;
    color: #fff;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
  }

  .sidebar img {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
  }

  .sidebar h5 {
    text-align: center;
    margin: 10px 0 20px;
    font-weight: 600;
  }

  .sidebar a {
    color: #fff;
    text-decoration: none;
    width: 100%;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-radius: 6px;
    margin: 2px 8px;
  }

  .sidebar a.active,
  .sidebar a:hover {
    background: rgba(0, 0, 0, 0.25);
  }

  /* Content layout */
  .content {
    margin-left: 220px;
    padding: 20px;
    flex: 1;
    width: 100%;
  }

  .topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 10px;
    margin-bottom: 20px;
  }

  .card {
    border-radius: 10px;
  }

  /* Dashboard card layout */
  .dashboard-section {
    margin-top: 20px;
  }
  .card-box, .dashboard-box {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }
  .card-box {
    text-align: center;
  }
  .dashboard-box {
    text-align: left;
  }
  .card-box i, .dashboard-box i {
    font-size: 1.25rem;
    color: #6c757d;
  }
  .card-value {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 6px;
  }
  .user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
  }
</style>
</head>
<body>
    <!-- Sidebar -->
<div class="sidebar">
  <img src="<?= base_url('public/images/2.jpg') ?>" alt="Logo">
  <h5>
    ChakaNoks<br>
    <?php
    $role = session()->get('role');
    if ($role === 'Supplier') {
      echo esc(session()->get('supplier_name'));
    } else {
      echo esc(session()->get('branch_name'));
    }
    ?>
  </h5>
  <?php $role = session()->get('role'); ?>

  <?php if ($role === 'Central Office Admin'): ?>
      <a href="<?= site_url('dashboard') ?>">
          <i class="bi bi-building-gear me-2"></i> Central Dashboard
      </a>
      <a href="<?= site_url('users') ?>"><i class="bi bi-people me-2"></i> User Management</a>
      <a href="<?= site_url('branches') ?>"><i class="bi bi-building me-2"></i> Branches</a>
      <a href="<?= site_url('purchase-request') ?>"><i class="bi bi-journal-text me-2"></i> Purchase Request</a>

  <?php elseif ($role === 'Inventory Staff'): ?>
      <a href="<?= site_url('inventory/overview') ?>" class="<?= (uri_string() == 'inventory/overview') ? 'active' : '' ?>">
          <i class="bi bi-graph-up me-2"></i> Overview
      </a>
      <a href="<?= site_url('inventory/stockin') ?>" class="<?= (uri_string() == 'inventory/stockin') ? 'active' : '' ?>">
          <i class="bi bi-plus-circle me-2"></i> Stock In
      </a>
      <a href="<?= site_url('inventory/stockout') ?>" class="<?= (uri_string() == 'inventory/stockout') ? 'active' : '' ?>">
          <i class="bi bi-dash-circle me-2"></i> Stock Out
      </a>
      <a href="<?= site_url('deliveries') ?>" class="<?= (uri_string() == 'deliveries') ? 'active' : '' ?>">
          <i class="bi bi-truck me-2"></i> Deliveries
      </a>
      <a href="<?= site_url('inventory/reports') ?>" class="<?= (uri_string() == 'inventory/reports') ? 'active' : '' ?>">
          <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
      </a>
      <a href="<?= site_url('inventory/scan') ?>" class="<?= (uri_string() == 'inventory/scan') ? 'active' : '' ?>">
          <i class="bi bi-upc-scan me-2"></i> Scan
      </a>

  <?php elseif ($role === 'Logistics Coordinator'): ?>
      <a href="<?= site_url('logistics-coordinator') ?>" class="<?= (uri_string() == 'logistics-coordinator') ? 'active' : '' ?>">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
      <a href="<?= site_url('logistics-coordinator/delivery-schedules') ?>" class="<?= (uri_string() == 'logistics-coordinator/delivery-schedules') ? 'active' : '' ?>">
          <i class="bi bi-calendar-check me-2"></i> Delivery Schedules
      </a>
      <a href="<?= site_url('logistics-coordinator/active-deliveries') ?>" class="<?= (uri_string() == 'logistics-coordinator/active-deliveries') ? 'active' : '' ?>">
          <i class="bi bi-truck me-2"></i> Active Deliveries
      </a>
      <a href="<?= site_url('logistics-coordinator/performance-reports') ?>" class="<?= (uri_string() == 'logistics-coordinator/performance-reports') ? 'active' : '' ?>">
          <i class="bi bi-graph-up me-2"></i> Performance Reports
      </a>

  <?php elseif ($role === 'Supplier'): ?>
      <a href="<?= site_url('supplier/dashboard') ?>" class="<?= (uri_string() == 'supplier/dashboard') ? 'active' : '' ?>">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
      <a href="<?= site_url('supplier/orders') ?>" class="<?= (uri_string() == 'supplier/orders') ? 'active' : '' ?>">
          <i class="bi bi-list-check me-2"></i> Purchase Orders
      </a>
      <a href="<?= site_url('supplier/deliveries') ?>" class="<?= (uri_string() == 'supplier/deliveries') ? 'active' : '' ?>">
          <i class="bi bi-truck me-2"></i> Delivery Management
      </a>
      <a href="<?= site_url('supplier/invoices') ?>" class="<?= (uri_string() == 'supplier/invoices') ? 'active' : '' ?>">
          <i class="bi bi-receipt me-2"></i> Invoices & Payments
      </a>
      <a href="<?= site_url('supplier/notifications') ?>" class="<?= (uri_string() == 'supplier/notifications') ? 'active' : '' ?>">
          <i class="bi bi-bell me-2"></i> Notifications
      </a>
      <a href="<?= site_url('supplier/profile') ?>" class="<?= (uri_string() == 'supplier/profile') ? 'active' : '' ?>">
          <i class="bi bi-person me-2"></i> Profile & Settings
      </a>

  <?php else: ?>

      <!-- Branch Manager -->
      <a href="<?= site_url('dashboard') ?>" class="<?= (uri_string() == 'dashboard') ? 'active' : '' ?>">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
      <a href="<?= site_url('inventory') ?>" class="<?= (uri_string() == 'inventory') ? 'active' : '' ?>">
          <i class="bi bi-box-seam me-2"></i> Inventory
      </a>
      <a href="<?= site_url('purchase-request') ?>" class="<?= (uri_string() == 'purchase-request') ? 'active' : '' ?>">
          <i class="bi bi-journal-text me-2"></i> Purchase Request
      </a>
      <a href="<?= site_url('deliveries') ?>" class="<?= (uri_string() == 'deliveries') ? 'active' : '' ?>">
          <i class="bi bi-truck me-2"></i> Deliveries
      </a>
  <?php endif; ?>

  <a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<!-- Content will be inserted here by controller -->
</body>
</html>
