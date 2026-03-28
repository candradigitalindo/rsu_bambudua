<?php

namespace App\Console\Commands;

use App\Imports\PasienImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportPasienCommand extends Command
{
    protected $signature = 'import:pasien {file? : Path to Excel file}';
    protected $description = 'Import data pasien dari file Excel';

    public function handle(): int
    {
        $file = $this->argument('file')
            ?? base_path('Data Pasien (Bambu 2 Clinic ) (1).xlsx');

        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        $this->info("Mengimpor data pasien dari: {$file}");
        $this->info('Mohon tunggu, ini memproses ~19.000 data...');

        $import = new PasienImport();
        Excel::import($import, $file);

        $this->newLine();
        $this->info("Import selesai!");
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Berhasil diimpor', $import->getImported()],
                ['Dilewati (duplikat/kosong)', $import->getSkipped()],
            ]
        );

        return self::SUCCESS;
    }
}
