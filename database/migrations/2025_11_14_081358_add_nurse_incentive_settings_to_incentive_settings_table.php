<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\IncentiveSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new incentive settings for nurses by service type
        $settings = [
            [
                'setting_key' => 'perawat_per_encounter_rawat_jalan',
                'setting_value' => '10000',
                'description' => 'Insentif untuk perawat per encounter Rawat Jalan - flat Rupiah'
            ],
            [
                'setting_key' => 'perawat_per_encounter_igd',
                'setting_value' => '15000',
                'description' => 'Insentif untuk perawat per encounter IGD - flat Rupiah'
            ],
            [
                'setting_key' => 'perawat_per_encounter_rawat_inap',
                'setting_value' => '20000',
                'description' => 'Insentif untuk perawat per tindakan Rawat Inap - flat Rupiah'
            ],
            [
                'setting_key' => 'perawat_fee_radiologi_mode',
                'setting_value' => '1',
                'description' => 'Mode fee radiologi untuk perawat: 0=flat, 1=persentase'
            ],
            [
                'setting_key' => 'perawat_fee_radiologi_value',
                'setting_value' => '5',
                'description' => 'Nilai fee radiologi untuk perawat (Rupiah atau %)'
            ],
        ];

        foreach ($settings as $setting) {
            IncentiveSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                [
                    'setting_value' => $setting['setting_value'],
                    'description' => $setting['description']
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        IncentiveSetting::whereIn('setting_key', [
            'perawat_per_encounter_rawat_jalan',
            'perawat_per_encounter_igd',
            'perawat_per_encounter_rawat_inap',
            'perawat_fee_radiologi_mode',
            'perawat_fee_radiologi_value'
        ])->delete();
    }
};
