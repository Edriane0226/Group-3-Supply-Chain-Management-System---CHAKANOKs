<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use Config\Email as EmailConfig;

class EmailService
{
    protected $email;
    protected $config;

    public function __construct()
    {
        $this->email = \Config\Services::email();
        $this->config = config('Email');
        
        // Configure SMTP settings
        $this->configureSMTP();
    }

    /**
     * Configure SMTP settings from config or environment
     */
    protected function configureSMTP(): void
    {
        // Use environment variables if available, otherwise use config
        $smtpHost = getenv('SMTP_HOST') ?: $this->config->SMTPHost;
        $smtpUser = getenv('SMTP_USER') ?: $this->config->SMTPUser;
        $smtpPass = getenv('SMTP_PASS') ?: $this->config->SMTPPass;
        $smtpPort = getenv('SMTP_PORT') ? (int)getenv('SMTP_PORT') : $this->config->SMTPPort;
        $smtpCrypto = getenv('SMTP_CRYPTO') ?: $this->config->SMTPCrypto;
        $fromEmail = getenv('SMTP_FROM_EMAIL') ?: $this->config->fromEmail;
        $fromName = getenv('SMTP_FROM_NAME') ?: $this->config->fromName;

        $this->email->initialize([
            'protocol' => 'smtp',
            'SMTPHost' => $smtpHost,
            'SMTPUser' => $smtpUser,
            'SMTPPass' => $smtpPass,
            'SMTPPort' => $smtpPort,
            'SMTPCrypto' => $smtpCrypto,
            'SMTPTimeout' => $this->config->SMTPTimeout,
            'SMTPKeepAlive' => $this->config->SMTPKeepAlive,
            'mailType' => 'html',
            'charset' => 'UTF-8',
            'wordWrap' => $this->config->wordWrap,
            'wrapChars' => $this->config->wrapChars,
            'validate' => $this->config->validate,
            'priority' => $this->config->priority,
            'CRLF' => $this->config->CRLF,
            'newline' => $this->config->newline,
            'BCCBatchMode' => $this->config->BCCBatchMode,
            'BCCBatchSize' => $this->config->BCCBatchSize,
            'DSN' => $this->config->DSN,
        ]);

        $this->email->setFrom($fromEmail, $fromName);
    }

    /**
     * Send email with template
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $template Template name (without .php extension)
     * @param array $data Data to pass to template
     * @param string|null $attachmentPath Optional attachment file path
     * @param string|null $attachmentName Optional attachment name
     * @return bool Success status
     */
    public function sendTemplate(
        string $to,
        string $subject,
        string $template,
        array $data = [],
        ?string $attachmentPath = null,
        ?string $attachmentName = null
    ): bool {
        try {
            // Load email template
            $emailContent = view("emails/{$template}", $data);
            
            $this->email->setTo($to);
            $this->email->setSubject($subject);
            $this->email->setMessage($emailContent);
            
            if ($attachmentPath && file_exists($attachmentPath)) {
                $this->email->attach($attachmentPath, 'attachment', $attachmentName ?? basename($attachmentPath));
            }
            
            $result = $this->email->send();
            
            if (!$result) {
                log_message('error', 'Email sending failed: ' . $this->email->printDebugger(['headers']));
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Email service error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send simple email (without template)
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email message (HTML or plain text)
     * @param string|null $attachmentPath Optional attachment file path
     * @param string|null $attachmentName Optional attachment name
     * @param string|null $replyTo Optional reply-to email address
     * @param string|null $replyToName Optional reply-to name
     * @return bool Success status
     */
    public function send(
        string $to,
        string $subject,
        string $message,
        ?string $attachmentPath = null,
        ?string $attachmentName = null,
        ?string $replyTo = null,
        ?string $replyToName = null
    ): bool {
        try {
            $this->email->setTo($to);
            $this->email->setSubject($subject);
            $this->email->setMessage($message);
            
            // Set reply-to if provided (useful for contact forms)
            if ($replyTo) {
                $this->email->setReplyTo($replyTo, $replyToName ?? '');
            }
            
            if ($attachmentPath && file_exists($attachmentPath)) {
                $this->email->attach($attachmentPath, 'attachment', $attachmentName ?? basename($attachmentPath));
            }
            
            $result = $this->email->send();
            
            if (!$result) {
                log_message('error', 'Email sending failed: ' . $this->email->printDebugger(['headers']));
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Email service error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk emails
     * 
     * @param array $recipients Array of recipient emails
     * @param string $subject Email subject
     * @param string $template Template name
     * @param array $data Data to pass to template
     * @return array Results with 'sent' and 'failed' counts
     */
    public function sendBulk(
        array $recipients,
        string $subject,
        string $template,
        array $data = []
    ): array {
        $results = ['sent' => 0, 'failed' => 0];
        
        foreach ($recipients as $recipient) {
            if ($this->sendTemplate($recipient, $subject, $template, $data)) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }

    /**
     * Send notification email (for NotificationModel)
     * 
     * @param string $to Recipient email
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $type Notification type (purchase_request, purchase_order, delivery, etc.)
     * @param int|null $referenceId Reference ID
     * @return bool Success status
     */
    public function sendNotification(
        string $to,
        string $title,
        string $message,
        string $type = 'general',
        ?int $referenceId = null
    ): bool {
        $template = $this->getNotificationTemplate($type);
        
        return $this->sendTemplate($to, $title, $template, [
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'referenceId' => $referenceId,
            'date' => date('F d, Y h:i A'),
        ]);
    }

    /**
     * Get notification template name based on type
     */
    protected function getNotificationTemplate(string $type): string
    {
        $templates = [
            'purchase_request' => 'notification_pr',
            'purchase_order' => 'notification_po',
            'delivery' => 'notification_delivery',
            'franchise' => 'notification_franchise',
            'supplier' => 'notification_supplier',
            'general' => 'notification_general',
        ];
        
        return $templates[$type] ?? 'notification_general';
    }

    /**
     * Test SMTP connection
     * 
     * @return array Test results
     */
    public function testConnection(): array
    {
        $results = [
            'success' => false,
            'message' => '',
            'config' => [
                'host' => getenv('SMTP_HOST') ?: $this->config->SMTPHost,
                'port' => getenv('SMTP_PORT') ? (int)getenv('SMTP_PORT') : $this->config->SMTPPort,
                'user' => getenv('SMTP_USER') ?: $this->config->SMTPUser,
                'crypto' => getenv('SMTP_CRYPTO') ?: $this->config->SMTPCrypto,
            ]
        ];
        
        try {
            // Try to send a test email to the configured email
            $testEmail = getenv('SMTP_FROM_EMAIL') ?: $this->config->fromEmail;
            $testResult = $this->send(
                $testEmail,
                'SMTP Test - ' . date('Y-m-d H:i:s'),
                '<p>This is a test email from ChakaNoks SCMS.</p><p>If you received this, your SMTP configuration is working correctly.</p>'
            );
            
            if ($testResult) {
                $results['success'] = true;
                $results['message'] = 'SMTP connection successful. Test email sent.';
            } else {
                $results['message'] = 'SMTP connection failed. Check your configuration.';
            }
        } catch (\Exception $e) {
            $results['message'] = 'SMTP test error: ' . $e->getMessage();
        }
        
        return $results;
    }
}

