<?php

namespace Database\Seeders;

use App\Models\Sample;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sample::create([
            'no' => 1,
            'name' => 'amin',
            'type' => 'Utama',
            'is_selected' => true,
            'bs_id' => 1,
            'status_id' => 1,
        ]);

        Sample::create([
            'no' => 2,
            'name' => 'amin',
            'type' => 'Utama',
            'is_selected' => true,
            'bs_id' => 1,
            'status_id' => 1,
        ]);

        Sample::create([
            'no' => 3,
            'name' => 'indra',
            'type' => 'Utama',
            'is_selected' => true,
            'bs_id' => 1,
            'status_id' => 1,
        ]);

        Sample::create([
            'no' => 4,
            'name' => 'irien',
            'type' => 'Utama',
            'is_selected' => true,
            'bs_id' => 1,
            'status_id' => 1,
        ]);

        Sample::create([
            'no' => 5,
            'name' => 'doni',
            'type' => 'Utama',
            'is_selected' => true,
            'bs_id' => 1,
            'status_id' => 1,
        ]);

        Sample::create([
            'no' => 6,
            'name' => 'eko',
            'type' => 'Utama',
            'is_selected' => true,
            'bs_id' => 1,
            'status_id' => 1,
        ]);

        Sample::create([
            'no' => 9,
            'name' => 'cadangan 1',
            'type' => 'Cadangan',
            'is_selected' => false,
            'bs_id' => 1,
            'status_id' => 1,
        ]);

        Sample::create([
            'no' => 10,
            'name' => 'cadangan 2',
            'type' => 'Cadangan',
            'is_selected' => false,
            'bs_id' => 1,
            'status_id' => 1,
        ]);
    }
}
