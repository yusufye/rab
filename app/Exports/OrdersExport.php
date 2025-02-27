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
}