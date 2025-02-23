<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use App\Models\Division;
use App\Models\OrderItem;

class OrderSummary extends Component
{
    public $orderId;
    public $totalItem = 0;
    public $totalSplitItems = [];
    public $profit = 0;

    protected $listeners = ['refreshOrderSummary' => 'calculateSummary'];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        // Total item (is_split = 0)
        $this->totalItem = OrderItem::whereHas('orderTitle.orderMak', function ($query) {
            $query->where('order_id', $this->orderId)
                  ->where('is_split', 0);
        })->sum('total_price');

        // Total split item (is_split = 1, grouped by split_to)
        $splitItems = OrderItem::whereHas('orderTitle.orderMak', function ($query) {
            $query->where('order_id', $this->orderId)
                  ->where('is_split', 1);
        })->join('order_titles', 'order_titles.id', '=', 'order_items.order_title_id')
          ->join('order_maks', 'order_maks.id', '=', 'order_titles.order_mak_id')
          ->selectRaw('order_maks.split_to, SUM(order_items.total_price) as total')
          ->groupBy('order_maks.split_to')
          ->get();

        $this->totalSplitItems = $splitItems->mapWithKeys(function ($item) {
            $division = Division::find($item->split_to);
            return [$division->division_name ?? 'Unknown' => $item->total];
        })->toArray();

        // Profit calculation
        $order = Order::find($this->orderId);
        $this->profit = $order ? ($order->price - $this->totalItem) : 0;
    }
    
    public function render()
    {
        return view('livewire.order-summary');
    }
}