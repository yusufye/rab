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
        
        // admin
        User::create([
            'division_id' => $divisionId,
            'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
            'name' => 'admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('test1234'),
            'remember_token' => Str::random(10),
            'active' => 1,
        ]);
        
        // reviewerd
        User::create([
            'division_id' => $divisionId,
            'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
            'name' => 'reviewer',
            'email' => 'reviewer@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('test1234'),
            'remember_token' => Str::random(10),
            'active' => 1,
        ]);

        // approval_satu
        User::create([
            'division_id' => $divisionId,
            'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
            'name' => 'approval_satu',
            'email' => 'approval_satu@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('test1234'),
            'remember_token' => Str::random(10),
            'active' => 1,
        ]);

        // approval_dua
        User::create([
            'division_id' => $divisionId,
            'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
            'name' => 'approval_dua',
            'email' => 'approval_dua@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('test1234'),
            'remember_token' => Str::random(10),
            'active' => 1,
        ]);

        // approval_tiga
        User::create([
            'division_id' => $divisionId,
            'nip' => fake()->unique()->numerify(str_repeat('#', 16)),
            'name' => 'approval_tiga',
            'email' => 'approval_tiga@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('test1234'),
            'remember_token' => Str::random(10),
            'active' => 1,
        ]);
       }
    }
}
