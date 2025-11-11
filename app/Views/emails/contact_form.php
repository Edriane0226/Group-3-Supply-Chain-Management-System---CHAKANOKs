<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #ff8c00; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; }
        .footer { margin-top: 20px; text-align: center; font-size: 0.9em; color: #777; }
        .field { margin-bottom: 15px; }
        .field-label { font-weight: bold; margin-bottom: 5px; display: block; }
        .field-value { padding: 8px; background: white; border: 1px solid #eee; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Contact Form Submission</h2>
    </div>
    
    <div class="content">
        <div class="field">
            <span class="field-label">From:</span>
            <div class="field-value"><?= $name ?> (<?= $email ?>)</div>
        </div>
        
        <div class="field">
            <span class="field-label">Subject:</span>
            <div class="field-value"><?= $subject ?></div>
        </div>
        
        <div class="field">
            <span class="field-label">Message:</span>
            <div class="field-value"><?= $message ?></div>
        </div>
    </div>
    
    <div class="footer">
        <p>This email was sent from the contact form on <?= date('F j, Y, g:i a') ?></p>
        <p>© <?= date('Y') ?> Chakanoks. All rights reserved.</p>
    </div>
</body>
</html>
