<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseRequestModel;
use App\Models\FranchiseModel;
use App\Models\FranchisePaymentModel;
use App\Libraries\ReportExport;
use CodeIgniter\Controller;

class ScheduledReports extends Controller
{
    protected $reportExport;

    public function __construct()
    {
        $this->reportExport = new ReportExport();
        helper(['email']);
    }

    /**
     * Generate and send scheduled reports
     * Can be called via cron job
     */
    public function generateDailyReports()
    {
        // This can be called via cron: 0 8 * * * (daily at 8 AM)
        
        $recipients = $this->getReportRecipients();
        $reports = [];

        // Generate Cost Analysis Report
        $purchaseOrderModel = new PurchaseOrderModel();
        $costByBranch = $purchaseOrderModel->getCostBreakdownByBranch();
        
        if (!empty($costByBranch)) {
            $data = [];
            $headers = ['Branch', 'Total Cost', 'Order Count', 'Average Order Value'];
            foreach ($costByBranch as $branch) {
                $data[] = [
                    $branch['branch_name'] ?? 'N/A',
                    '₱' . number_format($branch['total_cost'] ?? 0, 2),
                    $branch['order_count'] ?? 0,
                    '₱' . number_format($branch['avg_order_value'] ?? 0, 2)
                ];
            }
            
            $tempFile = WRITEPATH . 'temp/cost_report_' . date('Y-m-d') . '.pdf';
            if (!is_dir(WRITEPATH . 'temp')) {
                mkdir(WRITEPATH . 'temp', 0755, true);
            }
            
            $pdfContent = $this->reportExport->generatePDF($data, 'Daily Cost Analysis Report', $headers);
            file_put_contents($tempFile, $pdfContent);
            $reports[] = ['file' => $tempFile, 'name' => 'Daily Cost Analysis Report'];
        }

        // Generate Wastage Report
        $inventoryModel = new InventoryModel();
        $wastageByBranch = $inventoryModel->getWastageByBranch();
        
        if (!empty($wastageByBranch)) {
            $data = [];
            $headers = ['Branch', 'Total Wastage Value', 'Expired Value', 'Damaged Value'];
            foreach ($wastageByBranch as $branch) {
                $data[] = [
                    $branch['branch_name'] ?? 'N/A',
                    '₱' . number_format($branch['total_wastage_value'] ?? 0, 2),
                    '₱' . number_format($branch['expired_value'] ?? 0, 2),
                    '₱' . number_format($branch['damaged_value'] ?? 0, 2)
                ];
            }
            
            $tempFile = WRITEPATH . 'temp/wastage_report_' . date('Y-m-d') . '.pdf';
            $pdfContent = $this->reportExport->generatePDF($data, 'Daily Wastage Report', $headers);
            file_put_contents($tempFile, $pdfContent);
            $reports[] = ['file' => $tempFile, 'name' => 'Daily Wastage Report'];
        }

        // Send reports via email
        foreach ($recipients as $recipient) {
            foreach ($reports as $report) {
                $this->sendReportEmail(
                    $recipient,
                    $report['name'] . ' - ' . date('F d, Y'),
                    $this->getEmailBody($report['name']),
                    $report['file'],
                    basename($report['file'])
                );
            }
        }

        // Cleanup temp files
        foreach ($reports as $report) {
            if (file_exists($report['file'])) {
                unlink($report['file']);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Reports generated and sent successfully',
            'reports_sent' => count($reports),
            'recipients' => count($recipients)
        ]);
    }

    /**
     * Generate weekly franchise performance report
     */
    public function generateWeeklyFranchiseReport()
    {
        // Can be called via cron: 0 9 * * 1 (Every Monday at 9 AM)
        
        $franchiseModel = new FranchiseModel();
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');
        
        $performanceData = $franchiseModel->getAllFranchisesPerformance($startDate, $endDate);
        
        if (empty($performanceData)) {
            return $this->response->setJSON(['message' => 'No franchise data available']);
        }

        $data = [];
        $headers = ['Franchise', 'Status', 'Total Payments', 'Avg Monthly Revenue', 'Overall Score'];
        foreach ($performanceData as $perf) {
            $data[] = [
                $perf['franchise_name'] ?? 'N/A',
                ucfirst($perf['status'] ?? 'N/A'),
                '₱' . number_format($perf['total_payments'] ?? 0, 2),
                '₱' . number_format($perf['avg_monthly_revenue'] ?? 0, 2),
                number_format($perf['overall_score'] ?? 0, 1)
            ];
        }

        $tempFile = WRITEPATH . 'temp/franchise_weekly_' . date('Y-m-d') . '.pdf';
        if (!is_dir(WRITEPATH . 'temp')) {
            mkdir(WRITEPATH . 'temp', 0755, true);
        }

        $pdfContent = $this->reportExport->generatePDF($data, 'Weekly Franchise Performance Report', $headers);
        file_put_contents($tempFile, $pdfContent);

        $recipients = $this->getFranchiseReportRecipients();
        foreach ($recipients as $recipient) {
            $this->sendReportEmail(
                $recipient,
                'Weekly Franchise Performance Report - ' . date('F d, Y'),
                $this->getEmailBody('Weekly Franchise Performance Report'),
                $tempFile,
                basename($tempFile)
            );
        }

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Weekly franchise report generated and sent'
        ]);
    }

    /**
     * Get report recipients (Central Office Admins)
     */
    private function getReportRecipients(): array
    {
        $userModel = new \App\Models\UserModel();
        $admins = $userModel->select('email')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('roles.role_name', 'Central Office Admin')
            ->findAll();

        return array_column($admins, 'email');
    }

    /**
     * Get franchise report recipients
     */
    private function getFranchiseReportRecipients(): array
    {
        $userModel = new \App\Models\UserModel();
        $recipients = $userModel->select('email')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->whereIn('roles.role_name', ['Central Office Admin', 'Franchise Manager'])
            ->findAll();

        return array_column($recipients, 'email');
    }

    /**
     * Send report via email
     */
    private function sendReportEmail(string $to, string $subject, string $body, string $attachmentPath, string $attachmentName): bool
    {
        return $this->reportExport->sendReportEmail($to, $subject, $body, $attachmentPath, $attachmentName);
    }

    /**
     * Get email body template
     */
    private function getEmailBody(string $reportName): string
    {
        return "
        <html>
        <body>
            <h2>ChakaNoks SCMS - {$reportName}</h2>
            <p>Dear Administrator,</p>
            <p>Please find attached the {$reportName} for " . date('F d, Y') . ".</p>
            <p>This is an automated report generated by the ChakaNoks Supply Chain Management System.</p>
            <p>Best regards,<br>ChakaNoks SCMS</p>
        </body>
        </html>
        ";
    }
}

