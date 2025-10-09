<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;
use App\Models\CategoryRuangan;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure core categories exist
        $categories = [
            'Rawat Jalan' => 'RJ',
            'IGD' => 'IGD',
            'Rawat Inap' => 'RI',
            'Penunjang - Laboratorium' => 'LAB',
            'Penunjang - Radiologi' => 'RAD',
        ];

        $catMap = [];
        foreach ($categories as $name => $prefix) {
            $cat = CategoryRuangan::firstOrCreate(['name' => $name], ['description' => $name]);
            $catMap[$name] = [$cat->id, $prefix];
        }

        $rooms = [
            // Rawat Jalan
            ['no_kamar' => 'RJ-UMUM-01', 'description' => 'Poli Umum 01', 'category' => 'Rawat Jalan', 'harga' => 0, 'class' => null, 'capacity' => 1],
            ['no_kamar' => 'RJ-ANAK-01', 'description' => 'Poli Anak 01', 'category' => 'Rawat Jalan', 'harga' => 0, 'class' => null, 'capacity' => 1],
            ['no_kamar' => 'RJ-OBGYN-01', 'description' => 'Poli Kebidanan & Kandungan 01', 'category' => 'Rawat Jalan', 'harga' => 0, 'class' => null, 'capacity' => 1],

            // IGD
            ['no_kamar' => 'IGD-A', 'description' => 'Ruang IGD A', 'category' => 'IGD', 'harga' => 0, 'class' => null, 'capacity' => 4],
            ['no_kamar' => 'IGD-B', 'description' => 'Ruang IGD B', 'category' => 'IGD', 'harga' => 0, 'class' => null, 'capacity' => 4],

            // Rawat Inap (lengkapi class & harga)
            ['no_kamar' => 'RI-VIP-01', 'description' => 'Kamar VIP 01', 'category' => 'Rawat Inap', 'harga' => 500000, 'class' => 'VIP', 'capacity' => 1],
            ['no_kamar' => 'RI-I-01', 'description' => 'Kamar Kelas I - 01', 'category' => 'Rawat Inap', 'harga' => 300000, 'class' => 'I', 'capacity' => 2],
            ['no_kamar' => 'RI-II-01', 'description' => 'Kamar Kelas II - 01', 'category' => 'Rawat Inap', 'harga' => 200000, 'class' => 'II', 'capacity' => 3],
            ['no_kamar' => 'RI-III-01', 'description' => 'Kamar Kelas III - 01', 'category' => 'Rawat Inap', 'harga' => 100000, 'class' => 'III', 'capacity' => 4],

            // Penunjang
            ['no_kamar' => 'LAB-01', 'description' => 'Laboratorium Klinik 01', 'category' => 'Penunjang - Laboratorium', 'harga' => 0, 'class' => null, 'capacity' => 2],
            ['no_kamar' => 'RAD-01', 'description' => 'Radiologi 01', 'category' => 'Penunjang - Radiologi', 'harga' => 0, 'class' => null, 'capacity' => 2],
        ];

        foreach ($rooms as $r) {
            [$categoryId, $prefix] = $catMap[$r['category']];
            Ruangan::updateOrCreate(
                ['no_kamar' => $r['no_kamar']],
                [
                    'description' => $r['description'],
                    'category_id' => $categoryId,
                    'harga' => $r['harga'],
                    'class' => $r['class'],
                    'capacity' => $r['capacity'],
                ]
            );
        }
    }
}
