<?php
    include 'app\Views\reusables\sidenav.php';
?>

<div class="container mt-4">
  <h2>Edit User</h2>
  <form action="<?= base_url('update/'.$user['id']) ?>" method="post">
    <div class="mb-3">
      <label>First Name</label>
      <input type="text" name="first_name" value="<?= esc($user['first_Name']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Last Name</label>
      <input type="text" name="last_name" value="<?= esc($user['last_Name']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Middle Name</label>
      <input type="text" name="middle_name" value="<?= esc($user['middle_Name']) ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" value="<?= esc($user['email']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Role</label>
      <select name="role" class="form-control" required>
        <option value="">Select Role</option>
        <?php
        $roles = ['Branch Manager','Inventory Staff','Central Office Admin','Supplier','Logistics Coordinator','Franchise Manager','System Administrator'];
        foreach ($roles as $role): ?>
          <option value="<?= $role ?>" <?= ($user['role'] === $role) ? 'selected' : '' ?>><?= $role ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Branch</label>
      <select name="branch_id" class="form-control">
        <option value="">(No branch assigned)</option>
        <?php foreach ($branches as $branch): ?>
          <option value="<?= $branch['id'] ?>" <?= ($user['branch_id'] == $branch['id']) ? 'selected' : '' ?>>
            <?= esc($branch['branch_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>New Password (leave blank to keep current)</label>
      <input type="password" name="password" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
  </form>
</div>
