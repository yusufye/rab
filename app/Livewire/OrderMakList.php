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
        $this->orderMaks = Order::with([
            'orderMak.mak',
            'orderMak.division',
            'orderMak.orderTitle.orderItem'
        ])->find($this->orderId);
    }

    public function render()
    {
        return view('livewire.order-mak-list');
    }
}