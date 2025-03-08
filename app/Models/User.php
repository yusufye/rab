<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'division_id',
        'nip',
        'active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function create_role($roles){
        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            
        }
    }

    public function create_permissions()
    {
        $roles = Role::get();
        $prefix = ['create', 'read', 'update', 'delete'];
        $horizontalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
        $horizontalMenuData = json_decode($horizontalMenuJson, true);
        $menu_list = $horizontalMenuData;
    
        $menu_with_prefix = [];
        foreach ($menu_list['menu'] as $menu) {
            $menu_with_prefix = array_merge($menu_with_prefix, $this->processMenu($menu, $prefix));
        }
    
        $combined_menu = $menu_with_prefix;
    
        $superAdminRole = Role::where('name', 'Super_admin')->first();
    
        if ($superAdminRole) {
            $superAdminPermissionsMapped = [];
            foreach ($combined_menu as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName]);
                $superAdminPermissionsMapped[] = $permissionName;
            }
            $superAdminRole->syncPermissions(
                Permission::whereIn('name', $superAdminPermissionsMapped)->get()
            );
        }
    
        foreach ($roles as $role) {
            if ($role->name === 'admin') {
                $adminPermission = ['order','home'];
    
                $adminPermissionsMapped = [];
                foreach ($adminPermission as $permission) {
                    foreach ($prefix as $op) {
                        $permissionName = strtolower(str_replace(' ', '_', "{$op}_{$permission}"));
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                        ]);
                        $adminPermissionsMapped[] = $permissionName;
                    }
                }
    
                $role->syncPermissions(
                    Permission::whereIn('name', $adminPermissionsMapped)->get()
                );
            }
            if ($role->name === 'head_reviewer') {
                $headReviewerPermission = ['order','home'];
    
                $headReviewerPermissionsMapped = [];
                foreach ($headReviewerPermission as $permission) {
                   
                        $permissionName = strtolower(str_replace(' ', '_', "read_{$permission}"));
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                        ]);
                        $headReviewerPermissionsMapped[] = $permissionName;
                    
                }
    
                $role->syncPermissions(
                    Permission::whereIn('name', $headReviewerPermissionsMapped)->get()
                );
            }
            if ($role->name === 'reviewer') {
                $reviewerPermission = ['order','home'];
    
                $reviewerPermissionsMapped = [];
                foreach ($reviewerPermission as $permission) {
                
                        $permissionName = strtolower(str_replace(' ', '_', "read_{$permission}"));
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                        ]);
                        $reviewerPermissionsMapped[] = $permissionName;
                    
                }
    
                $role->syncPermissions(
                    Permission::whereIn('name', $reviewerPermissionsMapped)->get()
                );
            }
            if ($role->name === 'approval_satu') {
                $approvalSatuPermission = ['order','home'];
    
                $approvalSatuPermissionsMapped = [];
                foreach ($approvalSatuPermission as $permission) {
                  
                        $permissionName = strtolower(str_replace(' ', '_', "read_{$permission}"));
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                        ]);
                        $approvalSatuPermissionsMapped[] = $permissionName;
                    
                }
    
                $role->syncPermissions(
                    Permission::whereIn('name', $approvalSatuPermissionsMapped)->get()
                );
            }
            if ($role->name === 'approval_dua') {
                $approvalDuaPermission = ['order','home'];
    
                $approvalDuaPermissionsMapped = [];
                foreach ($approvalDuaPermission as $permission) {
             
                        $permissionName = strtolower(str_replace(' ', '_', "read_{$permission}"));
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                        ]);
                        $approvalDuaPermissionsMapped[] = $permissionName;
                    
                }
    
                $role->syncPermissions(
                    Permission::whereIn('name', $approvalDuaPermissionsMapped)->get()
                );
            }
            if ($role->name === 'approval_tiga') {
                $approvalTigaPermission = ['order','home'];
    
                $approvalTigaPermissionsMapped = [];
                foreach ($approvalTigaPermission as $permission) {
                   
                        $permissionName = strtolower(str_replace(' ', '_', "read_{$permission}"));
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                        ]);
                        $approvalTigaPermissionsMapped[] = $permissionName;
                    
                }
    
                $role->syncPermissions(
                    Permission::whereIn('name', $approvalTigaPermissionsMapped)->get()
                );
            }
            if ($role->name === 'checker') {
                $checkerPermission = ['order','home'];
    
                $checkerPermissionsMapped = [];
                foreach ($checkerPermission as $permission) {
                    foreach ($prefix as $op) {
                        $permissionName = strtolower(str_replace(' ', '_', "read_{$permission}"));
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                        ]);
                        $checkerPermissionsMapped[] = $permissionName;
                    }
                }
    
                $role->syncPermissions(
                    Permission::whereIn('name', $checkerPermissionsMapped)->get()
                );
            }
        }
    }
    

    public function processMenu($menu, $prefix)
    {
        $permissions = [];
    
        if (isset($menu['name'])) {
            foreach ($prefix as $p) {
                $permissions[] = "{$p}_" . str_replace(' ', '_', strtolower($menu['name']));
            }
        }
    
        if (isset($menu['submenu']) && is_array($menu['submenu'])) {
            foreach ($menu['submenu'] as $submenu) {
                $permissions = array_merge($permissions, $this->processMenu($submenu, $prefix));
            }
        }
    
        return $permissions;
    }

    public function approvals1()
    {
        return $this->hasMany(Order::class, 'approver_id_1');
    }

    public function approvals2()
    {
        return $this->hasMany(Order::class, 'approver_id_2');
    }

    public function approvals3()
    {
        return $this->hasMany(Order::class, 'approver_id_3');
    }

    public function creteOrder()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}