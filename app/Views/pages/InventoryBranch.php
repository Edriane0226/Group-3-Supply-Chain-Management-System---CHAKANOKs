<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory - Branch Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; display: flex; }
    .sidebar { width: 220px; background-color: orange; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; min-height: 100vh; }
    .sidebar img { width: 100px; height: 100px; border-radius: 50%; margin-bottom: 15px; }
    .sidebar h5 { margin-bottom: 20px; text-align: center; }
    .sidebar a { width: 100%; padding: 12px 20px; color: #fff; text-decoration: none; display: block; }
    .sidebar a:hover, .sidebar a.active { background-color: #495057; }
    .content { flex-grow: 1; padding: 20px; }
    .content-box {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-top: 20px;
    }
    .searchbar {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      gap: 10px;
    }
    .quick-actions button {
      min-width: 160px;
    }
  </style>
</head>
<body>

<?php echo view('reusables/sidenav'); ?>

  <div class="content p-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-white rounded">
      <h4 class="mb-0">ChakaNoks Branch Manager</h4>
      <div class="d-flex align-items-center">
        <span class="me-2">Admin Dashboard</span>
        <img src="/assets/images/profile-icon.png" alt="Profile" width="30" height="30" class="rounded-circle">
      </div>
    </div>

    <!-- Inventory Section -->
    <div class="content-box mt-4">
      <h5>Inventory</h5>

      <!-- Search Bar -->
      <div class="searchbar">
        <input id="searchInput" type="text" class="form-control" placeholder="Search items...">
        <button class="btn btn-light"><i class="bi bi-list"></i></button>
        <button class="btn btn-light"><i class="bi bi-gear"></i></button>
      </div>

      <!-- Inventory Table -->
      <div class="table-responsive">
        <table id="inventoryTable" class="table table-bordered table-hover table-sm">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Branch ID</th>
              <th>Item Name</th>
              <th>Category</th>
              <th>Type</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Expiry Date</th>
              <th>Barcode</th>
              <th>Reorder Level</th>
              <th>Price</th>
              <th>Updated At</th>
              <th>Created At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="inventoryBody">
            <?php if (!empty($inventory)): ?>
              <?php foreach ($inventory as $row): ?>
                <tr>
                  <td>N/A</td>
                  <td><?= esc($row['branch_id']) ?></td>
                  <td><?= esc($row['item_name']) ?></td>
                  <td>N/A</td>
                  <td>N/A</td>
                  <td><?= esc($row['current_stock']) ?></td>
                  <td><?= esc($row['unit']) ?></td>
                  <td><?= esc($row['expiry_date']) ?></td>
                  <td><?= esc($row['barcode']) ?></td>
                  <td>N/A</td>
                  <td>N/A</td>
                  <td>N/A</td>
                  <td>N/A</td>
                  <td>
                    <a href="<?= site_url('inventory/stockin') ?>" class="btn btn-sm btn-primary">Stock In</a>
                    <a href="<?= site_url('inventory/stockout') ?>" class="btn btn-sm btn-danger">Stock Out</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="14" class="text-center">No items found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

   <!-- Quick Actions -->
<div class="quick-actions mt-3 d-flex gap-2 flex-wrap">
  <a href="<?= site_url('inventory/stockin') ?>" class="btn"
     style="min-width: 160px; border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align:center; background-color: orange; color: #fff;">
     <i class="bi bi-plus-circle me-2"></i>Add Stock
  </a>
  <a href="<?= site_url('inventory/stockout') ?>" class="btn"
     style="min-width: 160px; border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align:center; background-color: orange; color:#fff;">
     <i class="bi bi-dash-circle me-2"></i>Remove Stock
  </a>
  <a href="<?= site_url('inventory/scan') ?>" class="btn"
     style="min-width: 160px; border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align:center; background-color: orange; color:#fff;">
     <i class="bi bi-upc-scan me-2"></i>Scan Barcode
  </a>
  <a href="<?= site_url('inventory/reports') ?>" class="btn"
     style="min-width: 160px; border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align:center; background-color: orange; color:#fff;">
     <i class="bi bi-file-earmark-bar-graph me-2"></i>Reports
  </a>
</div>


    </div>
  </div>

  <!-- Auto-refresh script -->
  <script>
    async function fetchInventory() {
      const response = await fetch('<?= site_url('inventory/liveInventory') ?>');
      if (!response.ok) return;
      const data = await response.json();
      const tbody = document.getElementById('inventoryBody');
      tbody.innerHTML = '';

      data.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>N/A</td>
          <td>${row.branch_id}</td>
          <td>${row.item_name}</td>
          <td>N/A</td>
          <td>N/A</td>
          <td>${row.current_stock}</td>
          <td>${row.unit}</td>
          <td>${row.expiry_date ?? ''}</td>
          <td>${row.barcode}</td>
          <td>N/A</td>
          <td>N/A</td>
          <td>N/A</td>
          <td>N/A</td>
          <td>
            <a href="<?= site_url('inventory/stockin') ?>" class="btn btn-sm btn-primary">Stock In</a>
            <a href="<?= site_url('inventory/stockout') ?>" class="btn btn-sm btn-danger">Stock Out</a>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    // Auto-refresh every 10 seconds
    setInterval(fetchInventory, 10000);
    // Initial load
    fetchInventory();
  </script>
</body>
</html>
