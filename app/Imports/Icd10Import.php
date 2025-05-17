<?php

namespace App\Imports;

use App\Models\Icd10;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class Icd10Import implements ToModel, WithHeadingRow, WithEvents
{
    public static function beforeImport(BeforeImport $event)
    {
        // Hapus semua data sebelum import
        Icd10::truncate();
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => [self::class, 'beforeImport'],
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Icd10([
            'code' => $row['code'],
            'description' => $row['display'],
            'version' => $row['version'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
