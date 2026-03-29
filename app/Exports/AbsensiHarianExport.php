<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AbsensiHarianExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    /** @var \Illuminate\Support\Collection<int, mixed> */
    protected Collection $rows;

    protected string $kelasName;

    public function __construct(Collection $rows, string $kelasName = 'Semua Kelas')
    {
        $this->rows = $rows;
        $this->kelasName = $kelasName;
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Foto (URL)',
            'Nama Siswa',
            'NISN',
            'Kelas',
            'Status',
            'Waktu',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Hitung kolom/row sebelum dan sesudah insert row secara benar.
                $highestCol = $sheet->getHighestColumn();
                if (!$highestCol) {
                    $highestCol = 'G';
                }
                $sheet->insertNewRowBefore(1, 2);
                $highestRow = $sheet->getHighestRow();
                if ($highestRow < 3) {
                    $highestRow = 3;
                }

                $title = 'REKAP ABSENSI HARIAN - ' . $this->kelasName;
                $subtitle = 'Diekspor pada: ' . now()->timezone('Asia/Jakarta')->format('d M Y H:i:s') . ' WIB';

                $sheet->setCellValue('A1', $title);
                $sheet->setCellValue('A2', $subtitle);

                $sheet->mergeCells('A1:' . $highestCol . '1');
                $sheet->mergeCells('A2:' . $highestCol . '2');

                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // Title style
                $sheet->getStyle('A1:' . $highestCol . '1')->applyFromArray([
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

                // Subtitle style
                $sheet->getStyle('A2:' . $highestCol . '2')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => '6B7280'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Headings row (now at row 3)
                $headerRange = 'A3:' . $highestCol . '3';
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

                // Freeze top 3 rows (title + subtitle + header)
                $sheet->freezePane('A4');

                // Border for full table area (mulai dari header di row 3)
                $tableRange = 'A3:' . $highestCol . $highestRow;
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                // Zebra rows (data rows start at 4)
                for ($r = 4; $r <= $highestRow; $r++) {
                    if ((($r - 4) % 2) === 1) {
                        $sheet->getStyle('A' . $r . ':' . $highestCol . $r)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F4F7FF'],
                            ],
                        ]);
                    }
                }

                // Column specific alignment
                if ($highestRow >= 4) {
                    $sheet->getStyle('A4:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E4:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F4:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Set sensible widths (autosize still on, but explicit helps)
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(45);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(16);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(16);
                $sheet->getColumnDimension('G')->setWidth(20);
            },
        ];
    }
}
