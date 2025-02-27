<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderItem;
use App\Models\PercentageSetting;

class OrderPercentageCalc extends Component
{
    public $orderId;
    public $totalItem = 0;
    public $profit = 0;
    public $getPercentage =[];

    protected $listeners = ['refreshOrderPercentage' => 'calculatePercentage'];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->calculatePercentage();
    }

    public function calculatePercentage()
    {
        // Total item (is_split = 0)
        $this->totalItem = OrderItem::whereHas('orderTitle.orderMak', function ($query) {
            $query->where('order_id', $this->orderId)
                  ->where('is_split', 0);
        })->sum('total_price');


        // Profit calculation
        $order = Order::find($this->orderId);
        $this->profit = $order ? ($order->price - $this->totalItem) : 0;

        $latestEffectiveDate = PercentageSetting::where('effective_date', '<=', $order->date_to)->max('effective_date');
        $this->getPercentage = PercentageSetting::where('effective_date', $latestEffectiveDate)->get();

    }
    
    public function render()
    {
        return view('livewire.order-percentage-calc');
    }
}