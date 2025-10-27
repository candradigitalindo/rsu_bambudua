# Fix: Observasi Cetak - Missing Anamnesis and Prices

## Problem

URL: `http://127.0.0.1:8000/kunjungan/observasi/{id}/cetak`

Issues found:

1. **Anamnesis masih kosong** - Anamnesis data tidak tampil
2. **Harga tindakan kosong** - Prices for tindakan showing Rp 0
3. **Harga resep kosong** - Prices for resep showing Rp 0

## Root Cause

### 1. Anamnesis Issue

-   Anamnesis relationship might return as array instead of object
-   Direct access to `$encounter->anamnesis->field` fails when it's an array

### 2. Tindakan Price Issue

-   View was using `$tindakan->price`
-   Correct field in TindakanEncounter model is `tindakan_harga`

### 3. Resep Price Issue

-   View was using `$detail->harga_satuan` and `$detail->sub_total`
-   Correct fields in ResepDetail model are `harga` and `total_harga`

## Solution Implemented

**File**: `resources/views/pages/encounter/cetak-rawat-jalan.blade.php`

### Fix 1: Anamnesis Handling

Added PHP logic to handle anamnesis as both object and array:

```php
@php
    $anamnesis = is_object($encounter->anamnesis)
        ? $encounter->anamnesis
        : (is_array($encounter->anamnesis) && count($encounter->anamnesis) > 0
            ? (object)$encounter->anamnesis[0]
            : null);
@endphp
@if ($anamnesis)
    <div class="section-title">ANAMNESIS</div>
    <table class="info-table">
        <tr>
            <td>Keluhan Utama</td>
            <td>:</td>
            <td>{{ $anamnesis->chief_complaint ?? '-' }}</td>
        </tr>
        ...
    </table>
@endif
```

Changed all references from `$encounter->anamnesis->field` to `$anamnesis->field` in:

-   Anamnesis section
-   Tanda Vital section
-   Pemeriksaan Fisik section

### Fix 2: Tindakan Prices

Changed field name from `price` to `tindakan_harga`:

**Before:**

```blade
<td class="text-end">Rp {{ number_format($tindakan->price ?? 0, 0, ',', '.') }}</td>
<td class="text-end"><strong>Rp
    {{ number_format(($tindakan->price ?? 0) * $tindakan->qty, 0, ',', '.') }}</strong>
</td>
```

**After:**

```blade
<td class="text-end">Rp {{ number_format($tindakan->tindakan_harga ?? 0, 0, ',', '.') }}</td>
<td class="text-end"><strong>Rp
    {{ number_format(($tindakan->tindakan_harga ?? 0) * $tindakan->qty, 0, ',', '.') }}</strong>
</td>
```

Total calculation also updated:

```blade
{{ number_format($encounter->tindakan->sum(fn($t) => ($t->tindakan_harga ?? 0) * $t->qty), 0, ',', '.') }}
```

### Fix 3: Resep Prices

Changed field names:

-   `harga_satuan` → `harga`
-   `sub_total` → `total_harga`

**Before:**

```blade
<td class="text-end">Rp {{ number_format($detail->harga_satuan ?? 0, 0, ',', '.') }}</td>
<td class="text-end"><strong>Rp
    {{ number_format($detail->sub_total ?? 0, 0, ',', '.') }}</strong></td>
```

**After:**

```blade
<td class="text-end">Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}</td>
<td class="text-end"><strong>Rp
    {{ number_format($detail->total_harga ?? 0, 0, ',', '.') }}</strong></td>
```

Total calculation:

```blade
{{ number_format($encounter->resep->details->sum('total_harga'), 0, ',', '.') }}
```

## Database Field Reference

### TindakanEncounter Model

```php
protected $fillable = [
    'encounter_id',
    'tindakan_id',
    'tindakan_name',
    'tindakan_description',
    'tindakan_harga',      // ← Unit price
    'qty',
    'total_harga',         // ← Total = tindakan_harga * qty
];
```

### ResepDetail Model

```php
protected $fillable = [
    'id',
    'resep_id',
    'nama_obat',
    'qty',
    'aturan_pakai',
    'expired_at',
    'product_apotek_id',
    'harga',              // ← Unit price
    'total_harga',        // ← Total = harga * qty
    'status',
];
```

### Anamnesis Model

Expected fields:

-   `chief_complaint` - Keluhan utama
-   `history_of_present_illness` - Riwayat penyakit sekarang
-   `past_medical_history` - Riwayat penyakit dahulu
-   `allergy_history` - Riwayat alergi
-   `physical_examination` - Pemeriksaan fisik
-   Vital signs: `systolic`, `diastolic`, `heart_rate`, `resp_rate`, `temperature`, `spo2`, `pain_scale`, `weight`, `height`

## Expected Result

After fix, the print page should display:

✅ **Anamnesis Section**

-   Keluhan Utama
-   Riwayat Penyakit Sekarang
-   Riwayat Penyakit Dahulu
-   Riwayat Alergi

✅ **Tanda Vital Section**

-   Tekanan Darah (mmHg)
-   Nadi (x/mnt)
-   Respirasi (x/mnt)
-   Suhu (°C)
-   SpO2 (%)
-   Skala Nyeri (/10)
-   Berat Badan (kg)
-   Tinggi Badan (cm)

✅ **Pemeriksaan Fisik Section**

-   Physical examination notes

✅ **Tindakan Section**

-   Correct unit prices (Rp)
-   Correct total per item
-   Correct total tindakan

✅ **Resep Section**

-   Correct unit prices (Rp)
-   Correct total per item
-   Correct total resep

## Testing

1. Open `http://127.0.0.1:8000/kunjungan/observasi/{encounter_id}/cetak`
2. Verify anamnesis data appears
3. Verify tindakan prices are not Rp 0
4. Verify resep prices are not Rp 0
5. Verify totals calculate correctly
6. Test print functionality (auto-print on load)

## Related Files

-   `resources/views/pages/encounter/cetak-rawat-jalan.blade.php` - Main print template
-   `app/Http/Controllers/EncounterController.php` - Controller
-   `app/Repositories/EncounterRepository.php` - Data retrieval with eager loading
-   `app/Models/TindakanEncounter.php` - Tindakan model
-   `app/Models/ResepDetail.php` - Resep detail model

## Route

```php
Route::get('/observasi/{id}/cetak', [EncounterController::class, 'cetakEncounter'])
    ->name('observasi.cetakEncounter');
```
