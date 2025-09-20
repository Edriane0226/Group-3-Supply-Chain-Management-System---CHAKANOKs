<?php
  include APPPATH . 'Views/reusables/sidenav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory - Branch Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
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
                  <td><?= esc($row['id']) ?></td>
                  <td><?= esc($row['branch_id']) ?></td>
                  <td><?= esc($row['item_name']) ?></td>
                  <td><?= esc($row['category']) ?></td>
                  <td><?= esc($row['type']) ?></td>
                  <td><?= esc($row['quantity']) ?></td>
                  <td><?= esc($row['unit']) ?></td>
                  <td><?= esc($row['expiry_date']) ?></td>
                  <td><?= esc($row['barcode']) ?></td>
                  <td><?= esc($row['reorder_level']) ?></td>
                  <td><?= esc($row['price']) ?></td>
                  <td><?= esc($row['updated_at']) ?></td>
                  <td><?= esc($row['created_at']) ?></td>
                  <td>
                    <a href="<?= site_url('inventory/edit/'.$row['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="<?= site_url('inventory/delete/'.$row['id']) ?>" class="btn btn-sm btn-danger">Delete</a>
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
  <a href="<?= site_url('inventory/add') ?>" class="btn" 
     style="min-width: 160px; border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
            text-align:center; background-color: orange; color: #fff;">
     Add New Items
  </a>
  <a href="<?= site_url('inventory/update-stock') ?>" class="btn" 
     style="min-width: 160px; border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            text-align:center; background-color: orange; color:#fff;">
     Update Stock
  </a>
  <a href="<?= site_url('inventory/scan') ?>" class="btn" 
     style="min-width: 160px; border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            text-align:center; background-color: orange; color:#fff;">
     Scan Barcode
  </a>
  <a href="<?= site_url('inventory/report') ?>" class="btn" 
     style="min-width: 160px; border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            text-align:center; background-color: orange; color:#fff;">
     Generate Inventory Report
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
          <td>${row.id}</td>
          <td>${row.branch_id}</td>
          <td>${row.item_name}</td>
          <td>${row.category}</td>
          <td>${row.type}</td>
          <td>${row.quantity}</td>
          <td>${row.unit}</td>
          <td>${row.expiry_date ?? ''}</td>
          <td>${row.barcode}</td>
          <td>${row.reorder_level}</td>
          <td>${row.price}</td>
          <td>${row.updated_at}</td>
          <td>${row.created_at}</td>
          <td>
            <a href="<?= site_url('inventory/edit') ?>/${row.id}" class="btn btn-sm btn-primary">Edit</a>
            <a href="<?= site_url('inventory/delete') ?>/${row.id}" class="btn btn-sm btn-danger">Delete</a>
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
