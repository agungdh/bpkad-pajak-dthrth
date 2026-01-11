<?php

namespace Database\Seeders;

use App\Models\KodePajak;
use Illuminate\Database\Seeder;

class KodePajakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kodePajaks = [
            ['kode' => '1.1.01', 'nama' => 'Pajak Kendaraan Bermotor (PKB)'],
            ['kode' => '1.1.02', 'nama' => 'Bea Balik Nama Kendaraan Bermotor (BBNKB)'],
            ['kode' => '1.1.03', 'nama' => 'Pajak Bahan Bakar Kendaraan Bermotor (PBBKB)'],
            ['kode' => '1.1.04', 'nama' => 'Pajak Air Permukaan'],
            ['kode' => '1.1.05', 'nama' => 'Pajak Rokok'],
            ['kode' => '1.2.01', 'nama' => 'Pajak Hotel'],
            ['kode' => '1.2.02', 'nama' => 'Pajak Restoran'],
            ['kode' => '1.2.03', 'nama' => 'Pajak Hiburan'],
            ['kode' => '1.2.04', 'nama' => 'Pajak Reklame'],
            ['kode' => '1.2.05', 'nama' => 'Pajak Penerangan Jalan'],
            ['kode' => '1.2.06', 'nama' => 'Pajak Parkir'],
            ['kode' => '1.2.07', 'nama' => 'Pajak Air Tanah'],
            ['kode' => '1.2.08', 'nama' => 'Pajak Sarang Burung Walet'],
            ['kode' => '1.2.09', 'nama' => 'Pajak Mineral Bukan Logam dan Batuan'],
            ['kode' => '1.3.01', 'nama' => 'Pajak Bumi dan Bangunan Perdesaan dan Perkotaan (PBB-P2)'],
            ['kode' => '1.3.02', 'nama' => 'Bea Perolehan Hak atas Tanah dan Bangunan (BPHTB)'],
        ];

        foreach ($kodePajaks as $data) {
            KodePajak::firstOrCreate(
                ['kode' => $data['kode']],
                $data
            );
        }
    }
}
