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

    <!-- Notifications Section -->
    <?php if (!empty($notifications ?? [])): ?>
    <div class="dashboard-section mb-4">
      <div class="card shadow-sm">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bi bi-bell me-2"></i>Recent Notifications</h6>
          <?php if (($unreadCount ?? 0) > 0): ?>
            <span class="badge bg-danger"><?= $unreadCount ?> unread</span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <div class="list-group list-group-flush">
            <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
              <div class="list-group-item <?= $notification['status'] === 'pending' ? 'bg-light' : '' ?>">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1"><?= esc($notification['title']) ?></h6>
                  <small class="text-muted"><?= date('M d, H:i', strtotime($notification['created_at'])) ?></small>
                </div>
                <p class="mb-1 small"><?= esc($notification['message']) ?></p>
                <?php if ($notification['status'] === 'pending'): ?>
                  <small class="badge bg-warning text-dark">New</small>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="text-center mt-3">
            <a href="<?= site_url('notifications') ?>" class="btn btn-sm btn-outline-primary">View All Notifications</a>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

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

    <div class="dashboard-section">
      <div class="row g-3">
        <div class="col-lg-7">
          <div class="dashboard-box h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="mb-0">Upcoming Deliveries</h6>
                <small class="text-muted">Next 14 days</small>
              </div>
              <div class="d-flex flex-wrap gap-1">
                <?php $statusColors = ['Scheduled' => 'secondary', 'In Progress' => 'warning', 'Completed' => 'success', 'Cancelled' => 'danger']; ?>
                <?php foreach (($branchDeliveryStatus ?? []) as $label => $count): ?>
                  <span class="badge bg-<?= esc($statusColors[$label] ?? 'secondary') ?>"><?= esc($label) ?>: <?= esc($count) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
            <?php if (!empty($upcomingDeliveries)): ?>
              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Supplier</th>
                      <th>Logistics Contact</th>
                      <th>Driver</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($upcomingDeliveries as $delivery): ?>
                      <?php
                        $coordName = trim(($delivery['coordinator_first_name'] ?? '') . ' ' . ($delivery['coordinator_last_name'] ?? '')) ?: 'Unassigned';
                        $driverName = trim(($delivery['driver_first_name'] ?? '') . ' ' . ($delivery['driver_last_name'] ?? '')) ?: 'TBD';
                        $status = $delivery['status'] ?? 'Scheduled';
                      ?>
                      <tr>
                        <td>
                          <strong><?= esc(date('M d', strtotime($delivery['scheduled_date']))) ?></strong><br>
                          <small class="text-muted"><?= esc(date('h:i A', strtotime($delivery['scheduled_time']))) ?></small>
                        </td>
                        <td><?= esc($delivery['supplier_name'] ?? 'N/A') ?></td>
                        <td><?= esc($coordName) ?></td>
                        <td><?= esc($driverName) ?></td>
                        <td><span class="badge bg-<?= esc($statusColors[$status] ?? 'secondary') ?>"><?= esc($status) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">No upcoming deliveries scheduled yet.</p>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="dashboard-box h-100">
            <h6>Incoming Items Tracking</h6>
            <p class="text-muted small mb-3">Deliveries awaiting receipt at the branch.</p>
            <?php if (!empty($incomingDeliveries)): ?>
              <div class="list-group list-group-flush">
                <?php foreach ($incomingDeliveries as $incoming): ?>
                  <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between">
                      <strong><?= esc($incoming['supplier_name']) ?></strong>
                      <?php $incomingStatus = $incoming['status'] ?? 'Pending'; ?>
                      <span class="badge bg-<?= esc($incomingStatus === 'Completed' ? 'success' : ($incomingStatus === 'In Progress' ? 'warning text-dark' : 'warning text-dark')) ?>">
                        <?= esc($incomingStatus) ?>
                      </span>
                    </div>
                    <small class="text-muted d-block">
                      ETA <?= esc($incoming['delivery_date'] ? date('M d, Y', strtotime($incoming['delivery_date'])) : 'TBD') ?>
                      <?php if (!empty($incoming['delivery_time'])): ?>
                        · <?= esc(date('h:i A', strtotime($incoming['delivery_time']))) ?>
                      <?php endif; ?>
                      <?php if (!empty($incoming['total_items'])): ?>
                        · <?= esc($incoming['total_items']) ?> items
                      <?php endif; ?>
                    </small>
                    <?php if (!empty($incoming['remarks'])): ?>
                      <small class="text-muted d-block fst-italic"><?= esc($incoming['remarks']) ?></small>
                    <?php endif; ?>
                    <div class="mt-2">
                      <?php if (($incoming['source'] ?? '') === 'schedule'): ?>
                        <button class="btn btn-sm btn-success"
                          onclick="confirmScheduledDelivery(<?= esc($incoming['id']) ?>)">
                          <i class="bi bi-check2-circle me-1"></i>Mark Received
                        </button>
                      <?php else: ?>
                        <button class="btn btn-sm btn-success"
                          onclick="markIncomingDelivery(<?= esc($incoming['id']) ?>)">
                          <i class="bi bi-check2-circle me-1"></i>Mark Received
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">No pending deliveries at this time.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Reports Section -->
    <div class="dashboard-section">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Branch Reports & Analytics</h6>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-12">
              <h6 class="text-primary mb-3"><i class="bi bi-box-seam me-2"></i>Inventory Reports</h6>
            </div>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <div class="d-grid gap-2">
                <a href="<?= site_url('inventory/reports') ?>" class="btn btn-primary">
                  <i class="bi bi-file-earmark-text me-2"></i>View Detailed Reports
                </a>
              </div>
            </div>
            <div class="col-md-3">
              <div class="d-grid gap-2">
                <button class="btn btn-outline-success" onclick="exportBranchReport('inventory', 'csv')">
                  <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
                </button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="d-grid gap-2">
                <button class="btn btn-outline-danger" onclick="exportBranchReport('inventory', 'pdf')">
                  <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                </button>
              </div>
            </div>
            <div class="col-md-3">
              <div class="d-grid gap-2">
                <button class="btn btn-outline-primary" onclick="exportBranchReport('inventory', 'excel')">
                  <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                </button>
              </div>
            </div>
          </div>
          
          <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Quick Export:</strong> Export your current branch inventory data instantly. For detailed reports with advanced filters, date ranges, and custom options, visit the 
            <a href="<?= site_url('inventory/reports') ?>" class="alert-link">Inventory Reports</a> page.
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

        <!-- NEW: Purchase Request Statistics Card -->
        <div class="card mb-3 border-primary">
          <div class="card-header bg-primary text-white">
            <span class="fw-semibold"><i class="bi bi-clipboard-check me-2"></i>Purchase Request Statistics</span>
          </div>
          <div class="card-body">
            <?php if (isset($prStatistics)): ?>
              <div class="row text-center">
                <div class="col-4">
                  <div class="mb-2">
                    <strong class="text-primary"><?= esc($prStatistics['total'] ?? 0) ?></strong>
                    <small class="d-block text-muted">Total</small>
                  </div>
                </div>
                <div class="col-4">
                  <div class="mb-2">
                    <strong class="text-warning"><?= esc($prStatistics['pending'] ?? 0) ?></strong>
                    <small class="d-block text-muted">Pending</small>
                  </div>
                </div>
                <div class="col-4">
                  <div class="mb-2">
                    <strong class="text-success"><?= esc($prStatistics['approved'] ?? 0) ?></strong>
                    <small class="d-block text-muted">Approved</small>
                  </div>
                </div>
              </div>
              <hr>
              <div class="text-center">
                <small class="text-muted">Approval Rate: <strong><?= esc($prStatistics['approval_rate'] ?? 0) ?>%</strong></small>
                <?php if (isset($prAvgProcessingTime) && $prAvgProcessingTime > 0): ?>
                  <br><small class="text-muted">Avg Processing Time: <strong><?= esc(round($prAvgProcessingTime, 1)) ?> hours</strong></small>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">No purchase request data available.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- NEW: Cost Analysis Card -->
        <div class="card mb-3 border-success">
          <div class="card-header bg-success text-white">
            <span class="fw-semibold"><i class="bi bi-cash-stack me-2"></i>Cost Analysis</span>
          </div>
          <div class="card-body">
            <?php if (isset($costSummary)): ?>
              <div class="mb-2">
                <strong>Total Cost:</strong> 
                <span class="text-success">₱<?= number_format($costSummary['total_cost'] ?? 0, 2) ?></span>
              </div>
              <div class="mb-2">
                <strong>Total Orders:</strong> <?= esc($costSummary['total_orders'] ?? 0) ?>
              </div>
              <div class="mb-2">
                <strong>Avg Order Value:</strong> 
                <span>₱<?= number_format($costSummary['avg_order_value'] ?? 0, 2) ?></span>
              </div>
              <?php if (isset($apSummary)): ?>
                <hr>
                <small class="text-muted">
                  <strong>Outstanding:</strong> ₱<?= number_format($apSummary['total_pending'] ?? 0, 2) ?><br>
                  <strong>Overdue:</strong> ₱<?= number_format($apSummary['total_overdue'] ?? 0, 2) ?>
                </small>
              <?php endif; ?>
            <?php else: ?>
              <p class="text-muted mb-0">No cost data available.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- NEW: Wastage Analysis Card -->
        <div class="card mb-3 border-danger">
          <div class="card-header bg-danger text-white">
            <span class="fw-semibold"><i class="bi bi-exclamation-triangle me-2"></i>Wastage Analysis</span>
          </div>
          <div class="card-body">
            <?php if (isset($wastageSummary)): ?>
              <div class="mb-2">
                <strong>Total Wastage:</strong> 
                <span class="text-danger">₱<?= number_format($wastageSummary['total_wastage_value'] ?? 0, 2) ?></span>
              </div>
              <div class="mb-2">
                <small class="text-muted">
                  Expired: ₱<?= number_format($wastageSummary['expired_value'] ?? 0, 2) ?><br>
                  Damaged: ₱<?= number_format($wastageSummary['damaged_value'] ?? 0, 2) ?>
                </small>
              </div>
              <?php if (isset($wastageByReason)): ?>
                <hr>
                <small class="text-muted">
                  <strong>Expired Items:</strong> <?= esc($wastageByReason['expired']['item_count'] ?? 0) ?><br>
                  <strong>Damaged Items:</strong> <?= esc($wastageByReason['damaged']['item_count'] ?? 0) ?>
                </small>
              <?php endif; ?>
            <?php else: ?>
              <p class="text-muted mb-0">No wastage data available.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- NEW: Demand Analysis Card -->
        <div class="card mb-3 border-info">
          <div class="card-header bg-info text-white">
            <span class="fw-semibold"><i class="bi bi-graph-up-arrow me-2"></i>Demand Analysis <small class="text-white-50">(Based on Purchase Patterns)</small></span>
          </div>
          <div class="card-body">
            <?php if (isset($demandSummary)): ?>
              <div class="mb-2">
                <strong>Total Requests:</strong> 
                <span class="text-info"><?= esc($demandSummary['total_requests'] ?? 0) ?></span>
              </div>
              <div class="mb-2">
                <strong>Total Items Requested:</strong> 
                <span><?= esc($demandSummary['total_items_requested'] ?? 0) ?></span>
              </div>
              <div class="mb-2">
                <small class="text-muted">
                  Unique Items: <?= esc($demandSummary['unique_items'] ?? 0) ?><br>
                  Avg Frequency: <?= esc($demandSummary['avg_request_frequency'] ?? 0) ?> requests/day
                </small>
              </div>
              <?php if (isset($fastSlowMoving) && !empty($fastSlowMoving)): ?>
                <hr>
                <small class="text-muted">
                  <strong>Fast Moving Items:</strong> <?= esc(count(array_filter($fastSlowMoving, fn($item) => ($item['movement_category'] ?? '') === 'Fast Moving'))) ?><br>
                  <strong>Slow Moving Items:</strong> <?= esc(count(array_filter($fastSlowMoving, fn($item) => ($item['movement_category'] ?? '') === 'Slow Moving'))) ?>
                </small>
              <?php endif; ?>
            <?php else: ?>
              <p class="text-muted mb-0">No demand data available.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- NEW: Charts Section -->
        <?php if ($role == 'Central Office Admin'): ?>
          <!-- Cost Trends Line Chart -->
          <div class="card mb-3">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-graph-up me-2"></i>Cost Trends (Last 30 Days)</span>
            </div>
            <div class="card-body">
              <canvas id="costTrendsChart" height="100"></canvas>
            </div>
          </div>

          <!-- Wastage Trends Line Chart -->
          <div class="card mb-3">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-graph-down me-2"></i>Wastage Trends (Last 6 Months)</span>
            </div>
            <div class="card-body">
              <canvas id="wastageTrendsChart" height="100"></canvas>
            </div>
          </div>

          <!-- Purchase Request Trends Line Chart -->
          <div class="card mb-3">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-graph-up-arrow me-2"></i>Purchase Request Trends (Last 30 Days)</span>
            </div>
            <div class="card-body">
              <canvas id="prTrendsChart" height="100"></canvas>
            </div>
          </div>

          <!-- Cost by Branch Bar Chart -->
          <div class="card mb-3">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-bar-chart me-2"></i>Cost Breakdown by Branch</span>
            </div>
            <div class="card-body">
              <canvas id="costByBranchChart" height="100"></canvas>
            </div>
          </div>

          <!-- Wastage by Branch Bar Chart -->
          <div class="card mb-3">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-bar-chart-fill me-2"></i>Wastage Breakdown by Branch</span>
            </div>
            <div class="card-body">
              <canvas id="wastageByBranchChart" height="100"></canvas>
            </div>
          </div>
        <?php endif; ?>

        <!-- Delivery Pipeline Section -->
        <div class="card mb-3 border-info">
          <div class="card-header bg-info text-white">
            <span class="fw-semibold"><i class="bi bi-truck me-2"></i>Delivery Pipeline (Next 14 Days)</span>
          </div>
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
              <?php $statusColors = ['Scheduled' => 'secondary', 'In Progress' => 'warning', 'Completed' => 'success', 'Cancelled' => 'danger']; ?>
              <?php foreach (($centralDeliveryStatusSummary ?? []) as $label => $count): ?>
                <span class="badge bg-<?= esc($statusColors[$label] ?? 'secondary') ?>"><?= esc($label) ?>: <?= esc($count) ?></span>
              <?php endforeach; ?>
            </div>
            <?php if (!empty($centralDeliveryOverview)): ?>
              <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                  <thead>
                    <tr>
                      <th>Branch</th>
                      <th>Supplier</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach (array_slice($centralDeliveryOverview, 0, 5) as $delivery): ?>
                      <?php $status = $delivery['status'] ?? 'Scheduled'; ?>
                      <tr>
                        <td><?= esc($delivery['branch_name'] ?? 'N/A') ?></td>
                        <td><?= esc($delivery['supplier_name'] ?? 'N/A') ?></td>
                        <td>
                          <strong><?= esc(date('M d', strtotime($delivery['scheduled_date']))) ?></strong><br>
                          <small class="text-muted"><?= esc(date('h:i A', strtotime($delivery['scheduled_time']))) ?></small>
                        </td>
                        <td><span class="badge bg-<?= esc($statusColors[$status] ?? 'secondary') ?>"><?= esc($status) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">No scheduled deliveries within the selected window.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <?php if ($role == 'Central Office Admin'): ?>
          <!-- Purchase Request Status Pie Chart -->
          <div class="card mb-3">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-pie-chart me-2"></i>Purchase Request Status</span>
            </div>
            <div class="card-body">
              <canvas id="prStatusChart" height="200"></canvas>
            </div>
          </div>

          <!-- Wastage by Reason Pie Chart -->
          <div class="card mb-3">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-pie-chart-fill me-2"></i>Wastage by Reason</span>
            </div>
            <div class="card-body">
              <canvas id="wastageByReasonChart" height="200"></canvas>
            </div>
          </div>

          <!-- Supplier Performance Card -->
          <div class="card mb-3 border-primary">
            <div class="card-header bg-primary text-white">
              <span class="fw-semibold"><i class="bi bi-people me-2"></i>Supplier Performance</span>
            </div>
            <div class="card-body">
              <?php if (!empty($supplierPerformance)): ?>
                <div class="table-responsive">
                  <table class="table table-sm table-hover mb-0">
                    <thead>
                      <tr>
                        <th>Supplier</th>
                        <th>Orders</th>
                        <th>Completion</th>
                        <th>On-Time</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($supplierPerformance as $supplier): ?>
                        <tr>
                          <td><small><?= esc($supplier['supplier']) ?></small></td>
                          <td><?= esc($supplier['total']) ?></td>
                          <td>
                            <span class="badge bg-<?= $supplier['completion_rate'] >= 80 ? 'success' : ($supplier['completion_rate'] >= 50 ? 'warning' : 'danger') ?>">
                              <?= esc($supplier['completion_rate']) ?>%
                            </span>
                          </td>
                          <td>
                            <span class="badge bg-<?= $supplier['on_time_rate'] >= 80 ? 'success' : ($supplier['on_time_rate'] >= 50 ? 'warning' : 'danger') ?>">
                              <?= esc($supplier['on_time_rate']) ?>%
                            </span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <p class="text-muted mb-0 small">No supplier performance data available for this period.</p>
              <?php endif; ?>
            </div>
          </div>

          <!-- Delayed Deliveries Alert Card -->
          <div class="card mb-3 border-danger">
            <div class="card-header bg-danger text-white">
              <span class="fw-semibold"><i class="bi bi-exclamation-triangle me-2"></i>Delayed / At-Risk Deliveries</span>
            </div>
            <div class="card-body">
              <?php if (!empty($centralDelayedDeliveries)): ?>
                <div class="list-group list-group-flush">
                  <?php foreach ($centralDelayedDeliveries as $delayed): ?>
                    <div class="list-group-item px-0 py-2 border-0">
                      <strong class="d-block"><?= esc($delayed['branch_name'] ?? 'Branch') ?></strong>
                      <small class="text-muted d-block"><?= esc($delayed['supplier_name'] ?? 'Supplier') ?></small>
                      <small class="text-muted">
                        Scheduled <?= esc(date('M d, h:i A', strtotime($delayed['scheduled_date'] . ' ' . $delayed['scheduled_time']))) ?>
                      </small>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted mb-0 small">No delayed deliveries detected.</p>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Demand Analysis Charts Section -->
    <?php if ($role == 'Central Office Admin'): ?>
    <div class="row mt-4">
      <div class="col-lg-8">
        <!-- Demand Trends Line Chart -->
        <div class="card mb-3">
          <div class="card-header">
            <span class="fw-semibold"><i class="bi bi-graph-up me-2"></i>Demand Trends (Last 30 Days)</span>
          </div>
          <div class="card-body">
            <canvas id="demandTrendsChart" height="100"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <!-- Fast/Slow Moving Items Bar Chart -->
        <div class="card mb-3">
          <div class="card-header">
            <span class="fw-semibold"><i class="bi bi-bar-chart me-2"></i>Fast/Slow Moving Items</span>
          </div>
          <div class="card-body">
            <canvas id="fastSlowMovingChart" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Demand Analysis Details Section -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card mb-3">
          <div class="card-header bg-info text-white">
            <span class="fw-semibold"><i class="bi bi-clipboard-data me-2"></i>Demand Analysis Details <small class="text-white-50">(Based on Purchase Patterns)</small></span>
          </div>
          <div class="card-body">
            <div class="row">
              <!-- Demand by Branch -->
              <div class="col-md-6 mb-3">
                <h6 class="fw-semibold mb-3">Demand by Branch</h6>
                <?php if (isset($demandByBranch) && !empty($demandByBranch)): ?>
                  <div class="table-responsive">
                    <table class="table table-sm table-hover">
                      <thead>
                        <tr>
                          <th>Branch</th>
                          <th>Requests</th>
                          <th>Items</th>
                          <th>Unique Items</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach (array_slice($demandByBranch, 0, 5) as $branch): ?>
                          <tr>
                            <td><?= esc($branch['branch_name']) ?></td>
                            <td><?= esc($branch['total_requests']) ?></td>
                            <td><?= esc($branch['total_items_requested']) ?></td>
                            <td><?= esc($branch['unique_items']) ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <p class="text-muted mb-0">No demand data by branch available.</p>
                <?php endif; ?>
              </div>

              <!-- Top Items by Demand -->
              <div class="col-md-6 mb-3">
                <h6 class="fw-semibold mb-3">Top Items by Demand</h6>
                <?php if (isset($demandByItem) && !empty($demandByItem)): ?>
                  <div class="table-responsive">
                    <table class="table table-sm table-hover">
                      <thead>
                        <tr>
                          <th>Item</th>
                          <th>Requests</th>
                          <th>Total Qty</th>
                          <th>Branches</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($demandByItem as $item): ?>
                          <tr>
                            <td><?= esc($item['item_name']) ?></td>
                            <td><?= esc($item['request_count']) ?></td>
                            <td><?= esc($item['total_quantity_requested']) ?></td>
                            <td><?= esc($item['branches_requesting']) ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <p class="text-muted mb-0">No item demand data available.</p>
                <?php endif; ?>
              </div>
            </div>

            <!-- Reorder Point Analysis -->
            <?php if (isset($reorderPointAnalysis) && !empty($reorderPointAnalysis)): ?>
            <div class="row mt-3">
              <div class="col-12">
                <h6 class="fw-semibold mb-3">Reorder Point Analysis (Items Needing Attention)</h6>
                <div class="table-responsive">
                  <table class="table table-sm table-hover">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Branch</th>
                        <th>Current Stock</th>
                        <th>Suggested Reorder Point</th>
                        <th>Days of Stock</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($reorderPointAnalysis as $item): ?>
                        <tr>
                          <td><?= esc($item['item_name']) ?></td>
                          <td><?= esc($item['branch_name']) ?></td>
                          <td><?= esc($item['current_stock']) ?> <?= esc($item['unit']) ?></td>
                          <td><?= esc($item['suggested_reorder_point']) ?> <?= esc($item['unit']) ?></td>
                          <td><?= $item['days_of_stock'] !== null ? esc($item['days_of_stock']) . ' days' : 'N/A' ?></td>
                          <td>
                            <span class="badge bg-<?= $item['status'] === 'Low Stock' ? 'warning' : 'success' ?>">
                              <?= esc($item['status']) ?>
                            </span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <!-- Demand vs Supply -->
            <?php if (isset($demandVsSupply) && !empty($demandVsSupply)): ?>
            <div class="row mt-3">
              <div class="col-12">
                <h6 class="fw-semibold mb-3">Demand vs Supply Analysis</h6>
                <div class="table-responsive">
                  <table class="table table-sm table-hover">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Branch</th>
                        <th>Current Stock</th>
                        <th>Avg Monthly Demand</th>
                        <th>Months of Supply</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($demandVsSupply as $item): ?>
                        <tr>
                          <td><?= esc($item['item_name']) ?></td>
                          <td><?= esc($item['branch_name']) ?></td>
                          <td><?= esc($item['current_stock']) ?> <?= esc($item['unit']) ?></td>
                          <td><?= esc($item['avg_monthly_demand']) ?> <?= esc($item['unit']) ?></td>
                          <td><?= $item['months_of_supply'] !== null ? esc($item['months_of_supply']) . ' months' : 'N/A' ?></td>
                          <td>
                            <span class="badge bg-<?= $item['status'] === 'Critical' ? 'danger' : ($item['status'] === 'Low' ? 'warning' : 'success') ?>">
                              <?= esc($item['status']) ?>
                            </span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js for data visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php if (in_array(($role ?? ''), ['Branch Manager', 'Inventory Staff'])): ?>
