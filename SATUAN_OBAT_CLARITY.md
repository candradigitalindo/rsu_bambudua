# Peningkatan Kejelasan Satuan Obat pada Resep

## Masalah

Jumlah satuan obat pada form resep masih ambigu - tidak jelas apakah "1" berarti 1 strip, 1 botol, atau 1 tablet.

## Solusi yang Diimplementasikan

### 1. Database Migration

**File:** `database/migrations/2025_11_19_075359_add_satuan_to_resep_details_table.php`

-   Menambahkan kolom `satuan` (string, nullable) pada tabel `resep_details`
-   Kolom ditempatkan setelah `nama_obat` untuk kemudahan pembacaan

### 2. Model Update

**File:** `app/Models/ResepDetail.php`

-   Menambahkan `satuan` ke dalam array `$fillable`
-   Memungkinkan mass assignment untuk field satuan

### 3. Repository Update

**File:** `app/Repositories/ObservasiRepository.php`

#### getProdukApotek()

-   Sudah mengembalikan field `satuan` dari tabel `product_apoteks`
-   Format: `['id', 'code', 'name', 'satuan', 'harga', 'stok']`

#### postResepDetail()

-   Menambahkan penyimpanan satuan dari produk apotek
-   `$resepDetail->satuan = $stokTerdekat->productApotek->satuan;`

### 4. View Update (Frontend)

**File:** `resources/views/pages/observasi/partials/_tatalaksana.blade.php`

#### Dropdown Select2

-   Menampilkan satuan dalam format: `[Nama Obat] [Satuan] - Rp [Harga]`
-   Contoh: "Paracetamol [Strip] - Rp 5.000"
-   Satuan disimpan dalam data object select2 untuk digunakan nanti

#### Form Input Jumlah Obat

-   Label berubah dinamis: "Jumlah **[Satuan]**"
-   Contoh: "Jumlah Strip", "Jumlah Botol", "Jumlah Tablet"
-   Hint text informatif: "Jumlah dalam satuan **Strip**"
-   Reset ke "Jumlah Obat" jika tidak ada obat dipilih

#### Tabel Resep

-   Kolom "Jumlah" sekarang menampilkan: `[Qty] [Satuan]`
-   Contoh: "5 Strip", "2 Botol", "10 Tablet"
-   Satuan ditampilkan dengan styling `<small class="text-muted">` untuk estetika

### 5. JavaScript Enhancement

**Event Handlers:**

```javascript
// Update label saat obat dipilih
$(document).on("select2:select", "#product_apotek_id", function (e) {
    const data = e.params.data;
    if (data.satuan) {
        $("#label-satuan-obat").text(data.satuan);
        $("#hint-satuan-obat").html(
            `Jumlah dalam satuan <strong>${data.satuan}</strong>`
        );
    }
});

// Reset label saat obat dihapus
$(document).on("select2:clear", "#product_apotek_id", function () {
    $("#label-satuan-obat").text("Obat");
    $("#hint-satuan-obat").text(
        "Pilih obat terlebih dahulu untuk melihat satuan"
    );
});
```

## Fitur yang Ditambahkan

### ✅ Satuan di Dropdown Pencarian

-   Membantu user memilih obat dengan melihat satuan langsung
-   Format: Nama + [Satuan] + Harga

### ✅ Label Dinamis pada Form

-   Label "Jumlah" berubah sesuai satuan obat yang dipilih
-   Menghilangkan ambiguitas saat input jumlah

### ✅ Satuan di Tabel Resep

-   Setiap baris menampilkan satuan obat
-   User bisa melihat dengan jelas: "5 Strip" bukan hanya "5"

### ✅ Hint Text Informatif

-   Memberikan petunjuk visual tentang satuan yang sedang digunakan
-   Auto-update saat obat berubah

## Contoh Penggunaan

### Sebelum:

```
Nama Obat: Paracetamol
Jumlah: 5  ← Ambigu! 5 apa?
```

### Sesudah:

```
Pilih Obat: Paracetamol [Strip] - Rp 5.000
Jumlah Strip: 5  ← Jelas! 5 Strip
```

### Di Tabel:

```
| Nama Obat      | Jumlah          | Aturan Pakai |
|----------------|-----------------|--------------|
| Paracetamol    | 5 Strip         | 3x Sehari    |
| Amoxicillin    | 2 Botol         | 2x Sehari    |
| Vitamin C      | 30 Tablet       | 1x Sehari    |
```

## Testing Checklist

-   [x] Migration berhasil dijalankan
-   [x] Model dapat menyimpan field satuan
-   [x] Repository menyimpan satuan dari product_apotek
-   [x] Dropdown menampilkan satuan obat
-   [x] Label form berubah dinamis sesuai satuan
-   [x] Tabel menampilkan satuan pada kolom jumlah
-   [ ] Test manual: Tambah obat dengan satuan berbeda
-   [ ] Test manual: Verifikasi satuan tersimpan di database
-   [ ] Test manual: Satuan muncul saat load resep

## Catatan Teknis

1. **Nullable Field**: Kolom `satuan` nullable untuk backward compatibility dengan data lama
2. **Relasi Data**: Satuan diambil dari `product_apoteks.satuan` saat membuat resep detail
3. **UI/UX**: Menggunakan Bootstrap badge dan text-muted untuk styling yang konsisten
4. **Real-time Update**: Event listener Select2 untuk update instant tanpa reload

## Manfaat

✨ **Kejelasan**: User tahu persis satuan obat yang diinput
✨ **Akurasi**: Mengurangi kesalahan dosis karena satuan yang jelas
✨ **Profesionalitas**: Resep lebih informatif dan mudah dibaca
✨ **Konsistensi**: Satuan selalu ditampilkan di semua bagian form

---

**Dibuat:** 19 November 2025
**Versi:** 1.0
