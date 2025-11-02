<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Stock Out</title>
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
        <h5 class="mb-3">Stock Out</h5>
        <div class="card shadow-sm">
            <div class="card-body">
                <form id="stockOutForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Branch ID</label>
                            <input type="number" name="branch_id" class="form-control" value="<?= session()->get('branch_id') ?>" required>
                        </div>
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
                            <label class="form-label">Item Name</label>
                            <input type="text" name="item_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="kg, pcs, liters" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Reason</label>
                            <select name="reason" class="form-control">
                                <option value="">Select Reason</option>
                                <option value="sold">Sold</option>
                                <option value="expired">Expired</option>
                                <option value="damaged">Damaged</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-dash-circle me-2"></i>Remove Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('stockOutForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        const res = await fetch('<?= base_url('inventory/stockout') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (res.ok) {
            alert('Stock removed successfully');
            this.reset();
        } else {
            alert('Error: ' + JSON.stringify(result.error));
        }
    });
</script>
</body>
</html>
