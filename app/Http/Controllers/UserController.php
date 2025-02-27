<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
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

        $list_users = User::orderByDesc('created_at')->get();        
    
        return DataTables::of($list_users)
            ->addColumn('roles', function ($user) {
                $roles = $user->roles->map(function ($role) {
                    return $role->name;
                })->toArray();
    
                return implode(', ', $roles);
            })
            ->rawColumns(['roles'])
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
