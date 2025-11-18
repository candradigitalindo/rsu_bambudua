# Fitur Modal Hasil Lab dan Radiologi

## Overview

Fitur ini menambahkan modal untuk menampilkan hasil pemeriksaan laboratorium dan radiologi lengkap dalam format yang mirip dengan format cetak, langsung dari halaman observasi.

## Implementasi

### 1. Frontend (Blade Template)

**File**: `resources/views/pages/observasi/partials/_anamnesis_ttv.blade.php`

#### A. Modal HTML Structure

Ditambahkan 2 modal Bootstrap:

1. **Modal Hasil Lab** (`#modalLabResults`)

    - Header dengan icon flask (ri-flask-line) dan background warning-subtle
    - Body dengan loading spinner dan area konten dinamis
    - Footer dengan tombol Tutup dan Cetak

2. **Modal Hasil Radiologi** (`#modalRadioResults`)
    - Header dengan icon scan (ri-scan-line) dan background info-subtle
    - Body dengan loading spinner dan area konten dinamis
    - Footer dengan tombol Tutup dan Cetak

#### B. JavaScript Functions

Ditambahkan 4 fungsi JavaScript:

1. **`viewLabResults(labRequestId)`**

    - Membuka modal dan menampilkan loading spinner
    - Melakukan AJAX GET ke `/kunjungan/lab/{id}/hasil`
    - Menampilkan hasil dalam format tabel lengkap:
        - Header klinik
        - Info pasien (No RM, Nama, Tgl Lahir)
        - Info permintaan (No, Tanggal, Dokter)
        - Tabel pemeriksaan dengan kolom: Pemeriksaan, Hasil, Nilai Normal, Satuan
        - Hasil abnormal ditandai dengan warna merah dan bold
        - Catatan (jika ada)
        - Tanda tangan petugas lab
    - Error handling dengan pesan yang informatif

2. **`viewRadioResults(radiologyRequestId)`**

    - Membuka modal dan menampilkan loading spinner
    - Melakukan AJAX GET ke `/kunjungan/radiologi/{id}/hasil`
    - Menampilkan hasil lengkap:
        - Header klinik
        - Info pasien
        - Info permintaan
        - Daftar jenis pemeriksaan
        - Hasil pemeriksaan (Findings)
        - Kesan (Impression)
        - Gambar hasil (jika ada)
        - Tanda tangan radiolog
    - Error handling dengan pesan yang informatif

3. **`printLabResults()`**

    - Membuka window baru untuk print
    - Memformat konten modal dengan CSS inline
    - Style khusus untuk print (border, tabel, typography)
    - Auto-focus dan print setelah 250ms

4. **`printRadioResults()`**
    - Membuka window baru untuk print
    - Memformat konten modal dengan CSS inline
    - Style khusus untuk print dengan page-break handling untuk gambar
    - Auto-focus dan print setelah 250ms

#### C. Integration dengan Last Encounter Summary

-   Button "Lihat Hasil Lengkap" pada card Lab memanggil `viewLabResults()`
-   Button "Lihat Hasil Lengkap" pada card Radiologi memanggil `viewRadioResults()`
-   Functions dibuat global (`window.viewLabResults`, dll) agar bisa dipanggil dari onclick

### 2. Backend (Controller & Routes)

#### A. ObservasiController.php

Ditambahkan 2 method baru:

1. **`getLabResults($id)`**

    - Parameter: `$id` = lab_request_id
    - Validasi status: hanya menampilkan jika status = 'completed'
    - Eager loading: encounter.user.pasien, items.jenisPemeriksaan, requester
    - Return data:
        ```php
        [
            'success' => true,
            'data' => [
                'klinik_nama' => 'Klinik Bambu Dua',
                'pasien_no_rm' => '...',
                'pasien_nama' => '...',
                'pasien_tgl_lahir' => 'd M Y',
                'nomor_permintaan' => '...',
                'tanggal_permintaan' => 'd M Y H:i',
                'dokter_nama' => '...',
                'catatan' => '...',
                'petugas_lab' => '...',
                'items' => [
                    [
                        'nama_pemeriksaan' => '...',
                        'hasil' => '...',
                        'nilai_normal' => '...',
                        'satuan' => '...',
                        'is_abnormal' => true/false
                    ]
                ]
            ]
        ]
        ```
    - Deteksi hasil abnormal: parsing reference range (format "10-20")
    - Error handling: try-catch dengan pesan error yang jelas

2. **`getRadiologyResults($id)`**
    - Parameter: `$id` = radiology_request_id
    - Validasi status: hanya menampilkan jika status = 'completed'
    - Eager loading: encounter.user.pasien, jenis, dokter, results.radiologist
    - Mengambil latest result (orderByDesc)
    - Return data:
        ```php
        [
            'success' => true,
            'data' => [
                'klinik_nama' => '...',
                'pasien_no_rm' => '...',
                'pasien_nama' => '...',
                'pasien_tgl_lahir' => 'd M Y',
                'nomor_permintaan' => '...',
                'tanggal_permintaan' => 'd M Y H:i',
                'dokter_nama' => '...',
                'items' => [
                    ['nama_pemeriksaan' => '...']
                ],
                'findings' => '...',
                'impression' => '...',
                'radiolog' => '...',
                'images' => [
                    ['url' => 'asset/storage/...']
                ]
            ]
        ]
        ```
    - Parse files JSON untuk gambar
    - Error handling: try-catch dengan pesan error yang jelas

