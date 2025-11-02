<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Stock In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { overflow-x: hidden; background: #f6f8fb; color:#212529; display: flex; }
        .sidebar { width: 220px; background-color: orange; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; min-height: 100vh; }
        .sidebar img { width: 100px; height: 100px; border-radius: 50%; margin-bottom: 15px; }
        .sidebar h5 { margin-bottom: 20px; text-align: center; }
        .sidebar a { width: 100%; padding: 12px 20px; color: #fff; text-decoration: none; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; }
        .content { flex-grow: 1; padding: 80px 24px 24px; }
    </style>
</head>
<body>

<?php echo view('reusables/sidenav'); ?>

<main class="content">
    <div class="container-fluid">
        <h5 class="mb-3">Stock In</h5>
        <div class="card shadow-sm">
            <div class="card-body">
                <form id="stockInForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Item Type</label>
                            <select name="item_type_id" class="form-control" required>
                                <option value="">Select Type</option>
                                <?php foreach ($stockTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>"><?= $type['type_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch ID</label>
                            <input type="number" name="branch_id" class="form-control" value="<?= session()->get('branch_id') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="item_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="kg, pcs, liters" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price (per unit)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barcode (optional)</label>
                            <input type="text" name="barcode" class="form-control">
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" id="addViaBarcodeBtn">
                            <i class="bi bi-upc-scan me-2"></i>Add via Barcode
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Manually
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('addViaBarcodeBtn').addEventListener('click', function() {
        window.location.href = '<?= site_url('inventory/scan') ?>';
    });

    document.getElementById('stockInForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        const res = await fetch('<?= base_url('inventory/stockin') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (res.ok) {
            alert('Stock added successfully');
            this.reset();
        } else {
            alert('Error: ' + JSON.stringify(result.error));
        }
    });
</script>
</body>
</html>
