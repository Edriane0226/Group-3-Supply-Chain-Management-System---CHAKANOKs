<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Receipt - Payment #<?= esc($payment['id']) ?></title>
  <style>
    @media print {
      body { margin: 0; padding: 0; }
      .no-print { display: none !important; }
      .print-only { display: block !important; }
      @page { size: A4; margin: 10mm; }
    }
    body {
      font-family: 'Courier New', monospace;
      max-width: 300px;
      margin: 0 auto;
      padding: 15px;
      background: #fff;
      position: relative;
      font-size: 12px;
    }
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('<?= base_url('public/images/2.jpg') ?>');
      background-size: 200px;
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0.03;
      z-index: -1;
      pointer-events: none;
    }
    .receipt-header {
      text-align: center;
      border-bottom: 2px dashed #000;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .receipt-header img {
      max-width: 60px !important;
      height: auto;
      margin-bottom: 5px;
    }
    .receipt-header h1 {
      margin: 0;
      font-size: 16px;
      color: #000;
      font-weight: bold;
    }
    .receipt-header p {
      margin: 2px 0;
      color: #666;
      font-size: 10px;
    }
    .receipt-info {
      margin-bottom: 12px;
    }
    .info-box {
      margin-bottom: 10px;
    }
    .info-box h3 {
      margin: 0 0 5px 0;
      font-size: 10px;
      color: #666;
      text-transform: uppercase;
      font-weight: bold;
    }
    .info-box p {
      margin: 2px 0;
      font-size: 11px;
      line-height: 1.3;
    }
    .receipt-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
      font-size: 11px;
    }
    .receipt-table th,
    .receipt-table td {
      padding: 4px 2px;
      text-align: left;
      border-bottom: 1px dotted #ddd;
    }
    .receipt-table th {
      background-color: transparent;
      font-weight: bold;
      font-size: 10px;
    }
    .receipt-table .text-right {
      text-align: right;
    }
    .receipt-footer {
      margin-top: 15px;
      padding-top: 10px;
      border-top: 2px dashed #000;
      text-align: center;
      color: #666;
      font-size: 9px;
      line-height: 1.4;
    }
    .print-actions {
      text-align: center;
      margin: 20px 0;
      padding: 20px;
      background: #f5f5f5;
    }
    .print-actions button {
      padding: 10px 30px;
      font-size: 16px;
      margin: 0 10px;
      cursor: pointer;
    }
    .status-badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 3px;
      font-weight: bold;
      font-size: 10px;
    }
    .status-completed {
      background-color: #28a745;
      color: white;
    }
    .divider {
      border-top: 1px dashed #000;
      margin: 8px 0;
    }
    .total-amount {
      text-align: center;
      margin: 20px 0;
      padding: 15px;
      background-color: #f0f8f0;
      border: 2px dashed #28a745;
    }
    .total-amount p {
      margin: 0;
      font-size: 11px;
      color: #666;
      text-transform: uppercase;
      font-weight: bold;
    }
    .total-amount .amount {
      margin: 5px 0 0 0;
      font-size: 24px;
      font-weight: bold;
      color: #28a745;
    }
  </style>
