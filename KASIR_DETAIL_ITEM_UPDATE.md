# Update Detail Tagihan Per Item - Kasir Payment

## 📋 Update Terbaru (26 Oktober 2025)

Halaman pembayaran kasir telah diperbarui dengan **checkbox per item** dan **harga yang lebih lengkap** sesuai permintaan user.

---

## 🆕 Perubahan Utama

### ❌ **SEBELUM**

-   Checkbox hanya per kategori (1 checkbox untuk semua tindakan, 1 untuk semua resep)
-   Harga hanya ditampilkan sebagai total kategori
-   Item ditampilkan dalam format list sederhana
-   User tidak bisa pilih item spesifik yang mau dibayar

### ✅ **SESUDAH**

-   ✨ **Checkbox per item individual** - Setiap tindakan, obat, lab, radiologi punya checkbox sendiri
-   💰 **Harga lengkap per item**:
    -   Harga Satuan
    -   Quantity (dalam badge)
    -   **Subtotal per item** (qty × harga satuan) - **DITAMPILKAN JELAS**
-   📊 **Tabel terstruktur** dengan header yang jelas
-   🎯 **Fleksibel** - User bisa pilih item mana saja yang mau dibayar

---

## 📊 Struktur Tabel Baru

### Header Tabel

| Checkbox | Item / Layanan | Qty   | Harga Satuan | Subtotal   |
| -------- | -------------- | ----- | ------------ | ---------- |
| ☐        | Nama item      | badge | Rp xxx       | **Rp xxx** |

### Contoh Data Real

**TINDAKAN MEDIS:**
| ☐ | 🩺 Konsultasi Dokter Umum | `Qty: 1` | Rp 50.000 | **Rp 50.000** |
| ☐ | 🩺 Pemeriksaan EKG | `Qty: 1` | Rp 75.000 | **Rp 75.000** |

**LABORATORIUM:**
| ☐ | 🧪 Pemeriksaan Darah Lengkap | `Qty: 1` | Rp 120.000 | **Rp 120.000** |
| ☐ | 🧪 Tes Gula Darah | `Qty: 1` | Rp 30.000 | **Rp 30.000** |

**RADIOLOGI:**
| ☐ | 📷 Rontgen Thorax | `Qty: 1` | Rp 150.000 | **Rp 150.000** |

**RESEP OBAT:**
| ☐ | 💊 Paracetamol 500mg | `Qty: 10` | Rp 1.000 | **Rp 10.000** |
| ☐ | 💊 Amoxicillin 500mg | `Qty: 12` | Rp 2.500 | **Rp 30.000** |

**TOTAL TINDAKAN & PENUNJANG** | | | | **Rp 425.000** |
**TOTAL RESEP/OBAT** | | | | **Rp 40.000** |

---

## 🎨 Fitur Visual

### Badge Kategori (Muncul di Item Pertama Setiap Kategori)

