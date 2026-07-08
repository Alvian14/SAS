<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceHistory;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AttendanceHistoryController extends Controller
{
    public function absensiMapel($classId, Request $request)
    {
        $kelas = Classes::findOrFail($classId);

        // Ambil semua siswa di kelas
        $allStudents = Student::where('id_class', $classId)->get();

        // Ambil daftar mapel dari jadwal kelas
        $mapelList = Schedule::with('subject')
            ->where('id_class', $classId)
            ->distinct('id_subject')
            ->get()
            ->map(function($schedule) {
                return $schedule->subject;
            })
            ->unique('id')
            ->sortBy('name')
            ->values();

        // Default ke hari ini jika tidak ada filter
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $subjectId = $request->get('subject_id');

        $absensi = collect();
        $belumAbsen = $allStudents;

        // Jika ada subject filter, ambil data berdasarkan tanggal + day_of_week
        if ($subjectId) {
            // Parse tanggal untuk ambil day of week
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggal);

            // Map hari ke format Indonesia
            $dayMap = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];

            $dayName = $dayMap[$date->dayOfWeek] ?? '';

            // Get schedule IDs untuk subject dan day ini
            $scheduleIds = Schedule::where('id_class', $classId)
                ->where('id_subject', $subjectId)
                ->where('day_of_week', $dayName)
                ->pluck('id')
                ->toArray();

            // Get attendance history untuk tanggal dan schedules spesifik
            if (!empty($scheduleIds)) {
                $absensi = AttendanceHistory::with(['student', 'class'])
                    ->whereIn('id_schedule', $scheduleIds)
                    ->where(function($q) use ($tanggal) {
                        $q->whereDate('created_at', $tanggal)
                          ->orWhereDate('attendance_date', $tanggal);
                    })
                    ->get();
            }

            // Ambil siswa yang sudah absen untuk tanggal + day ini
            $sudahAbsenIds = $absensi->pluck('id_student')->unique();

            // Siswa yang belum absen untuk tanggal + day ini
            $belumAbsen = $allStudents->whereNotIn('id', $sudahAbsenIds)->values();
        }

        return view('pages.absensi.absensi_mapel', [
            'absensi' => $absensi,
            'kelas'   => $kelas,
            'belumAbsen' => $belumAbsen,
            'mapelList' => $mapelList,
            'allStudents' => $allStudents,
            'selectedTanggal' => $tanggal,
        ]);
    }

    public function getAbsensiByMapel($classId, $subjectId, Request $request)
    {
        try {
            // Get schedules untuk subject ini di kelas ini
            $scheduleIds = Schedule::where('id_class', $classId)
                ->where('id_subject', $subjectId)
                ->pluck('id')
                ->toArray();

            // Build query untuk attendance history
            $query = AttendanceHistory::with(['student', 'class']);

            if (!empty($scheduleIds)) {
                $query->whereIn('id_schedule', $scheduleIds);
            }

            // Apply date filters jika ada
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');

            if ($bulan && $tahun) {
                $query->whereMonth('created_at', $bulan)
                      ->whereYear('created_at', $tahun);
            } elseif ($bulan) {
                $query->whereMonth('created_at', $bulan);
            } elseif ($tahun) {
                $query->whereYear('created_at', $tahun);
            }

            // Get attendance history
            $absensi = $query->get();

            // Get all students
            $allStudents = Student::where('id_class', $classId)->get();

            // Ambil id siswa yang sudah absen untuk mapel ini
            $sudahAbsenIds = $absensi->pluck('id_student')->unique();

            // Siswa yang belum absen untuk mapel ini
            $belumAbsen = $allStudents->whereNotIn('id', $sudahAbsenIds)->values();

            return response()->json([
                'success' => true,
                'absensi' => $absensi,
                'belumAbsen' => $belumAbsen,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getSchedules($classId, $subjectId)
    {
        try {
            // Get all schedules untuk subject ini di kelas ini
            $schedules = Schedule::where('id_class', $classId)
                ->where('id_subject', $subjectId)
                ->get()
                ->map(function($schedule) {
                    return [
                        'id' => $schedule->id,
                        'day' => $schedule->day_of_week ?? 'N/A',
                        'time' => ($schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '') . ' - ' .
                                  ($schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '')
                    ];
                });

            return response()->json([
                'success' => true,
                'schedules' => $schedules,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getAbsensiByMapelAndDate($classId, $subjectId, $tanggal)
    {
        try {
            // Parse tanggal
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggal);

            // Map hari ke format Indonesia
            $dayMap = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];

            $dayName = $dayMap[$date->dayOfWeek] ?? '';

            // Get schedules untuk subject ini yang sesuai dengan hari ini
            $scheduleIds = Schedule::where('id_class', $classId)
                ->where('id_subject', $subjectId)
                ->where('day_of_week', $dayName)
                ->pluck('id')
                ->toArray();

            // Get attendance history untuk tanggal spesifik dan schedules tersebut
            $absensi = collect();
            if (!empty($scheduleIds)) {
                $absensi = AttendanceHistory::with(['student', 'class'])
                    ->whereIn('id_schedule', $scheduleIds)
                    ->where(function($q) use ($tanggal) {
                        $q->whereDate('created_at', $tanggal)
                          ->orWhereDate('attendance_date', $tanggal);
                    })
                    ->get();
            }

            // Get all students
            $allStudents = Student::where('id_class', $classId)->get();

            // Siswa yang sudah absen untuk tanggal dan schedule ini
            $sudahAbsenIds = $absensi->pluck('id_student')->unique();

            // Siswa yang belum absen
            $belumAbsen = $allStudents->whereNotIn('id', $sudahAbsenIds)->values();

            return response()->json([
                'success' => true,
                'absensi' => $absensi,
                'belumAbsen' => $belumAbsen,
                'scheduleIds' => $scheduleIds,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function editStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string'
            ]);

            // Jika $id adalah 'null', create baru
            if ($id === 'null') {
                $request->validate([
                    'id_student' => 'required|integer',
                    'id_class' => 'required|integer',
                    'id_schedule' => 'required|integer',
                ]);

                // Create attendance record dengan id_schedule dari request
                $currentDate = now()->format('Y-m-d');
                AttendanceHistory::create([
                    'id_student' => $request->id_student,
                    'id_class' => $request->id_class,
                    'status' => $request->status,
                    'period_number' => 1,
                    'id_schedule' => $request->id_schedule,
                    'coordinate' => config('coordinate.coordinate', '-7.604032330848524, 112.10176449791652'),
                    'attendance_date' => $currentDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json(['success' => true, 'message' => 'Data absensi berhasil ditambahkan.']);
            } else {
                // Update existing
                $absensi = AttendanceHistory::findOrFail($id);
                $currentDate = now()->format('Y-m-d');
                $absensi->update([
                    'status' => $request->status,
                    'attendance_date' => $currentDate,
                    'updated_at' => now(),
                ]);

                return response()->json(['success' => true, 'message' => 'Status absensi berhasil diupdate.']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export absensi mapel ke Excel - Format Grid per Periode (seperti standar absensi)
     */
    public function exportExcel($classId, Request $request)
    {
        try {
            // Get kelas info
            $kelas = Classes::findOrFail($classId);
            $kelasName = $kelas->name ?? 'Kelas';

            // Get filter info
            $mapelId = $request->get('mapel');
            $bulan = $request->get('bulan') ?: date('m');
            $tahun = $request->get('tahun') ?: date('Y');

            // Get mapel/subject name
            $mapelName = 'Mapel';
            if ($mapelId) {
                $subject = Subject::find($mapelId);
                $mapelName = $subject ? $subject->name : 'Mapel';
            }

            // Get all students
            $students = Student::where('id_class', $classId)->orderBy('name', 'ASC')->get();
            if ($students->isEmpty()) {
                return response()->json(['error' => 'Tidak ada siswa di kelas'], 400);
            }

            // Get schedules untuk mapel ini
            $scheduleIds = Schedule::where('id_class', $classId)
                ->where('id_subject', $mapelId)
                ->pluck('id')
                ->toArray();

            // Generate filename
            $month_name = $this->getMonthName($bulan);
            $filename = 'Absensi_Mapel_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $mapelName) . '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $kelasName) . '.xlsx';

            // Create spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Absensi Mapel');

            // Row 1: Title (Yellow background)
            $title = 'ABSENSI ' . strtoupper($mapelName) . ' ' . strtoupper($kelasName);
            $sheet->setCellValue('A1', $title);
            $sheet->mergeCells('A1:AE1');
            $sheet->getRowDimension(1)->setRowHeight(25);
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFCC00']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Row 2: Period info
            $periodText = 'PERIODE ' . strtoupper($month_name) . ' ' . $tahun;
            $sheet->setCellValue('A2', $periodText);
            $sheet->mergeCells('A2:AE2');
            $sheet->getRowDimension(2)->setRowHeight(20);
            $sheet->getStyle('A2')->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFCC00']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Row 3: Empty
            $sheet->getRowDimension(3)->setRowHeight(10);

            // Row 4: Headers
            $sheet->setCellValue('A4', 'Nama');
            $sheet->setCellValue('B4', 'Jabatan');
            
            // Days columns (1-31)
            $daysInMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
            for ($day = 1; $day <= 31; $day++) {
                $colLetter = $this->getColumnLetter($day + 2); // +2 untuk offset dari kolom A (Nama) dan B (Jabatan)
                if ($day <= $daysInMonth) {
                    $sheet->setCellValue($colLetter . '4', $day);
                } else {
                    $sheet->setCellValue($colLetter . '4', '');
                }
            }
            
            // Summary columns
            $jumlahCol = $this->getColumnLetter(35); // Column after day 31
            $keteranganCol = $this->getColumnLetter(36);
            $sheet->setCellValue($jumlahCol . '4', 'Jumlah\nHari Kerja');
            $sheet->setCellValue($keteranganCol . '4', 'Keterangan');

            // Style headers (Row 4) - Yellow background
            $headerRange = 'A4:' . $keteranganCol . '4';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFCC00']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getRowDimension(4)->setRowHeight(30);

            // Get attendance data untuk bulan+tahun ini
            $attendanceData = [];
            if (!empty($scheduleIds)) {
                $attendance = AttendanceHistory::where(function($q) use ($bulan, $tahun) {
                    $q->where(function($qq) use ($bulan, $tahun) {
                        $qq->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun);
                    })->orWhere(function($qq) use ($bulan, $tahun) {
                        $qq->whereMonth('attendance_date', $bulan)->whereYear('attendance_date', $tahun);
                    });
                })->whereIn('id_schedule', $scheduleIds)->get();
                
                foreach ($attendance as $att) {
                    $date = $att->attendance_date ? \Carbon\Carbon::parse($att->attendance_date) : \Carbon\Carbon::parse($att->created_at);
                    $key = $att->id_student . '_' . $date->day;
                    $attendanceData[$key] = $att->status;
                }
            }

            // Add student rows
            $dataRow = 5;
            foreach ($students as $student) {
                $sheet->setCellValue('A' . $dataRow, $student->name);
                $sheet->setCellValue('B' . $dataRow, $student->classroom ? $student->classroom->name : '-');

                // Add attendance status for each day
                for ($day = 1; $day <= 31; $day++) {
                    $colLetter = $this->getColumnLetter($day + 2);
                    $key = $student->id . '_' . $day;
                    $status = isset($attendanceData[$key]) ? $attendanceData[$key] : '';
                    
                    // Convert status to letter code
                    $statusCode = '';
                    if ($status) {
                        switch($status) {
                            case 'hadir':
                                $statusCode = 'H';
                                break;
                            case 'izin':
                                $statusCode = 'I';
                                break;
                            case 'sakit':
                                $statusCode = 'S';
                                break;
                            case 'alpha':
                                $statusCode = 'A';
                                break;
                            default:
                                $statusCode = '';
                        }
                    }
                    
                    $sheet->setCellValue($colLetter . $dataRow, $statusCode);
                    $sheet->getStyle($colLetter . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Borders untuk semua kolom
                $dataRange = 'A' . $dataRow . ':' . $keteranganCol . $dataRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $dataRow++;
            }

            // Add legend
            $legendRow = $dataRow + 2;
            $sheet->setCellValue('A' . $legendRow, 'Keterangan:');
            $sheet->getStyle('A' . $legendRow)->applyFromArray(['font' => ['bold' => true, 'size' => 11]]);

            $legendRow++;
            $legends = [
                'H' => 'Hadir',
                'I' => 'Izin',
                'S' => 'Sakit',
                'A' => 'Alpha'
            ];

            foreach ($legends as $code => $meaning) {
                $sheet->setCellValue('A' . $legendRow, $code);
                $sheet->setCellValue('B' . $legendRow, $meaning);
                $sheet->getStyle('A' . $legendRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $legendRow++;
            }

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(15);
            
            // Day columns
            for ($day = 1; $day <= 31; $day++) {
                $colLetter = $this->getColumnLetter($day + 2);
                $sheet->getColumnDimension($colLetter)->setWidth(5);
            }
            
            $sheet->getColumnDimension($jumlahCol)->setWidth(12);
            $sheet->getColumnDimension($keteranganCol)->setWidth(15);

            // Save and download
            $writer = new Xlsx($spreadsheet);
            $tempPath = storage_path('app/temp/' . $filename);
            @mkdir(dirname($tempPath), 0755, true);
            $writer->save($tempPath);

            return response()->download($tempPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper: Get absensi mapel rows for export sesuai dengan filter
     * Export semua siswa di kelas dengan status absensi mereka (jika ada)
     */
    private function getAbsensiMapelRowsForExport($classId, $mapelId, $tanggal = null, $bulan = null, $tahun = null)
    {
        // Get kelas
        $kelas = Classes::findOrFail($classId);

        // Get semua siswa di kelas (PENTING: ambil SEMUA siswa)
        $students = Student::where('id_class', $classId)
            ->orderBy('name', 'ASC')
            ->get();

        if ($students->isEmpty()) {
            return [];
        }

        // Get schedules untuk mapel ini
        $scheduleIds = Schedule::where('id_class', $classId)
            ->where('id_subject', $mapelId)
            ->pluck('id')
            ->toArray();

        // Build query untuk attendance history - ambil SEMUA attendance untuk mapel ini
        $query = AttendanceHistory::with(['student', 'class']);

        // Filter by schedule if available
        if (!empty($scheduleIds)) {
            $query->whereIn('id_schedule', $scheduleIds);
        } else {
            // Jika tidak ada schedule, kembalikan semua siswa dengan keterangan "-"
            return $this->buildEmptyAttendanceRows($students);
        }

        // Apply date filters - PENTING: include semua attendance data sesuai filter
        if ($tanggal) {
            // Untuk tanggal spesifik
            $query->where(function($q) use ($tanggal) {
                $q->whereDate('created_at', $tanggal)
                  ->orWhereDate('attendance_date', $tanggal);
            });
        } elseif ($bulan && $tahun) {
            // Untuk bulan+tahun spesifik
            $query->where(function($q) use ($bulan, $tahun) {
                $q->where(function($qq) use ($bulan, $tahun) {
                    $qq->whereMonth('created_at', $bulan)
                       ->whereYear('created_at', $tahun);
                })->orWhere(function($qq) use ($bulan, $tahun) {
                    $qq->whereMonth('attendance_date', $bulan)
                       ->whereYear('attendance_date', $tahun);
                });
            });
        } elseif ($bulan) {
            // Untuk bulan spesifik
            $query->where(function($q) use ($bulan) {
                $q->whereMonth('created_at', $bulan)
                  ->orWhereMonth('attendance_date', $bulan);
            });
        } elseif ($tahun) {
            // Untuk tahun spesifik
            $query->where(function($q) use ($tahun) {
                $q->whereYear('created_at', $tahun)
                  ->orWhereYear('attendance_date', $tahun);
            });
        }

        // Get attendance records
        $absensi = $query->get();

        // Build rows untuk export - PENTING: include SEMUA students
        $rows = [];
        $no = 1;

        foreach ($students as $student) {
            // Cari attendance record untuk student ini
            // Jika ada multiple records, ambil yang paling recent
            $absensiItem = $absensi->where('id_student', $student->id)->sortByDesc('created_at')->first();

            $keterangan = '-';
            $attendanceDate = '-';
            $jamPertemuan = '-';

            if ($absensiItem) {
                // Tentukan keterangan berdasarkan status
                switch($absensiItem->status) {
                    case 'hadir':
                        $keterangan = 'Hadir';
                        break;
                    case 'izin':
                        $keterangan = 'Izin';
                        break;
                    case 'sakit':
                        $keterangan = 'Sakit';
                        break;
                    case 'alpha':
                        $keterangan = 'Alpha';
                        break;
                    case 'dispen':
                        $keterangan = 'Dispen';
                        break;
                    default:
                        $keterangan = ucfirst($absensiItem->status);
                }

                // Format tanggal dan jam dari attendance record
                $attendanceDate = $absensiItem->attendance_date
                    ? \Carbon\Carbon::parse($absensiItem->attendance_date)->format('d/m/Y')
                    : ($absensiItem->created_at ? $absensiItem->created_at->format('d/m/Y') : '-');

                $jamPertemuan = $absensiItem->created_at ? $absensiItem->created_at->format('H:i') : '-';
            }

            // Add row untuk student ini (terlepas ada attendance atau tidak)
            $rows[] = [
                'no' => $no,
                'student_name' => $student->name,
                'nisn' => $student->nisn ?? '-',
                'attendance_date' => $attendanceDate,
                'jam_pertemuan' => $jamPertemuan,
                'keterangan' => $keterangan,
            ];

            $no++;
        }

        return $rows;
    }

    /**
     * Helper: Build empty attendance rows jika tidak ada schedule
     */
    private function buildEmptyAttendanceRows($students)
    {
        $rows = [];
        $no = 1;

        foreach ($students as $student) {
            $rows[] = [
                'no' => $no,
                'student_name' => $student->name,
                'nisn' => $student->nisn ?? '-',
                'attendance_date' => '-',
                'jam_pertemuan' => '-',
                'keterangan' => '-',
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
