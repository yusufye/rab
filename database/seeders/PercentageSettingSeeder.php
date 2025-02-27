<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PercentageSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PercentageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            ['title'=>'a. P1 PNS dan Dewas
    b. P2 PNS, Bidang/Bagian/SPI/PILT
    c. P2 Non PNS BLU Bidang/Bagian/SPI/PILT
    d. Gaji Pegawai Non PNS BLU',
                'percentage'     => 48,
                'effective_date' => '2023-01-01'
            ],
            [
                'title'          => 'P2 (PNS dan Non PNS)',
                'percentage'     => 28,
                'effective_date' => '2023-01-01'
            ],
            [
                'title'          => 'Biaya Operasional KP/Pembinaan',
                'percentage'     => 3,
                'effective_date' => '2023-01-01'
            ],
            [
                'title'          => 'Biaya Operasional dan Pemeliharaan Kantor',
                'percentage'     => 17,
                'effective_date' => '2023-01-01'
            ],
            [
                'title'          => 'Biaya Operasional Kabalai, TU, PSP dan SPI',
                'percentage'     => 3,
                'effective_date' => '2023-01-01'
            ],
            [
                'title'          => 'Cadangan Saldo Akhir/ SILPA',
                'percentage'     => 1,
                'effective_date' => '2023-01-01'
            ]
        ];
        
        
        collect($data)->each(function ($row) { PercentageSetting::create($row); });
        
    }
}