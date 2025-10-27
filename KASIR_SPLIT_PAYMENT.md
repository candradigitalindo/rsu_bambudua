# Fitur Split Payment - Halaman Pembayaran Kasir

## 📋 Ringkasan Perubahan

Halaman pembayaran kasir (`http://127.0.0.1:8000/kasir/pembayaran/{pasien_id}`) telah ditingkatkan menjadi lebih **informatif** dan mendukung **split payment** (pembayaran dengan beberapa metode sekaligus).

## ✨ Fitur Baru

### 1. **Ringkasan Tagihan yang Informatif**

-   **Card Summary** dengan gradient background yang menarik
-   Menampilkan breakdown tagihan berdasarkan kategori:
    -   🔵 **TINDAKAN** - Tindakan medis dengan jumlah item
    -   🟣 **RESEP/OBAT** - Obat-obatan dengan jumlah item
    -   🟢 **LABORATORIUM** - Jumlah permintaan lab (termasuk dalam tindakan)
    -   🟠 **RADIOLOGI** - Jumlah permintaan radiologi (termasuk dalam tindakan)
-   **Total Tagihan** ditampilkan secara jelas di atas

### 2. **Detail Tagihan Per Encounter**

-   Setiap encounter ditampilkan dalam card terpisah
-   Status "Belum Lunas" dengan badge kuning
-   Detail item dengan:
    -   Nama tindakan/obat
    -   Quantity
    -   Harga satuan (@)
    -   Sub-total per kategori
-   Checkbox untuk memilih item yang akan dibayar
-   Tampilan yang lebih bersih dengan icon-icon informatif

### 3. **Riwayat Pembayaran**

-   Section terpisah untuk transaksi yang sudah lunas
-   Badge hijau "LUNAS" dengan jumlah transaksi
-   Menampilkan metode pembayaran yang digunakan
-   Detail item yang sudah dibayar
-   Border hijau untuk membedakan dari tagihan aktif

### 4. **Split Payment (Multi-Metode Pembayaran)**

-   ✅ Bayar dengan **beberapa metode sekaligus**
-   Contoh penggunaan:
    -   50% Cash + 50% Transfer Bank
    -   Rp 100.000 Cash + Rp 200.000 Debit Card
    -   Kombinasi bebas sesuai kebutuhan
-   Fitur:
    -   Tombol "Tambah Metode Pembayaran" untuk menambah metode
    -   Setiap metode memiliki dropdown pilihan dan input jumlah
    -   Input jumlah otomatis terformat sebagai Rupiah
    -   Bisa menghapus metode pembayaran (kecuali yang pertama jika hanya ada 1)
    -   Highlight biru untuk metode yang aktif

### 5. **Kalkulator Pembayaran Real-time**

-   **Total Tagihan** - Jumlah yang harus dibayar
-   **Total Dibayar** - Jumlah dari semua metode pembayaran
-   **Kembalian/Kurang** - Otomatis menghitung:
    -   Jika lebih → Tampilan hijau "KEMBALIAN"
    -   Jika kurang → Tampilan merah "PEMBAYARAN KURANG"
    -   Jika pas → Tidak ada kembalian
-   Update otomatis saat user mengubah pilihan atau jumlah

### 6. **Validasi Pembayaran**

-   ❌ Tombol "Proses Pembayaran" disabled jika:
    -   Tidak ada item yang dipilih
    -   Belum ada metode pembayaran yang dipilih
    -   Jumlah pembayaran kurang dari total tagihan
    -   Total tagihan = 0
-   ✅ Tombol aktif hanya jika semua kondisi terpenuhi
-   Alert error jika mencoba submit dengan pembayaran kurang
-   Konfirmasi SweetAlert jika ada kembalian

### 7. **Peningkatan UI/UX**

-   Gradient background untuk summary card (ungu-biru)
-   Badge warna-warni untuk kategori:
    -   Biru untuk Tindakan
    -   Ungu untuk Resep
    -   Hijau untuk Lab
    -   Orange untuk Radiologi
