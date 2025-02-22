<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisionId = Division::first()?->id;


       if($divisionId){
     
        $superAdmin = User::create([
            'division_id' => $divisionId,
            'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
            'name' => 'Super Admin',
            'email' => 'super_admin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('test1234'),
            'remember_token' => Str::random(10),
            'active' => 1,
        ]);

        $superAdmin->assignRole(['name' => 'Super_admin']);
        
        // admin
        $admin = User::create([
                'division_id' => $divisionId,
                'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
                'name' => 'admin',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test1234'),
                'remember_token' => Str::random(10),
                'active' => 1,
            ]);

        $admin->assignRole(['name' => 'admin']);
            
            
        // head reviewer
         $reviewer =  User::create([
                'division_id' => $divisionId,
                'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
                'name' => 'head_reviewer',
                'email' => 'head_reviewer@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test1234'),
                'remember_token' => Str::random(10),
                'active' => 1,
        ]);

        $reviewer->assignRole(['name' => 'head_reviewer']);

        // reviewer
         $reviewer =  User::create([
                'division_id' => $divisionId,
                'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
                'name' => 'reviewer',
                'email' => 'reviewer@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test1234'),
                'remember_token' => Str::random(10),
                'active' => 1,
        ]);

         $reviewer->assignRole(['name' => 'reviewer']);

            // approval_satu
          $approval_satu =  User::create([
                'division_id' => $divisionId,
                'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
                'name' => 'approval_satu',
                'email' => 'approval_satu@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test1234'),
                'remember_token' => Str::random(10),
                'active' => 1,
            ]);

         $approval_satu->assignRole(['name' => 'approval_satu']);

            // approval_dua
          $approval_dua =  User::create([
                'division_id' => $divisionId,
                'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
                'name' => 'approval_dua',
                'email' => 'approval_dua@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test1234'),
                'remember_token' => Str::random(10),
                'active' => 1,
            ]);

            $approval_dua->assignRole(['name' => 'approval_dua']);

            // approval_tiga
          $approval_tiga =  User::create([
                'division_id' => $divisionId,
                'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
                'name' => 'approval_tiga',
                'email' => 'approval_tiga@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test1234'),
                'remember_token' => Str::random(10),
                'active' => 1,
            ]);

            $approval_tiga->assignRole(['name' => 'approval_tiga']);

            // checker
          $checker =  User::create([
                'division_id' => $divisionId,
                'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
                'name' => 'checker',
                'email' => 'checker@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test1234'),
                'remember_token' => Str::random(10),
                'active' => 1,
            ]);

            $checker->assignRole(['name' => 'checker']);
        }
    }
}
