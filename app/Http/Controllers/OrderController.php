<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Category;
use App\Models\Division;
use App\Models\Mak;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMak;
use App\Models\OrderTitle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function index(Request $request)  {       
        

        if($request->ajax()){
            $orders = Order::all();

            return datatables()
            ->of($orders)
            ->addColumn('price_formatted', function ($row) {
                return 'Rp ' . number_format($row->price, 0, ',', '.');
            })
            ->addColumn('split_price_formatted', function ($row) {
                return 'Rp ' . number_format($row->split_price, 0, ',', '.');
            })
            ->addColumn('profit_formatted', function ($row) {
                return 'Rp ' . number_format($row->profit, 0, ',', '.');
            })
            ->addColumn('actions', function ($row) {
                $editUrl = url('order/' . $row->id . '/edit');
                $viewUrl = url('order/' . $row->id);
                $reviseUrl = url('order/' . $row->id . '/revise');
            
                return '
                    <a href="'.$editUrl.'" class="btn btn-sm btn-success">Edit</a>
                    <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a>
                    <a href="'.$reviseUrl.'" class="btn btn-sm btn-warning">Revise</a>
                ';
            })
            ->rawColumns(['price_formatted', 'split_price_formatted', 'profit_formatted','actions'])
            ->toJson();
        }


        return view('content.order.index');
        
    }

    public function create()  {       
        
        $divisions = Division::all();
        $categorys = Category::all();

        return view('content.order.add',compact('divisions','categorys'));
        
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'job_number' => 'required|max:50|unique:orders,job_number',
            'title' => 'required|max:100',
            'group' => 'required|max:100',
            'customer' => 'required|max:100',
            'study_lab' => 'required|max:100',
            'date_range' => 'required',
            'category_id' => 'required',
        ], [
            'job_number.unique' => 'Job Number sudah tersedia',
        ]);
        
        try{
             DB::beginTransaction();
             $dates = explode(' to ', $request->date_range);
           

             $dateFrom = \Carbon\Carbon::createFromFormat('d-M-Y', trim($dates[0]))->format('Y-m-d');
             $dateTo = isset($dates[1]) ? \Carbon\Carbon::createFromFormat('d-M-Y', trim($dates[1]))->format('Y-m-d') : $dateFrom;             

            $price = Helpers::parseCurrency($request->price);
            $splitPrice = Helpers::parseCurrency($request->split_price);


             Order::create([
                'job_number' => $request->job_number,
                'title' => $request->title,
                'category_id' => $request->category_id,
                'group' => $request->group,
                'customer' => $request->customer,
                'study_lab' => $request->study_lab,
                'date_from' => $dateFrom,
                'date_to' =>  $dateTo,
                'status' =>  'DRAFT',
                'price' =>  $price,
                'split_price' =>  $splitPrice,
                'split_to' =>  $request->division,
                'profit' =>   $price - $splitPrice,
             ]);
        
            DB::commit();
            $message = ['success' => 'Order berhasil di simpan'];
            return redirect('/order')->with($message);        
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'Order gagal di simpan'];
            return redirect('/order')->with($message);
        }

    }

    public function edit(Order $order){

        $divisions = Division::all();
        $categorys = Category::all();
        $maks = Mak::all();

        $divisions_id = isset($order->split_to) && is_array($order->split_to)
        ? Division::whereIn('id', $order->split_to)->pluck('id')->toArray()
        : [];

        $divisions_by_order_header = $order->split_to ? Division::whereIn('id', $order->split_to)->get():[];

        $order_mak = OrderMak::with(['mak','orderTitle','orderTitle.orderItem'])->where('order_id',$order->id)->get();

        return view('content.order.edit',compact('order','divisions','categorys','divisions_id','maks','divisions_by_order_header','order_mak'));
    }

    public function update(Request $request,Order $order)
    {
        $validated = $request->validate([
            'job_number' => 'required|max:50|unique:orders,job_number,'.$order->id,
            'title' => 'required|max:100',
            'group' => 'required|max:100',
            'customer' => 'required|max:100',
            'study_lab' => 'required|max:100',
            'date_range' => 'required',
            'category_id' => 'required',
        ], [
            'job_number.unique' => 'Job Number sudah tersedia',
        ]);
        
        try{
             DB::beginTransaction();
             $dates = explode(' to ', $request->date_range);
            //  dd($request->date_range);

             $dateFrom = \Carbon\Carbon::createFromFormat('d-M-Y', trim($dates[0]))->format('Y-m-d');
             $dateTo = isset($dates[1]) ? \Carbon\Carbon::createFromFormat('d-M-Y', trim($dates[1]))->format('Y-m-d') : $dateFrom;             

            $price = Helpers::parseCurrency($request->price);
            $splitPrice = Helpers::parseCurrency($request->split_price);


            $order->update([
                'job_number' => $request->job_number,
                'title' => $request->title,
                'category_id' => $request->category_id,
                'group' => $request->group,
                'customer' => $request->customer,
                'study_lab' => $request->study_lab,
                'date_from' => $dateFrom,
                'date_to' =>  $dateTo,
                'price' =>  $price,
                'split_price' =>  $splitPrice,
                'split_to' =>  $request->division,
                'profit' =>   $price - $splitPrice,
             ]);
        
            DB::commit();
            $message = ['success' => 'Order berhasil di simpan'];
            return redirect('/order')->with($message);        
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'Order gagal di simpan'];
            return redirect('/order')->with($message);
        }

    }
    
    public function storeMak(Request $request)
    {       
        $validated = $request->validate([
            'mak' => 'required',
        ]);

        
        try{
             DB::beginTransaction();

            if($request->type_form){
                $orders = OrderMak::create([
                    'order_id' => $request->order_id,
                    'mak_id' => $request->mak,
                    'is_split' => $request->is_split ? 1 : 0,
                    'split_to' => $request->split_to??null,
                 ]);                
            }else{
                $order_mak = OrderMak::find($request->order_mak_id);
                $orders = $order_mak->update([
                    'mak_id' => $request->mak,
                    'is_split' => $request->is_split,
                    'split_to' => $request->split_to,
                 ]);                
            }

        
            DB::commit();
            return response()->json(['success'=>true,'msg'=> 'Order Mak berhasil di simpan','data'=>$orders],200);      
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg'=> 'Order Mak gagal di simpan','data'=>[]],500);
        }

    }

    public function storeTitle(Request $request)
    {       
        $validated = $request->validate([
            'order_title' => 'required',
        ]);
        
        try{
             DB::beginTransaction();

            if($request->type_form){
                $order_title = OrderTitle::create([
                    'order_mak_id' => $request->order_mak_id,
                    'title' => $request->order_title,
                 ]);                
            }else{
                $order_title = OrderTitle::find($request->order_title_id);
                $order_title->update([
                    'title' => $request->order_title,
                ]);
            }

        
            DB::commit();
            return response()->json(['success'=>true,'msg'=> 'Order Title berhasil di simpan','data'=>$order_title],200);      
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg'=> 'Order Title gagal di simpan','data'=>[]],500);
        }

    }

    public function storeItem(Request $request)
    {       
        $validated = $request->validate([
            'order_item' => 'required',
        ]);
        
        try{
             DB::beginTransaction();

             $price_unit = Helpers::parseCurrency($request->price_unit);
             $total_price = Helpers::parseCurrency($request->total_price);

             Log::info([$price_unit,$total_price]);

            if($request->type_form){
                $order_item = OrderItem::create([
                    'order_title_id' => $request->order_title_id,
                    'item' => $request->order_item,
                    'qty_1' => $request->order_item_qty_1,
                    'unit_1' => $request->order_item_unit_1,
                    'qty_2' => $request->order_item_qty_2,
                    'unit_2' => $request->order_item_unit_2,
                    'qty_3' => $request->order_item_qty_3,
                    'unit_3' => $request->order_item_unit_3,
                    'qty_total' => $request->qty_total,
                    'qty_unit' => $request->qty_unit,
                    'price_unit' => $price_unit,
                    'total_price' => $total_price,
                 ]);                
            }else{
                $order_item = OrderItem::find($request->order_item_id);
                $order_item->update([
                    'item' => $request->order_item,
                    'qty_1' => $request->order_item_qty_1,
                    'unit_1' => $request->order_item_unit_1,
                    'qty_2' => $request->order_item_qty_2,
                    'unit_2' => $request->order_item_unit_2,
                    'qty_3' => $request->order_item_qty_3,
                    'unit_3' => $request->order_item_unit_3,
                    'qty_total' => $request->qty_total,
                    'qty_unit' => $request->qty_unit,
                    'price_unit' => $price_unit,
                    'total_price' => $total_price,
                ]);
            }

        
            DB::commit();
            return response()->json(['success'=>true,'msg'=> 'Order Item berhasil di simpan','data'=>$order_item],200);      
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg'=> 'Order Item gagal di simpan','data'=>[]],500);
        }

    }

    public function deleteMak(Request $request)
    {
        $id = $request->id;

        try {
            DB::beginTransaction();

            $order = OrderMak::findOrFail($id); 

            // Hapus semua OrderItem terkait melalui OrderTitle
            foreach ($order->orderTitle as $title) {
                $title->orderItem()->delete();
            }
    
            // Hapus semua OrderTitle terkait
            $order->orderTitle()->delete();

            // Hapus entitas utama
            $order->delete();

            DB::commit();

            return response()->json(['success'=>true,'msg' => 'Order Mak berhasil dihapus'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg' => 'Terjadi kesalahan saat menghapus data', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function deleteTitle(Request $request)
    {
        $id = $request->id;

        try {
            DB::beginTransaction();

            $orderTitle = OrderTitle::findOrFail($id); 
    
            // Hapus semua OrderTitle terkait
            $orderTitle->orderItem()->delete();

            // Hapus entitas utama
            $orderTitle->delete();

            DB::commit();

            return response()->json(['success'=>true,'msg' => 'Order Title berhasil dihapus'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg' => 'Terjadi kesalahan saat menghapus data', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteItem(Request $request)
    {
        $id = $request->id;

        try {
            DB::beginTransaction();

            $orderItem = OrderItem::findOrFail($id); 

            // Hapus entitas utama
            $orderItem->delete();

            DB::commit();

            return response()->json(['success'=>true,'msg' => 'Order Item berhasil dihapus'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg' => 'Terjadi kesalahan saat menghapus data', 'error' => $e->getMessage()], 500);
        }
    }

    public function view(Order $order){

        $divisions = Division::all();
        $categorys = Category::all();
        $maks = Mak::all();

        $divisions_id = isset($order->split_to) && is_array($order->split_to)
        ? Division::whereIn('id', $order->split_to)->pluck('id')->toArray()
        : [];

        $divisions_by_order_header = $order->split_to ? Division::whereIn('id', $order->split_to)->get():[];

        $order_mak = OrderMak::with(['mak','orderTitle','orderTitle.orderItem'])->where('order_id',$order->id)->get();

        return view('content.order.view',compact('order','divisions','categorys','divisions_id','maks','divisions_by_order_header','order_mak'));
    }

}
