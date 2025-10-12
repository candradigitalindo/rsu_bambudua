<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Tablet', 'abbrev' => 'tab'],
            ['name' => 'Kapsul', 'abbrev' => 'kaps'],
            ['name' => 'Botol', 'abbrev' => 'btl'],
            ['name' => 'Sachet', 'abbrev' => 'sch'],
            ['name' => 'Vial', 'abbrev' => 'vial'],
            ['name' => 'Ampul', 'abbrev' => 'amp'],
            ['name' => 'Tube', 'abbrev' => 'tube'],
            ['name' => 'Strip', 'abbrev' => 'strip'],
            ['name' => 'Box', 'abbrev' => 'box'],
            ['name' => 'Dus', 'abbrev' => 'dus'],
            ['name' => 'Pack', 'abbrev' => 'pack'],
            ['name' => 'Pot', 'abbrev' => 'pot'],
            ['name' => 'Flakon', 'abbrev' => 'flk'],
            ['name' => 'Ml', 'abbrev' => 'ml'],
            ['name' => 'Mg', 'abbrev' => 'mg'],
            ['name' => 'Gram', 'abbrev' => 'gr'],
            ['name' => 'Kg', 'abbrev' => 'kg'],
            ['name' => 'Unit', 'abbrev' => 'unit'],
            ['name' => 'IU', 'abbrev' => 'iu'],
            ['name' => 'Pcs', 'abbrev' => 'pcs'],
            ['name' => 'Buah', 'abbrev' => 'bh'],
            ['name' => 'Lembar', 'abbrev' => 'lbr'],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'id' => Str::uuid(),
                'name' => $unit['name'],
                'abbrev' => $unit['abbrev'],
                'active' => true,
            ]);
        }
    }
}
