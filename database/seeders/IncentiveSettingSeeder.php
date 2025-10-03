<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncentiveSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['setting_key' => 'perawat_per_encounter', 'setting_value' => '10000', 'description' => 'Insentif untuk perawat per encounter (RJ/IGD)'],
            ['setting_key' => 'dokter_per_encounter', 'setting_value' => '25000', 'description' => 'Insentif untuk dokter per encounter (RJ/IGD)'],
            ['setting_key' => 'visit_inap', 'setting_value' => '50000', 'description' => 'Insentif untuk dokter per visit rawat inap'],
            ['setting_key' => 'cutoff_day', 'setting_value' => '25', 'description' => 'Tanggal batas (cut-off) untuk perhitungan gaji dan insentif bulanan.'],
            ['setting_key' => 'fee_dokter_penunjang', 'setting_value' => '10', 'description' => 'Insentif (persen) untuk dokter yang menginput pemeriksaan penunjang (Lab/Radiologi)'],
        ];

        foreach ($settings as $setting) {
            \App\Models\IncentiveSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
