<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Purchase Request</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 mb-0">New Purchase Request</h1>
      <a href="<?= site_url('purchase-requests') ?>" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <form action="<?= site_url('purchase-requests') ?>" method="post" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Item Name</label>
            <input type="text" name="item_name" class="form-control" required value="<?= esc(old('item_name')) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Quantity</label>
            <input type="number" min="1" name="quantity" class="form-control" required value="<?= esc(old('quantity')) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Unit</label>
            <input type="text" name="unit" class="form-control" value="<?= esc(old('unit','pcs')) ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?= esc(old('description')) ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select" required>
              <option value="" disabled selected>Select supplier</option>
              <?php foreach (($suppliers ?? []) as $s): ?>
                <option value="<?= esc($s['id']) ?>" <?= old('supplier_id') == $s['id'] ? 'selected' : '' ?>>
                  <?= esc($s['supplier_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <?php if (!empty($branches)): ?>
            <div class="col-md-6">
              <label class="form-label">Branch</label>
              <select name="branch_id" class="form-select" <?= session()->get('branch_id') ? 'disabled' : '' ?>>
                <?php foreach ($branches as $b): ?>
                  <option value="<?= esc($b['id']) ?>" <?= (old('branch_id', session()->get('branch_id')) == $b['id']) ? 'selected' : '' ?>>
                    <?= esc($b['branch_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>

          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-send me-1"></i>Submit Request
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
