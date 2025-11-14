<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\RadiologyRequest;
use App\Models\Encounter;
use App\Models\Incentive;
use App\Models\IncentiveSetting;
use Illuminate\Support\Str;

class GenerateRetroactiveNurseRadiologyIncentive extends Seeder
{
    /**
     * Run the database seeds.
     * Generate retroactive nurse radiology incentives for completed and paid radiology requests
     */
    public function run(): void
    {
        echo "🔍 Mencari radiology requests yang sudah completed dan dibayar...\n";

        // Get all completed radiology requests with paid encounters
        $radiologyRequests = RadiologyRequest::with(['jenis', 'encounter.nurses'])
            ->where('status', 'completed')
            ->whereHas('encounter', function ($q) {
                $q->where('status_bayar_tindakan', 1);
            })
            ->get();

        echo "📊 Ditemukan {$radiologyRequests->count()} radiology requests\n";

        // Get settings
        $mode = (int) IncentiveSetting::where('setting_key', 'perawat_fee_radiologi_mode')->value('setting_value') ?? 1;
        $val = (float) IncentiveSetting::where('setting_key', 'perawat_fee_radiologi_value')->value('setting_value') ?? 0;
        $pertindakanValue = (float) IncentiveSetting::where('setting_key', 'perawat_fee_radiologi_pertindakan_value')->value('setting_value') ?? 0;

        echo "⚙️  Settings:\n";
        echo "   - Echo/USG Mode: " . ($mode == 0 ? 'Flat' : 'Persentase') . "\n";
        echo "   - Echo/USG Value: " . $val . ($mode == 0 ? ' Rupiah' : '%') . "\n";
        echo "   - Radiologi Lainnya: Rp " . number_format($pertindakanValue, 0, ',', '.') . "\n\n";

        $created = 0;
        $skipped = 0;

        foreach ($radiologyRequests as $request) {
            $encounter = $request->encounter;
            $nurses = $encounter->nurses;

            if (!$nurses || $nurses->isEmpty()) {
                $skipped++;
                continue;
            }

            $namaPemeriksaan = optional($request->jenis)->name ?? 'Radiologi';
            $hargaPemeriksaan = (float) $request->price;

            // Check if this is Echo or USG
            $isEchoOrUSG = (
                stripos($namaPemeriksaan, 'echo') !== false ||
                stripos($namaPemeriksaan, 'usg') !== false ||
                stripos($namaPemeriksaan, 'ultraso') !== false
            );

            if ($isEchoOrUSG) {
                // Echo/USG: Calculate based on mode
                if ($mode == 0) {
                    // Flat
                    $amount = $val;
                } else {
                    // Percentage
                    $amount = $hargaPemeriksaan * ($val / 100);
                }
            } else {
                // Other radiology: Flat per tindakan
                $amount = $pertindakanValue;
            }

            if ($amount <= 0) {
                $skipped++;
                continue;
            }

            foreach ($nurses as $nurse) {
                // Check if incentive already exists
                $exists = Incentive::where('user_id', $nurse->id)
                    ->where('type', 'fee_perawat_radiologi')
                    ->where('description', 'like', "%$namaPemeriksaan%")
                    ->where('description', 'like', "%{$encounter->name_pasien}%")
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $description = "Fee Radiologi Perawat ($namaPemeriksaan) untuk " . $encounter->name_pasien;

                Incentive::create([
                    'id' => Str::uuid(),
                    'user_id' => $nurse->id,
                    'amount' => $amount,
                    'type' => 'fee_perawat_radiologi',
                    'description' => $description,
                    'year' => $request->created_at->year,
                    'month' => $request->created_at->month,
                    'status' => 'pending',
                ]);

                $created++;
                echo "✅ Created incentive for {$nurse->name} - {$namaPemeriksaan} - Rp " . number_format($amount, 0, ',', '.') . "\n";
            }
        }

        echo "\n📈 Summary:\n";
        echo "   - Total Radiology Requests: {$radiologyRequests->count()}\n";
        echo "   - Incentives Created: {$created}\n";
        echo "   - Skipped: {$skipped}\n";
        echo "\n✅ Selesai!\n";
    }
}
