<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function index(){
        return view('content.user.index');
    }

    public function create(){

        $divisions = Division::all();
        $user = auth()->user();

        $role = Role::query();

        if(!$user->hasRole('Super_admin')){
            $role->where('name','<>','Super_admin');
        }

        $roles = $role->get();

        return view('content.user.add',compact('divisions','roles'));
    }

    public function store(Request $request){

        $validated = $request->validate([
            'division_id' => 'required',
            'nip' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'password' => 'required|min:8',
            'role_id' => 'required',
        ]);
        
        try{
            DB::beginTransaction();

            $user = User::create([
                'division_id' => $request->division_id,
                'nip' => $request->nip,
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'active' => 1,
                'email_verified_at' => now(),
            ]);

            $roleName = Role::where('id', $request->role_id)->value('name');
            $user->assignRole($roleName);

        
            DB::commit();
            $message = ['success' => 'User berhasil di simpan'];
            return redirect('/user')->with($message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'User gagal di simpan'];
            return redirect('/user')->with($message);
        }
    }

    public function edit(User $user)  {

        $divisions = Division::all();
        $authUser = auth()->user();

        $role = Role::query();

        if(!$authUser->hasRole('Super_admin')){
            $role->where('name','<>','Super_admin');
        }

        $roles = $role->get();
        

        return view('content.user.edit',compact('user','divisions','roles'));
        
    }

    public function update(Request $request,User $user){

     
        $validated = $request->validate([
            'division_id' => 'required',
            'nip' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'role_id' => 'required',
        ]);
        
        try{
            DB::beginTransaction();

            $user->update([
                'division_id' => $request->division_id,
                'nip' => $request->nip,
                'name' => $request->name,
                'email' => $request->email,
                'active' => 1,
                'email_verified_at' => now(),
            ]);

            $roleName = Role::where('id', $request->role_id)->value('name');
            
            $user->syncRoles([$roleName]); 

        
            DB::commit();
            $message = ['success' => 'User berhasil di simpan'];
            return redirect('/user')->with($message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            $message = ['failed' => 'User gagal di simpan'];
            return redirect('/user')->with($message);
        }
    }


    public function indexRole(Request $request) {
        $data = [];

        if ($request->has('roleId')) {
            $roleId = $request->input('roleId');
            $data['roles'] = Role::where('id', $roleId)->get();
            $roles = Role::where('id', $roleId)->get();
            $data['permissions_data'] = Permission::join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')->where('role_has_permissions.role_id', $roleId)
            ->get();
            return response()->json($data);
        }
    
        $roles = Role::all();
        $horizontalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
        $menuList = json_decode($horizontalMenuJson,true);

        if (isset($menuList['menu'])) {
            $permissions = $this->flattenMenu($menuList['menu']);
        }

        $create_roles_and_permission = auth()->user()->hasPermissionTo('create_role_&_permission');
        $update_roles_and_permission = auth()->user()->hasPermissionTo('update_role_&_permission');
        $delete_roles_and_permission = auth()->user()->hasPermissionTo('delete_role_&_permission');

        return view('content.user.roles', compact('data','roles','permissions','create_roles_and_permission','update_roles_and_permission','delete_roles_and_permission'));
    }

    public function flattenMenu($menuList)
    {
        $flatMenu = [];

        foreach ($menuList as $menu) {
            $flatMenu[] = [
                'url' => $menu['url'] ?? null,
                'name' => $menu['name'] ?? null,
                'icon' => $menu['icon'] ?? null,
                'slug' => $menu['slug'] ?? null,
                'permission_name' => $menu['permission_name'] ?? null,
            ];

            if (isset($menu['submenu']) && is_array($menu['submenu'])) {
                $flatMenu = array_merge($flatMenu, $this->flattenMenu($menu['submenu']));
            }
        }

        return $flatMenu;
    }

    
    public function ajax_list_users()
    {

        $role = auth()->user()->getRoleNames()->first();

        if ($role === 'Super_admin') {
            $list_users = User::all();
        } else {
            $list_users = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Super_admin');
            })->orderByDesc('created_at')->get();
        }       
      
    
        return DataTables::of($list_users)
            ->addColumn('roles', function ($user) {
                $roles = $user->roles->map(function ($role) {
                    return $role->name;
                })->toArray();
    
                return implode(', ', $roles);
            })
            ->addColumn('actions', function ($row) {
                $editUrl = url('user/' . $row->id . '/edit');
            
                return '
                    <a href="'.$editUrl.'" class="btn btn-sm btn-warning" title="Edit"><span class="mdi mdi-square-edit-outline"></a>
                ';
            })
            ->rawColumns(['roles','actions'])
            ->make(true);
    }

    public function add_roles(Request $request){
        $validatedData = $request->validate([
            'modalRoleName' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::firstOrCreate([
            'name' => $validatedData['modalRoleName'],
            'guard_name' => 'web',
        ]);
     

        if($role){
            $permissions = [];
            foreach ($request->except('_token', 'modalRoleName') as $key => $value) {
                if (strpos($key, 'permission') !== false) {
                    $permissions[$key] = $value;
                }
            }


            foreach ($permissions as $permissionName => $value) {
                foreach ($value as $p) {
                    $permission = Permission::where('name', $p)->where('guard_name', 'web')->first();

                    if (!$permission) {
                        $permission = Permission::firstOrCreate([
                            'name' => $p,
                            'guard_name' => 'web'
                        ]);
                    }

                    $role->givePermissionTo($permission);
                }
            }


            $message = ['success' => 'Created'];
        } else {
            $message = ['failed' => 'Data Gagal di Simpan'];
        }

        return redirect('roles-and-permission')->with($message);
    }

    public function edit_roles(Request $request){
        $role = Role::find($request->role_id);

        if (!$role) {
            return redirect('roles')->with(['Gagal' => 'Role not found']);
        }

        $validatedData = $request->validate([
            'modalRoleNameEdit' => 'required|max:255|unique:permissions,name,' .$role->id,
        ]);

        $role->update([
            'name' =>  $request->modalRoleNameEdit,
            'guard_name' => 'web'
        ]);

        $permissions = collect($request->except('_token', 'modalRoleNameEdit'))
            ->filter(fn($value, $key) => strpos($key, 'permission') !== false)
            ->flatten();

        $current_permissions = $role->permissions()->pluck('name')->toArray();


        $permissions_to_add = array_diff($permissions->toArray(), $current_permissions);
        $permissions_to_remove = array_diff($current_permissions, $permissions->toArray());


        foreach ($permissions_to_add as $p) {
            $permission = Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
            $role->givePermissionTo($permission);
        }

        foreach ($permissions_to_remove as $p) {
            $permission = Permission::where('name', $p)->where('guard_name', 'web')->first();
            if ($permission) {
                $role->revokePermissionTo($permission); 
            }
        }

        return redirect('roles-and-permission')->with(['Success' => 'Updated']);
    }
    
}
