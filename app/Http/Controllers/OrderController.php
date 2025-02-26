<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Mak;
use App\Models\Order;
use App\Helpers\Helpers;
use App\Models\Category;
use App\Models\Division;
use App\Models\OrderMak;
use App\Models\OrderItem;
use App\Models\OrderTitle;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\QrCodeHelper;
use App\Models\OrderChecklist;

class OrderController extends Controller
{

    public function index(Request $request)  {    
        
        $user = auth()->user();

        if($request->ajax()){            

            $orders = Order::query();

            if (auth()->user()->hasAnyRole(['reviewer','head_reviewer'])) {
                $orders->where('status','TO REVIEW');                     
            }

            if ($user->hasRole('approval_satu')) {
                $orders->where('status','REVIEWED');                    
            }

            if ($user->hasRole('approval_dua')) {
                $orders->where('status','APPROVED');                     
                $orders->where('approval_step',1);                     
            }

            if ($user->hasRole('approval_tiga')) {
                $orders->where('status','APPROVED');                     
                $orders->where('approval_step',2);                     
            }     

            if ($user->hasRole('checker')) {
                $orders->where('status','APPROVED');                     
                $orders->where('approval_step',3);                     
            }         

            $orders->get();

            return datatables()
            ->of($orders)
            ->addColumn('job_number', function ($row) {
                return $row->job_number.'<span class="mx-2 badge pill bg-label-dark py-2 px-3 fw-semibold text-center">R-'.$row->rev.'</span>';
            })->escapeColumns([])
            ->addColumn('status', function ($row) {
                $badgeClass = match ($row->status) {
                    'DRAFT'     => 'bg-secondary',
                    'TO REVIEW' => 'bg-warning',
                    'REVIEWED'  => 'bg-label-warning',
                    'RELEASED'  => 'bg-info',
                    'APPROVED'  => 'bg-primary',
                    'REVISED'   => 'bg-dark',
                    default     => 'bg-secondary',
                };
                
                return '<span class="badge rounded-pill '.$badgeClass.' py-2 px-3 fw-semibold text-center">'.$row->status.($row->status=='APPROVED'?' '.$row->approval_step:'').'</span>';
            })->escapeColumns([])
            ->addColumn('price_formatted', function ($row) {
                return 'Rp ' . number_format($row->price, 0, ',', '.');
            })
            ->addColumn('biaya_operasional_formatted', function ($row) {
                // Cek apakah orderMak ada, jika tidak return Rp 0
                if (!$row->orderMak) {
                    return 'Rp 0';
                }
                $biaya_operasional = collect($row->orderMak)
                ->where('is_split', 0)
                ->flatMap(fn($orderMak) => optional($orderMak->orderTitle) 
                    ->flatMap(fn($orderTitle) => optional($orderTitle->orderItem)->all() ?? []) 
                )
                ->sum('total_price'); 
        
                return 'Rp ' . number_format($biaya_operasional, 0, ',', '.');
            })           
                ->addColumn('profit_formatted', function ($row) {

                    if (!$row->orderMak) {
                        return 'Rp ' . number_format($row->price, 0, ',', '.'); 
                    }
                
                    // Hitung total biaya operasional
                    $biaya_operasional = collect($row->orderMak)
                        ->where('is_split', 0)
                        ->flatMap(fn($orderMak) => optional($orderMak->orderTitle)
                            ->flatMap(fn($orderTitle) => optional($orderTitle->orderItem)->all() ?? [])
                        )
                        ->sum('total_price'); 
                
                    // Hitung profit
                    $profit = $row->price - $biaya_operasional;
                
                    return 'Rp ' . number_format($profit, 0, ',', '.');
                })
            
            ->addColumn('actions', function ($row) use ($user) {
                $editUrl   = url('order/' . $row->id . '/edit');
                $viewUrl   = url('order/' . $row->id);
                $reviseUrl = url('order/' . $row->id . '/revise');
                $printUrl  = url('order/' . $row->id . '/download');

                if ($user->hasAnyRole(['admin', 'Super_admin'])) {
                    return '
                        <a href="'.$editUrl.'" class="btn btn-sm btn-warning" title="Edit"><span class="mdi mdi-square-edit-outline"></span></a>
                        <a href="'.$viewUrl.'" class="btn btn-sm btn-info" title="View"><span class="mdi mdi-file-outline"></span></a>
                        <a href="'.$reviseUrl.'" class="btn btn-sm btn-dark" title="Revise"><span class="mdi mdi-autorenew"></span></a>
                        <a href="'.$printUrl.'" class="btn btn-sm btn-success" title="Download"><span class="mdi mdi-file-download"></span></a>
                    ';                    
                }else{
                    return '
                        <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a>
                    ';                    

                }              


                return '';

            
            })
            ->rawColumns(['price_formatted', 'biaya_operasional_formatted', 'profit_formatted','actions'])
            ->toJson();
        }
       
        $isAdmin = $user->hasRole('admin');
        $isSuperAdmin = $user->hasRole('Super_admin');
        return view('content.order.index',compact('isAdmin','isSuperAdmin'));
        
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

        $order_mak = OrderMak::with(['mak', 'orderTitle.orderItem'])->where('order_id', $order->id)->get();

        $sum_array = [];
        if ($order_mak->isNotEmpty()) {
            // Fungsi untuk menghitung total_price dari orderItem dengan aman
            $calculateTotalPrice = function ($orderMak) {
                return optional($orderMak->orderTitle) // Bisa koleksi atau single object
                    ->flatMap(fn($orderTitle) => optional($orderTitle->orderItem)->all() ?? [])
                    ->sum('total_price');
            };
        
            // Biaya operasional (hanya untuk is_split == 0)
            $biaya_operasional = $order_mak
                ->filter(fn($orderMak) => $orderMak->is_split == 0)
                ->sum($calculateTotalPrice);
        
            $sum_array['biaya_operasional'] = intval($biaya_operasional);
        
            // Profit
            $sum_array['profit'] = intval($order->price) - $sum_array['biaya_operasional'];
        
            // Ambil daftar divisi
            $division_pluck = $divisions->pluck('division_name', 'id')->toArray();
        
            // Split total berdasarkan split_to
            $sum_array['split_totals'] = $order_mak->whereNotNull('split_to')
                ->groupBy('split_to')
                ->mapWithKeys(function ($group, $splitToId) use ($division_pluck, $calculateTotalPrice) {
                    $divisionName = $division_pluck[$splitToId] ?? '-';
                    $total = $group->sum($calculateTotalPrice);
                    return [$divisionName => intval($total)];
                })->toArray();
                
        } else {
            $sum_array = [
                'biaya_operasional' => 0,
                'profit' => 0,
                'split_totals' => [],
            ];
        }
        

        return view('content.order.edit',compact('order','divisions','categorys','divisions_id','maks','divisions_by_order_header','order_mak','sum_array'));
    }

