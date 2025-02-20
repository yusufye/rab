<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roles = ['Super_admin','admin','reviewer','approval_satu','approval_dua','approval_tiga','checker'];

        foreach ($roles as $role) {    
                Role::firstOrCreate([
                    'name' => $role,
                    'guard_name' => 'web',
                ]);
            
        }
    }
}
