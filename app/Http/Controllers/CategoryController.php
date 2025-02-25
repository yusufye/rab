<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)  {
        

        if($request->ajax()){
            $category = Category::all();

            return datatables()
            ->of($category)
            
            ->addColumn('category_name', function ($row) {
                return $row->category_name;
            })
            ->addColumn('actions', function ($row) {
                $editUrl = url('category/' . $row->id . '/edit');
            
                return '
                    <a href="'.$editUrl.'" class="btn btn-sm btn-warning" title="Edit"><span class="mdi mdi-square-edit-outline"></a>
                ';
            })
            ->rawColumns(['category_code', 'category_name','actions'])
            ->toJson();
        }


        return view('content.category.index');
        
    }

    public function create()  {

        return view('content.category.add');
        
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'category_name' => 'required|max:100',
        ]);
        
        try{
             DB::beginTransaction();
           
             Category::create([
                'category_name' => $request->category_name
             ]);
        
            DB::commit();
            $message = ['success' => 'category berhasil di simpan'];
            return redirect('/category')->with($message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'category gagal di simpan'];
            return redirect('/category')->with($message);
        }

    }

    public function edit(category $category){

        return view('content.category.edit',compact('category'));
    }

    public function update(Request $request,category $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|max:100',
        ]);
        
        try{
             DB::beginTransaction();

            $category->update([
                'category_name' => $request->category_name
             ]);
        
            DB::commit();
            $message = ['success' => 'category berhasil di simpan'];
            return redirect('/category')->with($message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'category gagal di simpan'];
            return redirect('/category')->with($message);
        }

    }
}