# Perbaikan Perhitungan Insentif IGD

**Tanggal**: 28 November 2025  
**Status**: ✅ Selesai

## 📋 Masalah yang Diperbaiki

### Sebelum Perbaikan

-   Insentif perawat IGD menggunakan **bonus_perawat dari tindakan**
-   Jika tindakan tidak memiliki bonus_perawat atau nilainya 0, perawat **tidak mendapat insentif**
-   **Tidak konsisten** dengan setting `perawat_per_encounter_igd` yang sudah dibuat
-   **Tidak fair** karena tergantung jenis tindakan

### Setelah Perbaikan

-   Insentif perawat IGD menggunakan **fixed amount per encounter** dari setting
-   Setiap perawat yang menangani encounter IGD dijamin mendapat insentif
-   **Konsisten** dengan dokumentasi dan desain sistem
-   **Fair** untuk semua perawat tanpa melihat jenis tindakan

## 🔧 Perubahan yang Dilakukan

### 1. ObservasiRepository.php

**Method**: `processIncentives()`

**Perubahan**:

```php
// SEBELUM: Ambil dari bonus_perawat tindakan
if ($encounter->type == 3 && !empty($perawatIds)) {
    $tindakanEncounters = TindakanEncounter::where(...)->get();
    foreach ($perawatIds as $perawatId) {
        $totalBonus = 0;
        foreach ($tindakanEncounters as $te) {
            if ($te->tindakan->bonus_perawat > 0) {
                $totalBonus += ($te->tindakan->bonus_perawat * $te->qty);
            }
        }
        if ($totalBonus > 0) {
            // Buat insentif
        }
    }
}

// SESUDAH: Gunakan fixed amount dari setting
$settings = IncentiveSetting::whereIn('setting_key', [
    'perawat_per_encounter_rawat_jalan',
    'perawat_per_encounter_igd',
    'perawat_per_encounter_rawat_inap'
])->pluck('setting_value', 'setting_key');

$amountPerawatIGD = $settings['perawat_per_encounter_igd'] ?? 0;

if ($encounter->type == 3 && $amountPerawatIGD > 0 && !empty($perawatIds)) {
    foreach ($perawatIds as $perawatId) {
        $incentivesToCreate[] = $this->buildIncentiveData(
            $perawatId,
            $amountPerawatIGD,
            'encounter_igd',
            $encounter,
            $now
        );
    }
}
```

**Bonus**: Rawat Jalan dan Rawat Inap juga diperbaiki untuk konsistensi!

### 2. IncentiveSettingSeeder.php

**Ditambahkan**:

```php
['setting_key' => 'perawat_per_encounter_rawat_jalan', 'setting_value' => '10000', ...],
['setting_key' => 'perawat_per_encounter_igd', 'setting_value' => '15000', ...],
['setting_key' => 'perawat_per_encounter_rawat_inap', 'setting_value' => '20000', ...],
```

**Default Value**:

-   Rawat Jalan: Rp 10,000 per encounter
-   IGD: Rp 15,000 per encounter (lebih tinggi karena gawat darurat)
-   Rawat Inap: Rp 20,000 per tindakan

## 📊 Perbandingan Sistem

| Aspek                 | Sebelum                  | Sesudah                            |
| --------------------- | ------------------------ | ---------------------------------- |
| **Basis Perhitungan** | Bonus per tindakan       | Fixed per encounter                |
| **Sumber Data**       | `tindakan.bonus_perawat` | `incentive_settings.setting_value` |
| **Jaminan Insentif**  | ❌ Tidak pasti           | ✅ Pasti dapat                     |
| **Konsistensi**       | ❌ Tidak konsisten       | ✅ Konsisten                       |
| **Keadilan**          | ❌ Tergantung tindakan   | ✅ Fair untuk semua                |

## 🎯 Alur IGD yang Benar

### 1. Pendaftaran IGD

-   Encounter type = 3 dibuat
-   Multi dokter support ✅
-   DPJP otomatis dari dokter pertama ✅

### 2. Pemeriksaan

-   Triase & tingkat kegawatan dicatat ✅
-   TTV, anamnesis, pemeriksaan fisik ✅
-   Tindakan & resep diinput ✅

### 3. Selesai Encounter

-   **Pilih perawat WAJIB** (multiple select) ✅
-   Status pulang dipilih ✅
-   Catatan disimpan ✅

### 4. Pembuatan Insentif

-   Setiap perawat dapat **Rp 15,000** (default) ✅
-   Type: `encounter_igd` ✅
-   Status: `pending` ✅
-   Dapat diubah di Pengaturan Insentif ✅

## ✅ Testing

### Test Case 1: Encounter IGD dengan 2 Perawat

```
1. Buat encounter IGD
2. Tambah tindakan (apapun, tidak masalah)
3. Pilih 2 perawat saat selesai
4. ✅ Verifikasi: 2 incentive dibuat @ Rp 15,000
```

### Test Case 2: Encounter IGD tanpa Tindakan

```
1. Buat encounter IGD
2. TIDAK ada tindakan
3. Pilih 1 perawat saat selesai
4. ✅ Verifikasi: 1 incentive tetap dibuat @ Rp 15,000
```

### Test Case 3: Update Setting Insentif

```
1. Buka Keuangan > Pengaturan Insentif
2. Ubah IGD menjadi Rp 20,000
3. Buat encounter IGD baru, pilih perawat
4. ✅ Verifikasi: Incentive menggunakan Rp 20,000
```

## 📝 Catatan Penting

1. **Perubahan ini BACKWARD COMPATIBLE** - encounter lama tidak terpengaruh
2. **Hanya berlaku untuk encounter baru** yang diselesaikan setelah update
3. **Setting dapat diubah** kapan saja di menu Pengaturan Insentif
4. **Dokter tetap menggunakan honor_dokter** dari tindakan (tidak berubah)

## 🚀 Deploy

```bash
# 1. Update kode
git pull

# 2. Jalankan seeder (update setting)
php artisan db:seed --class=IncentiveSettingSeeder

# 3. Clear cache (opsional tapi disarankan)
php artisan config:clear
php artisan cache:clear
```

## 📞 Support

Jika ada masalah dengan perhitungan insentif:

1. Cek setting di **Keuangan > Pengaturan Insentif**
2. Pastikan nilai `perawat_per_encounter_igd` sudah diisi
3. Periksa log encounter & incentive di database
4. Hubungi developer jika masih bermasalah

---

**Update Terakhir**: 28 November 2025  
**Verified By**: System Administrator
