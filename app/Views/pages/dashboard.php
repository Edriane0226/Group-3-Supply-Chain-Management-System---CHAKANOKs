<?php
  include 'app\Views\reusables\sidenav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Branch Manager Dashboard</title>

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
