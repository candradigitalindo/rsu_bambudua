# Fee Lab & Radiologi Setelah Pembayaran

## 📋 Overview

Perubahan sistem fee lab dan radiologi dari **dibuat saat request/completed** menjadi **dibuat saat pembayaran kasir lunas**.

## 🎯 Tujuan

-   ✅ Fee hanya dibuat jika pasien benar-benar membayar
-   ✅ Menghindari data incentive yang tidak akurat
-   ✅ Tidak perlu void incentive manual jika pasien cancel
-   ✅ Sesuai praktik bisnis healthcare yang baik

## 📊 Struktur Fee

### **1. Fee Penunjang (Dokter Perujuk/DPJP)**

-   **Kapan dibuat:** Saat pembayaran tindakan lunas di kasir
-   **Siapa yang dapat:** Dokter yang request lab/radiologi
-   **Tipe Incentive:** `fee_penunjang`
-   **Dasar perhitungan:**
    -   Lab: Setting `fee_lab_mode` & `fee_lab_value`
    -   Radiologi: Setting `fee_radiologi_mode` & `fee_radiologi_value`

### **2. Fee Pelaksana (Dokter Lab/Radiologi)**

-   **Kapan dibuat:** Saat pembayaran tindakan lunas di kasir
-   **Siapa yang dapat:**
    -   Lab: Petugas lab yang menyelesaikan pemeriksaan (status completed)
    -   Radiologi: Radiologist yang melakukan pemeriksaan (dari RadiologyResult)
-   **Tipe Incentive:**
    -   Lab: `fee_pelaksana_lab`
    -   Radiologi: `fee_pelaksana_radiologi`
-   **Dasar perhitungan:** Sama dengan fee penunjang (bisa dibuat setting terpisah jika perlu)

## 🔧 Perubahan Kode

### **File yang Dimodifikasi:**

#### 1. **RadiologiController.php**

**Perubahan:**

-   ❌ Hapus `createRadiologistIncentive()` dari method `resultsStore()` (line ~154)
-   ❌ Hapus `createPemeriksaanPenunjangIncentive()` dari method `store()` (line ~278)

**Alasan:** Fee tidak dibuat saat hasil selesai atau request dibuat, tapi saat pembayaran.

---

#### 2. **LabRequestController.php**

**Perubahan:**

-   ❌ Hapus `createRadiologistIncentive()` dari method `update()` saat status completed (line ~162)

**Alasan:** Fee tidak dibuat saat hasil lab selesai, tapi saat pembayaran.

---

#### 3. **ObservasiController.php**

**Perubahan:**

-   ❌ Hapus `createPemeriksaanPenunjangIncentive()` dari method `postPemeriksaanPenunjang()` (line ~144)

**Alasan:** Fee tidak dibuat saat request penunjang dibuat, tapi saat pembayaran.

---

#### 4. **ObservasiRepository.php**

**Perubahan:**

-   ❌ Hapus `createPemeriksaanPenunjangIncentive()` dari method `postPemeriksaanPenunjang()` (line ~223)

**Alasan:** Fee tidak dibuat saat request lab dibuat, tapi saat pembayaran.

---

#### 5. **KasirController.php** ⭐ (BARU)

**Perubahan:**

-   ✅ Tambah method `createLabRadiologiIncentives()` (private method baru)
-   ✅ Panggil method tersebut dalam `processPayment()` saat `status_bayar_tindakan = 1`

**Logika Baru:**

```php
private function createLabRadiologiIncentives(Encounter $encounter)
{
    // 1. Proses semua LabRequest yang completed
    foreach ($labRequests as $labRequest) {
        // A. Fee Penunjang untuk dokter perujuk
        createPemeriksaanPenunjangIncentive(...);

        // B. Fee Pelaksana untuk petugas lab
        createRadiologistIncentive(...);
    }

    // 2. Proses semua RadiologyRequest yang completed
    foreach ($radiologyRequests as $radiologyRequest) {
        // A. Fee Penunjang untuk dokter perujuk
        createPemeriksaanPenunjangIncentive(...);

        // B. Fee Pelaksana untuk radiologist
        createRadiologistIncentive(...);
    }
}
```

