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
                'mak_code'=>'424915',
                'mak_name'=>'PENERIMAAN KEMBALI BELANJA BARANG BLU TAHUN ANGGARAN YANG LALU'
            ],
            [
                'mak_code'=>'425811',
                'mak_name'=>'PENDAPATAN DENDA PENYELESAIAN PEKERJAAN PEMERINTAH'
            ],
            [
                'mak_code'=>'525111',
                'mak_name'=>'BEBAN GAJI DAN TUNJANGAN'
            ],
            [
                'mak_code'=>'525112',
                'mak_name'=>'BEBAN BARANG'
            ],
            [
                'mak_code'=>'525113',
                'mak_name'=>'BEBAN JASA'
            ],
            [
                'mak_code'=>'525114',
                'mak_name'=>'BEBAN PEMELIHARAAN'
            ],
            [
                'mak_code'=>'525115',
                'mak_name'=>'BEBAN PERJALANAN'
            ],
            [
                'mak_code'=>'525119',
                'mak_name'=>'BEBAN PENYEDIAAN BARANG DAN JASA BLU LAINNYA'
            ],
            [
                'mak_code'=>'525121',
                'mak_name'=>'BELANJA BARANG PERSEDIAAN BARANG KONSUMSI / BLU'
            ],
            [
                'mak_code'=>'525125',
                'mak_name'=>'BELANJA BARANG PERSEDIAAN UNTUK DIJUAL/DISERAHKAN KEPADA MASYARAKAT - BLU'
            ],
            [
                'mak_code'=>'525143',
                'mak_name'=>'BELANJA JASA BLU KEPADA BLU LAIN DALAM SATU KEMENTERIAN NEGARA/LEMBAGA'
            ],
            [
                'mak_code'=>'537112',
                'mak_name'=>'BELANJA MODAL PERALATAN DAN MESIN'
            ],
            [
                'mak_code'=>'537113',
                'mak_name'=>'BELANJA MODAL GEDUNG DAN BANGUNAN'
            ],
            [
                'mak_code'=>'537115',
                'mak_name'=>'BELANJA MODAL FISIK LAINNYA BLU'
            ],
            [
                'mak_code'=>'537122',
                'mak_name'=>'BELANJA MODAL PERALATAN DAN MESIN BLU - PENANGANAN PANDEMI COVID 19'
            ]
        ];

        foreach($mak as $ma){
            Mak::create([
                'mak_code' => $ma['mak_code'], 
                'mak_name' => $ma['mak_name'],
            ]);
        }
        
    }
}