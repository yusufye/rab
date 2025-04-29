<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\PercentageSettingSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            DivisionSeeder::class,
            CategorySeeder::class,
            MakSeeder::class,
            UserSeeder::class,
            PercentageSettingSeeder::class

       ]);
    }
}