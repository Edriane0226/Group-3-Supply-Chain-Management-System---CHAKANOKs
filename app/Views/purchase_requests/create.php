<div class="content">
  <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <h5 class="fw-bold mb-0">
      <i class="bi bi-journal-text me-2 text-warning"></i> New Purchase Request
    </h5>
    <a href="<?= site_url('purchase-request') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="<?= site_url('purchase-requests') ?>" method="post" id="purchaseRequestForm">

        <div id="bulk-orders-container">
          <div class="card mb-3 order-card p-3 bg-light border">
            <div class="row g-3 align-items-end">
              
              <div class="col-md-4">
                <label class="form-label fw-semibold">Supplier</label>
                <select name="supplier_id[]" class="form-select supplier-select" required>
                  <option value="" disabled selected>Select supplier</option>
                  <?php foreach ($suppliers as $s): ?>
                    <option value="<?= esc($s['id']) ?>"><?= esc($s['supplier_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-semibold">Item Name</label>
                <select name="item_name[]" class="form-select item-select" required>
                  <option value="" disabled selected>Select supplier first</option>
                </select>
              </div>

              <div class="col-md-2">
                <label class="form-label fw-semibold">Quantity</label>
                <input type="number" min="1" name="quantity[]" class="form-control" required>
              </div>

              <div class="col-md-2">
                <label class="form-label fw-semibold">Unit</label>
                 <select name="unit[]" class="form-select" required>
                  <option value="pcs">pcs</option>
                  <option value="box">box</option>
                  <option value="kg">kg</option>
                  <option value="liters">liters</option>
                </select>
              </div>

              <div class="col-md-2">
                <label class="form-label fw-semibold">Unit Price (₱)</label>
                <input type="text" name="price[]" class="form-control unit-price" readonly>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description[]" class="form-control" rows="2"></textarea>
              </div>

              <div class="col-12 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-order-btn">
                  <i class="bi bi-trash"></i> Remove
                </button>
              </div>

            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
          <button type="button" class="btn btn-secondary" id="add-order-btn">
            <i class="bi bi-plus-circle me-1"></i> Add Item
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-send me-1"></i> Submit Request
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
const supplierItems = <?= json_encode($supplier_items) ?>;

// Add new order row
document.getElementById('add-order-btn').addEventListener('click', function() {
  const container = document.getElementById('bulk-orders-container');
  const card = container.querySelector('.order-card');
  const clone = card.cloneNode(true);
  
  clone.querySelectorAll('input').forEach(el => el.value = '');
  clone.querySelectorAll('select').forEach(el => {
    el.selectedIndex = 0;
  });

  container.appendChild(clone);
});

// Remove order row
document.getElementById('bulk-orders-container').addEventListener('click', function(e) {
  if (e.target.classList.contains('remove-order-btn') || e.target.closest('.remove-order-btn')) {
    const card = e.target.closest('.order-card');
    if (document.querySelectorAll('.order-card').length > 1) {
      card.remove();
    } else {
      alert('At least one item is required.');
    }
  }
});

// Supplier → Item dropdown
document.getElementById('bulk-orders-container').addEventListener('change', function(e) {
    const target = e.target;

    const card = target.closest('.order-card');
    const itemSelect = card.querySelector('.item-select');
    const priceInput = card.querySelector('.unit-price');

    if (target.matches('.supplier-select')) {
        const supplierId = target.value;

        // Clear previous items
        itemSelect.innerHTML = '<option value="" disabled selected>Select item</option>';
        priceInput.value = '';

        // Filter items for this supplier
        const items = supplierItems.filter(i => String(i.supplier_id) === String(supplierId));

        items.forEach(i => {
            const option = document.createElement('option');
            option.value = i.item_name;
            option.dataset.price = i.unit_price;
            option.textContent = `${i.item_name} - ₱${parseFloat(i.unit_price).toFixed(2)}`;
            itemSelect.appendChild(option);
        });
    }

    // Set price when item is selected
    if (target.matches('.item-select')) {
        const selectedOption = target.selectedOptions[0];
        priceInput.value = selectedOption ? parseFloat(selectedOption.dataset.price).toFixed(2) : '';
    }
});
</script>
