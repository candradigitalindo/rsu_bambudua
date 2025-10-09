<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pekerjaan;

class PekerjaanSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            'PNS/ASN',
            'TNI/Polri',
            'Karyawan Swasta',
            'Wiraswasta/Usahawan',
            'Petani',
            'Nelayan',
            'Guru/Dosen',
            'Dokter/Perawat/Nakes',
            'Pelajar/Mahasiswa',
            'Ibu Rumah Tangga',
            'Pensiunan',
            'Tidak Bekerja',
        ];

        foreach ($jobs as $name) {
            Pekerjaan::firstOrCreate(['name' => $name]);
        }
    }
}