    public function update(Request $request,Order $order)
    {
        $validated = $request->validate([
            // 'job_number' => 'required|max:50|unique:orders,job_number,'.$order->id,
            'title' => 'required|max:100',
            'group' => 'required|max:100',
            'customer' => 'required|max:100',
            'study_lab' => 'required|max:100',
            'date_range' => 'required',
            'category_id' => 'required',
        ], [
            // 'job_number.unique' => 'Job Number sudah tersedia',
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

        $sum_array = [];
        if ($order_mak->isNotEmpty()) {
            // Fungsi untuk menghitung total_price dari orderItem dengan aman
            $calculateTotalPrice = function ($orderMak) {
                return optional($orderMak->orderTitle) // Bisa koleksi atau single object
                    ->flatMap(fn($orderTitle) => optional($orderTitle->orderItem)->all() ?? [])
                    ->sum('total_price');
            };
        
            // Biaya operasional (hanya untuk is_split == 0)
            $biaya_operasional = $order_mak
                ->filter(fn($orderMak) => $orderMak->is_split == 0)
                ->sum($calculateTotalPrice);
        
            $sum_array['biaya_operasional'] = intval($biaya_operasional);
        
            // Profit
            $sum_array['profit'] = intval($order->price) - $sum_array['biaya_operasional'];
        
            // Ambil daftar divisi
            $division_pluck = $divisions->pluck('division_name', 'id')->toArray();
        
            // Split total berdasarkan split_to
            $sum_array['split_totals'] = $order_mak->whereNotNull('split_to')
                ->groupBy('split_to')
                ->mapWithKeys(function ($group, $splitToId) use ($division_pluck, $calculateTotalPrice) {
                    $divisionName = $division_pluck[$splitToId] ?? '-';
                    $total = $group->sum($calculateTotalPrice);
                    return [$divisionName => intval($total)];
                })->toArray();
                
        } else {
            $sum_array = [
                'biaya_operasional' => 0,
                'profit' => 0,
                'split_totals' => [],
            ];
        }

        return view('content.order.view',compact('order','divisions','categorys','divisions_id','maks','divisions_by_order_header','order_mak','sum_array'));
    }

    public function revise(Order $order){
        try {
            $invalid=0;
            $message = ['success' => 'Order berhasil di simpan'];
           //validate
           //valid user
        //    if (Auth::id()<>$order->user_id) {
        //     $message = ['error' => 'Revise Order Failed, Invalid Users'];
        //     $invalid++;
        //    }

           if (in_array($order->status,['DRAFT','CLOSED','REVISED'])) {
            $message = ['success' => "{$order->status} Can't be Revised" ];
            $invalid++;
           }
           if ($invalid==0) {
                
                $new_order         = $order->replicate();
                $new_order->status = 'DRAFT';
                $new_order->rev    = $order->rev+1;
                $new_order->prev_id= $order->id;
                $new_order->save();

                $order_maks = OrderMak::with(['orderTitle.orderItem']) ->where('order_id', $order->id) ->get();

                foreach ($order_maks as $order_mak) {
                    // 3. Replicate OrderMak
                    $new_order_mak = $order_mak->replicate();
                    $new_order_mak->order_id = $new_order->id;
                    $new_order_mak->save();

                    foreach ($order_mak->orderTitle as $order_title) {
                        // 4. Replicate OrderTitle
                        $new_order_title = $order_title->replicate();
                        $new_order_title->order_mak_id = $new_order_mak->id;
                        $new_order_title->save();

                        foreach ($order_title->orderItem as $order_item) {
                            // 5. Replicate OrderItem
                            $new_order_item = $order_item->replicate();
                            $new_order_item->order_title_id = $new_order_title->id;
                            $new_order_item->save();
                        }
                    }
                }

                $order->update(['status'=>'REVISED']);


            }
           
            return redirect('/order')->with($message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'Order gagal di simpan'];
            return redirect('/order')->with($message);
        }
        
    }

    public function updateStatus(Request $request)
    {       
        
        try{
            DB::beginTransaction();

            $order = Order::find($request->order_id);

            if(!$order){
                return response()->json(['success'=>false,'msg'=> 'Order tidak tersedia' ],404);     
            }

            $message = null;
            $data_update = null;

            if($request->status == 'TO REVIEW'){     
                $data_update= [
                    'status' => $request->status,
                ];         
                $message = 'Order berhasil di Submit';
            }

            if($request->status == 'DRAFT'){
                $data_update= [
                    'status' => $request->status,
                ];                               
                $message = 'Order berhasil di Reject';
            }

            if($request->status == 'REVIEWED'){

                    $data_update= [
                        'status' => $request->status,
                        'reviewed_notes' => $request->reviewed_notes,
                        'reviewed_datetime' => now(),
                        'released_by' => auth()->user()->id,
                    ];                               

                $message = 'Order berhasil di Release';
            }

            if($request->status == 'APPROVED'){

               

                $approved_by = match(true) {
                    auth()->user()->hasRole('approval_satu') => 
                    [
                        'approved_1_by' => auth()->user()->id,
                        'approved_date_1' => now(),
                        'approval_step' => 1,
                    ],
                    auth()->user()->hasRole('approval_dua') => 
                    [
                        'approved_2_by' => auth()->user()->id,
                        'approved_date_2' => now(),
                        'approval_step' => 2,
                    ],
                    auth()->user()->hasRole('approval_tiga') => 
                    [
                        'approved_3_by' => auth()->user()->id,
                        'approved_date_3' => now(),
                        'approval_step' => 3,

                    ],
                    default => [],
                };

                $data_update = array_merge([
                    'status' => $request->status,
                ], $approved_by); 

                $message = 'Order berhasil di Approved';
            }


            $order->update($data_update);  

            DB::commit();
            return response()->json(['success'=>true,'msg'=> $message,'data'=>$order],200);      
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg'=> 'Order Item gagal di simpan','data'=>[]],500);
        }

    }

    public function download(Order $order,$type = 'pdf'){

        $order = Order::with([
            'orderMak' => function ($query) {
                $query->orderBy('is_split', 'asc')->orderBy('id', 'asc');
            },
            'orderMak.mak',
            'orderMak.division',
            'orderMak.orderTitle.orderItem',
            'approver1', 'approver2', 'approver3' // Relasi ke User untuk approval
        ])->findOrFail($order->id);
    
        $orderMaks = $order->orderMak;
        // Generate QR Code untuk tiap approver
        $approver_1 = $order->approver1 ? QrCodeHelper::generateQrCode("{$order->job_number},{$order->approver1->nip},{$order->approver1->name}") : null;
        $approver_2 = $order->approver2 ? QrCodeHelper::generateQrCode("{$order->job_number},{$order->approver2->nip},{$order->approver2->name}") : null;
        $approver_3 = $order->approver3 ? QrCodeHelper::generateQrCode("{$order->job_number},{$order->approver3->nip},{$order->approver3->name}") : null;
    
        $pdf = Pdf::loadView('content.order.order_printout', compact('orderMaks', 'approver_1', 'approver_2', 'approver_3','order'));
        return $pdf->download("order-{$order->id}.pdf");

        // return view('content.order.order_printout', compact('orderMaks', 'approver_1', 'approver_2', 'approver_3', 'order'));
    }

    public function getChecklist($order_item_id)
    {               
        try{
            $order_cheklist = OrderChecklist::where('order_item_id',$order_item_id)->get();
            return response()->json(['success'=>true,'msg'=> 'Order Checklist berhasil diambil','data'=> $order_cheklist],200);      
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg'=> 'Order Checklist gagal diambil','data'=>[]],500);
        }

    }

    public function saveChecklist(Request $request, $order_item_id)
    {
        DB::beginTransaction(); 

        try {
            $orderItem = OrderItem::find($order_item_id);

            if (!$orderItem) {
                return response()->json(['success' => false, 'msg' => 'Order item tidak ditemukan'], 404);
            }

            // Ambil semua checklist terkait order item ini
            $orderChecklist = OrderChecklist::where('order_item_id', $order_item_id)->get();

            // Hitung total amount yang tersedia untuk checklist
            $total_available_to_check = intval($orderItem->total_price - $orderChecklist->sum('amount'));    

            $total_requested_amount = collect($request->checklist)->sum('amount');

            // Validasi jumlah yang diminta tidak boleh melebihi jumlah tersedia
            if ($total_requested_amount > intval($orderItem->total_price)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Total Amount tidak boleh lebih besar dari jumlah Total Price',
                    'data' => $orderChecklist
                ], 200);
            }


            $orderChecklist = OrderChecklist::where('order_item_id', $order_item_id)->delete();

            foreach ($request->checklist as $item) {
                OrderChecklist::create([
                    'order_id' => $orderItem->orderTitle->orderMak->order_id,
                    'order_item_id' => $order_item_id,
                    'checklist_number' => $item['checklist_number'],
                    'amount' => $item['amount'],
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);
            }

            DB::commit(); 

            return response()->json([
                'success' => true,
                'msg' => 'Order Checklist berhasil disimpan',
                'data' => $request->checklist
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error($e->getMessage()); 

            return response()->json([
                'success' => false,
                'msg' => 'Order Checklist gagal disimpan',
                'data' => []
            ], 500);
        }
    }
}