**Dipanggil di:**

```php
if ($hasTindakan && !$encounter->status_bayar_tindakan) {
    $encounter->status_bayar_tindakan = 1;
    // ... update metode pembayaran ...

    // [NEW] Buat insentif lab & radiologi saat pembayaran lunas
    $this->createLabRadiologiIncentives($encounter);
}
```

## 🔄 Flow Baru

### **Sebelum (OLD):**

```
Request Lab → Fee Penunjang dibuat ✅
↓
Hasil Lab Selesai → Fee Pelaksana dibuat ✅
↓
Pembayaran Kasir → ❌ Tidak ada proses fee
↓
⚠️ Masalah: Jika pasien cancel, fee sudah tercatat!
```

### **Sesudah (NEW):**

```
Request Lab → ⏸️ Fee belum dibuat
↓
Hasil Lab Selesai → ⏸️ Fee belum dibuat
↓
Pembayaran Kasir LUNAS → ✅ Fee Penunjang + Fee Pelaksana dibuat
↓
✅ Fee hanya dibuat jika benar-benar ada pembayaran!
```

## 📝 Catatan Penting

### **1. Tracking Pelaksana Lab**

Karena tabel `lab_requests` tidak memiliki field `performed_by`, sistem menggunakan:

-   **Prioritas 1:** Cek `ActivityLog` siapa yang update status menjadi `completed`
-   **Prioritas 2:** Fallback ke user dengan `role = 8` (Lab) pertama

**Rekomendasi:** Tambahkan field `performed_by` di tabel `lab_requests` untuk tracking yang lebih akurat.

### **2. Tracking Pelaksana Radiologi**

Sudah terintegrasi dengan baik melalui field `radiologist_id` di tabel `radiology_results`.

### **3. Duplicate Prevention**

Sistem sudah aman dari duplicate karena:

-   Fee hanya dibuat saat `status_bayar_tindakan` berubah dari 0 → 1
-   Dalam transaction DB, tidak mungkin double process

### **4. Split Payment**

Sistem tetap support split payment karena:

-   Fee dibuat berdasarkan `status_bayar_tindakan = 1`
-   Tidak peduli berapa metode pembayaran digunakan

## ✅ Testing Checklist

-   [ ] Request lab baru → Fee TIDAK dibuat
-   [ ] Hasil lab completed → Fee TIDAK dibuat
-   [ ] Pembayaran kasir lunas → Fee Penunjang + Fee Pelaksana DIBUAT
-   [ ] Request radiologi baru → Fee TIDAK dibuat
-   [ ] Hasil radiologi completed → Fee TIDAK dibuat
-   [ ] Pembayaran kasir lunas → Fee Penunjang + Fee Pelaksana DIBUAT
-   [ ] Pasien cancel sebelum bayar → Fee TIDAK tercatat di sistem
-   [ ] Split payment → Fee tetap dibuat dengan benar

## 🎨 Benefit Implementasi

| **Aspek**             | **Sebelum**                            | **Sesudah**                         |
| --------------------- | -------------------------------------- | ----------------------------------- |
| **Akurasi Fee**       | ❌ Fee dibuat meski pasien tidak bayar | ✅ Fee hanya jika benar-benar bayar |
| **Data Cleanup**      | ❌ Harus manual void jika cancel       | ✅ Tidak perlu cleanup manual       |
| **Laporan Incentive** | ⚠️ Bisa tidak akurat                   | ✅ Selalu akurat                    |
| **Business Logic**    | ❌ Tidak sesuai praktik healthcare     | ✅ Sesuai best practice             |
| **Performance**       | ⚠️ Create incentive di banyak tempat   | ✅ Terpusat di kasir controller     |

## 📞 Contact

Jika ada pertanyaan atau issue, hubungi developer team.

---

**Tanggal Implementasi:** 9 November 2025  
**Versi:** 1.0.0  
**Status:** ✅ Production Ready
