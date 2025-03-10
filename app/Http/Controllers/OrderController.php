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
use App\Models\ApprovalLog;
use Illuminate\Http\Request;
use App\Helpers\QrCodeHelper;
use App\Models\OrderChecklist;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OrdersPrintExcel;
use App\Models\PercentageSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{

    public function index(Request $request)  {    
        
        $user = auth()->user();

        if($request->ajax()){                  
            
            $orders = Order::query();

            if ($user->hasRole('checker')) {
                $orders->where('status','APPROVED');                     
                $orders->where('approval_step',3);                     
            }     

            if ($user->hasRole('admin')) {                                 
                $orders->where('created_by',$user->id);             
            }         
            
            if ($request->status) {                                 
                $orders->whereIn('status',$request->status);             
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
                    'CANCELLED'   => 'bg-danger',
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
                $editUrl    = url('order/' . $row->id . '/edit');
                $viewUrl    = url('order/' . $row->id);
                $reviseUrl  = url('order/' . $row->id . '/revise');
                $pdf        = url('order/' . $row->id . '/download/pdf');
                $excel      = url('order/' . $row->id . '/download/excel');
                $list_auth = auth()->user()->hasAnyRole(['admin','reviewer','head_reviewer','approval_satu','approval_dua','approval_tiga']);
                $disabled_button_pdf = $list_auth ? '' : 'btn-disabled';

                if ($user->hasAnyRole(['admin', 'Super_admin'])) {
                    $list_disabled_btn_revise = ['DRAFT','CLOSED','REVISED','TO REVIEW'];
                    $disabled_button_revise = in_array($row->status, $list_disabled_btn_revise) 
                    ? 'btn-disabled' 
                    : '';

                    return '
                    <div class="dropdown dropstart">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <span class="mdi mdi-dots-vertical"></span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="'.$editUrl.'" class="dropdown-item" title="Edit">
                                <span class="mdi mdi-square-edit-outline"></span> Edit
                            </a>
                            <a href="'.$viewUrl.'" class="dropdown-item" title="View">
                                <span class="mdi mdi-file-outline"></span> View
                            </a>
                            <a data-url="'.$reviseUrl.'" class="dropdown-item '.$disabled_button_revise.' btn-revise" title="Revise" style="cursor:pointer;">
                                <span class="mdi mdi-autorenew"></span> Revise
                            </a>
                            <a href="'.$pdf.'" class="dropdown-item '. $disabled_button_pdf.'" title="Pdf">
                                <span class="mdi mdi-file-pdf-box"></span> Pengesahan
                            </a>
                            <a href="'.$excel.'" class="dropdown-item" title="Excel">
                                <span class="mdi mdi-file-excel-box"></span> Excel
                            </a>

                        </div>
                    </div>';                
                }else{
                    return '
                      <div class="dropdown dropstart">
                       <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <span class="mdi mdi-dots-vertical"></span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="'.$viewUrl.'" class="dropdown-item" title="View">
                                <span class="mdi mdi-file-outline"></span> View
                            </a>
                            <a href="'.$pdf.'" class="dropdown-item '. $disabled_button_pdf.'" title="Pdf">
                                <span class="mdi mdi-file-pdf-box"></span> Pengesahan
                            </a>
                            <a href="'.$excel.'" class="dropdown-item" title="Excel">
                                <span class="mdi mdi-file-excel-box"></span> Excel
                            </a>
                         </div>  
                     </div>';              

                }              


                return '';

            
            })
            ->rawColumns(['price_formatted', 'biaya_operasional_formatted', 'profit_formatted','actions'])
            ->toJson();
        }
       
        $isAdmin = $user->hasRole('admin');
        $isSuperAdmin = $user->hasRole('Super_admin');

        $status_selected_by_role = match (true) {
            $user->hasRole('head_reviewer')  => 'TO REVIEW',
            $user->hasRole('approval_satu')  => 'REVIEWED',
            $user->hasRole('approval_dua') || $user->hasRole('approval_tiga') || $user->hasRole('checker') => 'APPROVED',
            default => '',
        };         

        return view('content.order.index',compact('isAdmin','isSuperAdmin','status_selected_by_role'));
        
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
            
            $contract_price = Helpers::parseCurrency($request->contract_price);
        

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
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'job_type' => $request->job_type,
                'contract_number' => $request->contract_number,
                'contract_price' => $contract_price,
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

            if($order->status == 'APPROVED'){
                $message = ['failed' => 'Order telah APPROVED. Tidak dapat mengubah data Order'];
                return redirect('/order')->with($message);
            }

             DB::beginTransaction();
             $dates = explode(' to ', $request->date_range);
            //  dd($request->date_range);

             $dateFrom = \Carbon\Carbon::createFromFormat('d-M-Y', trim($dates[0]))->format('Y-m-d');
             $dateTo = isset($dates[1]) ? \Carbon\Carbon::createFromFormat('d-M-Y', trim($dates[1]))->format('Y-m-d') : $dateFrom;             

            $price = Helpers::parseCurrency($request->price);
            $splitPrice = Helpers::parseCurrency($request->split_price);

            $contract_price = Helpers::parseCurrency($request->contract_price);


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
                'updated_by' => Auth::id(),
                'job_type' => $request->job_type,
                'contract_number' => $request->contract_number,
                'contract_price' => $contract_price,
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
            DB::beginTransaction(); // Mulai transaksi
        
            $invalid = 0;
            $message = ['success' => 'Revisi Berhasil'];
        
            if (in_array($order->status, ['DRAFT', 'CLOSED', 'REVISED','TO REVIEW'])) {
                $message = ['success' => "{$order->status} Tidak dapat direvisi"];
                $invalid++;
            }

           //valid user
           if (auth()->user()->id<>$order->created_by) {
                $message = ['success' => 'Revise Failed, Invalid Users'];
                $invalid++;
           }
        
            if ($invalid == 0) {
                $new_order                             = $order->replicate();
                $new_order->status                     = 'DRAFT';
                $new_order->rev                        = $order->rev + 1;
                $new_order->prev_id                    = $order->id;
                $new_order->approved_date_1            = null;
                $new_order->approved_1_by              = null;
                $new_order->approved_date_2            = null;
                $new_order->approved_2_by              = null;
                $new_order->approved_date_3            = null;
                $new_order->approved_3_by              = null;
                $new_order->approval_rejected_notes    = null;
                $new_order->approval_rejected_by       = null;
                $new_order->approval_rejected_datetime = null;
                $new_order->reviewed_notes             = null;
                $new_order->approval_step              = 0;
                $new_order->save();
        
                $order_maks = OrderMak::with(['orderTitle.orderItem.orderChecklist'])
                    ->where('order_id', $order->id)
                    ->get();
        
                foreach ($order_maks as $order_mak) {
                    $new_order_mak = $order_mak->replicate();
                    $new_order_mak->order_id = $new_order->id;
                    $new_order_mak->save();
        
                    foreach ($order_mak->orderTitle as $order_title) {
                        $new_order_title = $order_title->replicate();
                        $new_order_title->order_mak_id = $new_order_mak->id;
                        $new_order_title->save();
        
                        foreach ($order_title->orderItem as $order_item) {
                            $new_order_item = $order_item->replicate();
                            $new_order_item->order_title_id = $new_order_title->id;
                            $new_order_item->save();

                            foreach ($order_item->orderChecklist as $order_checklist) {
                                $new_order_checklist = $order_checklist->replicate();
                                $new_order_checklist->order_item_id = $new_order_item->id;
                                $new_order_checklist->order_id = $new_order->id;
                                $new_order_checklist->save();
                            }
                        }
                    }
                }
        
                // Perbaikan bagian OrderChecklist
                // $order_checklists = OrderChecklist::where('order_id', $order->id)->get();
                // foreach ($order_checklists as $order_checklist) {
                //     $new_order_checklist = $order_checklist->replicate();
                //     $new_order_checklist->order_id = $new_order->id;
                //     $new_order_checklist->save();
                // }
        
                $order->update(['status' => 'REVISED']);
        
                DB::commit(); // Simpan semua perubahan
            }
        
            return redirect('/order')->with($message);
        } catch (Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika ada error
            Log::error($e);
            $message = ['failed' => 'Revisi Gagal'];
            return redirect('/order')->with($message);
        }
        
        
    }

    public function updateStatus(Request $request)
    {       
        try{
            DB::beginTransaction();

            $order = Order::find($request->order_id);           


            if(!$order){
                return response()->json(['success'=>false,'msg'=> 'Order tidak tersedia' ]);     
            }

            $message = null;
            $data_update = null;
            $type = null;
            $notes = null;

            $user = auth()->user();

            // admin submit
            if($request->status == 'TO REVIEW'){    

                if($order->status == 'APPROVED'){
                    return response()->json(['success' => false, 'msg' => 'Status Order harus DRAFT']);
                }
                
                $data_update= [
                    'status' => $request->status,
                ];         
                $message = 'Order berhasil di Submit';
            }

            // admin batal
            if($request->status == 'CANCELLED'){    

                if($order->status != 'DRAFT'){
                    return response()->json(['success' => false, 'msg' => 'Status Order harus DRAFT']);
                }
                
                $data_update= [
                    'status' => $request->status,
                ];         
                $message = 'Order berhasil di Batalkan';
            }

            // reject head_reviewer and approver 1,2,3
            if($request->status == 'DRAFT'){

                if ($user->hasRole('head_reviewer') && $order->status != 'TO REVIEW') {
                    return response()->json(['success' => false, 'msg' => 'Status Order harus TO REVIEW']);
                }

                if ($user->hasRole('approval_satu') && $order->status != 'REVIEWED') {
                    return response()->json(['success' => false, 'msg' => 'Status Order harus REVIEWED']);
                }

                if ($user->hasRole('approval_dua') && ($order->status != 'APPROVED' || $order->approval_step != 1)) {
                    return response()->json(['success' => false, 'msg' => 'Status Order harus APPROVED ']);
                }

                if ($user->hasRole('approval_tiga') && ($order->status != 'APPROVED' || $order->approval_step != 2)) {
                    return response()->json(['success' => false, 'msg' => 'Status Order harus APPROVED 2']);
                }        

                $data_update= [
                    'status' => $request->status,
                    'approval_rejected_notes' => $request->approvalRejectedNotes,
                    'approval_rejected_by' => auth()->user()->id,
                    'approval_rejected_datetime' => now(),
                    'approval_step' => 0,
                    'approved_1_by' => null,
                    'approved_date_1' => null,
                    'approved_2_by' => null,
                    'approved_date_2' => null,
                    'approved_3_by' => null,
                    'approved_date_3' => null,
                    
                ];          

                $message = 'Order berhasil di Reject';
                $type = 'REJECTED';
                $notes = $request->approvalRejectedNotes;
            }

            // head_checker release
            if($request->status == 'REVIEWED'){

                    if ($order->status != 'TO REVIEW') {
                        return response()->json(['success' => false, 'msg' => 'Status Order harus TO REVIEW']);
                    }

                    $data_update= [
                        'status' => $request->status,
                        'reviewed_notes' => $request->reviewed_notes,
                        'reviewed_datetime' => now(),
                        'approval_rejected_notes' => null,
                        'approval_rejected_by' => null,
                        'approval_rejected_datetime' => null,
                        'released_by' => $user->id,
                    ];                               

                $message = 'Order berhasil di Release';
                $type = 'APPROVED';
                $notes = $request->reviewed_notes;
            }

             // approve by approver
            if($request->status == 'APPROVED'){

                if ($user->hasRole('approval_satu') && $order->status != 'REVIEWED') {
                    return response()->json(['success' => false, 'msg' => 'Status Order harus REVIEWED']);
                }

                if ($user->hasRole('approval_dua') && ($order->status != 'APPROVED' || $order->approval_step != 1)) {
                    return response()->json(['success' => false, 'msg' => 'Status Order harus APPROVED 1']);
                }

                if ($user->hasRole('approval_tiga') && ($order->status != 'APPROVED' || $order->approval_step != 2)) {
                    return response()->json(['success' => false, 'msg' => 'Status Order harus APPROVED 2']);
                }               

                $approved_by = match(true) {

                    $user->hasRole('approval_satu') => 
                    [
                        'approved_1_by' => $user->id,
                        'approved_date_1' => now(),
                        'approval_step' => 1,
                    ],
                    $user->hasRole('approval_dua') => 
                    [
                        'approved_2_by' => $user->id,
                        'approved_date_2' => now(),
                        'approval_step' => 2,
                    ],
                    $user->hasRole('approval_tiga') => 
                    [
                        'approved_3_by' => $user->id,
                        'approved_date_3' => now(),
                        'approval_step' => 3,

                    ],
                    default => [],
                };

                $data_update = array_merge([
                    'status' => $request->status,
                ], $approved_by); 

                $message = 'Order berhasil di Approved';

                $type = 'APPROVED';
            }


            $order->update($data_update);  

            if($type){
                // insert to approval logs
                ApprovalLog::create([
                    'order_id' => $order->id,
                    'type' => $type,
                    'notes' => $notes,
                    'log_by' => $user->id,
                    'log_datetime' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['success'=>true,'msg'=> $message,'data'=>$order],200);      
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg'=> 'Order gagal di simpan','data'=>[]],500);
        }

    }

    public function download(Order $order,$type = 'pdf'){

        $order = Order::with([
            'orderMak' => function ($query) {
                $query->where('is_split',0)->orderBy('is_split', 'asc')->orderBy('id', 'asc');
            },
            'orderMak.mak',
            'orderMak.division',
            'orderMak.orderTitle.orderItem',
            'approver1', 'approver2', 'approver3' // Relasi ke User untuk approval
        ])->findOrFail($order->id);

        $sumItem = OrderItem::whereHas('orderTitle.orderMak', function ($query) use ($order)  {
            $query->where('order_id', $order->id)
                  ->where('is_split', 0);
        })->sum('total_price');

        $splitItems = OrderItem::whereHas('orderTitle.orderMak', function ($query) use ($order)  {
            $query->where('order_id', $order->id)
                  ->where('is_split', 1);
        })->join('order_titles', 'order_titles.id', '=', 'order_items.order_title_id')
        ->join('order_maks', 'order_maks.id', '=', 'order_titles.order_mak_id')
        ->selectRaw('order_maks.split_to, SUM(order_items.total_price) as total')
        ->groupBy('order_maks.split_to')
        ->get();

        
        $sumPerDiv=$splitItems->mapWithKeys(function ($item) {
            $division = Division::find($item->split_to);
            return [$division->division_name ?? 'Unknown' => $item->total];
        })->toArray();


        // Profit calculation
        $profit = $order ? ($order->price - $sumItem) : 0;

        $latestEffectiveDate = PercentageSetting::where('effective_date', '<=', $order->date_to)->max('effective_date');
        $getPercentage = PercentageSetting::where('effective_date', $latestEffectiveDate)->get();
    
        $orderMaks = $order->orderMak;
        // Generate QR Code untuk tiap approver
        $approver_1 = $order->approver1 ? QrCodeHelper::generateQrCode("{$order->job_number}\r\n{$order->approver1->name}\r\n{$order->approver1->nip}") : null;
        $approver_2 = $order->approver2 ? QrCodeHelper::generateQrCode("{$order->job_number}\r\n{$order->approver2->name}\r\n{$order->approver2->nip}") : null;
        $approver_3 = $order->approver3 ? QrCodeHelper::generateQrCode("{$order->job_number}\r\n{$order->approver3->name}\r\n{$order->approver3->nip}") : null;
        
        $fileName       = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $order->job_number);
        
        if ($type=='pdf') {
            $pdf = Pdf::loadView('content.order.order_printout_pdf', compact('orderMaks', 'approver_1', 'approver_2', 'approver_3', 'order','sumItem','profit','getPercentage','sumPerDiv'));
            return $pdf->download("job-{$fileName}.pdf");
        }elseif ($type='excel') {
            return Excel::download(new OrdersPrintExcel($order,$orderMaks,$sumItem,$profit,$getPercentage,$sumPerDiv), "orders{$fileName}.xlsx");
            
        }
        
        return view('content.order.order_printout_pdf', compact('orderMaks', 'approver_1', 'approver_2', 'approver_3', 'order','sumItem','profit','getPercentage','sumPerDiv'));
    }

    public function getDivisions(Order $order){
        $divisions = Division::all();

        $split_to_mak = OrderMak::where('order_id',$order->id)->pluck('split_to')->toArray(); 
        $selected_divisions  = isset($order->split_to) && is_array($order->split_to)
        ? Division::whereIn('id', $order->split_to)->pluck('id')->toArray()
        : [];
    
        return response()->json([
            'divisions' => $divisions,
            'split_to_mak' => $split_to_mak,
            'selected_divisions' => $selected_divisions
        ]);

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
                return response()->json(['success' => false, 'msg' => 'Order item tidak ditemukan']);
            }

            if (!($orderItem->orderTitle->orderMak->order?->status == 'APPROVED' && 
            $orderItem->orderTitle->orderMak->order?->approval_step == 3)) {
                
                return response()->json([
                    'success' => false, 
                    'msg' => 'Order belum disetujui atau belum memenuhi syarat untuk checklist'
                ]);
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
                    'checklist_type' => $item['checklist_type'],
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

    public function getOrderMak(OrderMak $orderMak){

        if(!$orderMak){
            return response()->json(['orderMak' => []]);
        }
    
        return response()->json(['orderMak' => $orderMak],200);

    }

    public function getOrderTitle($id){

        $orderTitle = OrderTitle::with('orderMak.mak')->find($id);

        if(!$orderTitle){
            return response()->json(['orderTitle' => []]);
        }
    
        return response()->json(['orderTitle' => $orderTitle],200);

    }

    public function getOrderItem($id){

        $orderItem = OrderItem::with('orderTitle.orderMak.mak')->find($id);

        if(!$orderItem){
            return response()->json(['orderItem' => []]);
        }
    
        return response()->json(['orderItem' => $orderItem],200);

    }

    public function getStatusOrderHistory($id)
    {               
        try{
            $status_logs = ApprovalLog::with('user')->where('order_id',$id)->orderByDesc('updated_at')->get();
            return response()->json(['success'=>true,'msg'=> 'Status Order berhasil diambil','data'=> $status_logs],200);      
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['success'=>false,'msg'=> 'Status Order gagal diambil','data'=>[]],500);
        }

    }
}