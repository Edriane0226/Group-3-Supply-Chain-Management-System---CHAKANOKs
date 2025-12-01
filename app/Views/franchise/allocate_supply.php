<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-box me-2"></i>Allocate Supplies</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise/view/' . $franchise['id']) ?>"><?= esc($franchise['applicant_name']) ?></a></li>
                    <li class="breadcrumb-item active">Allocate Supplies</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('franchise/view/' . $franchise['id']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Franchise Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-1"><?= esc($franchise['applicant_name']) ?></h5>
                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt me-1"></i><?= esc($franchise['proposed_location'] ?? 'N/A') ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-<?= $franchise['status'] === 'active' ? 'success' : 'primary' ?> fs-6">
                        <?= ucfirst(esc($franchise['status'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Allocation Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h6 class="fw-semibold mb-0"><i class="bi bi-plus-circle me-2"></i>Supply Items</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('franchise/allocate/' . $franchise['id']) ?>" method="post">
                <div class="table-responsive">
                    <table class="table align-middle" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">Item Name</th>
                                <th style="width: 15%;">Quantity</th>
                                <th style="width: 15%;">Unit</th>
                                <th style="width: 20%;">Unit Price (₱)</th>
                                <th style="width: 15%;">Subtotal</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemRows">
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="item_name[]" class="form-control" placeholder="Item name" required>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="form-control qty-input" placeholder="0" min="1" required>
                                </td>
                                <td>
                                    <select name="unit[]" class="form-select">
                                        <option value="pcs">pcs</option>
                                        <option value="kg">kg</option>
                                        <option value="g">g</option>
                                        <option value="L">L</option>
                                        <option value="mL">mL</option>
                                        <option value="box">box</option>
                                        <option value="pack">pack</option>
                                        <option value="set">set</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="unit_price[]" class="form-control price-input" placeholder="0.00" min="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal-display" value="₱0.00" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">
                                        <i class="bi bi-plus me-1"></i> Add Item
                                    </button>
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold">Total Amount:</td>
                                <td class="fw-bold text-success" id="grandTotal">₱0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Expected Delivery Date</label>
                        <input type="date" name="delivery_date" class="form-control" 
                               value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional notes or instructions...">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?= site_url('franchise/view/' . $franchise['id']) ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Submit Allocation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemRows = document.getElementById('itemRows');
    const addRowBtn = document.getElementById('addRowBtn');
    const grandTotal = document.getElementById('grandTotal');

    // Add new row
    addRowBtn.addEventListener('click', function() {
        const newRow = document.querySelector('.item-row').cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => {
            if (input.classList.contains('subtotal-display')) {
                input.value = '₱0.00';
            } else {
                input.value = '';
            }
        });
        itemRows.appendChild(newRow);
        attachRowListeners(newRow);
    });

    // Remove row
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                calculateGrandTotal();
            }
        }
    });

    // Calculate subtotal for a row
    function calculateRowSubtotal(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = qty * price;
        row.querySelector('.subtotal-display').value = '₱' + subtotal.toFixed(2);
        calculateGrandTotal();
    }

    // Calculate grand total
    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            total += qty * price;
        });
        grandTotal.textContent = '₱' + total.toFixed(2);
    }

    // Attach listeners to a row
    function attachRowListeners(row) {
        row.querySelector('.qty-input').addEventListener('input', () => calculateRowSubtotal(row));
        row.querySelector('.price-input').addEventListener('input', () => calculateRowSubtotal(row));
    }

    // Attach listeners to initial row
    document.querySelectorAll('.item-row').forEach(attachRowListeners);
});
</script>

