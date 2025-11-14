<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Performance Reports</h1>
      <div class="text-muted small">
        <?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)
      </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form method="GET" action="<?= site_url('logistics-coordinator/performance-reports') ?>" class="row g-3">
          <div class="col-md-4">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate) ?>">
          </div>
          <div class="col-md-4">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate) ?>">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
              <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="<?= site_url('logistics-coordinator/performance-reports') ?>" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
          </div>
        </form>
      </div>
    </div>

    <!-- Performance Metrics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card border-primary">
          <div class="card-body text-center">
            <h5 class="card-title text-primary">Total Schedules</h5>
            <h3 class="mb-0"><?= esc($totalSchedules) ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-success">
          <div class="card-body text-center">
            <h5 class="card-title text-success">Completed</h5>
            <h3 class="mb-0"><?= esc($completedSchedules) ?></h3>
            <small class="text-muted"><?= esc($completionRate) ?>%</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-info">
          <div class="card-body text-center">
            <h5 class="card-title text-info">On-Time Rate</h5>
            <h3 class="mb-0"><?= esc($onTimeRate) ?>%</h3>
            <small class="text-muted"><?= esc($onTimeDeliveries) ?> of <?= esc($completedSchedules) ?></small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-warning">
          <div class="card-body text-center">
            <h5 class="card-title text-warning">In Progress</h5>
            <h3 class="mb-0"><?= esc($inProgressSchedules) ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Breakdown -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Status Breakdown</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm">
              <tbody>
                <tr>
                  <td><span class="badge bg-secondary">Scheduled</span></td>
                  <td class="text-end"><strong><?= esc($statusBreakdown['Scheduled']) ?></strong></td>
                </tr>
                <tr>
                  <td><span class="badge bg-warning">In Progress</span></td>
                  <td class="text-end"><strong><?= esc($statusBreakdown['In Progress']) ?></strong></td>
                </tr>
                <tr>
                  <td><span class="badge bg-success">Completed</span></td>
                  <td class="text-end"><strong><?= esc($statusBreakdown['Completed']) ?></strong></td>
                </tr>
                <tr>
                  <td><span class="badge bg-danger">Cancelled</span></td>
                  <td class="text-end"><strong><?= esc($statusBreakdown['Cancelled']) ?></strong></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="bi bi-calendar me-2"></i>Daily Delivery Count</h6>
          </div>
          <div class="card-body">
            <div style="max-height: 300px; overflow-y: auto;">
              <?php if (!empty($dailyCounts)): ?>
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th class="text-end">Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($dailyCounts as $date => $count): ?>
                      <tr>
                        <td><?= esc(date('M d, Y', strtotime($date))) ?></td>
                        <td class="text-end"><strong><?= esc($count) ?></strong></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p class="text-muted text-center">No delivery data for selected period</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Performance Summary -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-success text-white">
        <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Performance Summary</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h6>Completion Rate</h6>
            <div class="progress mb-3" style="height: 25px;">
              <div class="progress-bar bg-success" role="progressbar" style="width: <?= esc($completionRate) ?>%">
                <?= esc($completionRate) ?>%
              </div>
            </div>
            <p class="text-muted small">
              <?= esc($completedSchedules) ?> out of <?= esc($totalSchedules) ?> deliveries completed
            </p>
          </div>
          <div class="col-md-6">
            <h6>On-Time Delivery Rate</h6>
            <div class="progress mb-3" style="height: 25px;">
              <div class="progress-bar bg-info" role="progressbar" style="width: <?= esc($onTimeRate) ?>%">
                <?= esc($onTimeRate) ?>%
              </div>
            </div>
            <p class="text-muted small">
              <?= esc($onTimeDeliveries) ?> out of <?= esc($completedSchedules) ?> completed deliveries were on time
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

