<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Scan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <style>
        body { overflow-x: hidden; background: #f6f8fb; color:#212529; display: flex; }
        .sidebar { width: 220px; background-color: orange; color: #fff; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; min-height: 100vh; }
        .sidebar img { width: 100px; height: 100px; border-radius: 50%; margin-bottom: 15px; }
        .sidebar h5 { margin-bottom: 20px; text-align: center; }
        .sidebar a { width: 100%; padding: 12px 20px; color: #fff; text-decoration: none; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; }
        .content { flex-grow: 1; padding: 80px 24px 24px; }
        #interactive.viewport { position: relative; width: 100%; height: 300px; border: 1px solid #ccc; border-radius: 8px; overflow: hidden; }
        #interactive.viewport > canvas, #interactive.viewport > video { max-width: 100%; width: 100%; }
        canvas.drawing, canvas.drawingBuffer { position: absolute; left: 0; top: 0; }
        .camera-container { position: relative; }
        .camera-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80%; height: 2px; background: rgba(255, 0, 0, 0.8); border-radius: 1px; }
        .camera-overlay::before { content: ''; position: absolute; top: -10px; left: -10px; width: 20px; height: 20px; border: 2px solid rgba(255, 0, 0, 0.8); border-radius: 50%; }
        .camera-overlay::after { content: ''; position: absolute; top: -10px; right: -10px; width: 20px; height: 20px; border: 2px solid rgba(255, 0, 0, 0.8); border-radius: 50%; }
    </style>
</head>
<body>

<?php echo view('reusables/sidenav'); ?>

<main class="content">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">Barcode Scanner</h5>
            <span class="badge rounded-pill text-bg-secondary small">Real-time Camera</span>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header section-header fw-semibold">
                        <i class="bi bi-upc-scan me-2 text-primary"></i>Scan Barcode
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="barcodeInput" class="form-label">Barcode</label>
                            <input type="text" class="form-control" id="barcodeInput" placeholder="Scan or enter barcode manually">
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <button class="btn btn-primary" id="scanBtn">
                                <i class="bi bi-search me-2"></i>Find Item
                            </button>
                            <button class="btn btn-outline-secondary" id="cameraBtn">
                                <i class="bi bi-camera me-2"></i>Camera Scan
                            </button>
                        </div>
                        <div class="camera-container">
                            <div id="interactive" class="viewport d-none">
                                <div class="camera-overlay"></div>
                            </div>
                        </div>
                        <div class="mt-2 text-muted small">
                            <i class="bi bi-info-circle me-1"></i>Position barcode within the red lines for best results
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header section-header fw-semibold">
                        <i class="bi bi-info-circle me-2 text-info"></i>Item Details
                    </div>
                    <div class="card-body">
                        <div id="itemDetails" class="text-center text-muted">
                            <i class="bi bi-upc-scan fs-1 mb-3 text-primary"></i>
                            <p>Scan a barcode to view item details</p>
                        </div>
                        <div id="itemInfo" class="d-none">
                            <div class="row g-2">
                                <div class="col-6"><strong>Item Name:</strong></div>
                                <div class="col-6" id="itemName">-</div>
                                <div class="col-6"><strong>Available Stock:</strong></div>
                                <div class="col-6" id="availableStock">-</div>
                                <div class="col-6"><strong>Unit:</strong></div>
                                <div class="col-6" id="unit">-</div>
                                <div class="col-6"><strong>Price:</strong></div>
                                <div class="col-6" id="price">-</div>
                                <div class="col-6"><strong>Expiry Date:</strong></div>
                                <div class="col-6" id="expiryDate">-</div>
                            </div>
                            <div class="mt-3">
                                <a href="#" class="btn btn-sm btn-primary me-2" id="stockInBtn">
                                    <i class="bi bi-plus-circle me-1"></i>Stock In
                                </a>
                                <a href="#" class="btn btn-sm btn-danger" id="stockOutBtn">
                                    <i class="bi bi-dash-circle me-1"></i>Stock Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const branchId = <?php echo json_encode(session()->get('branch_id') ?? null); ?>;
    let scanning = false;

    document.getElementById('scanBtn').addEventListener('click', async () => {
        const barcode = document.getElementById('barcodeInput').value.trim();
        if (!barcode) {
            alert('Please enter a barcode');
            return;
        }

        await findItem(barcode);
    });

    document.getElementById('cameraBtn').addEventListener('click', () => {
        if (scanning) {
            stopScanning();
        } else {
            startScanning();
        }
    });

    async function findItem(barcode) {
        try {
            const url = new URL('<?php echo base_url('inventory/find'); ?>', window.location.origin);
            url.searchParams.set('barcode', barcode);
            if (branchId) url.searchParams.set('branch_id', branchId);

            const res = await fetch(url);
            const data = await res.json();

            if (res.ok) {
                displayItem(data);
            } else {
                document.getElementById('itemDetails').innerHTML = `
                    <i class="bi bi-exclamation-triangle fs-1 mb-3 text-warning"></i>
                    <p>${data.error}</p>
                `;
                document.getElementById('itemInfo').classList.add('d-none');
                document.getElementById('itemDetails').classList.remove('d-none');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error scanning barcode');
        }
    }

    function startScanning() {
        document.getElementById('interactive').classList.remove('d-none');
        document.getElementById('cameraBtn').innerHTML = '<i class="bi bi-stop-circle me-2"></i>Stop Camera';
        scanning = true;

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive'),
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: "environment"
                },
            },
            locator: {
                patchSize: "medium",
                halfSample: true
            },
            numOfWorkers: 2,
            decoder: {
                readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "upc_reader", "upc_e_reader"]
            },
            locate: true
        }, function(err) {
            if (err) {
                console.log(err);
                alert('Camera access failed. Please check permissions.');
                stopScanning();
                return;
            }
            Quagga.start();
        });

        Quagga.onDetected(function(result) {
            const code = result.codeResult.code;
            document.getElementById('barcodeInput').value = code;
            stopScanning();
            findItem(code);
        });
    }

    function stopScanning() {
        if (scanning) {
            Quagga.stop();
            document.getElementById('interactive').classList.add('d-none');
            document.getElementById('cameraBtn').innerHTML = '<i class="bi bi-camera me-2"></i>Camera Scan';
            scanning = false;
        }
    }

    function displayItem(item) {
        document.getElementById('itemName').textContent = item.item_name;
        document.getElementById('availableStock').textContent = item.available_stock;
        document.getElementById('unit').textContent = item.unit;
        document.getElementById('price').textContent = '₱' + parseFloat(item.price).toFixed(2);
        document.getElementById('expiryDate').textContent = item.expiry_date || 'N/A';

        document.getElementById('stockInBtn').href = '<?php echo base_url('inventory/stockin'); ?>';
        document.getElementById('stockOutBtn').href = '<?php echo base_url('inventory/stockout'); ?>';

        document.getElementById('itemDetails').classList.add('d-none');
        document.getElementById('itemInfo').classList.remove('d-none');
    }

    // Auto-focus on barcode input
    document.getElementById('barcodeInput').focus();

    // Allow scanning by pressing Enter
    document.getElementById('barcodeInput').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            document.getElementById('scanBtn').click();
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (scanning) {
            stopScanning();
        }
    });
</script>
</body>
</html>
