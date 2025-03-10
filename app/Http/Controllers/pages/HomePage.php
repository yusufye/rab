<?php

namespace App\Http\Controllers\pages;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomePage extends Controller
{
  public function index()
  {
    $order= Order::when(auth()->user()->hasRole('admin'), function ($query) {
        $query->where('created_by',auth()->user()->id);
    })
    ->get();

    $order_checked=Order::where('status', 'APPROVED')->where('approval_step', 3) ->whereHas('orderChecklist') ->count();

    
    return view('content.pages.pages-home',compact('order','order_checked'));
  }
}