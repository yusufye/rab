<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class OrdersPrintExcel implements FromView
{
    protected $orders;

    public function __construct($orders,$mak)
    {
        $this->orders = $orders;
        $this->orderMaks = $mak;
    }

    public function view(): View
    {
        return view('content.order.order_printout_excel', [
            'order' => $this->orders,
            'orderMaks' => $this->orderMaks
        ]);
    }
}