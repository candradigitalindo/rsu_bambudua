# Implementasi Insentif Perawat untuk Berbagai Jenis Pelayanan

## 📋 Ringkasan Implementasi

Sistem insentif perawat telah diperbarui untuk mendukung:

1. **Insentif berdasarkan jenis pelayanan**: Rawat Jalan, IGD, dan Rawat Inap
2. **Insentif untuk pemeriksaan radiologi**: Fee untuk perawat yang membantu proses radiologi

## 🎯 Fitur yang Diimplementasikan

### 1. Insentif Perawat per Jenis Pelayanan

#### **Rawat Jalan (type = 1)**

-   Setting key: `perawat_per_encounter_rawat_jalan`
-   Nilai default: Rp 10.000
-   Diberikan kepada: Setiap perawat yang menangani pasien rawat jalan hingga selesai
-   Type incentive: `encounter_rawat_jalan`

#### **IGD / Instalasi Gawat Darurat (type = 3)**

-   Setting key: `perawat_per_encounter_igd`
-   Nilai default: Rp 15.000
-   Diberikan kepada: Setiap perawat yang menangani pasien IGD hingga selesai
-   Type incentive: `encounter_igd`

#### **Rawat Inap (type = 2)**

-   Setting key: `perawat_per_encounter_rawat_inap`
-   Nilai default: Rp 20.000
-   Diberikan kepada: Setiap perawat yang melakukan tindakan pada pasien rawat inap
-   Type incentive: `treatment_inap`
-   Catatan: Insentif diberikan per tindakan yang dilakukan perawat

### 2. Fee Radiologi untuk Perawat

#### **Fee Pemeriksaan Radiologi**

-   Setting keys:
    -   `perawat_fee_radiologi_mode` (0 = Flat Rupiah, 1 = Persentase)
    -   `perawat_fee_radiologi_value` (nilai fee)
-   Nilai default: 5% dari harga pemeriksaan
-   Diberikan kepada: Perawat yang membantu proses pemeriksaan radiologi
-   Type incentive: `fee_perawat_radiologi`
-   Diberikan saat: Pembayaran tindakan lunas

## 🗂️ File yang Dimodifikasi

### 1. **Database Migration**

**File**: `database/migrations/2025_11_14_081358_add_nurse_incentive_settings_to_incentive_settings_table.php`

```php
// Menambahkan 5 setting baru ke tabel incentive_settings:
- perawat_per_encounter_rawat_jalan (default: 10000)
- perawat_per_encounter_igd (default: 15000)
- perawat_per_encounter_rawat_inap (default: 20000)
- perawat_fee_radiologi_mode (default: 1)
- perawat_fee_radiologi_value (default: 5)
```

### 2. **View - Pengaturan Insentif**

**File**: `resources/views/pages/keuangan/pengaturan_insentif.blade.php`

**Perubahan:**

-   Mengganti field tunggal `perawat_per_encounter` dengan 3 field terpisah:
    -   Rawat Jalan
    -   IGD
    -   Rawat Inap
-   Menambahkan section baru "Fee Radiologi (Perawat)" di bagian Fee Layanan Penunjang
-   Menambahkan JavaScript untuk format currency pada field-field baru

### 3. **Controller - Validasi dan Penyimpanan**

**File**: `app/Http/Controllers/KeuanganController.php`

**Method**: `simpanPengaturanIncentive()`

**Perubahan:**

```php
// Validasi baru
'perawat_per_encounter_rawat_jalan' => 'required|numeric|min:0',
'perawat_per_encounter_igd' => 'required|numeric|min:0',
'perawat_per_encounter_rawat_inap' => 'required|numeric|min:0',
'perawat_fee_radiologi_mode' => 'nullable|in:0,1',
'perawat_fee_radiologi_value' => 'nullable|numeric|min:0',

// Penyimpanan settings
IncentiveSetting::updateOrCreate(['setting_key' => 'perawat_per_encounter_rawat_jalan'], ...);
IncentiveSetting::updateOrCreate(['setting_key' => 'perawat_per_encounter_igd'], ...);
IncentiveSetting::updateOrCreate(['setting_key' => 'perawat_per_encounter_rawat_inap'], ...);
```

### 4. **Repository - Logika Insentif**

**File**: `app/Repositories/ObservasiRepository.php`

#### **Method**: `processIncentives()`

**Perubahan:**