#### B. Routes (web.php)

Ditambahkan 2 routes baru di dalam grup kunjungan:

```php
Route::get('/kunjungan/lab/{id}/hasil', [ObservasiController::class, 'getLabResults'])->name('kunjungan.lab.hasil');
Route::get('/kunjungan/radiologi/{id}/hasil', [ObservasiController::class, 'getRadiologyResults'])->name('kunjungan.radiologi.hasil');
```

## Model Dependencies

### LabRequest

-   Relationships: encounter, requester, items
-   Status: pending, collected, completed, cancelled
-   Fields: requested_at, collected_at, completed_at, notes, total_charge

### LabRequestItem

-   Relationships: request, jenisPemeriksaan
-   Fields: test_name, result_value, result_unit, result_reference, result_notes
-   Casts: result_payload as array

### RadiologyRequest

-   Relationships: encounter, pasien, jenis, dokter, results
-   Status: requested, processing, completed, canceled
-   Fields: notes, price, created_by

### RadiologyResult

-   Relationships: request, radiologist, reporter
-   Fields: findings, impression, payload, files, reported_at
-   Casts: payload as array, files as array

## Features

### Lab Results Modal

✅ Tampilan format cetak lengkap
✅ Info pasien dan dokter
✅ Tabel hasil pemeriksaan dengan satuan dan nilai normal
✅ Highlighting hasil abnormal (warna merah, bold)
✅ Catatan pemeriksaan
✅ Tanda tangan petugas lab
✅ Fungsi cetak langsung
✅ Loading spinner saat fetch data
✅ Error handling dengan pesan informatif
✅ Validasi status (hanya completed)

### Radiologi Results Modal

✅ Tampilan format cetak lengkap
✅ Info pasien dan dokter
✅ Daftar jenis pemeriksaan
✅ Hasil pemeriksaan (Findings)
✅ Kesan (Impression)
✅ Tampilan gambar hasil (grid responsive)
✅ Tanda tangan radiolog
✅ Fungsi cetak langsung (dengan page-break handling)
✅ Loading spinner saat fetch data
✅ Error handling dengan pesan informatif
✅ Validasi status (hanya completed)

## Usage

### Di Halaman Observasi

Ketika melihat "Ringkasan Kunjungan Terakhir":

1. **Untuk Lab**:

    - Klik button "Lihat Hasil Lengkap" pada card Pemeriksaan Lab
    - Modal akan terbuka dengan loading spinner
    - Data hasil lab akan ditampilkan dalam format tabel lengkap
    - Klik "Cetak" untuk print hasil lab

2. **Untuk Radiologi**:
    - Klik button "Lihat Hasil Lengkap" pada card Radiologi
    - Modal akan terbuka dengan loading spinner
    - Data hasil radiologi akan ditampilkan dengan findings, impression, dan gambar
    - Klik "Cetak" untuk print hasil radiologi

## Error Handling

### Frontend

-   Loading spinner saat fetch data
-   Alert warning untuk data tidak ditemukan
-   Alert danger untuk error server/network
-   SweetAlert untuk error saat print

### Backend

-   Validasi status completed
-   Try-catch untuk database errors
-   Response JSON standar dengan success flag
-   Error message yang informatif

## Print Functionality

### Lab Print

-   Header: Judul + Nama Klinik
-   Patient Info: No RM, Nama, Tgl Lahir
-   Request Info: No Permintaan, Tanggal, Dokter
-   Results Table: Border, padding, alternating colors
-   Footer: Signature area untuk petugas lab

### Radiologi Print

-   Header: Judul + Nama Klinik
-   Patient Info: No RM, Nama, Tgl Lahir
-   Request Info: No Permintaan, Tanggal, Dokter
-   Test List: Bullet points
-   Findings & Impression: Bordered box dengan background light
-   Images: Responsive grid dengan page-break-inside: avoid
-   Footer: Signature area untuk radiolog

## Testing

### Manual Testing Checklist

-   [ ] Modal lab dapat dibuka dari last encounter summary
-   [ ] Modal radiologi dapat dibuka dari last encounter summary
-   [ ] Loading spinner tampil saat fetch data
-   [ ] Data lab ditampilkan dengan format yang benar
-   [ ] Data radiologi ditampilkan dengan format yang benar
-   [ ] Hasil abnormal ditandai dengan warna merah
-   [ ] Gambar radiologi ditampilkan dalam grid
-   [ ] Button cetak berfungsi untuk lab
-   [ ] Button cetak berfungsi untuk radiologi
-   [ ] Error handling bekerja untuk status belum completed
-   [ ] Error handling bekerja untuk request tidak ditemukan
-   [ ] Modal dapat ditutup dengan button Tutup
-   [ ] Modal dapat ditutup dengan click backdrop
-   [ ] Print preview menampilkan format yang benar

## Notes

-   Modal menggunakan Bootstrap 5 Modal component
-   Icons menggunakan Remix Icons
-   AJAX menggunakan jQuery
-   Print menggunakan window.open() dan native browser print
-   Responsive design untuk mobile dan desktop
-   Fallback values ('-') untuk data yang tidak ada
