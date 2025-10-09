<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pendidikan;

class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            'Tidak/Belum Sekolah',
            'TK/PAUD',
            'SD/MI',
            'SMP/MTS',
            'SMA/SMK/MA',
            'D1', 'D2', 'D3',
            'S1', 'S2', 'S3',
        ];

        foreach ($levels as $name) {
            Pendidikan::firstOrCreate(['name' => $name]);
        }
    }
}
