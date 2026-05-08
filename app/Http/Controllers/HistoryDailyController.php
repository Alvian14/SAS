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

        // Ambil semua siswa di kelas
        $students = Student::where('id_class', $classId)->get();

        // Ambil SEMUA absensi harian dari database, diurutkan by tanggal (terbaru dulu)
        $absensiFromDB = AttendanceHistoryDaily::with(['student', 'class'])
            ->where('id_class', $classId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Dapatkan semua hari UNIK dari records
        $uniqueDates = $absensiFromDB
            ->map(function($item) { return $item->created_at->format('Y-m-d'); })
            ->unique()
            ->values()
            ->sort()
            ->reverse() // Terbaru dulu
            ->values();

        // Build result: untuk SETIAP HARI, tampilkan SEMUA SISWA
        $result = [];
        foreach ($uniqueDates as $date) {
            foreach ($students as $student) {
                // Cari record di database untuk student + date ini
                $record = $absensiFromDB
                    ->where('id_student', $student->id)
                    ->filter(function($item) use ($date) {
                        return $item->created_at->format('Y-m-d') === $date;
                    })
                    ->first();

                if ($record) {
                    // Ada record di database
                    $result[] = (object)[
                        'id' => $record->id,
                        'student' => $record->student,
                        'class' => $record->class,
                        'status' => $record->status,
                        'created_at' => $record->created_at,
                        'picture' => $record->picture,
                    ];
                } else {
                    // Dummy record: siswa belum absen di hari ini
                    // Set created_at ke hari tersebut 00:00:00 untuk filter bekerja dengan baik
                    $dummyDateTime = \Carbon\Carbon::parse($date . ' 00:00:00');

                    $result[] = (object)[
                        'id' => null,
                        'student' => $student,
                        'class' => $kelas,
                        'status' => 'in progress',
                        'created_at' => $dummyDateTime,
                        'picture' => null,
                    ];
                }
            }
        }

        // Group data by tanggal (created_at)
        $groupedByDate = collect();
        foreach ($result as $item) {
            $dateKey = $item->created_at->format('Y-m-d');

            if (!$groupedByDate->has($dateKey)) {
                $groupedByDate->put($dateKey, []);
            }
            $items = $groupedByDate->get($dateKey);
            $items[] = $item;
            $groupedByDate->put($dateKey, $items);
        }

        // Urutkan by tanggal (terbaru dulu)
        $groupedArray = $groupedByDate->toArray();
        krsort($groupedArray);

        // Get today's date untuk default filter
        $today = now()->format('Y-m-d');

        return view('pages.absensi.absensi_harian', [
            'absensi' => collect($result),
            'groupedByDate' => $groupedArray,
            'today' => $today,
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

            try {
                AttendanceHistoryDaily::create([
                    'id_student' => $request->id_student,
                    'id_class' => $request->id_class,
                    'status' => $request->status,
                    'picture' => $request->picture ?? '',
                ]);

                return response()->json(['success' => true, 'message' => 'Data absensi berhasil ditambahkan.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
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

        // Generate filename dan tanggal suffix berdasarkan filter
        $tanggal = $request->get('tanggal');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        // Build filename suffix
        if ($tanggal) {
            $suffixTanggal = '_' . preg_replace('/[^0-9\-]/', '', $tanggal);
        } else if ($bulan && $tahun) {
            $suffixTanggal = '_' . $tahun . '-' . $bulan;
        } else if ($bulan) {
            $suffixTanggal = '_Bulan' . $bulan;
        } else if ($tahun) {
            $suffixTanggal = '_' . $tahun;
        } else {
            $suffixTanggal = '_' . now()->format('Y-m-d');
        }

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

        // Determine filter parameters
        $tanggal = $request->get('tanggal');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        // Build query untuk fetch attendance records
        $query = AttendanceHistoryDaily::with(['student', 'class'])
            ->where('id_class', $classId);

        // Jika tanggal diset, filter by exact date
        if ($tanggal) {
            $query->whereDate('created_at', $tanggal);
        }
        // Jika bulan dan tahun keduanya diset, filter by month AND year
        else if ($bulan && $tahun) {
            $query->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun);
        }
        // Jika hanya bulan diset, filter by month
        else if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }
        // Jika hanya tahun diset, filter by year
        else if ($tahun) {
            $query->whereYear('created_at', $tahun);
        }
        // Jika tidak ada filter, default ke hari ini
        else {
            $today = now()->format('Y-m-d');
            $query->whereDate('created_at', $today);
        }

        $absensi = $query->get();

        // Get unique dates dari filtered data
        $uniqueDates = $absensi
            ->map(function($item) { return $item->created_at->format('Y-m-d'); })
            ->unique()
            ->values()
            ->sort()
            ->reverse()
            ->values();

        // Build rows untuk export: untuk setiap hari, untuk setiap siswa
        $rows = [];
        $no = 1;

        foreach ($uniqueDates as $date) {
            foreach ($students as $student) {
                // Cari record untuk student + date ini
                $record = $absensi
                    ->where('id_student', $student->id)
                    ->filter(function($item) use ($date) {
                        return $item->created_at->format('Y-m-d') === $date;
                    })
                    ->first();

                if ($record) {
                    // Ada record di database
                    $rows[] = [
                        'no' => $no,
                        'student_name' => $record->student->name ?? '-',
                        'nisn' => $record->student->nisn ?? '-',
                        'class_name' => $record->class->name ?? '-',
                        'status' => ucfirst($record->status),
                        'waktu' => $record->created_at->format('d M Y H:i:s'),
                    ];
                } else {
                    // Dummy record: siswa belum absen di hari ini
                    $rows[] = [
                        'no' => $no,
                        'student_name' => $student->name,
                        'nisn' => $student->nisn ?? '-',
                        'class_name' => $kelas->name,
                        'status' => 'In Progress',
                        'waktu' => '-',
                    ];
                }

                $no++;
            }
        }

        return $rows;
    }
}