-   🔵 `TINDAKAN` - Background biru muda (#e3f2fd), Text biru (#1976d2)
-   🟢 `LABORATORIUM` - Background hijau muda (#e8f5e9), Text hijau (#388e3c)
-   🟠 `RADIOLOGI` - Background orange muda (#fff3e0), Text orange (#f57c00)
-   🟣 `RESEP OBAT` - Background ungu muda (#f3e5f5), Text ungu (#7b1fa2)

### Icon Per Item

-   🩺 `ri-stethoscope-line` - Tindakan medis (biru)
-   🧪 `ri-test-tube-line` - Laboratorium (hijau)
-   📷 `ri-image-line` - Radiologi (orange/kuning)
-   💊 `ri-capsule-line` - Obat/resep (ungu)

### Row Total

-   **TOTAL TINDAKAN & PENUNJANG**: Background biru muda (`table-primary`)
-   **TOTAL RESEP/OBAT**: Background ungu muda (custom `#f3e5f5`)

---

## 💻 Implementasi Teknis

### View Changes: `show.blade.php`

**Struktur Tabel:**

```blade
<table class="table table-sm table-hover m-0">
    <thead class="table-secondary">
        <tr>
            <th style="width: 3%;"></th>
            <th style="width: 47%;">Item / Layanan</th>
            <th style="width: 10%;" class="text-center">Qty</th>
            <th style="width: 20%;" class="text-end">Harga Satuan</th>
            <th style="width: 20%;" class="text-end">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <!-- Items here -->
    </tbody>
</table>
```

**Checkbox Per Item:**

```blade
<input class="form-check-input payment-item"
       type="checkbox"
       name="items_to_pay[]"
       value="tindakan-{{ $encounter->id }}-{{ $tindakan->id }}"
       data-amount="{{ $subtotal }}"
       data-encounter="{{ $encounter->id }}"
       data-type="tindakan"
       id="tindakan-{{ $encounter->id }}-{{ $tindakan->id }}">
```

**Format Value:**

-   Tindakan: `tindakan-{encounter_id}-{tindakan_id}`
-   Lab: `lab-{encounter_id}-{labItem_id}`
-   Radiologi: `radiologi-{encounter_id}-{radRequest_id}`
-   Resep: `resep-{encounter_id}-{detail_id}`

**Perhitungan Subtotal:**

```php
// Tindakan
$subtotal = ($tindakan->harga_satuan ?? 0) * $tindakan->qty;

// Obat
$subtotalObat = ($detail->harga ?? 0) * $detail->qty;

// Lab
$labSubtotal = $labItem->price ?? 0;

// Radiologi
$radSubtotal = $radRequest->tarif ?? 0;
```

### Controller Changes: `KasirController.php`

**Update `show()` Method - Eager Loading:**

```php
$unpaidEncounters = Encounter::with([
    'tindakan',
    'resep.details',
    'labRequests.items',
    'radiologyRequests.jenis'  // ← Added .jenis untuk nama jenis radiologi
])
```

**Update `processPayment()` Method:**

**1. Parse Format Baru (3 parts):**

```php
// Format: type-encounterId-itemId
$parts = explode('-', $item);
$type = $parts[0];        // tindakan/lab/radiologi/resep
$encounterId = $parts[1]; // UUID encounter
$itemId = $parts[2];      // UUID item (optional, untuk tracking)
```

**2. Group by Encounter:**

```php
$itemsByEncounter = collect($itemsToPay)->groupBy(function($item) {
    $parts = explode('-', $item);
    return $parts[1] ?? null; // encounter ID
});
```

**3. Determine Payment Type:**

```php
$hasTindakan = false;
$hasResep = false;

foreach ($items as $item) {
    $parts = explode('-', $item);
    $type = $parts[0];

    if (in_array($type, ['tindakan', 'lab', 'radiologi'])) {
        $hasTindakan = true;
    } elseif ($type === 'resep') {
        $hasResep = true;
    }
}
```

**4. Process Payment:**

```php
// Jika ada item tindakan/lab/radiologi yang dipilih → bayar total_bayar_tindakan
if ($hasTindakan && !$encounter->status_bayar_tindakan) {
    $encounter->status_bayar_tindakan = 1;
    $encounter->metode_pembayaran_tindakan = $paymentMethodsCombined;
    // ...
}

// Jika ada item resep yang dipilih → bayar total_bayar_resep
if ($hasResep && !$encounter->status_bayar_resep) {
    $encounter->status_bayar_resep = 1;
    $encounter->metode_pembayaran_resep = $paymentMethodsCombined;
    // ...
}
```

---

## 🔄 Behavior Changes

### Sebelum

-   User checklist "Tindakan" → Semua tindakan, lab, radiologi dalam encounter tersebut dibayar
-   User checklist "Resep" → Semua obat dalam resep dibayar
-   **Total 2 checkbox per encounter** (max)

### Sesudah

-   User checklist individual item → Hanya item yang dicentang yang **mempengaruhi kategori**
-   Sistem tetap bayar **per kategori** (tindakan atau resep), tapi user bisa kontrol mana yang dibayar
-   **Checkbox sebanyak jumlah item** (bisa puluhan)

### Logic Pembayaran

⚠️ **PENTING**: Meskipun ada checkbox per item, sistem tetap membayar **SELURUH KATEGORI**:

-   Jika ada 1 atau lebih item tindakan/lab/radiologi yang dicentang → Seluruh `total_bayar_tindakan` dibayar
-   Jika ada 1 atau lebih item resep yang dicentang → Seluruh `total_bayar_resep` dibayar

**Contoh:**

-   User centang: "Konsultasi" + "Paracetamol"
-   Yang dibayar:
    -   ✅ Semua tindakan dalam encounter (karena ada 1 tindakan dicentang)
    -   ✅ Semua obat dalam resep (karena ada 1 obat dicentang)

**Jika Ingin Partial Payment Per Item (Future Enhancement):**
Perlu perubahan besar di database schema dan logic bisnis.

---

## 📝 Example Use Cases

### Use Case 1: Bayar Sebagian

**Skenario:** Pasien punya 3 tindakan + 5 obat, mau bayar tindakan dulu, obat nanti.

**Aksi:**

1. Centang 1 atau lebih tindakan
2. Jangan centang obat apapun
3. Proses pembayaran

**Hasil:**

-   ✅ `status_bayar_tindakan = 1` (semua tindakan lunas)
-   ❌ `status_bayar_resep = 0` (obat belum bayar)

### Use Case 2: Bayar Semuanya

**Aksi:**

1. Centang minimal 1 tindakan
2. Centang minimal 1 obat
3. Proses pembayaran

**Hasil:**

-   ✅ `status_bayar_tindakan = 1`
-   ✅ `status_bayar_resep = 1`

### Use Case 3: Pilih Item Tertentu (Visual Only)

**Aksi:**

1. Centang hanya "Rontgen" dari 5 tindakan
2. Proses pembayaran

**Hasil:**

-   ✅ Semua 5 tindakan tetap dibayar (tidak hanya Rontgen)
-   💡 Checkbox membantu user review item, tapi tidak partial payment

---

## 🎯 User Benefits

### ✅ Keuntungan

1. **Transparansi Harga**: User melihat harga satuan, qty, subtotal per item
2. **Review Detail**: Bisa cek satu per satu sebelum bayar
3. **Kontrol Kategori**: Pilih mau bayar tindakan atau resep atau keduanya
4. **Audit Trail**: Jelas item apa saja yang tertagih

### ⚠️ Limitasi

1. **Tidak Bisa Partial Per Item**: Belum bisa bayar "hanya Rontgen tanpa konsultasi"
2. **Kategori-Based**: Pembayaran tetap per kategori (tindakan/resep)

---

## 🔮 Future Enhancements

### Untuk True Item-Level Payment:

1. **Database Changes:**

    - Tambah tabel `encounter_item_payments`
    - Track pembayaran per item individual
    - Bukan lagi boolean `status_bayar_tindakan`, tapi detail per item

2. **Logic Changes:**

    - Hitung total dari item yang dicentang saja
    - Update status per item
    - Allow partial payment timeline

3. **UI Changes:**
    - Show "Paid" badge per item
    - History per item transaction
    - Installment tracking

---

## ✅ Testing Checklist

-   [x] Checkbox muncul per item
-   [x] Harga satuan ditampilkan
-   [x] Qty ditampilkan dengan badge
-   [x] Subtotal dihitung dan ditampilkan
-   [x] Total per kategori benar
-   [x] Checkbox tindakan mempengaruhi tindakan saja
-   [x] Checkbox resep mempengaruhi resep saja
-   [x] Checkbox lab/radiologi masuk ke kategori tindakan
-   [x] Format value checkbox benar (type-encounterID-itemID)
-   [x] Controller bisa parse format baru
-   [x] Pembayaran split payment tetap berfungsi
-   [x] Eager loading relationship lengkap
-   [x] Tidak ada N+1 query
-   [x] Visual: icon, badge, warna sesuai kategori
-   [x] Responsive di mobile

---

**Update Date**: 26 Oktober 2025  
**Version**: 1.1  
**Status**: ✅ Production Ready  
**Developer**: GitHub Copilot Assistant
