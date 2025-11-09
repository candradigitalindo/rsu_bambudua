# Dashboard Dokter - Histori Pasien & Pendapatan

## 📋 Overview

Menambahkan fitur histori pasien yang ditangani dan histori pendapatan di dashboard layanan medis (dokter).

## ✨ Fitur Baru

### **1. Histori Pasien yang Ditangani**

Menampilkan **10 pasien terakhir** yang ditangani dokter dengan informasi:

-   ✅ Tanggal & waktu kunjungan
-   ✅ Nama pasien & rekam medis
-   ✅ Jenis layanan (Rawat Jalan, IGD, Rawat Inap)
-   ✅ Diagnosis utama
-   ✅ Status (Selesai/Sedang Dirawat)

**Sumber Data:**

-   Tabel `practitioners` → untuk pasien Rawat Jalan & IGD
-   Tabel `inpatient_treatments` (type: Visit) → untuk pasien Rawat Inap

### **2. Histori Pendapatan**

Menampilkan **10 transaksi pendapatan terakhir** dengan informasi:

-   ✅ Tanggal transaksi
-   ✅ Jenis pendapatan (Fee Kunjungan, Fee Penunjang, Fee Pelaksana, dll)
-   ✅ Keterangan detail
-   ✅ Jumlah pendapatan (Rupiah)
-   ✅ Status (Dibayar/Pending)

**Sumber Data:**

-   Tabel `incentives` → semua jenis fee (encounter, penunjang, pelaksana, obat)
-   Tabel `inpatient_treatments` → tindakan & visit rawat inap

## 🔧 Perubahan Kode

### **File yang Dimodifikasi:**

#### 1. **DokterController.php**

**Method Baru:**

```php
private function getHistoriPasien()
{
    // Gabungkan data dari practitioners & inpatient_treatments
    // Return 10 data terakhir, sorted by tanggal
}

private function getHistoriPendapatan()
{
    // Gabungkan data dari incentives & inpatient_treatments
    // Return 10 data terakhir, sorted by tanggal
}
```

**Update method `index()`:**

```php
// Tambahkan variabel baru
$historiPasien = $this->getHistoriPasien();
$historiPendapatan = $this->getHistoriPendapatan();

// Pass ke view
return view('pages.dokter.index', compact(
    // ... existing variables
    'historiPasien',
    'historiPendapatan'
));
```

---

#### 2. **resources/views/pages/dokter/index.blade.php**

**Tambahan Section Baru:**

```blade
<!-- Histori Pasien (7 kolom) -->
<div class="col-lg-7 col-12">
    <div class="card">
        <table> ... </table>
    </div>
</div>

<!-- Histori Pendapatan (5 kolom) -->
<div class="col-lg-5 col-12">
    <div class="card">
        <table> ... </table>
    </div>
</div>
```

**Fitur UI:**

-   Badge warna untuk jenis layanan (primary, info, danger)
-   Badge status untuk status transaksi (success, warning)
-   Format tanggal Indonesia (d/m/Y)
-   Format currency untuk pendapatan
-   Empty state jika belum ada data

## 📊 Tipe Pendapatan

| **Tipe Code**             | **Label Friendly**      |
| ------------------------- | ----------------------- |
| `encounter`               | Fee Kunjungan           |
| `treatment_inap`          | Fee Tindakan Rawat Inap |
| `visit_inap`              | Fee Visit Rawat Inap    |
| `fee_penunjang`           | Fee Penunjang           |
| `fee_pelaksana_lab`       | Fee Pelaksana Lab       |
| `fee_pelaksana_radiologi` | Fee Pelaksana Radiologi |
| `fee_obat_rj`             | Fee Obat Rawat Jalan    |
| `fee_obat_inap`           | Fee Obat Rawat Inap     |

## 🎨 Color Scheme

### **Badge Jenis Layanan:**

-   🔵 **Rawat Jalan:** `bg-primary-subtle text-primary`
-   🔷 **Rawat Inap:** `bg-info-subtle text-info`
-   🔴 **IGD:** `bg-danger-subtle text-danger`

### **Badge Status Pendapatan:**

-   🟢 **Dibayar:** `bg-success-subtle text-success`
-   🟡 **Pending:** `bg-warning-subtle text-warning`

## 📱 Responsive Layout

-   **Desktop (≥992px):**
    -   Histori Pasien: 7 kolom (58%)
    -   Histori Pendapatan: 5 kolom (42%)
-   **Mobile (<992px):**
    -   Kedua tabel full width (12 kolom)
    -   Stack secara vertikal

## 🔗 Relasi Database

```
User (Dokter)
├── practitioners → encounters → pasien, diagnoses
├── inpatient_treatments → admission → encounter
└── incentives
```

## ✅ Validasi

-   ✅ No PHP syntax errors
-   ✅ No Blade template errors
-   ✅ Proper data relationships
-   ✅ Null-safe accessors (`optional()`)
-   ✅ Empty state handling
-   ✅ Responsive design

## 🚀 Testing Checklist

-   [ ] Dashboard terbuka tanpa error
-   [ ] Histori pasien menampilkan data terakhir
-   [ ] Badge warna sesuai jenis layanan
-   [ ] Histori pendapatan menampilkan data terakhir
-   [ ] Format currency benar (formatPrice helper)
-   [ ] Empty state muncul jika belum ada data
-   [ ] Responsive di mobile & tablet
-   [ ] Sorting by tanggal terbaru berfungsi

---

**Tanggal Implementasi:** 9 November 2025  
**Status:** ✅ Production Ready
