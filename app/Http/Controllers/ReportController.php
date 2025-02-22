<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(){
        $divisions = Division::all();
        $order = Order::groupBy('job_number')->pluck('job_number');

        return view('content.report.order',compact('divisions','order'));

    }

    public function show(Request $request){
        // $orders=Order::with(['orderMak.orderTitle.orderItem'])->whereIn('job_number',$request->order)->get();
        $orders = Order::with([
            'orderMak.mak',
            'orderMak.orderTitle.orderItem'
        ])
        ->whereIn('job_number', $request->order)
        ->orderBy('job_number') // Urutkan berdasarkan job_number
        ->orderBy('rev') // Urutkan berdasarkan revisi
        ->get()
        ->groupBy('job_number');
    
    // Ambil semua revisi unik
    $revisions = $orders->flatten()->pluck('rev')->unique()->sort()->values();
    

        // dd($order_header);
        return view('content.report.order_show',compact('orders','revisions'));

    }
}