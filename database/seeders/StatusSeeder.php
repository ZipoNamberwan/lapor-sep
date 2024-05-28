<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::create([
            'name' => 'Belum Dicacah',
            'code' => '-',
            'color' => 'warning'
        ]);
        Status::create([
            'name' => 'Sedang Dicacah',
            'code' => '-',
            'color' => 'warning'
        ]);
        Status::create([
            'name' => 'Tidak/belum produksi',
            'code' => '1',
            'color' => 'danger'
        ]);
        Status::create([
            'name' => 'Bukan usaha pertanian perorangan',
            'code' => '2',
            'color' => 'danger'
        ]);
        Status::create([
            'name' => 'Pindah ke luar blok sensus',
            'code' => '3',
            'color' => 'danger'
        ]);
        Status::create([
            'name' => 'Tidak dapat diwawancarai',
            'code' => '4',
            'color' => 'danger'
        ]);
        Status::create([
            'name' => 'Menolak dicacah',
            'code' => '5',
            'color' => 'danger'
        ]);
        Status::create([
            'name' => 'Tidak ditemukan',
            'code' => '6',
            'color' => 'danger'
        ]);
        Status::create([
            'name' => 'Berhasil dicacah',
            'code' => '7',
            'color' => 'success'
        ]);
    }
}
