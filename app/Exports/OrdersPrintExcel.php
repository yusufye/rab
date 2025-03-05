<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class OrdersPrintExcel implements FromView, WithEvents
{
    protected $orders;
    protected $orderMaks;
    protected $sumItem;
    protected $profit;
    protected $getPercentage;
    protected $sumPerDiv;

    public function __construct($orders,$mak,$sumItem,$profit,$getPercentage,$sumPerDiv)
    {
        $this->orders        = $orders;
        $this->orderMaks     = $mak;
        $this->sumItem       = $sumItem;
        $this->profit        = $profit;
        $this->getPercentage = $getPercentage;
        $this->sumPerDiv     = $sumPerDiv;
    }

    public function view(): View
    {
        return view('content.order.order_printout_excel', [
            'order'         => $this->orders,
            'orderMaks'     => $this->orderMaks,
            'sumItem'       => $this->sumItem,
            'profit'        => $this->profit,
            'getPercentage' => $this->getPercentage,
            'sumPerDiv'     => $this->sumPerDiv
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastRow = $sheet->getHighestRow(); // Baris terakhir
                $lastColumn = $sheet->getHighestColumn(); // Kolom terakhir

                // Debugging untuk cek apakah event berjalan
                // dd($lastColumn, $lastRow);

                // Konversi kolom ke indeks numerik
                $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);

                // Gaya border
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

                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Terapkan border ke seluruh area
                $tbl_percentage = "A8:D15";
                $tbl_percentage_wrap = "B9:C14";
                $tbl_detail = "A17:{$lastColumn}{$lastRow}";
                
                $sheet->getStyle($tbl_percentage)->applyFromArray($styleArray);
                $sheet->getStyle($tbl_detail)->applyFromArray($styleArray);
                $sheet->getStyle($tbl_percentage_wrap)->getAlignment()->setWrapText(true);
            },
        ];
    }
}