</head>
<body>
  <div class="print-actions no-print">
    <button onclick="window.print()" style="background: #007bff; color: white; border: none; border-radius: 5px;">
      <i class="bi bi-printer"></i> Print Receipt
    </button>
    <a href="<?= site_url('franchise/payments/' . $payment['franchise_id']) ?>" style="padding: 10px 30px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">
      <i class="bi bi-arrow-left"></i> Back to Payments
    </a>
  </div>

  <div class="receipt-header">
    <img src="<?= base_url('public/images/2.jpg') ?>" alt="ChakaNoks Logo" style="max-width: 60px; height: auto; margin-bottom: 5px;">
    <h1>CHAKANOKS SCMS</h1>
    <p>Supply Chain Management System</p>
    <p style="margin-top: 5px; font-size: 12px; font-weight: bold;">PAYMENT RECEIPT</p>
  </div>

  <?php
    // Set Philippine timezone and convert database time to PHT
    date_default_timezone_set('Asia/Manila');
    
    $paymentDate = $payment['payment_date'] ?? date('Y-m-d');
    $paymentTime = $payment['created_at'] ?? date('Y-m-d H:i:s');
    
    // Create DateTime object and convert to Philippine timezone
    $dateTime = new \DateTime($paymentTime, new \DateTimeZone('UTC'));
    $dateTime->setTimezone(new \DateTimeZone('Asia/Manila'));
    
    $phDate = $dateTime->format('M d, Y');
    $phTime = $dateTime->format('h:i A');
  ?>

  <div class="receipt-info">
    <div class="info-box">
      <p><strong>Receipt #:</strong> RCP-<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></p>
      <p><strong>Payment #:</strong> PAY-<?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></p>
      <p><strong>Date:</strong> <?= $phDate ?></p>
      <p><strong>Time:</strong> <?= $phTime ?> (PHT)</p>
      <p><strong>Status:</strong> 
        <span class="status-badge status-completed"><?= strtoupper($payment['payment_status'] ?? 'completed') ?></span>
      </p>
    </div>
    <div class="divider"></div>
    <div class="info-box">
      <p><strong>Franchise:</strong> <?= esc($franchise['applicant_name'] ?? 'N/A') ?></p>
      <p><strong>Payment Type:</strong> <?= ucfirst(str_replace('_', ' ', $payment['payment_type'] ?? 'N/A')) ?></p>
      <p><strong>Payment Method:</strong> <?= ucfirst($payment['payment_method'] ?? 'Cash') ?></p>
      <?php if ($payment['reference_number']): ?>
        <p><strong>Reference:</strong> <?= esc($payment['reference_number']) ?></p>
      <?php endif; ?>
      <?php if (!empty($payment['period_start']) && !empty($payment['period_end'])): ?>
        <p><strong>Period:</strong> <?= date('M d', strtotime($payment['period_start'])) ?> - <?= date('M d, Y', strtotime($payment['period_end'])) ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="divider"></div>

  <table class="receipt-table">
    <tbody>
      <tr>
        <td><strong>Payment Type</strong></td>
        <td class="text-right"><?= ucfirst(str_replace('_', ' ', $payment['payment_type'] ?? 'N/A')) ?></td>
      </tr>
      <tr>
        <td><strong>Amount Paid</strong></td>
        <td class="text-right"><strong>₱<?= number_format($payment['amount'], 2) ?></strong></td>
      </tr>
    </tbody>
  </table>

  <div class="total-amount">
    <p>Total Amount</p>
    <p class="amount">₱<?= number_format($payment['amount'], 2) ?></p>
  </div>

  <?php if ($payment['remarks']): ?>
    <div class="divider"></div>
    <div class="info-box" style="margin-top: 10px;">
      <p><strong>Remarks:</strong></p>
      <p style="font-size: 10px;"><?= nl2br(esc($payment['remarks'])) ?></p>
    </div>
  <?php endif; ?>

  <div class="receipt-footer">
    <p><strong>Thank you for your payment!</strong></p>
    <p>Computer-generated receipt</p>
    <p>No signature required</p>
    <div class="divider"></div>
    <?php
      // Get current time in Philippine timezone
      $now = new \DateTime('now', new \DateTimeZone('Asia/Manila'));
      $generatedDate = $now->format('M d, Y h:i A');
    ?>
    <p style="margin-top: 5px;">
      Generated: <?= $generatedDate ?> (PHT)
    </p>
    <p>By: <?= esc(($currentUser['first_Name'] ?? '') . ' ' . ($currentUser['last_Name'] ?? 'Central Admin')) ?></p>
    <p style="margin-top: 8px;">
      CHAKANOKS SCMS - Supply Chain Management System<br>
      For inquiries, please contact the Central Office
    </p>
  </div>

  <script>
    // Auto-print when page loads (optional - can be removed if not needed)
    // window.onload = function() {
    //   window.print();
    // }
  </script>
</body>
</html>

