<?php

namespace Database\Seeders;

use App\Models\Mak;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mak = [
            [
                'mak_code' => '525115',
                'mak_name' => 'Belanja Perjalanan',
            ],
            [
                'mak_code' => '525119',
                'mak_name' => 'Beban Penyediaan Barang dan Jasa BLU Lainnya',
            ],
            [
                'mak_code' => '525112',
                'mak_name' => 'Belanja Barang',
            ],
            [
                'mak_code' => '525113',
                'mak_name' => 'Belanja Jasa',
            ],
            [
                'mak_code' => '525121',
                'mak_name' => 'Belanja Barang Persediaan Barang Konsumsi / BLU',
            ],
        ];

        foreach($mak as $ma){
            Mak::create([
                'mak_code' => $ma['mak_code'], 
                'mak_name' => $ma['mak_name'],
            ]);
        }
        
    }
}