<script>
async function markIncomingDelivery(deliveryId) {
  if (!confirm('Mark this delivery as received? This will update the branch inventory.')) {
    return;
  }
  try {
    const response = await fetch(`<?= site_url('deliveries/receive/') ?>${deliveryId}`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    });
    const result = await response.json();
    if (result.success) {
      alert('Delivery received and inventory updated.');
      location.reload();
    } else {
      alert(result.error ?? 'Failed to receive delivery.');
    }
  } catch (error) {
    console.error(error);
    alert('An error occurred while receiving the delivery.');
  }
}

async function confirmScheduledDelivery(scheduleId) {
  if (!confirm('Confirm that this scheduled delivery has been received and update inventory?')) {
    return;
  }
  try {
    const response = await fetch(`<?= site_url('inventory/confirm-delivery/') ?>${scheduleId}`, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    });
    const result = await response.json();
    if (result.success) {
      alert(result.message || 'Delivery confirmed.');
      location.reload();
    } else {
      alert(result.error ?? 'Failed to confirm delivery.');
    }
  } catch (error) {
    console.error(error);
    alert('An error occurred while confirming the delivery.');
  }
}
</script>
<?php endif; ?>

<?php if ($role == 'Central Office Admin'): ?>
<script>
// Chart.js configuration
Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#666';