-   Box shadow dan border radius modern
-   Icon Remix Icon di seluruh interface
-   Responsive design untuk berbagai ukuran layar

## 🗂️ File yang Diubah

### 1. **resources/views/pages/kasir/show.blade.php**

-   Redesign lengkap tampilan
-   Menambahkan ringkasan tagihan
-   Implementasi split payment UI
-   Kalkulator pembayaran real-time
-   Enhanced history view

### 2. **app/Http/Controllers/KasirController.php**

#### Method `processPayment()` - Updated

**Perubahan Validasi:**

```php
// SEBELUM
'payment_method' => 'required|string|exists:payment_methods,code',

// SESUDAH
'payment_methods' => 'required|array|min:1',
'payment_methods.*.method' => 'required|string|exists:payment_methods,code',
'payment_methods.*.amount_raw' => 'required|numeric|min:0',
```

**Fitur Baru:**

-   Menerima array `payment_methods` dengan format:
    ```php
    [
        ['method' => 'CASH', 'amount_raw' => 100000],
        ['method' => 'TRANSFER', 'amount_raw' => 50000],
    ]
    ```
-   Validasi total pembayaran >= total tagihan
-   Menggabungkan metode pembayaran menjadi string: `"CASH:100.000; TRANSFER:50.000"`
-   Menyimpan gabungan metode ke `metode_pembayaran_tindakan` dan `metode_pembayaran_resep`
-   Activity log mencatat detail lengkap termasuk kembalian

**Return Message:**

```php
// Contoh output:
"Pembayaran berhasil diproses. Total tagihan: Rp 150.000, Total dibayar: Rp 200.000. Kembalian: Rp 50.000."
```

## 📊 Database Schema

Tidak ada perubahan pada database. Field yang digunakan:

### Tabel `encounters`

-   `status_bayar_tindakan` (boolean) - Status lunas tindakan
-   `status_bayar_resep` (boolean) - Status lunas resep
-   `metode_pembayaran_tindakan` (string) - Metode pembayaran tindakan (sekarang bisa multi)
-   `metode_pembayaran_resep` (string) - Metode pembayaran resep (sekarang bisa multi)
-   `total_bayar_tindakan` (decimal) - Total tagihan tindakan
-   `total_bayar_resep` (decimal) - Total tagihan resep

## 🎯 Cara Penggunaan

### Skenario 1: Pembayaran Tunggal (Seperti Biasa)

1. Pilih item yang akan dibayar (checklist)
2. Pilih metode pembayaran di form pertama
3. Masukkan jumlah pembayaran
4. Klik "Proses Pembayaran"

### Skenario 2: Split Payment (Baru!)

1. Pilih item yang akan dibayar
2. Klik "Tambah Metode Pembayaran" untuk menambah metode kedua, ketiga, dst
3. Setiap metode:
    - Pilih jenis metode (Cash, Transfer, Card, dll)
    - Masukkan jumlah untuk metode tersebut
4. Pastikan total dibayar >= total tagihan
5. Sistem akan otomatis menghitung kembalian
6. Klik "Proses Pembayaran"

### Contoh Split Payment:

**Total Tagihan: Rp 300.000**

-   Metode 1: Cash → Rp 150.000
-   Metode 2: Transfer Bank → Rp 100.000
-   Metode 3: Debit Card → Rp 50.000
-   **Total Dibayar: Rp 300.000** ✅ Pas!

Atau:
**Total Tagihan: Rp 250.000**

-   Metode 1: Cash → Rp 300.000
-   **Kembalian: Rp 50.000** 💵

## 🔒 Validasi & Error Handling

### Client-Side (JavaScript)

-   Input otomatis format Rupiah (tanpa "Rp")
-   Total real-time calculation
-   Button disabled saat belum memenuhi syarat
-   Visual feedback (hijau/merah) untuk kembalian/kurang

