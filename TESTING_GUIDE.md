# 🧪 Testing Guide - Central Office Dashboard Enhancements

## ✅ **PAANO I-TEST**

### **Method 1: Direct Browser Test (Pinakamadali)**

1. **I-start ang XAMPP** (Apache at MySQL)
2. **I-access ang system:**
   ```
   http://localhost/CHAKANOKS_SCMS/login
   ```
3. **Mag-login bilang Central Office Admin:**
   - **User ID:** `23116000`
   - **Password:** `password123`
4. **Pumunta sa Dashboard:**
   - After login, automatic redirect sa dashboard
   - O i-access: `http://localhost/CHAKANOKS_SCMS/dashboard`

5. **I-check ang browser console (F12) para makita kung may errors**

---

### **Method 2: Test via Code (Debug Mode)**

I-add ang code sa dashboard view para makita ang data:

**File:** `app/Views/pages/dashboard.php`

I-add sa Central Office Admin section (after line 303):

```php
<!-- DEBUG: Test New Data -->
<div class="card mb-3 border-info">
  <div class="card-header bg-info text-white">
    <strong>🔍 DEBUG: New Data Available</strong>
  </div>
  <div class="card-body">
    <h6>Purchase Request Statistics:</h6>
    <pre><?= print_r($prStatistics ?? 'NOT SET', true) ?></pre>
    
    <h6>Cost Summary:</h6>
    <pre><?= print_r($costSummary ?? 'NOT SET', true) ?></pre>
    
    <h6>Wastage Summary:</h6>
    <pre><?= print_r($wastageSummary ?? 'NOT SET', true) ?></pre>
  </div>
</div>
```

---

### **Method 3: Test via API/JSON Endpoint**

Gumawa ng test endpoint para makita ang data:

**File:** `app/Controllers/Dashboard.php`

I-add ang method na ito:

```php
public function testData()
{
    $session = session();
    
    if (!$session->get('isLoggedIn') || $session->get('role') !== 'Central Office Admin') {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
    }

    $purchaseRequestModel = new PurchaseRequestModel();
    $purchaseOrderModel = new PurchaseOrderModel();
    $inventoryModel = new InventoryModel();

    $data = [
        'pr_statistics' => $purchaseRequestModel->getStatisticsSummary(),
        'pr_by_branch' => $purchaseRequestModel->getStatisticsByBranch(),
        'cost_summary' => $purchaseOrderModel->getCostSummary(),
        'cost_by_branch' => $purchaseOrderModel->getCostBreakdownByBranch(),
        'wastage_summary' => $inventoryModel->getWastageSummary(),
        'wastage_by_branch' => $inventoryModel->getWastageByBranch(),
    ];

    return $this->response->setJSON($data);
}
```

**Route:** I-add sa `app/Config/Routes.php`:
```php
$routes->get('dashboard/test-data', 'Dashboard::testData');
```

**Access:** `http://localhost/CHAKANOKS_SCMS/dashboard/test-data`

---

### **Method 4: Test via Terminal (PHP CLI)**

Gumawa ng test script:

**File:** `test_dashboard_data.php` (sa root directory)

```php
<?php
require 'vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsConfig = APPPATH . 'Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
require realpath($bootstrap) ?: $bootstrap;
$app = Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

// Test the models
$purchaseRequestModel = new \App\Models\PurchaseRequestModel();
$purchaseOrderModel = new \App\Models\PurchaseOrderModel();
$inventoryModel = new \App\Models\InventoryModel();

echo "=== Testing Purchase Request Statistics ===\n";
$prStats = $purchaseRequestModel->getStatisticsSummary();
print_r($prStats);

echo "\n=== Testing Cost Analysis ===\n";
$costSummary = $purchaseOrderModel->getCostSummary();
print_r($costSummary);

echo "\n=== Testing Wastage Analysis ===\n";
$wastageSummary = $inventoryModel->getWastageSummary();
print_r($wastageSummary);

echo "\n✅ All tests completed!\n";
```

**Run:**
```bash
php test_dashboard_data.php
```

---

## 🔍 **VERIFICATION CHECKLIST**

### ✅ **Check 1: Data is Available**
- [ ] Login as Central Office Admin
- [ ] Open browser console (F12)
- [ ] Check for JavaScript errors
- [ ] Verify page loads without errors

### ✅ **Check 2: Methods Work**
- [ ] Test via Method 3 (JSON endpoint)
- [ ] Verify data structure is correct
- [ ] Check if arrays are not empty (if may data sa database)

### ✅ **Check 3: Database Queries**
- [ ] Check MySQL logs for errors
- [ ] Verify queries execute successfully
- [ ] Check if data exists in database

---

## 🐛 **TROUBLESHOOTING**

### **Problem: "Variable not defined" error**
**Solution:** I-check kung na-include ang data sa `$data` array sa Dashboard controller

### **Problem: "Method not found" error**
**Solution:** I-verify na na-save ang model files at walang syntax errors

### **Problem: Empty data**
**Solution:** Normal lang kung walang data sa database. I-check kung may:
- Purchase requests
- Purchase orders
- Inventory items

### **Problem: Database error**
**Solution:** I-check kung:
- MySQL is running
- Database connection is correct
- Tables exist (run migrations)

---

## 📊 **EXPECTED OUTPUT**

Kung may data sa database, dapat makita:

**Purchase Request Statistics:**
```php
Array
(
    [total] => 50
    [pending] => 10
    [approved] => 35
    [rejected] => 3
    [cancelled] => 2
    [approval_rate] => 70.0
)
```

**Cost Summary:**
```php
Array
(
    [total_orders] => 30
    [total_cost] => 150000.00
    [avg_order_value] => 5000.00
    [min_order_value] => 500.00
    [max_order_value] => 10000.00
)
```

**Wastage Summary:**
```php
Array
(
    [total_wastage_value] => 5000.00
    [expired_value] => 3000.00
    [damaged_value] => 2000.00
    [expired_items_count] => 0
    [damaged_items_count] => 15
)
```

---

## 🎯 **QUICK TEST**

**Pinakamabilis na paraan:**

1. Login as Central Office Admin (ID: 23116000)
2. I-access: `http://localhost/CHAKANOKS_SCMS/dashboard`
3. I-right-click → "View Page Source"
4. I-search ang: `prStatistics` o `costSummary`
5. Kung makita mo ang variable names, ibig sabihin naipasa na ang data! ✅

---

*Last Updated: 2025-12-04*

