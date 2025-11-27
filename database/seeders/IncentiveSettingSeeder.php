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
            // Insentif Perawat per Jenis Pelayanan
            ['setting_key' => 'perawat_per_encounter_rawat_jalan', 'setting_value' => '10000', 'description' => 'Insentif untuk perawat per encounter Rawat Jalan'],
            ['setting_key' => 'perawat_per_encounter_igd', 'setting_value' => '15000', 'description' => 'Insentif untuk perawat per encounter IGD'],
            ['setting_key' => 'perawat_per_encounter_rawat_inap', 'setting_value' => '20000', 'description' => 'Insentif untuk perawat per tindakan Rawat Inap'],

            // Insentif Dokter
            ['setting_key' => 'dokter_per_encounter', 'setting_value' => '25000', 'description' => 'Insentif untuk dokter per encounter (RJ/IGD) - flat Rupiah'],
            ['setting_key' => 'visit_inap', 'setting_value' => '50000', 'description' => 'Insentif untuk dokter per visit rawat inap - flat Rupiah'],

            // Pengaturan Umum
            ['setting_key' => 'cutoff_day', 'setting_value' => '25', 'description' => 'Tanggal batas (cut-off) untuk perhitungan gaji dan insentif bulanan.'],

            // Backward compatibility (deprecated)
            ['setting_key' => 'perawat_per_encounter', 'setting_value' => '10000', 'description' => 'DEPRECATED: Gunakan perawat_per_encounter_rawat_jalan'],
            // Penunjang - dukung flat / persen per jenis
            ['setting_key' => 'fee_lab_mode', 'setting_value' => '1', 'description' => 'Mode fee Lab: 0=flat, 1=percent'],
            ['setting_key' => 'fee_lab_value', 'setting_value' => '10', 'description' => 'Nilai fee Lab (Rupiah jika flat, persen jika percent)'],
            ['setting_key' => 'fee_radiologi_mode', 'setting_value' => '1', 'description' => 'Mode fee Radiologi: 0=flat, 1=percent'],
            ['setting_key' => 'fee_radiologi_value', 'setting_value' => '10', 'description' => 'Nilai fee Radiologi (Rupiah jika flat, persen jika percent)'],
            // Farmasi / Obat
            ['setting_key' => 'fee_obat_mode', 'setting_value' => '1', 'description' => 'Mode fee Obat: 0=flat, 1=percent'],
            ['setting_key' => 'fee_obat_value', 'setting_value' => '5', 'description' => 'Nilai fee Obat (Rupiah jika flat, persen jika percent)'],
            ['setting_key' => 'fee_obat_target_mode', 'setting_value' => '0', 'description' => 'Target fee obat: 0=DPJP (practitioner pertama), 1=prescriber'],
            // Backward compatibility key (optional)
            ['setting_key' => 'fee_dokter_penunjang', 'setting_value' => '10', 'description' => 'Deprecated: gunakan fee_lab_* atau fee_radiologi_*'],
        ];

        foreach ($settings as $setting) {
            \App\Models\IncentiveSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
