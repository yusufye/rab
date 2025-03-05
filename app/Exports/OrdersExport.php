<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class OrdersExport implements FromView
{
    protected $orders;
    protected $revisions;

    public function __construct($orders, $revisions)
    {
        $this->orders = $orders;
        $this->revisions = $revisions;
    }

    public function view(): View
    {
        return view('content.report.order_show', [
            'orders' => $this->orders,
            'revisions' => $this->revisions,
        ]);
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

                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray($styleArray);
            },
        ];
    }
}