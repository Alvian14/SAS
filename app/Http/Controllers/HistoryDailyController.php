<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceHistoryDaily;
use App\Models\Classes;
use App\Models\Student;
use App\Traits\ExcelExportTrait;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HistoryDailyController extends Controller
{
    use ExcelExportTrait;
    public function absensiHarian($classId)
    {
        $kelas = Classes::findOrFail($classId);

        // Ambil semua siswa di kelas berdasarkan kolom id_class
        $students = Student::where('id_class', $classId)->get();

        // Ambil absensi harian yang sudah ada
        $absensi = AttendanceHistoryDaily::with(['student', 'class'])
            ->where('id_class', $classId)
            ->get();

        // Gabungkan data siswa dan absensi
        $result = [];
        foreach ($students as $student) {
            $absensiItem = $absensi->where('id_student', $student->id)->first();
            $result[] = (object)[
                'id' => $absensiItem ? $absensiItem->id : null,
                'student' => $student,
                'class' => $kelas,
                'status' => $absensiItem ? $absensiItem->status : 'in progress',
                'created_at' => $absensiItem ? $absensiItem->created_at : null,
                'picture' => $absensiItem ? $absensiItem->picture : null,
            ];
        }

        return view('pages.absensi.absensi_harian', [
            'absensi' => collect($result),
            'kelas' => $kelas
        ]);
    }

    public function editStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        // Jika $id adalah 'null', artinya create baru (belum ada di database)
        if ($id === 'null') {
            $request->validate([
                'id_student' => 'required|exists:students,id',
                'id_class' => 'required|exists:clases,id',
            ]);

            AttendanceHistoryDaily::create([
                'id_student' => $request->id_student,
                'id_class' => $request->id_class,
                'status' => $request->status,
                'picture' => $request->picture ?? '',
            ]);

            return response()->json(['success' => true, 'message' => 'Data absensi berhasil ditambahkan.']);
        } else {
            // Update existing
            $absensi = AttendanceHistoryDaily::findOrFail($id);
            $absensi->status = $request->status;
            $absensi->save();

            return response()->json(['success' => true, 'message' => 'Status absensi berhasil diupdate.']);
        }
    }

    public function exportExcel($classId, Request $request)
    {
        // Get kelas info
        $kelas = Classes::findOrFail($classId);
        $kelasName = $kelas->name ?? 'Kelas';

        // Get data absensi harian
        $rows = $this->getAbsensiHarianRowsForExport($classId, $request);

        // Generate filename dengan tanggal
        $tanggal = $request->get('tanggal');
        $suffixTanggal = $tanggal ? ('_' . preg_replace('/[^0-9\-]/', '', $tanggal)) : '';
        $filename = 'Rekap_Absensi_Harian_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $kelasName) . $suffixTanggal;

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Absensi Harian');

        // Insert 2 rows untuk title dan subtitle
        $sheet->insertNewRowBefore(1, 2);

        // Title and subtitle
        $title = 'REKAP ABSENSI HARIAN - ' . strtoupper($kelasName);
        $subtitle = 'Diekspor pada: ' . now()->timezone('Asia/Jakarta')->format('d M Y H:i:s') . ' WIB';

        $sheet->setCellValue('A1', $title);
        $sheet->setCellValue('A2', $subtitle);

        // Merge cells untuk title dan subtitle
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // Style title (Row 1)
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '1F2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style subtitle (Row 2)
        $sheet->getStyle('A2:G2')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '6B7280'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Add headers at row 3
        $headers = ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Status', 'Waktu'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($this->getColumnLetter($col + 1) . '3', $header);
        }

        // Style headers (Row 3)
        $headerRange = 'A3:F3';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '365CF5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // Add data rows starting from row 4
        $dataStartRow = 4;
        foreach ($rows as $index => $row) {
            $currentRow = $dataStartRow + $index;
            $sheet->setCellValue('A' . $currentRow, $row['no']);
            $sheet->setCellValue('B' . $currentRow, $row['student_name']);
            $sheet->setCellValue('C' . $currentRow, $row['nisn']);
            $sheet->setCellValue('D' . $currentRow, $row['class_name']);
            $sheet->setCellValue('E' . $currentRow, $row['status']);
            $sheet->setCellValue('F' . $currentRow, $row['waktu']);

            // Zebra rows (alternate background color)
            if (($index % 2) === 1) {
                $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4F7FF'],
                    ],
                ]);
            }
        }

        // Get highest row
        $highestRow = $sheet->getHighestRow();

        // Apply borders to all data
        $tableRange = 'A3:F' . $highestRow;
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('A4:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D4:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E4:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Freeze top 3 rows (title + subtitle + header)
        $sheet->freezePane('A4');

        // Save and download
        $writer = new Xlsx($spreadsheet);
        $tempPath = storage_path('app/temp/' . $filename . '.xlsx');
        @mkdir(dirname($tempPath), 0755, true);
        $writer->save($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
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
     * Helper method untuk prepare data export absensi harian
     */
    private function getAbsensiHarianRowsForExport($classId, Request $request)
    {
        // Get kelas
        $kelas = Classes::findOrFail($classId);

        // Get semua siswa di kelas
        $students = Student::where('id_class', $classId)->get();

        // Filter by tanggal jika ada
        $query = AttendanceHistoryDaily::with(['student', 'class'])
            ->where('id_class', $classId);

        if ($request->has('tanggal') && $request->get('tanggal')) {
            $tanggal = $request->get('tanggal');
            $query->whereDate('created_at', $tanggal);
        }

        $absensi = $query->get();

        // Build rows untuk export
        $rows = [];
        $no = 1;

        foreach ($students as $student) {
            $absensiItem = $absensi->where('id_student', $student->id)->first();

            $rows[] = [
                'no' => $no,
                'student_name' => $student->name,
                'nisn' => $student->nisn ?? '-',
                'class_name' => $kelas->name,
                'status' => $absensiItem ? ucfirst($absensiItem->status) : 'In Progress',
                'waktu' => $absensiItem ? $absensiItem->created_at->format('Y-m-d H:i:s') : '-',
            ];

            $no++;
        }

        return $rows;
    }
}
