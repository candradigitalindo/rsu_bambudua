<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryRuangan;

class CategoryRuanganSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Rawat Jalan', 'description' => 'Poliklinik rawat jalan'],
            ['name' => 'IGD', 'description' => 'Instalasi gawat darurat'],
            ['name' => 'Rawat Inap', 'description' => 'Kamar perawatan rawat inap'],
            ['name' => 'Penunjang - Laboratorium', 'description' => 'Laboratorium klinik'],
            ['name' => 'Penunjang - Radiologi', 'description' => 'Instalasi radiologi'],
            ['name' => 'Tindakan/Prosedur', 'description' => 'Ruang tindakan/prosedur'],
            ['name' => 'Farmasi', 'description' => 'Apotek/Instalasi Farmasi'],
            ['name' => 'Administrasi', 'description' => 'Administrasi/Kasir/Registrasi'],
        ];

        foreach ($categories as $cat) {
            CategoryRuangan::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
