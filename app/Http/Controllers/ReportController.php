<?php

namespace App\Http\Controllers;

use App\Models\Mak;
use App\Models\Order;
use App\Models\Category;
use App\Models\Division;
use Illuminate\Http\Request;
use App\Exports\ReportDetail;
use App\Exports\ReportCompare;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function filter($type){
        if ($type=='compare') {
            $divisions = Division::all();
            $order = Order::when(auth()->user()->hasRole('admin'), function ($query) {
                $query->where('created_by',auth()->user()->id);
            })
            ->groupBy('job_number')->pluck('job_number');

            return view('content.report.compare_filter',compact('type','divisions','order'));
        }elseif ($type=='detail') {
            return view('content.report.detail_filter',compact('type'));
        }
    }

    public function show($type=null,Request $request){
        if ($type=='compare') {
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
            
            $revisions = $orders->flatten()->pluck('rev')->unique()->sort()->values();
    
            if($request->action == 'view'){
                return view('content.report.compare_preview',compact('orders','revisions'));
            }
           
            if($request->action == 'download'){
                $safeJobNumber=[];
                foreach ($request->order as $job_number) {
                    $safeJobNumber[]=str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $job_number);
                }
                $send_file_name=join('-',$safeJobNumber);
                $fileName       = $send_file_name;
                
                return Excel::download(new ReportCompare($orders, $revisions), "orders-{$fileName}.xlsx");
            }
        }elseif ($type=='detail') {
            $orders = Order::with([
                'orderMak' => function ($query) {
                    $query->orderBy('is_split', 'asc')->orderBy('id', 'asc');
                },
                'orderMak.division', // Ambil data divisi dari orderMak
                'createdBy.division', // Ambil user yang membuat order dan relasi divisinya
                'orderMak.orderTitle.orderItem'
            ])
            ->whereNotIn('status',['REVISED','CANCELLED'])
            ->when($request->start_date, function ($query) use ($request) {
                $query->whereDate('date_from', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->whereDate('date_to', '<=', $request->end_date);
            })
            ->when(auth()->user()->hasRole('admin'), function ($query) use ($request) {
                $query->where('created_by',auth()->user()->id);
            })
            ->orderBy('job_number')
            ->orderBy('rev')
            ->get();
            // dd($orders->toArray());

            $maks      = Mak::get();
            $divisions = Division::get();
            $categorys = Category::get()->toArray();

            if($request->action == 'view'){
                return view('content.report.detail_preview',compact('orders','maks','divisions','categorys'));
            }
           
            if($request->action == 'download'){
                    
                    return Excel::download(new ReportDetail($orders,$maks,$divisions,$categorys), "orders-detail.xlsx");
            }
        }
        
    }
}