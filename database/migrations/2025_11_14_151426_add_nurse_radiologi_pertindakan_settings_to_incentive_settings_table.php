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
        // Add new settings for radiologi pertindakan (non-Echo/USG)
        $settings = [
            [
                'setting_key' => 'perawat_fee_radiologi_pertindakan_value',
                'setting_value' => '15000',
                'description' => 'Fee perawat untuk radiologi per tindakan (selain Echo/USG) - flat Rupiah'
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
            'perawat_fee_radiologi_pertindakan_value'
        ])->delete();
    }
};
