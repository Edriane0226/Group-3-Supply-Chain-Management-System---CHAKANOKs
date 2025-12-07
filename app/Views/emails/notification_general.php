<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #ff8c00 0%, #ff6b00 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .message-box {
            background-color: #f9f9f9;
            border-left: 4px solid #ff8c00;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ff8c00;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .date-info {
            color: #777;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>ChakaNoks SCMS</h1>
        </div>
        
        <div class="content">
            <h2><?= esc($title) ?></h2>
            
            <div class="message-box">
                <?= nl2br(esc($message)) ?>
            </div>
            
            <div class="date-info">
                <strong>Date:</strong> <?= esc($date) ?>
            </div>
            
            <?php if (isset($referenceId) && $referenceId): ?>
            <div class="date-info">
                <strong>Reference ID:</strong> #<?= esc($referenceId) ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from ChakaNoks Supply Chain Management System.</p>
            <p>© <?= date('Y') ?> ChakaNoks. All rights reserved.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>

