<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Create Transfer Request</h1>
      <a href="<?= site_url('branch-transfers') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
      </a>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Transfer Request Form</h6>
      </div>
      <div class="card-body">
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
              <?php 
              $errors = session()->getFlashdata('errors');
              if (is_array($errors)) {
                foreach ($errors as $error): 
                  $errorMsg = is_array($error) ? implode(', ', $error) : $error;
              ?>
                <li><?= esc($errorMsg) ?></li>
              <?php 
                endforeach;
              } else {
                echo '<li>' . esc($errors) . '</li>';
              }
              ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <form action="<?= site_url('branch-transfers/store') ?>" method="POST">
          <?= csrf_field() ?>
          
          <!-- Hidden field for from_branch_id -->
          <input type="hidden" name="from_branch_id" value="<?= esc($currentBranchId) ?>">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
              <select class="form-select" id="item_name" name="item_name" required>
                <option value="">Select Item</option>
                <?php foreach ($inventory as $item): ?>
                  <?php if (($item['current_stock'] ?? 0) > 0): ?>
                    <option value="<?= esc($item['item_name']) ?>" 
                            data-stock="<?= esc($item['current_stock']) ?>"
                            data-unit="<?= esc($item['unit'] ?? 'pcs') ?>"
                            data-item-type="<?= esc($item['item_type_id'] ?? '') ?>"
                            <?= old('item_name') == $item['item_name'] ? 'selected' : '' ?>>
                      <?= esc($item['item_name']) ?> 
                      (Available: <?= esc($item['current_stock']) ?> <?= esc($item['unit'] ?? 'pcs') ?>)
                    </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
              <?php if (empty($inventory)): ?>
                <small class="form-text text-muted">No items available for transfer in this branch.</small>
              <?php endif; ?>
            </div>

            <div class="col-md-6 mb-3">
              <label for="to_branch_id" class="form-label">Transfer To Branch <span class="text-danger">*</span></label>
              <select class="form-select" id="to_branch_id" name="to_branch_id" required>
                <option value="">Select Branch</option>
                <?php foreach ($branches as $branch): ?>
                  <?php if ($branch['id'] != $currentBranchId): ?>
                    <option value="<?= esc($branch['id']) ?>" <?= old('to_branch_id') == $branch['id'] ? 'selected' : '' ?>>
                      <?= esc($branch['branch_name']) ?> - <?= esc($branch['location'] ?? '') ?>
                    </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="quantity" name="quantity" 
                     min="1" value="<?= old('quantity') ?>" required>
              <small class="form-text text-muted" id="available_stock_info"></small>
            </div>

            <div class="col-md-6 mb-3">
              <label for="unit" class="form-label">Unit</label>
              <input type="text" class="form-control" id="unit" name="unit" 
                     placeholder="pcs" value="<?= old('unit', 'pcs') ?>" readonly>
            </div>
          </div>

          <input type="hidden" id="stock_in_id" name="stock_in_id" value="<?= old('stock_in_id') ?>">

          <div class="mb-3">
            <label for="notes" class="form-label">Notes (Optional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" 
                      placeholder="Add any notes or reason for this transfer..."><?= old('notes') ?></textarea>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="<?= site_url('branch-transfers') ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle me-2"></i>Submit Request
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const itemSelect = document.getElementById('item_name');
  const quantityInput = document.getElementById('quantity');
  const unitInput = document.getElementById('unit');
  const stockInIdInput = document.getElementById('stock_in_id');
  const availableStockInfo = document.getElementById('available_stock_info');

  // If item is pre-selected (from validation error), load its details
  if (itemSelect.value && !stockInIdInput.value) {
    itemSelect.dispatchEvent(new Event('change'));
  }

  itemSelect.addEventListener('change', async function() {
    const selectedOption = this.options[this.selectedIndex];
    const itemName = selectedOption.value;
    
    if (!itemName) {
      unitInput.value = '';
      stockInIdInput.value = '';
      availableStockInfo.textContent = '';
      return;
    }

    const availableStock = parseInt(selectedOption.dataset.stock) || 0;
    const unit = selectedOption.dataset.unit || 'pcs';
    
    unitInput.value = unit;
    availableStockInfo.textContent = `Available stock: ${availableStock} ${unit}`;
    quantityInput.max = availableStock;
    quantityInput.value = '';

    // Fetch stock_in_id
    try {
      const response = await fetch(`<?= site_url('branch-transfers/get-item-details') ?>?item_name=${encodeURIComponent(itemName)}&branch_id=<?= $currentBranchId ?>`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data.error) {
        console.error('API Error:', data.error);
        alert('Error: ' + data.error);
        stockInIdInput.value = '';
        return;
      }
      
      if (data.stock_in_id) {
        stockInIdInput.value = data.stock_in_id;
        console.log('Stock In ID set:', data.stock_in_id);
      } else {
        console.error('No stock_in_id returned from API');
        alert('Error: Unable to retrieve stock information. Please try selecting the item again.');
        stockInIdInput.value = '';
      }
    } catch (error) {
      console.error('Error fetching item details:', error);
      alert('Error loading item details. Please try again.');
      stockInIdInput.value = '';
    }
  });

  quantityInput.addEventListener('input', function() {
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    const maxStock = parseInt(selectedOption?.dataset.stock) || 0;
    const quantity = parseInt(this.value) || 0;

    if (quantity > maxStock) {
      this.setCustomValidity(`Quantity cannot exceed available stock (${maxStock})`);
    } else {
      this.setCustomValidity('');
    }
  });

  // Form submission validation
  const form = document.querySelector('form');
  form.addEventListener('submit', function(e) {
    if (!stockInIdInput.value || stockInIdInput.value === '') {
      e.preventDefault();
      alert('Please select an item first. The system needs to load item details.');
      itemSelect.focus();
      return false;
    }
  });
});
</script>
