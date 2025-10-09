<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClinicSeederSafe extends Seeder
{
    /**
     * Run the database seeds.
     * This version uses firstOrCreate to prevent duplicates
     */
    public function run(): void
    {
        $clinics = [
            [
                'nama' => 'Poliklinik Umum',
                'alamat' => 'Gedung Utama Lantai 1',
                'telepon' => '(061) 6610112',
                'deskripsi' => 'Pelayanan kesehatan umum untuk berbagai keluhan dan pemeriksaan rutin'
            ],
            [
                'nama' => 'Poliklinik Anak',
                'alamat' => 'Gedung Utama Lantai 1',
                'telepon' => '(061) 6610113',
                'deskripsi' => 'Pelayanan kesehatan khusus untuk bayi, anak-anak, dan remaja'
            ],
            [
                'nama' => 'Poliklinik Kandungan & Kebidanan',
                'alamat' => 'Gedung Utama Lantai 2',
                'telepon' => '(061) 6610114',
                'deskripsi' => 'Pelayanan kesehatan ibu hamil, persalinan, dan kesehatan reproduksi wanita'
            ],
            [
                'nama' => 'Poliklinik Gigi & Mulut',
                'alamat' => 'Gedung Utama Lantai 1',
                'telepon' => '(061) 6610115',
                'deskripsi' => 'Pelayanan kesehatan gigi dan mulut, termasuk perawatan dan pencabutan gigi'
            ],
            [
                'nama' => 'Poliklinik Mata',
                'alamat' => 'Gedung Utama Lantai 2',
                'telepon' => '(061) 6610116',
                'deskripsi' => 'Pelayanan kesehatan mata, pemeriksaan visus, dan konsultasi gangguan penglihatan'
            ],
            [
                'nama' => 'Poliklinik THT',
                'alamat' => 'Gedung Utama Lantai 2',
                'telepon' => '(061) 6610117',
                'deskripsi' => 'Pelayanan kesehatan telinga, hidung, dan tenggorokan'
            ],
            [
                'nama' => 'Poliklinik Kulit & Kelamin',
                'alamat' => 'Gedung Utama Lantai 2',
                'telepon' => '(061) 6610118',
                'deskripsi' => 'Pelayanan kesehatan kulit dan penyakit kelamin'
            ],
            [
                'nama' => 'Poliklinik Jantung',
                'alamat' => 'Gedung Utama Lantai 3',
                'telepon' => '(061) 6610119',
                'deskripsi' => 'Pelayanan kesehatan jantung dan pembuluh darah, EKG, dan konsultasi kardiologi'
            ],
            [
                'nama' => 'Poliklinik Penyakit Dalam',
                'alamat' => 'Gedung Utama Lantai 2',
                'telepon' => '(061) 6610120',
                'deskripsi' => 'Pelayanan untuk penyakit dalam seperti diabetes, hipertensi, dan penyakit metabolik'
            ],
            [
                'nama' => 'Poliklinik Saraf',
                'alamat' => 'Gedung Utama Lantai 3',
                'telepon' => '(061) 6610121',
                'deskripsi' => 'Pelayanan kesehatan sistem saraf dan gangguan neurologis'
            ],
            [
                'nama' => 'Poliklinik Bedah',
                'alamat' => 'Gedung Utama Lantai 3',
                'telepon' => '(061) 6610122',
                'deskripsi' => 'Konsultasi bedah, tindakan bedah minor, dan perawatan luka'
            ],
            [
                'nama' => 'Poliklinik Orthopedi',
                'alamat' => 'Gedung Utama Lantai 3',
                'telepon' => '(061) 6610123',
                'deskripsi' => 'Pelayanan kesehatan tulang, sendi, dan otot'
            ],
            [
                'nama' => 'Poliklinik Psikiatri',
                'alamat' => 'Gedung Utama Lantai 3',
                'telepon' => '(061) 6610124',
                'deskripsi' => 'Pelayanan kesehatan mental dan konseling psikologi'
            ],
            [
                'nama' => 'Poliklinik Gizi',
                'alamat' => 'Gedung Utama Lantai 1',
                'telepon' => '(061) 6610125',
                'deskripsi' => 'Konsultasi gizi dan diet untuk berbagai kondisi kesehatan'
            ],
            [
                'nama' => 'Poliklinik Geriatri',
                'alamat' => 'Gedung Utama Lantai 2',
                'telepon' => '(061) 6610126',
                'deskripsi' => 'Pelayanan kesehatan khusus untuk lansia dan geriatri'
            ],
            [
                'nama' => 'Poliklinik Fisioterapi',
                'alamat' => 'Gedung Rehabilitasi Lantai 1',
                'telepon' => '(061) 6610127',
                'deskripsi' => 'Pelayanan terapi fisik dan rehabilitasi medik'
            ],
            [
                'nama' => 'Poliklinik Paru',
                'alamat' => 'Gedung Utama Lantai 2',
                'telepon' => '(061) 6610128',
                'deskripsi' => 'Pelayanan kesehatan paru-paru dan saluran pernapasan'
            ],
            [
                'nama' => 'Poliklinik Urologi',
                'alamat' => 'Gedung Utama Lantai 3',
                'telepon' => '(061) 6610129',
                'deskripsi' => 'Pelayanan kesehatan sistem kemih dan reproduksi pria'
            ],
            [
                'nama' => 'Poliklinik Ginjal & Hipertensi',
                'alamat' => 'Gedung Utama Lantai 3',
                'telepon' => '(061) 6610130',
                'deskripsi' => 'Pelayanan kesehatan ginjal dan penanganan hipertensi'
            ],
            [
                'nama' => 'Poliklinik Endokrin',
                'alamat' => 'Gedung Utama Lantai 2',
                'telepon' => '(061) 6610131',
                'deskripsi' => 'Pelayanan gangguan hormonal dan kelenjar endokrin'
            ]
        ];

        $created = 0;
        $existing = 0;

        foreach ($clinics as $clinicData) {
            $clinic = Clinic::firstOrCreate(
                ['nama' => $clinicData['nama']], // Kriteria pencarian
                [
                    'id' => Str::uuid(),
                    'alamat' => $clinicData['alamat'],
                    'telepon' => $clinicData['telepon'],
                    'deskripsi' => $clinicData['deskripsi'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            if ($clinic->wasRecentlyCreated) {
                $created++;
                $this->command->info("✓ Poliklinik '{$clinicData['nama']}' berhasil ditambahkan.");
            } else {
                $existing++;
                $this->command->warn("- Poliklinik '{$clinicData['nama']}' sudah ada, dilewati.");
            }
        }

        $this->command->info("Selesai! {$created} poliklinik baru ditambahkan, {$existing} sudah ada sebelumnya.");
    }
}