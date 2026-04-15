<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceHistory;
use App\Models\Classes;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AttendanceHistoryController extends Controller
{
    public function absensiMapel($classId)
    {
        $kelas = Classes::findOrFail($classId);
        $absensi = AttendanceHistory::with(['student', 'class'])
            ->where('id_class', $classId)
            ->get();

        // Ambil semua siswa di kelas
        $allStudents = Student::where('id_class', $classId)->get();
        // Ambil id siswa yang sudah absen
        $sudahAbsenIds = $absensi->pluck('id_student')->unique();
        // Siswa yang belum absen
        $belumAbsen = $allStudents->whereNotIn('id', $sudahAbsenIds);

        return view('pages.absensi.absensi_mapel', [
            'absensi' => $absensi,
            'kelas'   => $kelas,
            'belumAbsen' => $belumAbsen,
        ]);
    }

    public function editStatus(Request $request, $id)
    {
        $absensi = AttendanceHistory::find($id);
        $siswa = Student::find($id);

        // Jika data absensi ditemukan, update status
        if ($absensi) {
            $request->validate([
                'status' => 'required|string'
            ]);
            $absensi->status = $request->status;
            $absensi->save();
            return response()->json(['success' => true, 'message' => 'Status absensi berhasil diupdate.']);
        }

        // Jika data absensi tidak ditemukan, cek apakah $id adalah id siswa yang belum absen
        if ($siswa) {
            // Buat data absensi baru untuk siswa ini
            $newAbsensi = new AttendanceHistory();
            $newAbsensi->id_student = $siswa->id;
            $newAbsensi->id_class = $siswa->id_class;
            $newAbsensi->status = $request->status;
            $newAbsensi->created_at = now();
            $newAbsensi->updated_at = now();
            $newAbsensi->save();
            return response()->json(['success' => true, 'message' => 'Status absensi berhasil ditambahkan.']);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
    }

    /**
     * Export absensi mapel ke Excel dengan filter bulan/tahun
     */
    public function exportExcel($classId, Request $request)
    {
        // Get kelas info
        $kelas = Classes::findOrFail($classId);
        $kelasName = $kelas->name ?? 'Kelas';

        // Ambil filter bulan dan tahun dari request
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Get data absensi mapel dengan filter
        $rows = $this->getAbsensiMapelRowsForExport($classId, $bulan, $tahun);

        // Generate filename
        $month_name = $this->getMonthName($bulan);
        $filename = 'Rekap_Absensi_Mapel_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $kelasName) . '_' . $month_name . '_' . $tahun;

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Absensi Mapel');

        // Insert 3 rows untuk info header
        $sheet->insertNewRowBefore(1, 3);

        // Row 1: Title
        $title = 'REKAP ABSENSI MAPEL - ' . strtoupper($kelasName);
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:F1');

        // Row 2: Bulan/Tahun info
        $subtitle = $month_name . ' ' . $tahun;
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:F2');

        // Row 3: Export timestamp
        $timestamp = 'Diekspor pada: ' . now()->timezone('Asia/Jakarta')->format('d M Y H:i:s') . ' WIB';
        $sheet->setCellValue('A3', $timestamp);
        $sheet->mergeCells('A3:F3');

        // Style title rows
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(16);

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '4B5563']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle('A3:F3')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Add headers at row 4
        $headers = ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Keterangan', 'Jam Pertemuan'];
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($this->getColumnLetter($col + 1) . '4', $header);
        }

        // Style headers (Row 4)
        $headerRange = 'A4:F4';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '365CF5']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        // Add data rows starting from row 5
        $dataStartRow = 5;
        foreach ($rows as $index => $row) {
            $currentRow = $dataStartRow + $index;
            $sheet->setCellValue('A' . $currentRow, $row['no']);
            $sheet->setCellValue('B' . $currentRow, $row['student_name']);
            $sheet->setCellValue('C' . $currentRow, $row['nisn']);
            $sheet->setCellValue('D' . $currentRow, $row['class_name']);
            $sheet->setCellValue('E' . $currentRow, $row['keterangan']);
            $sheet->setCellValue('F' . $currentRow, $row['jam_pertemuan']);

            // Zebra rows (alternate background color)
            if (($index % 2) === 1) {
                $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F4F7FF']],
                ]);
            }
        }

        // Get highest row
        $highestRow = $sheet->getHighestRow();

        // Apply borders to all data
        $tableRange = 'A4:F' . $highestRow;
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('A5:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F5:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Freeze top rows
        $sheet->freezePane('A5');

        // Save and download
        $writer = new Xlsx($spreadsheet);
        $tempPath = storage_path('app/temp/' . $filename . '.xlsx');
        @mkdir(dirname($tempPath), 0755, true);
        $writer->save($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    /**
     * Helper: Get absensi mapel rows for export
     */
    private function getAbsensiMapelRowsForExport($classId, $bulan, $tahun)
    {
        // Get kelas
        $kelas = Classes::findOrFail($classId);

        // Get semua siswa di kelas
        $students = Student::where('id_class', $classId)->get();

        // Get absensi mapel dengan filter bulan/tahun
        $query = AttendanceHistory::with(['student', 'class'])
            ->where('id_class', $classId);

        if ($bulan && $tahun) {
            $query->whereRaw('YEAR(created_at) = ?', [$tahun])
                  ->whereRaw('MONTH(created_at) = ?', [$bulan]);
        }

        $absensi = $query->get();

        // Build rows untuk export
        $rows = [];
        $no = 1;

        foreach ($students as $student) {
            $absensiItem = $absensi->where('id_student', $student->id)->first();

            $keterangan = '-';
            if ($absensiItem) {
                if ($absensiItem->status == 'hadir') {
                    $keterangan = 'Hadir';
                } elseif ($absensiItem->status == 'izin') {
                    $keterangan = 'Izin';
                } elseif ($absensiItem->status == 'sakit') {
                    $keterangan = 'Sakit';
                } elseif ($absensiItem->status == 'alpha') {
                    $keterangan = 'Alpha';
                } elseif ($absensiItem->status == 'dispen') {
                    $keterangan = 'Dispen';
                } else {
                    $keterangan = ucfirst($absensiItem->status);
                }
            }

            $rows[] = [
                'no' => $no,
                'student_name' => $student->name,
                'nisn' => $student->nisn ?? '-',
                'class_name' => $kelas->name,
                'keterangan' => $keterangan,
                'jam_pertemuan' => $absensiItem ? ($absensiItem->created_at->format('H:i') ?? '-') : '-',
            ];

            $no++;
        }

        return $rows;
    }

    /**
     * Helper: Convert column number to letter
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
     * Helper: Get month name in Indonesian
     */
    private function getMonthName($bulan)
    {
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $months[$bulan] ?? 'Bulan';
    }
}
