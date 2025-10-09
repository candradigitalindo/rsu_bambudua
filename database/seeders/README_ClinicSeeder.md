# Clinic Seeder Documentation

## Overview
ClinicSeeder adalah seeder yang digunakan untuk mengisi tabel `clinics` dengan data poliklinik yang umum ditemukan di klinik atau rumah sakit.

## Data Poliklinik yang Disediakan

Seeder ini akan menambahkan 20 jenis poliklinik dengan data lengkap:

### Poliklinik Umum & Dasar
1. **Poliklinik Umum** - Pelayanan kesehatan umum untuk berbagai keluhan dan pemeriksaan rutin
2. **Poliklinik Anak** - Pelayanan kesehatan khusus untuk bayi, anak-anak, dan remaja
3. **Poliklinik Gigi & Mulut** - Pelayanan kesehatan gigi dan mulut, termasuk perawatan dan pencabutan gigi

### Poliklinik Spesialis
4. **Poliklinik Kandungan & Kebidanan** - Pelayanan kesehatan ibu hamil, persalinan, dan kesehatan reproduksi wanita
5. **Poliklinik Mata** - Pelayanan kesehatan mata, pemeriksaan visus, dan konsultasi gangguan penglihatan
6. **Poliklinik THT** - Pelayanan kesehatan telinga, hidung, dan tenggorokan
7. **Poliklinik Kulit & Kelamin** - Pelayanan kesehatan kulit dan penyakit kelamin
8. **Poliklinik Jantung** - Pelayanan kesehatan jantung dan pembuluh darah, EKG, dan konsultasi kardiologi
9. **Poliklinik Penyakit Dalam** - Pelayanan untuk penyakit dalam seperti diabetes, hipertensi, dan penyakit metabolik
10. **Poliklinik Saraf** - Pelayanan kesehatan sistem saraf dan gangguan neurologis

### Poliklinik Bedah & Orthopedi
11. **Poliklinik Bedah** - Konsultasi bedah, tindakan bedah minor, dan perawatan luka
12. **Poliklinik Orthopedi** - Pelayanan kesehatan tulang, sendi, dan otot
13. **Poliklinik Urologi** - Pelayanan kesehatan sistem kemih dan reproduksi pria

### Poliklinik Khusus
14. **Poliklinik Psikiatri** - Pelayanan kesehatan mental dan konseling psikologi
15. **Poliklinik Gizi** - Konsultasi gizi dan diet untuk berbagai kondisi kesehatan
16. **Poliklinik Geriatri** - Pelayanan kesehatan khusus untuk lansia dan geriatri
17. **Poliklinik Fisioterapi** - Pelayanan terapi fisik dan rehabilitasi medik

### Poliklinik Organ Khusus
18. **Poliklinik Paru** - Pelayanan kesehatan paru-paru dan saluran pernapasan
19. **Poliklinik Ginjal & Hipertensi** - Pelayanan kesehatan ginjal dan penanganan hipertensi
20. **Poliklinik Endokrin** - Pelayanan gangguan hormonal dan kelenjar endokrin

## Struktur Data

Setiap poliklinik memiliki data:
- **ID**: UUID yang di-generate otomatis
- **Nama**: Nama poliklinik
- **Alamat**: Lokasi poliklinik dalam gedung
- **Telepon**: Nomor telepon ekstensi poliklinik
- **Deskripsi**: Penjelasan singkat layanan yang tersedia
- **Created_at & Updated_at**: Timestamp

## Cara Menjalankan

### Menjalankan ClinicSeeder saja:
```bash
php artisan db:seed --class=ClinicSeeder
```

### Menjalankan semua seeder (termasuk ClinicSeeder):
```bash
php artisan db:seed
```

### Menjalankan dengan refresh database:
```bash
php artisan migrate:fresh --seed
```

## Verifikasi Data

Untuk mengecek apakah data sudah berhasil ditambahkan:

```bash
# Mengecek jumlah total poliklinik
php artisan tinker --execute="echo 'Total Poliklinik: ' . App\Models\Clinic::count();"

# Melihat 5 poliklinik pertama
php artisan tinker --execute="App\Models\Clinic::take(5)->get(['nama', 'alamat'])->each(function(\$clinic) { echo \$clinic->nama . ' - ' . \$clinic->alamat . PHP_EOL; });"
```

## Catatan

1. Seeder ini menggunakan UUID untuk primary key sesuai dengan struktur tabel `clinics`
2. Nomor telepon menggunakan format yang konsisten dengan nomor utama klinik (061) 6610112-131
3. Lokasi alamat menggunakan gedung dan lantai yang realistis untuk organisasi klinik
4. Seeder sudah terintegrasi dengan `DatabaseSeeder` utama
5. Data tidak akan duplikat jika dijalankan berulang kali karena menggunakan `create()` method

## Dependencies

- Laravel Framework
- Model `App\Models\Clinic`
- UUID Helper dari `Illuminate\Support\Str`