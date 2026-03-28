<?php

namespace App\Imports;

use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PasienImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected int $imported = 0;
    protected int $skipped = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $name = trim($row['nama_pasien'] ?? '');
            if (empty($name)) {
                $this->skipped++;
                continue;
            }

            $rekamMedis = trim($row['nomr'] ?? '');

            // Skip if no MR or rekam_medis already exists
            if (empty($rekamMedis) || Pasien::where('rekam_medis', $rekamMedis)->exists()) {
                $this->skipped++;
                continue;
            }

            // Calculate approximate birth date from age
            $usia = intval($row['usia'] ?? 0);
            $tglLahir = $usia > 0
                ? Carbon::now()->subYears($usia)->startOfYear()->toDateString()
                : '1990-01-01';

            $noHpMedan = trim($row['nohp_medan'] ?? '');
            $noHpLuar = trim($row['nohp_luar'] ?? '');

            Pasien::create([
                'rekam_medis' => $rekamMedis,
                'name'        => strtoupper($name),
                'tgl_lahir'   => $tglLahir,
                'alamat'      => trim($row['alamat'] ?? '') ?: null,
                'no_hp'       => $noHpMedan ?: null,
                'no_telepon'  => $noHpLuar ?: null,
            ]);

            $this->imported++;
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }
}