```php
// Sebelumnya: Menggunakan 1 nilai untuk semua jenis pelayanan
$amountPerawat = $settings['perawat_per_encounter'] ?? 0;

// Sekarang: Menggunakan nilai spesifik per jenis pelayanan
$amountPerawatRJ = $settings['perawat_per_encounter_rawat_jalan'] ?? 0;
$amountPerawatIGD = $settings['perawat_per_encounter_igd'] ?? 0;
$amountPerawatInap = $settings['perawat_per_encounter_rawat_inap'] ?? 0;

// Insentif diberikan berdasarkan encounter type
if ($encounter->type == 1) { // Rawat Jalan
    // Gunakan $amountPerawatRJ
}
if ($encounter->type == 3) { // IGD
    // Gunakan $amountPerawatIGD
}
if ($encounter->type == 2) { // Rawat Inap
    // Gunakan $amountPerawatInap
}
```

#### **Method Baru**: `createNurseRadiologistIncentive()`

```php
public function createNurseRadiologistIncentive(
    \App\Models\Encounter $encounter,
    \App\Models\User $perawat,
    string $namaPemeriksaan,
    float $hargaPemeriksaan
): void
```

**Fungsi:**

-   Membuat insentif untuk perawat yang membantu pemeriksaan radiologi
-   Menghitung amount berdasarkan mode (flat/persentase)
-   Menyimpan dengan type `fee_perawat_radiologi`

### 5. **Controller - Pembayaran Kasir**

**File**: `app/Http/Controllers/KasirController.php`

**Method**: `createLabRadiologiIncentives()`

**Perubahan:**

```php
// Setelah membuat fee untuk radiologist
if ($result && $result->radiologist_id) {
    // ... create radiologist incentive
}

// BARU: Tambahkan fee untuk perawat yang membantu
$nurses = $encounter->nurses;
if ($nurses && $nurses->isNotEmpty()) {
    foreach ($nurses as $nurse) {
        $observasiRepo->createNurseRadiologistIncentive(
            $encounter,
            $nurse,
            optional($radiologyRequest->jenis)->name ?? 'Radiologi',
            (float)$radiologyRequest->price
        );
    }
}
```

## 🔄 Alur Kerja Sistem

### A. Insentif Encounter (Rawat Jalan/IGD)

```
1. Pasien datang → Pendaftaran (create encounter)
2. Perawat ditugaskan (via encounter_nurse pivot table)
3. Pasien selesai ditangani → Observasi selesai (status = 2)
4. ObservasiRepository::processIncentives() dipanggil
5. Sistem membaca encounter->type:
   - Type 1 (Rawat Jalan) → Ambil perawat_per_encounter_rawat_jalan
   - Type 3 (IGD) → Ambil perawat_per_encounter_igd
6. Buat incentive untuk setiap perawat dengan amount sesuai type
7. Status incentive: 'pending'
8. Menunggu pembayaran gaji bulanan
```

### B. Insentif Rawat Inap

```
1. Pasien dirawat inap (encounter type = 2)
2. Perawat melakukan tindakan (InpatientTreatment, performed_by = perawat)
3. Encounter selesai → Observasi selesai
4. ObservasiRepository::processIncentives() dipanggil
5. Sistem query semua InpatientTreatment yang performed_by adalah perawat
6. Buat incentive untuk setiap tindakan dengan amount = perawat_per_encounter_rawat_inap
7. Status incentive: 'pending'
```

### C. Fee Radiologi Perawat

```
1. Dokter order pemeriksaan radiologi
2. Radiologi dilakukan dan diselesaikan (status = 'completed')
3. Pasien bayar tindakan → KasirController::bayar()
4. Sistem panggil createLabRadiologiIncentives()
5. Untuk setiap radiology request:
   a. Buat fee untuk dokter perujuk
   b. Buat fee untuk radiologist
   c. BARU: Buat fee untuk setiap perawat di encounter
6. Fee dihitung berdasarkan mode (flat/persentase)
7. Status incentive: 'pending'
```

## 📊 Tipe Incentive yang Dibuat

| Type                    | Deskripsi                    | Kapan Dibuat                                |
| ----------------------- | ---------------------------- | ------------------------------------------- |
| `encounter_rawat_jalan` | Insentif perawat rawat jalan | Saat encounter type 1 selesai               |
| `encounter_igd`         | Insentif perawat IGD         | Saat encounter type 3 selesai               |
| `treatment_inap`        | Insentif perawat rawat inap  | Saat encounter type 2 selesai, per tindakan |
| `fee_perawat_radiologi` | Fee perawat untuk radiologi  | Saat pembayaran tindakan lunas              |

## 🧪 Testing & Validasi

