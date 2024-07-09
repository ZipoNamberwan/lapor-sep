<?php

namespace Database\Seeders;

use App\Models\Bs;
use App\Models\BsEdcod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BsEdcodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bs = Bs::all();
        foreach ($bs as $b) {
            $string = $b->long_code;
            $prefix = '3578';
            if (substr($string, 0, strlen($prefix)) !== $prefix) {
                BsEdcod::create([
                    'bs_id' => $b->id,
                    'edcoded' => 0,
                    'short_code' => $b->short_code,
                    'long_code' => $b->long_code,
                ]);
            }
        }
    }
}
