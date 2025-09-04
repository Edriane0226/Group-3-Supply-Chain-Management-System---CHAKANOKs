<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Branch Manager Dashboard</title>
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
    <h5>ChakaNoks<br>Branch Manager</h5>
    <a href="<?= base_url('dashboard') ?>" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="#"><i class="bi bi-box-seam me-2"></i> Inventory</a>
    <a href="#"><i class="bi bi-cart-check me-2"></i> Purchase Request</a>
    <a href="#"><i class="bi bi-arrow-left-right me-2"></i> Transfer</a>
    <a href="#"><i class="bi bi-gear-wide-connected me-2"></i> Operations</a>
    <a href="#"><i class="bi bi-truck me-2"></i> Delivery</a>
    <a href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
  </div>

  <!-- Content -->
  <div class="content">
    <!-- Top Bar -->
    <div class="topbar">
      <div class="topbar-header">
        <h5>Branch Dashboard</h5>
        <div class="d-flex align-items-center">
          <span class="me-2">Admin Dashboard</span>
          <img src="https://via.placeholder.com/40" alt="Profile" class="rounded-circle">
        </div>
      </div>
    </div>

    <!-- Search Bar (moved here above contents) -->
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
            <div class="card-value">loading...</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-box">
            <i class="bi bi-star-fill"></i>
            <h6>Top-Selling Items</h6>
            <div class="card-value">loading...</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-box">
            <i class="bi bi-archive"></i>
            <h6>Inventory Value</h6>
            <div class="card-value">loading...</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-box">
            <i class="bi bi-file-earmark-text"></i>
            <h6>Pending PRs</h6>
            <div class="card-value">loading...</div>
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
            <p>loading...</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="dashboard-box">
            <i class="bi bi-pie-chart-fill"></i>
            <h6>Sales Breakdown</h6>
            <p>loading...</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="dashboard-box">
            <i class="bi bi-boxes"></i>
            <h6>Inventory Levels</h6>
            <p>loading...</p>
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
            <p>loading...</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="dashboard-box">
            <i class="bi bi-clock-history"></i>
            <h6>Recent Activity</h6>
            <p>loading...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