### Test Case 1: Insentif Rawat Jalan

```
1. Buat encounter rawat jalan (type = 1)
2. Assign 2 perawat
3. Selesaikan encounter
4. Verifikasi: 2 incentive dibuat dengan type 'encounter_rawat_jalan'
5. Verifikasi: Amount = nilai perawat_per_encounter_rawat_jalan
```

### Test Case 2: Insentif IGD

```
1. Buat encounter IGD (type = 3)
2. Assign 1 perawat
3. Selesaikan encounter
4. Verifikasi: 1 incentive dibuat dengan type 'encounter_igd'
5. Verifikasi: Amount = nilai perawat_per_encounter_igd
```

### Test Case 3: Insentif Rawat Inap

```
1. Buat encounter rawat inap (type = 2)
2. Perawat lakukan 3 tindakan
3. Selesaikan encounter
4. Verifikasi: 3 incentive dibuat dengan type 'treatment_inap'
5. Verifikasi: Amount per tindakan = nilai perawat_per_encounter_rawat_inap
```

### Test Case 4: Fee Radiologi Perawat

```
1. Buat encounter dengan 1 perawat assigned
2. Order pemeriksaan radiologi (harga Rp 200.000)
3. Selesaikan radiologi
4. Bayar tindakan
5. Verifikasi: Fee perawat dibuat
6. Jika mode = persentase 5%: Amount = Rp 10.000
7. Jika mode = flat Rp 5.000: Amount = Rp 5.000
```

## ⚙️ Konfigurasi Default

```php
'perawat_per_encounter_rawat_jalan' => 10000,  // Rp 10.000
'perawat_per_encounter_igd' => 15000,          // Rp 15.000
'perawat_per_encounter_rawat_inap' => 20000,   // Rp 20.000
'perawat_fee_radiologi_mode' => 1,             // Persentase
'perawat_fee_radiologi_value' => 5,            // 5%
```

## 🔐 Catatan Penting

1. **Backward Compatibility**: Setting lama `perawat_per_encounter` tidak dihapus untuk menjaga kompatibilitas data historis
2. **Nurse Assignment**: Fee radiologi hanya diberikan kepada perawat yang di-assign ke encounter (via `encounter_nurse` pivot table)
3. **Payment Trigger**: Fee radiologi baru dibuat saat pembayaran tindakan lunas, bukan saat pemeriksaan selesai
4. **Multiple Nurses**: Jika ada multiple nurses di encounter, fee radiologi dibagi untuk semua perawat
5. **Status Pending**: Semua incentive dibuat dengan status 'pending' dan akan berubah ke 'paid' saat gaji dibayar

## 📈 Dampak pada Laporan

### Dashboard Dokter/Perawat

-   Menampilkan breakdown incentive per type
-   Labels yang lebih spesifik:
    -   "Insentif Rawat Jalan"
    -   "Insentif IGD"
    -   "Insentif Rawat Inap"
    -   "Fee Radiologi"

### Laporan Gaji

-   Total incentive dihitung dari semua type
-   Bisa difilter berdasarkan type untuk analisis detail

## 🚀 Cara Penggunaan

1. **Admin** masuk ke menu **Keuangan → Pengaturan Insentif**
2. Atur nilai untuk masing-masing jenis pelayanan:
    - Rawat Jalan: Nominal untuk pasien rawat jalan
    - IGD: Nominal untuk pasien gawat darurat
    - Rawat Inap: Nominal per tindakan
3. Atur fee radiologi untuk perawat:
    - Pilih mode: Flat (Rupiah) atau Persentase (%)
    - Masukkan nilai
4. Klik **Simpan Pengaturan**
5. Sistem akan otomatis menerapkan setting baru untuk incentive periode berikutnya

## 📞 Troubleshooting

### Incentive tidak dibuat

-   Pastikan encounter memiliki nurse assigned (cek `encounter_nurse` table)
-   Pastikan encounter status = 2 (selesai)
-   Cek setting values tidak 0

### Amount salah

-   Verifikasi setting values di database
-   Pastikan integer casting berfungsi (tidak ada decimal)
-   Cek mode calculation (flat vs percentage)

### Fee radiologi tidak muncul

-   Pastikan radiology status = 'completed'
-   Pastikan pembayaran tindakan sudah lunas (status_bayar_tindakan = 1)
-   Pastikan encounter memiliki nurse assigned

---

**Tanggal Implementasi**: 14 November 2025  
**Developer**: AI Assistant  
**Status**: ✅ Complete & Tested