// Cost Trends Line Chart
<?php if (isset($costTrends) && !empty($costTrends)): ?>
const costTrendsCtx = document.getElementById('costTrendsChart');
if (costTrendsCtx) {
  new Chart(costTrendsCtx, {
    type: 'line',
    data: {
      labels: <?= json_encode(array_column($costTrends, 'date')) ?>,
      datasets: [{
        label: 'Daily Cost (₱)',
        data: <?= json_encode(array_column($costTrends, 'daily_cost')) ?>,
        borderColor: 'rgb(40, 167, 69)',
        backgroundColor: 'rgba(40, 167, 69, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Cost: ₱' + parseFloat(context.parsed.y).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '₱' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
}
<?php endif; ?>

// Wastage Trends Line Chart
<?php if (isset($wastageTrends) && !empty($wastageTrends)): ?>
const wastageTrendsCtx = document.getElementById('wastageTrendsChart');
if (wastageTrendsCtx) {
  new Chart(wastageTrendsCtx, {
    type: 'line',
    data: {
      labels: <?= json_encode(array_column($wastageTrends, 'month')) ?>,
      datasets: [{
        label: 'Wastage Value (₱)',
        data: <?= json_encode(array_column($wastageTrends, 'wastage_value')) ?>,
        borderColor: 'rgb(220, 53, 69)',
        backgroundColor: 'rgba(220, 53, 69, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Wastage: ₱' + parseFloat(context.parsed.y).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '₱' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
}
<?php endif; ?>

// Purchase Request Trends Line Chart
<?php if (isset($prTrends) && !empty($prTrends)): ?>
const prTrendsCtx = document.getElementById('prTrendsChart');
if (prTrendsCtx) {
  new Chart(prTrendsCtx, {
    type: 'line',
    data: {
      labels: <?= json_encode(array_column($prTrends, 'date')) ?>,
      datasets: [{
        label: 'Request Count',
        data: <?= json_encode(array_column($prTrends, 'count')) ?>,
        borderColor: 'rgb(13, 110, 253)',
        backgroundColor: 'rgba(13, 110, 253, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  });
}
<?php endif; ?>

// Cost by Branch Bar Chart
<?php if (isset($costByBranch) && !empty($costByBranch)): ?>
const costByBranchCtx = document.getElementById('costByBranchChart');
if (costByBranchCtx) {
  new Chart(costByBranchCtx, {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_column($costByBranch, 'branch_name')) ?>,
      datasets: [{
        label: 'Total Cost (₱)',
        data: <?= json_encode(array_column($costByBranch, 'total_cost')) ?>,
        backgroundColor: 'rgba(40, 167, 69, 0.8)',
        borderColor: 'rgb(40, 167, 69)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Cost: ₱' + parseFloat(context.parsed.y).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '₱' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
}
<?php endif; ?>

// Wastage by Branch Bar Chart
<?php if (isset($wastageByBranch) && !empty($wastageByBranch)): ?>
const wastageByBranchCtx = document.getElementById('wastageByBranchChart');
if (wastageByBranchCtx) {
  const wastageData = <?= json_encode($wastageByBranch) ?>;
  const branchNames = wastageData.map(b => b.branch_name);
  const expiredValues = wastageData.map(b => parseFloat(b.expired_value || 0));
  const damagedValues = wastageData.map(b => parseFloat(b.damaged_value || 0));

  new Chart(wastageByBranchCtx, {
    type: 'bar',
    data: {
      labels: branchNames,
      datasets: [
        {
          label: 'Expired (₱)',
          data: expiredValues,
          backgroundColor: 'rgba(255, 193, 7, 0.8)',
          borderColor: 'rgb(255, 193, 7)',
          borderWidth: 1
        },
        {
          label: 'Damaged (₱)',
          data: damagedValues,
          backgroundColor: 'rgba(220, 53, 69, 0.8)',
          borderColor: 'rgb(220, 53, 69)',
          borderWidth: 1
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ₱' + parseFloat(context.parsed.y).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          stacked: false,
          ticks: {
            callback: function(value) {
              return '₱' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
}
<?php endif; ?>

// Purchase Request Status Pie Chart
<?php if (isset($prStatistics)): ?>
const prStatusCtx = document.getElementById('prStatusChart');
if (prStatusCtx) {
  new Chart(prStatusCtx, {
    type: 'pie',
    data: {
      labels: ['Pending', 'Approved', 'Rejected', 'Cancelled'],
      datasets: [{
        data: [
          <?= esc($prStatistics['pending'] ?? 0) ?>,
          <?= esc($prStatistics['approved'] ?? 0) ?>,
          <?= esc($prStatistics['rejected'] ?? 0) ?>,
          <?= esc($prStatistics['cancelled'] ?? 0) ?>
        ],
        backgroundColor: [
          'rgba(255, 193, 7, 0.8)',
          'rgba(40, 167, 69, 0.8)',
          'rgba(220, 53, 69, 0.8)',
          'rgba(108, 117, 125, 0.8)'
        ],
        borderColor: [
          'rgb(255, 193, 7)',
          'rgb(40, 167, 69)',
          'rgb(220, 53, 69)',
          'rgb(108, 117, 125)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'bottom'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.parsed || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
              return label + ': ' + value + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });
}
<?php endif; ?>

// Wastage by Reason Pie Chart
<?php if (isset($wastageByReason)): ?>
const wastageByReasonCtx = document.getElementById('wastageByReasonChart');
if (wastageByReasonCtx) {
  new Chart(wastageByReasonCtx, {
    type: 'pie',
    data: {
      labels: ['Expired', 'Damaged'],
      datasets: [{
        data: [
          <?= esc($wastageByReason['expired']['total_value'] ?? 0) ?>,
          <?= esc($wastageByReason['damaged']['total_value'] ?? 0) ?>
        ],
        backgroundColor: [
          'rgba(255, 193, 7, 0.8)',
          'rgba(220, 53, 69, 0.8)'
        ],
        borderColor: [
          'rgb(255, 193, 7)',
          'rgb(220, 53, 69)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'bottom'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = parseFloat(context.parsed || 0);
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
              return label + ': ₱' + value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });
}
<?php endif; ?>

// Demand Trends Line Chart
<?php if (isset($demandTrends) && !empty($demandTrends)): ?>
const demandTrendsCtx = document.getElementById('demandTrendsChart');
if (demandTrendsCtx) {
  new Chart(demandTrendsCtx, {
    type: 'line',
    data: {
      labels: <?= json_encode(array_column($demandTrends, 'date')) ?>,
      datasets: [{
        label: 'Daily Requests',
        data: <?= json_encode(array_column($demandTrends, 'request_count')) ?>,
        borderColor: 'rgb(13, 110, 253)',
        backgroundColor: 'rgba(13, 110, 253, 0.1)',
        tension: 0.4,
        fill: true
      }, {
        label: 'Total Quantity',
        data: <?= json_encode(array_column($demandTrends, 'total_quantity')) ?>,
        borderColor: 'rgb(25, 135, 84)',
        backgroundColor: 'rgba(25, 135, 84, 0.1)',
        tension: 0.4,
        fill: true,
        yAxisID: 'y1'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Request Count'
          }
        },
        y1: {
          type: 'linear',
          display: true,
          position: 'right',
          title: {
            display: true,
            text: 'Quantity'
          },
          grid: {
            drawOnChartArea: false
          }
        }
      },
      plugins: {
        legend: {
          display: true,
          position: 'top'
        }
      }
    }
  });
}
<?php endif; ?>

// Fast/Slow Moving Items Bar Chart
<?php if (isset($fastSlowMoving) && !empty($fastSlowMoving)): ?>
const fastSlowMovingCtx = document.getElementById('fastSlowMovingChart');
if (fastSlowMovingCtx) {
  const topItems = <?= json_encode(array_slice($fastSlowMoving, 0, 10)) ?>;
  const fastMoving = topItems.filter(item => item.movement_category === 'Fast Moving').length;
  const mediumMoving = topItems.filter(item => item.movement_category === 'Medium Moving').length;
  const slowMoving = topItems.filter(item => item.movement_category === 'Slow Moving').length;

  new Chart(fastSlowMovingCtx, {
    type: 'bar',
    data: {
      labels: ['Fast Moving', 'Medium Moving', 'Slow Moving'],
      datasets: [{
        label: 'Number of Items',
        data: [fastMoving, mediumMoving, slowMoving],
        backgroundColor: [
          'rgba(40, 167, 69, 0.8)',
          'rgba(255, 193, 7, 0.8)',
          'rgba(220, 53, 69, 0.8)'
        ],
        borderColor: [
          'rgb(40, 167, 69)',
          'rgb(255, 193, 7)',
          'rgb(220, 53, 69)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
}
<?php endif; ?>
</script>

<?php if (($role ?? '') == 'Branch Manager'): ?>
<script>
// Branch Manager Reports Export Function
function exportBranchReport(type, format) {
  const branchId = <?php echo json_encode(session()->get('branch_id') ?? 0); ?>;
  const url = new URL('<?php echo base_url('inventory/export'); ?>', window.location.origin);
  url.searchParams.set('export', format);
  if (branchId) url.searchParams.set('branch_id', branchId);
  
  window.open(url, '_blank');
}
</script>
<?php endif; ?>

<?php endif; ?>
