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
      <a href="<?= site_url('inventory/stockin') ?>" class="<?= (uri_string() == 'inventory/stockin') ? 'active' : '' ?>">
          <i class="bi bi-plus-circle me-2"></i> Stock In
      </a>
      <a href="<?= site_url('inventory/stockout') ?>" class="<?= (uri_string() == 'inventory/stockout') ? 'active' : '' ?>">
          <i class="bi bi-dash-circle me-2"></i> Stock Out
      </a>
      <a href="<?= site_url('inventory/reports') ?>" class="<?= (uri_string() == 'inventory/reports') ? 'active' : '' ?>">
          <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
      </a>
      <a href="<?= site_url('inventory/scan') ?>" class="<?= (uri_string() == 'inventory/scan') ? 'active' : '' ?>">
          <i class="bi bi-upc-scan me-2"></i> Scan
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
