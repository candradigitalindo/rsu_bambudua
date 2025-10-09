<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jenisjaminan;

class JenisJaminanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Umum/Tunai',        'status' => 1],
            ['name' => 'BPJS Kesehatan',    'status' => 1],
            ['name' => 'Asuransi Swasta',   'status' => 1],
            ['name' => 'Perusahaan/Korporat','status' => 1],
        ];

        foreach ($data as $row) {
            Jenisjaminan::updateOrCreate(['name' => $row['name']], $row);
        }
    }
}
