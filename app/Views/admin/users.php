<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-people me-2"></i>User Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Admin</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Add New User
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="liveSearch" class="form-control border-start-0" placeholder="Type to search by name, email, or role..." autofocus>
                    </div>
                    <small class="text-muted">Results filter automatically as you type</small>
                </div>
                <div class="col-md-4">
                    <select id="roleFilter" class="form-select">
                        <option value="">All Roles</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= esc(strtolower($r['role_name'])) ?>" <?= ($roleFilter ?? '') == $r['id'] ? 'selected' : '' ?>>
                                <?= esc($r['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Results count -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span id="resultsCount" class="text-muted small">Showing <?= count($users) ?> users</span>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><span class="badge bg-secondary">#<?= $user['id'] ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2">
                                                <?= strtoupper(substr($user['first_Name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <span class="fw-semibold"><?= esc($user['first_Name'] . ' ' . $user['last_Name']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td><span class="badge bg-info"><?= esc($user['role_name'] ?? 'N/A') ?></span></td>
                                    <td><?= esc($user['branch_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="<?= site_url('admin/users/reset-password/' . $user['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Reset password to default?')">
                                                <button type="submit" class="btn btn-outline-warning" title="Reset Password">
                                                    <i class="bi bi-key"></i>
                                                </button>
                                            </form>
                                            <?php if ($user['id'] != session()->get('user_id')): ?>
                                                <form action="<?= site_url('admin/users/delete/' . $user['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted fs-1"></i>
                    <p class="text-muted mt-2">No users found</p>
                    <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i> Add First User
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Live Search JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('liveSearch');
    const roleFilter = document.getElementById('roleFilter');
    const clearBtn = document.getElementById('clearFilters');
    const resultsCount = document.getElementById('resultsCount');
    const tableBody = document.querySelector('table tbody');
    
    if (!tableBody) return;
    
    const rows = tableBody.querySelectorAll('tr');
    const totalUsers = rows.length;
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedRole = roleFilter.value.toLowerCase();
        let visibleCount = 0;
        
        rows.forEach(row => {
            const name = row.cells[1]?.textContent.toLowerCase() || '';
            const email = row.cells[2]?.textContent.toLowerCase() || '';
            const role = row.cells[3]?.textContent.toLowerCase() || '';
            const branch = row.cells[4]?.textContent.toLowerCase() || '';
            
            // Check search term
            const matchesSearch = searchTerm === '' || 
                name.includes(searchTerm) || 
                email.includes(searchTerm) || 
                role.includes(searchTerm) ||
                branch.includes(searchTerm);
            
            // Check role filter
            const matchesRole = selectedRole === '' || role.includes(selectedRole);
            
            // Show/hide row
            if (matchesSearch && matchesRole) {
                row.style.display = '';
                visibleCount++;
                
                // Highlight matching text if search term exists
                if (searchTerm) {
                    highlightText(row, searchTerm);
                } else {
                    removeHighlight(row);
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update results count
        if (searchTerm || selectedRole) {
            resultsCount.innerHTML = `<span class="badge bg-primary">${visibleCount}</span> of ${totalUsers} users found`;
        } else {
            resultsCount.textContent = `Showing ${totalUsers} users`;
        }
        
        // Show "no results" message if needed
        const noResultsRow = tableBody.querySelector('.no-results-row');
        if (visibleCount === 0) {
            if (!noResultsRow) {
                const newRow = document.createElement('tr');
                newRow.className = 'no-results-row';
                newRow.innerHTML = `<td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-search fs-4 d-block mb-2"></i>
                    No users match "<strong>${searchTerm}</strong>"
                </td>`;
                tableBody.appendChild(newRow);
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    }
    
    function highlightText(row, term) {
        // Only highlight name and email cells
        [1, 2].forEach(cellIndex => {
            const cell = row.cells[cellIndex];
            if (cell) {
                const originalText = cell.textContent;
                const regex = new RegExp(`(${term})`, 'gi');
                if (cell.querySelector('.fw-semibold')) {
                    const span = cell.querySelector('.fw-semibold');
                    span.innerHTML = span.textContent.replace(regex, '<mark class="bg-warning p-0">$1</mark>');
                } else if (!cell.querySelector('.badge')) {
                    cell.innerHTML = originalText.replace(regex, '<mark class="bg-warning p-0">$1</mark>');
                }
            }
        });
    }
    
    function removeHighlight(row) {
        row.querySelectorAll('mark').forEach(mark => {
            mark.outerHTML = mark.textContent;
        });
    }
    
    // Event listeners
    searchInput.addEventListener('input', function() {
        filterTable();
    });
    
    roleFilter.addEventListener('change', function() {
        filterTable();
    });
    
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        roleFilter.value = '';
        filterTable();
        searchInput.focus();
    });
    
    // Keyboard shortcut: Escape to clear
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            filterTable();
        }
    });
});
</script>

