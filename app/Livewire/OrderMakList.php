<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderMakList extends Component
{
    public $orderId;
    public $orderMaks = [];

    protected $listeners = ['refreshOrderMak' => 'loadOrderMaks'];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrderMaks();
    }

    public function loadOrderMaks()
    {
        $order = Order::with([
            'orderMak' => function ($query) {
                $query->orderBy('is_split', 'asc')->orderBy('id', 'asc');
            },
            'orderMak.mak',
            'orderMak.division',
            'orderMak.orderTitle.orderItem'
        ])->find($this->orderId);

        $this->orderMaks = $order ? $order->orderMak : [];
    }

    public function render()
    {
        return view('livewire.order-mak-list', [
            'orderMaks' => $this->orderMaks
        ]);
    }

    public function getTotalPriceForMak($orderMak)
    {
        return $orderMak->orderTitle->sum(function ($title) {
           return $title->orderItem->sum('total_price');
        });
    }
}