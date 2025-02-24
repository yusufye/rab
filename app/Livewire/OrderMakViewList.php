<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class OrderMakViewList extends Component
{
    public $orderId;
    public $orderMaks = [];

    protected $listeners = ['refreshOrderViewMak' => 'handleRefreshOrderViewMak'];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrderViewMaks();
    }
    
    public function loadOrderViewMaks()
    {

        $this->orderMaks = []; 
        
        $order = Order::with([
            'orderMak' => function ($query) {
                $query->orderBy('is_split', 'asc')->orderBy('id', 'asc');
            },
            'orderMak.mak',
            'orderMak.division',
            'orderMak.orderTitle.orderItem.orderChecklist'
        ])->find($this->orderId);

        $this->orderMaks = $order ? $order->orderMak : [];

    }
    
    
    public function render()
    {
        return view('livewire.order-mak-view-list', [
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
