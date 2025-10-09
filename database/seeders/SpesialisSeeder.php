<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Spesialis;

class SpesialisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'ANA', 'name' => 'Anestesiologi'],
            ['kode' => 'INT', 'name' => 'Penyakit Dalam'],
            ['kode' => 'BED', 'name' => 'Bedah'],
            ['kode' => 'OBG', 'name' => 'Obstetri & Ginekologi'],
            ['kode' => 'PED', 'name' => 'Anak'],
            ['kode' => 'SAR', 'name' => 'Neurologi (Saraf)'],
            ['kode' => 'KAR', 'name' => 'Kardiologi'],
            ['kode' => 'THT', 'name' => 'THT-KL'],
            ['kode' => 'MAT', 'name' => 'Mata'],
            ['kode' => 'PAR', 'name' => 'Pulmonologi (Paru)'],
            ['kode' => 'RAD', 'name' => 'Radiologi'],
            ['kode' => 'PAT', 'name' => 'Patologi Klinik'],
            ['kode' => 'SKL', 'name' => 'Kulit & Kelamin'],
            ['kode' => 'REH', 'name' => 'Rehabilitasi Medik'],
            ['kode' => 'GIG', 'name' => 'Gigi & Mulut'],
        ];

        foreach ($data as $row) {
            Spesialis::updateOrCreate(['kode' => $row['kode']], $row);
        }
    }
}