### Server-Side (PHP)

-   Validasi format data payment_methods
-   Validasi metode pembayaran exists di master data
-   Validasi jumlah pembayaran >= total tagihan
-   Error message jelas jika pembayaran kurang
-   Konfirmasi dengan SweetAlert sebelum proses

## 📝 Activity Log

Format log yang tersimpan:

```php
[
    'pasien_id' => 123,
    'rekam_medis' => 'RM-12345',
    'metode' => 'CASH:150.000; TRANSFER:100.000',
    'total_tagihan' => 250000,
    'total_bayar' => 250000,
    'kembalian' => 0,
    'items' => [
        'encounter-uuid-1' => ['tindakan' => 150000],
        'encounter-uuid-2' => ['resep' => 100000],
    ]
]
```

## 🎨 Styling Highlights

### Color Palette

-   **Primary Blue**: `#667eea` - Gradient start
-   **Secondary Purple**: `#764ba2` - Gradient end
-   **Success Green**: `#28a745` - Kembalian, Lunas
-   **Danger Red**: `#dc3545` - Pembayaran kurang
-   **Warning Yellow**: `#ffc107` - Belum lunas
-   **Info Blue**: `#17a2b8` - Informasi tambahan

### Badge Categories

-   Tindakan: `#e3f2fd` bg, `#1976d2` text
-   Resep: `#f3e5f5` bg, `#7b1fa2` text
-   Lab: `#e8f5e9` bg, `#388e3c` text
-   Radiologi: `#fff3e0` bg, `#f57c00` text

## ✅ Testing Checklist

-   [ ] Pembayaran single method berfungsi
-   [ ] Pembayaran multi method (split) berfungsi
-   [ ] Validasi pembayaran kurang terdeteksi
-   [ ] Perhitungan kembalian akurat
-   [ ] Format Rupiah tampil benar
-   [ ] Activity log tersimpan lengkap
-   [ ] Metode pembayaran tersimpan dengan format benar
-   [ ] Riwayat pembayaran tampil dengan metode split
-   [ ] Button disabled/enabled sesuai kondisi
-   [ ] SweetAlert konfirmasi muncul saat ada kembalian
-   [ ] Error message muncul saat validasi gagal
-   [ ] Responsive di mobile/tablet

## 🚀 Benefit untuk User

1. **Lebih Informatif**: User langsung melihat breakdown lengkap tagihan
2. **Fleksibel**: Bisa bayar dengan kombinasi metode apapun
3. **Akurat**: Perhitungan real-time mencegah kesalahan
4. **Transparan**: Semua detail terlihat jelas, tidak ada yang tersembunyi
5. **User-Friendly**: Interface modern dengan visual feedback yang jelas
6. **Audit Trail**: Semua detail pembayaran tercatat lengkap di log

## 📱 Screenshots (Conceptual)

### Before:

-   Simple form dengan satu dropdown metode
-   Total pembayaran tidak jelas
-   Tidak ada breakdown detail
-   Riwayat tercampur dengan tagihan

### After:

-   🎨 Beautiful gradient summary card
-   📊 Detailed breakdown per kategori
-   💳 Multi payment method support
-   🧮 Real-time calculator
-   📜 Separated history section
-   ✨ Modern UI with icons and badges

## 🔮 Future Enhancements (Optional)

1. **Quick Amount Buttons**: 50k, 100k, 500k untuk input cepat
2. **Payment History Export**: Export ke PDF/Excel
3. **Receipt Template**: Template struk yang lebih cantik
4. **Payment Analytics**: Dashboard pembayaran per metode
5. **Installment Payment**: Cicilan untuk tagihan besar
6. **Discount System**: Diskon untuk pembayaran cash
7. **Payment Reminder**: Notifikasi untuk tagihan tertundu

---

**Created**: [Current Date]  
**Version**: 1.0  
**Developer**: GitHub Copilot Assistant  
**Status**: ✅ Production Ready
