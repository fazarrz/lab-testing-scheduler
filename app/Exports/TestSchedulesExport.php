<?php

namespace App\Exports;

use App\Models\TestSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Events\AfterSheet;

class TestSchedulesExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    public function collection()
    {
        return TestSchedule::select('test_name', 'start_time', 'end_time', 'image_path', 'status')->get(); // Menyertakan 'status'
    }

    public function headings(): array
    {
        return [
            'Nama Pengujian',
            'Waktu Mulai',
            'Waktu Selesai',
            'Gambar',
            'Status Uji', // Menambahkan judul untuk kolom status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFCCCCCC',
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $count = count($this->collection());
                $event->sheet->getStyle('A1:E' . ($count + 1))
                    ->getBorders()->getAllBorders()->applyFromArray([
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ]);

                // Mengatur lebar kolom
                $event->sheet->getColumnDimension('A')->setWidth(30); // Nama Pengujian
                $event->sheet->getColumnDimension('B')->setWidth(25); // Waktu Mulai
                $event->sheet->getColumnDimension('C')->setWidth(25); // Waktu Selesai
                $event->sheet->getColumnDimension('D')->setWidth(30); // Gambar
                $event->sheet->getColumnDimension('E')->setWidth(20); // Status Uji

                $event->sheet->getStyle('A2:E' . ($count + 1))
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $event->sheet->getStyle('B2:C' . ($count + 1))
                    ->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');

                // Mengatur batas untuk setiap kolom
                $event->sheet->getStyle('A1:A' . ($count + 1))
                    ->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $event->sheet->getStyle('E1:E' . ($count + 1))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
