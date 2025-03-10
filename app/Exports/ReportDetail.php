<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ReportDetail implements FromView,WithEvents
{
    protected $orders;
    protected $divisions;
    protected $maks;

    public function __construct($orders, $maks, $divisions, $categorys)
    {
        $this->orders    = $orders;
        $this->divisions = $divisions;
        $this->maks      = $maks;
        $this->categorys = $categorys;
    }

    public function view(): View
    {
        $orders    = $this->orders ;
        $divisions = $this->divisions ;
        $maks      = $this->maks ;
        $categorys = $this->categorys ;
        return view('content.report.detail_excel', compact('orders','maks','divisions','categorys'));
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow(); // Ambil baris terakhir yang terisi
                $lastColumn = $sheet->getHighestColumn(); // Ambil kolom terakhir yang terisi
                
                // Terapkan border ke semua sel yang digunakan
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];

                foreach (range('A', 'AZ') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray($styleArray);
            },
        ];
    }

}