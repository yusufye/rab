<?php

namespace App\Http\Controllers;

use App\Models\Mak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MakController extends Controller
{
    public function index(Request $request)  {       
        

        if($request->ajax()){
            $mak = Mak::all();

            return datatables()
            ->of($mak)
            ->addColumn('mak_code', function ($row) {
                return $row->mak_code;
            })
            ->addColumn('mak_name', function ($row) {
                return $row->mak_name;
            })
            ->addColumn('actions', function ($row) {
                $editUrl = url('mak/' . $row->id . '/edit');
            
                return '
                    <a href="'.$editUrl.'" class="btn btn-sm btn-warning" title="Edit"><span class="mdi mdi-square-edit-outline"></a>
                ';
            })
            ->rawColumns(['mak_code', 'mak_name','actions'])
            ->toJson();
        }


        return view('content.mak.index');
        
    }

    public function create()  {

        return view('content.mak.add');
        
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'mak_code' => 'required|integer|digits_between:5,20',
            'mak_name' => 'required|max:100',
        ]);
        
        try{
             DB::beginTransaction();
           
             Mak::create([
                'mak_code' => $request->mak_code,
                'mak_name' => $request->mak_name
             ]);
        
            DB::commit();
            $message = ['success' => 'MAK berhasil di simpan'];
            return redirect('/mak')->with($message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'MAK gagal di simpan'];
            return redirect('/mak')->with($message);
        }

    }

    public function edit(Mak $mak){

        return view('content.mak.edit',compact('mak'));
    }

    public function update(Request $request,Mak $mak)
    {
        $validated = $request->validate([
            'mak_code' => 'required|integer|digits_between:5,20|unique:maks,mak_code,'.$mak->id,
            'mak_name' => 'required|max:100',
        ]);
        
        try{
             DB::beginTransaction();

            $mak->update([
                'mak_code' => $request->mak_code,
                'mak_name' => $request->mak_name
             ]);
        
            DB::commit();
            $message = ['success' => 'MAK berhasil di simpan'];
            return redirect('/mak')->with($message);        
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'MAK gagal di simpan'];
            return redirect('/mak')->with($message);
        }

    }
}