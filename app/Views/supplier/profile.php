<div class="content">
  <div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h5 fw-bold mb-0">Profile & Settings</h1>
      <div class="text-muted small">
        <?= esc(session()->get('supplier_name')) ?> (Supplier)
      </div>
    </div>

    <div class="row">
      <!-- Profile Information -->
      <div class="col-md-8">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-person me-2"></i>Supplier Information</h6>
          </div>
          <div class="card-body">
            <form action="<?= site_url('supplier/update-profile') ?>" method="post">
              <?= csrf_field() ?>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="supplier_name" class="form-label">Supplier Name</label>
                    <input type="text" class="form-control" id="supplier_name" value="<?= esc($supplier['supplier_name']) ?>" readonly>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="supplier_id" class="form-label">Supplier ID</label>
                    <input type="text" class="form-control" id="supplier_id" value="<?= esc($supplier['id']) ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label for="contact_info" class="form-label">Contact Information</label>
                <textarea class="form-control" id="contact_info" name="contact_info" rows="3"><?= esc($supplier['contact_info']) ?></textarea>
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= esc($supplier['address']) ?></textarea>
              </div>
              <div class="mb-3">
                <label for="terms" class="form-label">Payment Terms</label>
                <input type="text" class="form-control" id="terms" value="<?= esc($supplier['terms']) ?>" readonly>
              </div>
              <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Change Password -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-header bg-warning text-white">
            <h6 class="mb-0"><i class="bi bi-key me-2"></i>Change Password</h6>
          </div>
          <div class="card-body">
            <form action="<?= site_url('supplier/change-password') ?>" method="post">
              <?= csrf_field() ?>
              <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
              </div>
              <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
              </div>
              <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
              </div>
              <button type="submit" class="btn btn-warning">Change Password</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Password confirmation validation
document.querySelector('form[action*="change-password"]').addEventListener('submit', function(e) {
  const newPassword = document.getElementById('new_password').value;
  const confirmPassword = document.getElementById('confirm_password').value;

  if (newPassword !== confirmPassword) {
    e.preventDefault();
    alert('New password and confirmation do not match.');
  }
});
</script>
