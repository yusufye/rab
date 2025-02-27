<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Division;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(){
        $divisions = Division::all();
        $order = Order::groupBy('job_number')->pluck('job_number');

        return view('content.report.order',compact('divisions','order'));

    }

    public function show(Request $request){
        $orders = Order::with([
            'orderMak' => function ($query) {
                $query->orderBy('is_split', 'asc')->orderBy('id', 'asc');
            },
            'orderMak.division', // Ambil data divisi
            'orderMak.orderTitle.orderItem'
        ])
        ->whereIn('job_number', $request->order)
        ->orderBy('job_number')
        ->orderBy('rev')
        ->get()
        ->groupBy('job_number');
        //    dd($orders);  
        $revisions = $orders->flatten()->pluck('rev')->unique()->sort()->values();

        if($request->action == 'view'){
            return view('content.report.order_show',compact('orders','revisions'));
        }
       
       if($request->action == 'download'){
            $safeJobNumber=[];
            foreach ($request->order as $job_number) {
                $safeJobNumber[]=str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $job_number);
            }
            $send_file_name=join('-',$safeJobNumber);
            $fileName       = $send_file_name;
            
            return Excel::download(new OrdersExport($orders, $revisions), "orders-{$fileName}.xlsx");
       }
    }
}