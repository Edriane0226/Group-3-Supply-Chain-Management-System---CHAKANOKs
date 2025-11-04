<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Delivery Management') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color:#f8f9fa; min-height:100vh; display:flex; margin:0; }
    .sidebar { width:220px; background-color:orange; color:#fff; flex-shrink:0; display:flex; flex-direction:column; align-items:center; padding-top:20px; position:fixed; top:0; bottom:0; }
    .sidebar img{ width:72px; height:72px; border-radius:50%; object-fit:cover; margin-bottom:10px; }
    .sidebar h5{ font-weight:600; text-align:center; margin:10px 0 20px; line-height:1.2; }
    .sidebar a{ color:#fff; text-decoration:none; width:100%; padding:10px 16px; display:flex; align-items:center; gap:8px; border-radius:6px; margin:2px 8px; }
    .sidebar a.active, .sidebar a:hover{ background:rgba(0,0,0,.25); }
    .main-content { margin-left:220px; padding:20px; width:100%; }

    .tab-bar { background:#fff; border:1px solid #dee2e6; border-radius:14px; padding:8px; display:flex; gap:8px; }
    .tab-btn { border-radius:999px; padding:6px 16px; border:1px solid #ced4da; background:#f8f9fa; font-weight:500; }
    .tab-btn.active { background:#dee2e6; }

    .section-card { background:#fff; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,.06); padding:16px; height:100%; }
    .right-panel { background:#fff; border:1px solid #dee2e6; border-radius:12px; padding:16px; }
    .pill-btn { border-radius:999px; }
    .status-badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
  </style>
</head>
<body>
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 fw-bold mb-0">Delivery Management</h1>
      <div class="text-muted small"><?= esc(session()->get('first_Name')) ?> <?= esc(session()->get('last_Name')) ?> (<?= esc($role ?? '') ?>)</div>
    </div>

    <!-- Top segmented buttons -->
    <div class="tab-bar mb-3">
      <button class="tab-btn active" data-target="#sec-pending">Pending Deliveries</button>
      <button class="tab-btn" data-target="#sec-received">Received Deliveries</button>
      <button class="tab-btn" data-target="#sec-cancelled">Cancelled Deliveries</button>
      <button class="tab-btn" data-target="#sec-create">Create Delivery</button>
    </div>

    <!-- Pending Deliveries -->
    <div id="sec-pending" class="section">
      <div class="row g-3">
        <?php if (!empty($pendingDeliveries)): ?>
          <?php foreach ($pendingDeliveries as $delivery): ?>
            <div class="col-md-4">
              <div class="section-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="small text-muted">Delivery #<?= esc($delivery['id']) ?></span>
                  <span class="badge bg-warning status-badge">Pending</span>
                </div>
                <div class="small text-muted">Supplier: <?= esc($delivery['supplier_name']) ?></div>
                <div class="small text-muted">Date: <?= esc($delivery['delivery_date']) ?></div>
                <div class="small text-muted mb-3">Items: <?= esc($delivery['total_items']) ?></div>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-outline-primary" onclick="viewDeliveryDetails(<?= $delivery['id'] ?>)">View Details</button>
                  <?php if (in_array($role, ['Inventory Staff', 'Branch Manager'])): ?>
                    <button class="btn btn-sm btn-success" onclick="receiveDelivery(<?= $delivery['id'] ?>)">Mark Received</button>
                  <?php endif; ?>
                  <?php if (in_array($role, ['Branch Manager', 'Central Office Admin'])): ?>
                    <button class="btn btn-sm btn-outline-danger" onclick="cancelDelivery(<?= $delivery['id'] ?>)">Cancel</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="section-card text-center">
              <p class="mb-0 text-muted">No pending deliveries.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Received Deliveries -->
    <div id="sec-received" class="section d-none">
      <div class="row g-3">
        <?php if (!empty($receivedDeliveries)): ?>
          <?php foreach ($receivedDeliveries as $delivery): ?>
            <div class="col-md-4">
              <div class="section-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="small text-muted">Delivery #<?= esc($delivery['id']) ?></span>
                  <span class="badge bg-success status-badge">Received</span>
                </div>
                <div class="small text-muted">Supplier: <?= esc($delivery['supplier_name']) ?></div>
                <div class="small text-muted">Date: <?= esc($delivery['delivery_date']) ?></div>
                <div class="small text-muted mb-3">Items: <?= esc($delivery['total_items']) ?></div>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-outline-primary" onclick="viewDeliveryDetails(<?= $delivery['id'] ?>)">View Details</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="section-card text-center">
              <p class="mb-0 text-muted">No received deliveries.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Cancelled Deliveries -->
    <div id="sec-cancelled" class="section d-none">
      <div class="row g-3">
        <?php if (!empty($cancelledDeliveries)): ?>
          <?php foreach ($cancelledDeliveries as $delivery): ?>
            <div class="col-md-4">
              <div class="section-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="small text-muted">Delivery #<?= esc($delivery['id']) ?></span>
                  <span class="badge bg-danger status-badge">Cancelled</span>
                </div>
                <div class="small text-muted">Supplier: <?= esc($delivery['supplier_name']) ?></div>
                <div class="small text-muted">Date: <?= esc($delivery['delivery_date']) ?></div>
                <div class="small text-muted mb-3">Items: <?= esc($delivery['total_items']) ?></div>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-outline-primary" onclick="viewDeliveryDetails(<?= $delivery['id'] ?>)">View Details</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="section-card text-center">
              <p class="mb-0 text-muted">No cancelled deliveries.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Create Delivery  -->
    <!-- <Purchase Request dapat ni> -->
    <div id="sec-create" class="section d-none">
      <div class="row g-3">
        <div class="col-md-8">
          <div class="section-card">
            <h6 class="mb-3">Create New Delivery</h6>
            <form id="createDeliveryForm">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Supplier Name</label>
                  <input type="text" class="form-control" id="supplier_name" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Delivery Date</label>
                  <input type="date" class="form-control" id="delivery_date" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Remarks (Optional)</label>
                  <textarea class="form-control" id="remarks" rows="2"></textarea>
                </div>
              </div>

              <h6 class="mt-4 mb-3">Delivery Items</h6>
              <div id="itemsContainer">
                <div class="item-row border rounded p-3 mb-3">
                  <div class="row g-3">
                    <div class="col-md-3">
                      <label class="form-label">Item Name</label>
                      <input type="text" class="form-control item-name" required>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Quantity</label>
                      <input type="number" class="form-control item-quantity" min="1" required>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Unit</label>
                      <input type="text" class="form-control item-unit" placeholder="pcs, kg, etc." required>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Price</label>
                      <input type="number" class="form-control item-price" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Item Type</label>
                      <select class="form-select item-type-id" required>
                        <option value="">Select Type</option>
                        <?php foreach ($stockTypes as $type): ?>
                          <option value="<?= esc($type['id']) ?>"><?= esc($type['type_name']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                      <button type="button" class="btn btn-outline-danger btn-sm remove-item">×</button>
                    </div>
                  </div>
                  <div class="row g-3 mt-2">
                    <div class="col-md-4">
                      <label class="form-label">Expiry Date (Optional)</label>
                      <input type="date" class="form-control item-expiry">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Barcode (Optional)</label>
                      <input type="text" class="form-control item-barcode">
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" id="addItemBtn">Add Another Item</button>
                <button type="submit" class="btn btn-primary">Create Delivery</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-4">
          <div class="section-card">
            <h6>Instructions</h6>
            <ul class="small text-muted">
              <li>Fill in supplier and delivery details</li>
              <li>Add one or more items to the delivery</li>
              <li>All required fields must be filled</li>
              <li>Barcode and expiry date are optional</li>
              <li>Delivery will be created as "Pending"</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Delivery Details Modal -->
  <div class="modal fade" id="deliveryDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delivery Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="deliveryDetailsContent">
          <!-- Content will be loaded here -->
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Tab switcher
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.section').forEach(s => s.classList.add('d-none'));
        btn.classList.add('active');
        const target = document.querySelector(btn.getAttribute('data-target'));
        if (target) target.classList.remove('d-none');
      });
    });

    // Add item functionality
    document.getElementById('addItemBtn').addEventListener('click', () => {
      const container = document.getElementById('itemsContainer');
      const firstRow = container.querySelector('.item-row');
      const newRow = firstRow.cloneNode(true);
      // Clear inputs
      newRow.querySelectorAll('input').forEach(input => input.value = '');
      newRow.querySelector('select').selectedIndex = 0;
      container.appendChild(newRow);
    });

    // Remove item functionality
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('remove-item')) {
        const container = document.getElementById('itemsContainer');
        if (container.children.length > 1) {
          e.target.closest('.item-row').remove();
        }
      }
    });

    // Create delivery form
    document.getElementById('createDeliveryForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const items = [];
      document.querySelectorAll('.item-row').forEach(row => {
        items.push({
          item_name: row.querySelector('.item-name').value,
          quantity: parseInt(row.querySelector('.item-quantity').value),
          unit: row.querySelector('.item-unit').value,
          price: parseFloat(row.querySelector('.item-price').value),
          expiry_date: row.querySelector('.item-expiry').value || null,
          barcode: row.querySelector('.item-barcode').value || null,
          item_type_id: parseInt(row.querySelector('.item-type-id').value),
        });
      });

      const data = {
        supplier_name: document.getElementById('supplier_name').value,
        branch_id: <?= (int)session()->get('branch_id') ?>,
        delivery_date: document.getElementById('delivery_date').value,
        remarks: document.getElementById('remarks').value,
        items: items,
      };

      try {
        const response = await fetch('<?= site_url('deliveries/create') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(data),
        });

        const result = await response.json();

        if (result.success) {
          alert('Delivery created successfully!');
          location.reload();
        } else {
          alert('Error: ' + result.error);
        }
      } catch (error) {
        alert('An error occurred while creating the delivery.');
      }
    });

    // View delivery details
    async function viewDeliveryDetails(deliveryId) {
      try {
        const response = await fetch(`<?= site_url('deliveries/details/') ?>${deliveryId}`);
        const delivery = await response.json();

        if (delivery.error) {
          alert('Error: ' + delivery.error);
          return;
        }

        let content = `
          <div class="row g-3">
            <div class="col-md-6">
              <strong>Supplier:</strong> ${delivery.supplier_name}<br>
              <strong>Delivery Date:</strong> ${delivery.delivery_date}<br>
              <strong>Status:</strong> <span class="badge bg-${delivery.status === 'Pending' ? 'warning' : delivery.status === 'Received' ? 'success' : 'danger'}">${delivery.status}</span><br>
              ${delivery.remarks ? `<strong>Remarks:</strong> ${delivery.remarks}` : ''}
            </div>
            <div class="col-md-6">
              <strong>Items:</strong>
              <ul class="list-group mt-2">
        `;

        delivery.items.forEach(item => {
          content += `
            <li class="list-group-item">
              <strong>${item.item_name}</strong> - ${item.quantity} ${item.unit} @ ₱${item.price}
              ${item.barcode ? `<br><small>Barcode: ${item.barcode}</small>` : ''}
              ${item.expiry_date ? `<br><small>Expiry: ${item.expiry_date}</small>` : ''}
            </li>
          `;
        });

        content += `
              </ul>
            </div>
          </div>
        `;

        document.getElementById('deliveryDetailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('deliveryDetailsModal')).show();
      } catch (error) {
        alert('An error occurred while loading delivery details.');
      }
    }

    // Receive delivery
    async function receiveDelivery(deliveryId) {
      if (!confirm('Are you sure you want to mark this delivery as received? This will update the inventory.')) {
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
          alert('Delivery received and inventory updated!');
          location.reload();
        } else {
          alert('Error: ' + result.error);
        }
      } catch (error) {
        alert('An error occurred while receiving the delivery.');
      }
    }

    // Cancel delivery
    async function cancelDelivery(deliveryId) {
      if (!confirm('Are you sure you want to cancel this delivery?')) {
        return;
      }

      try {
        const response = await fetch(`<?= site_url('deliveries/cancel/') ?>${deliveryId}`, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        const result = await response.json();

        if (result.success) {
          alert('Delivery cancelled!');
          location.reload();
        } else {
          alert('Error: ' + result.error);
        }
      } catch (error) {
        alert('An error occurred while cancelling the delivery.');
      }
    }
  </script>
</body>
</html>
