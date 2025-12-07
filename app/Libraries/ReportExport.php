<?php

namespace App\Libraries;

use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportExport
{
    /**
     * Ensure vendor autoload is loaded
     */
    public function __construct()
    {
        // Ensure vendor autoload is loaded
        $vendorAutoload = ROOTPATH . 'vendor/autoload.php';
        if (file_exists($vendorAutoload) && !class_exists('TCPDF')) {
            require_once $vendorAutoload;
        }
    }
    /**
     * Generate PDF report
     */
    public function generatePDF(array $data, string $title, array $headers, string $filename = null): string
    {
        // Try to use TCPDF - will throw error if not available
        try {
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        } catch (\Error $e) {
            throw new \Exception('TCPDF library is not installed. Please run: composer require tecnickcom/tcpdf. Error: ' . $e->getMessage());
        }
        
        // Set document information
        $pdf->SetCreator('ChakaNoks SCMS');
        $pdf->SetAuthor('ChakaNoks SCMS');
        $pdf->SetTitle($title);
        $pdf->SetSubject($title);
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, 'Generated: ' . date('F d, Y h:i A'), 0, 1, 'C');
        $pdf->Ln(10);
        
        // Table header
        $pdf->SetFont('helvetica', 'B', 10);
        $colWidths = $this->calculateColumnWidths($headers, count($data));
        
        // Header row
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        foreach ($headers as $index => $header) {
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell($colWidths[$index], 7, $header, 1, 0, 'L', true);
        }
        $pdf->Ln();
        
        // Data rows
        $pdf->SetFont('helvetica', '', 9);
        foreach ($data as $row) {
            $rowArray = is_array($row) ? array_values($row) : [];
            foreach ($headers as $index => $header) {
                $value = isset($rowArray[$index]) ? $rowArray[$index] : '';
                // Truncate long values
                if (strlen($value) > 30) {
                    $value = substr($value, 0, 27) . '...';
                }
                $pdf->Cell($colWidths[$index], 6, $value, 1, 0, 'L');
            }
            $pdf->Ln();
        }
        
        // Output PDF as string for download
        return $pdf->Output('', 'S'); // Return as string
    }
    
    /**
     * Generate Excel report
     */
    public function generateExcel(array $data, string $title, array $headers, string $filename = null): string
    {
        // Try to use PhpSpreadsheet - will throw error if not available
        try {
            $spreadsheet = new Spreadsheet();
        } catch (\Error $e) {
            throw new \Exception('PhpSpreadsheet library is not installed. Please run: composer require phpoffice/phpspreadsheet. Error: ' . $e->getMessage());
        }
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:' . $this->getColumnLetter(count($headers)) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set generated date
        $sheet->setCellValue('A2', 'Generated: ' . date('F d, Y h:i A'));
        $sheet->mergeCells('A2:' . $this->getColumnLetter(count($headers)) . '2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Header row
        $row = 4;
        $col = 1;
        foreach ($headers as $header) {
            $cell = $this->getColumnLetter($col) . $row;
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getStyle($cell)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }
        
        // Data rows
        $row = 5;
        foreach ($data as $dataRow) {
            $rowArray = is_array($dataRow) ? array_values($dataRow) : [];
            $col = 1;
            foreach ($headers as $index => $header) {
                $cell = $this->getColumnLetter($col) . $row;
                $value = isset($rowArray[$index]) ? $rowArray[$index] : '';
                $sheet->setCellValue($cell, $value);
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimension($this->getColumnLetter($col))->setAutoSize(true);
        }
        
        // Add borders
        $sheet->getStyle('A4:' . $this->getColumnLetter(count($headers)) . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Save to file or return
        if ($filename) {
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);
            return $filename;
        }
        
        // Return as download
        $tempFile = WRITEPATH . 'temp/' . uniqid('excel_') . '.xlsx';
        if (!is_dir(WRITEPATH . 'temp')) {
            mkdir(WRITEPATH . 'temp', 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        
        return $tempFile;
    }
    
    /**
     * Generate CSV report
     */
    public function generateCSV(array $data, array $headers, string $filename = null): string
    {
        $csv = '';
        
        // Headers
        $csv .= '"' . implode('","', $headers) . '"' . "\n";
        
        // Data rows
        foreach ($data as $row) {
            $rowArray = is_array($row) ? array_values($row) : [];
            $csvRow = [];
            foreach ($headers as $index => $header) {
                $value = isset($rowArray[$index]) ? $rowArray[$index] : '';
                $csvRow[] = '"' . str_replace('"', '""', $value) . '"';
            }
            $csv .= implode(',', $csvRow) . "\n";
        }
        
        if ($filename) {
            file_put_contents($filename, $csv);
            return $filename;
        }
        
        return $csv;
    }
    
    /**
     * Calculate column widths for PDF
     */
    private function calculateColumnWidths(array $headers, int $dataCount): array
    {
        $pageWidth = 180; // A4 width minus margins
        $colCount = count($headers);
        $baseWidth = $pageWidth / $colCount;
        
        return array_fill(0, $colCount, $baseWidth);
    }
    
    /**
     * Get Excel column letter from number
     */
    private function getColumnLetter(int $col): string
    {
        $letter = '';
        while ($col > 0) {
            $col--;
            $letter = chr(65 + ($col % 26)) . $letter;
            $col = intval($col / 26);
        }
        return $letter;
    }
    
    /**
     * Send report via email
     */
    public function sendReportEmail(string $to, string $subject, string $body, string $attachmentPath = null, string $attachmentName = null): bool
    {
        // Use EmailService for consistent email handling
        $emailService = new \App\Libraries\EmailService();
        return $emailService->send($to, $subject, $body, $attachmentPath, $attachmentName);
    }
}

