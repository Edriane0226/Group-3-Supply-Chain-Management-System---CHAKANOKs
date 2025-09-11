<?php
    include 'app\\Views\\reusables\\sidenav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Low Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<main class="content">
    <div class="container-fluid">
        <h5 class="mb-3">Low Stock</h5>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Reorder</th><th class="text-end">Actions</th></tr>
                        </thead>
                        <tbody id="low_stock"><tr><td colspan="4" class="text-center text-muted">Loading…</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const branchId = <?php echo json_encode(session()->get('branch_id') ?? null); ?>;

    async function loadLow() {
        const url = new URL('<?php echo base_url('inventory/summary'); ?>', window.location.origin);
        if (branchId) url.searchParams.set('branch_id', branchId);
        const res = await fetch(url);
        if (!res.ok) return;
        const data = await res.json();
        const low = data.lowStock || [];
        const lowBody = document.getElementById('low_stock');
        lowBody.innerHTML = '';
        if (!low.length) lowBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">All good</td></tr>';
        low.forEach(i => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${i.item_name}</td><td class="text-end">${i.quantity}</td><td class="text-end">${i.reorder_level}</td>
                            <td class=\"text-end\"><a class=\"btn btn-sm btn-primary\" href=\"<?php echo base_url('inventory/scan'); ?>\">Scan/Update</a></td>`;
            lowBody.appendChild(tr);
        });
    }

    loadLow();
    setInterval(loadLow, 15000);
</script>
</body>
</html>

