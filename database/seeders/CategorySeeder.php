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
        $category = ['Laboratorium'=>70, 'Studi'=>40, 'Kajian (desk job)'=>65, 'Dominan Lab'=>55, 'Dominan Studi'=>50, 'Special Case'=>0];

        foreach($category as $key=>$val){
            Category::create([
                'category_name' => $key,
                'category_percentage' => $val
            ]);
        }
    }
}