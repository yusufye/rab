<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $division = ['DPMA','DPME','DPMG','DPMP','DPMR','DPMS','DPMT','DPMU'];

        foreach($division as $div){
            Division::create([
                'division_name' => $div
            ]);
        }

        
    }
}