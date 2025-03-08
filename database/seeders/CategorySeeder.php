<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = ['Laboratorium', 'Studi', 'Kajian (desk job)', 'Dominan Lab', 'Dominan Studi', 'Special Case',];

        foreach($category as $div){
            Category::create([
                'category_name' => $div
            ]);
        }
    }
}