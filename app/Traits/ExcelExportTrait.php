<?php

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

trait ExcelExportTrait
{
    /**
     * Export data ke file Excel dengan styling standar
     *
     * @param array $headers - Array key:label untuk headers ['name'=>'Nama', 'email'=>'Email']
     * @param array $data - Array data yang akan diekspor
     * @param string $filename - Nama file (tanpa .xlsx)
     * @param array $options - Option: ['sheetName', 'headerColor', 'alternateRowColor', 'title', 'subtitle']
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel($headers, $data, $filename = 'export', $options = [])
    {
        // Extract options
        $sheetName = $options['sheetName'] ?? 'Data';
        $headerColor = $options['headerColor'] ?? '365CF5';
        $alternateRowColor = $options['alternateRowColor'] ?? true;
        $title = $options['title'] ?? null;
        $subtitle = $options['subtitle'] ?? null;

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetName);

        $currentRow = 1;

        // Add title if provided
        if ($title) {
            $sheet->setCellValue('A' . $currentRow, $title);
            $sheet->mergeCells('A' . $currentRow . ':' . $this->getColumnLetter(count($headers)) . $currentRow);
            $sheet->getRowDimension($currentRow)->setRowHeight(24);
            $sheet->getStyle('A' . $currentRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $currentRow++;
        }

        // Add subtitle if provided
        if ($subtitle) {
            $sheet->setCellValue('A' . $currentRow, $subtitle);
            $sheet->mergeCells('A' . $currentRow . ':' . $this->getColumnLetter(count($headers)) . $currentRow);
            $sheet->getRowDimension($currentRow)->setRowHeight(18);
            $sheet->getStyle('A' . $currentRow)->applyFromArray([
                'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $currentRow++;
        }

        // Add headers
        $headerRow = $currentRow;
        foreach ($headers as $key => $header) {
            $sheet->setCellValue($this->getColumnLetter(count(array_slice($headers, 0, array_search($key, array_keys($headers)))) + 1) . $headerRow, $header);
        }

        // Better way to add headers
        foreach (array_values($headers) as $colIndex => $header) {
            $sheet->setCellValue($this->getColumnLetter($colIndex + 1) . $headerRow, $header);
        }

        // Style headers
        $headerRange = 'A' . $headerRow . ':' . $this->getColumnLetter(count($headers)) . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerColor]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(20);

        // Add data rows
        $dataStartRow = $headerRow + 1;
        foreach ($data as $index => $row) {
            $rowNum = $dataStartRow + $index;
            foreach (array_keys($headers) as $colIndex => $key) {
                $value = $this->getNestedValue($row, $key);
                $sheet->setCellValue($this->getColumnLetter($colIndex + 1) . $rowNum, $value);
            }

            // Alternate row color
            if ($alternateRowColor && (($index % 2) === 1)) {
                $sheet->getStyle('A' . $rowNum . ':' . $this->getColumnLetter(count($headers)) . $rowNum)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F4F7FF']],
                ]);
            }
        }

        // Get highest row
        $highestRow = $sheet->getHighestRow();

        // Apply borders
        $tableRange = 'A' . $headerRow . ':' . $this->getColumnLetter(count($headers)) . $highestRow;
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Auto-size columns
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimension($this->getColumnLetter($col))->setAutoSize(true);
        }

        // Freeze header
        if ($title && $subtitle) {
            $sheet->freezePane('A' . ($headerRow + 1));
        } elseif ($title || $subtitle) {
            $sheet->freezePane('A' . $headerRow);
        }

        return $this->downloadExcel($spreadsheet, $filename);
    }

    /**
     * Export dengan PhpSpreadsheet Advanced (untuk custom styling)
     */
    public function exportToExcelAdvanced($headers, $data, $filename = 'export', $options = [])
    {
        return $this->exportToExcel($headers, $data, $filename, $options);
    }

    /**
     * Convert column number to letter (1->A, 2->B, etc)
     */
    private function getColumnLetter($col)
    {
        $letter = '';
        while ($col > 0) {
            $col--;
            $letter = chr(65 + ($col % 26)) . $letter;
            $col = intdiv($col, 26);
        }
        return $letter;
    }

    /**
     * Get nested value from array or object (support dot notation)
     * Example: getNestedValue($item, 'user.name')
     */
    private function getNestedValue($data, $key)
    {
        // Simple key
        if (is_array($data) && isset($data[$key])) {
            return $data[$key];
        }

        if (is_object($data) && property_exists($data, $key)) {
            return $data->$key;
        }

        // Dot notation
        $keys = explode('.', $key);
        $value = $data;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } elseif (is_object($value) && property_exists($value, $k)) {
                $value = $value->$k;
            } else {
                return '';
            }
        }

        return $value;
    }

    /**
     * Download Excel file
     */
    private function downloadExcel($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);

        // Save to temp file
        $tempPath = storage_path('app/temp/' . $filename . '.xlsx');
        @mkdir(dirname($tempPath), 0755, true);
        $writer->save($tempPath);

        // Download and delete
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
