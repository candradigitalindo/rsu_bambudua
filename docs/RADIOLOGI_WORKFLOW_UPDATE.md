# 🔄 Update Workflow Radiologi - Simplified Process

## Overview

Workflow radiologi telah disederhanakan untuk meningkatkan efisiensi. **Tidak lagi memerlukan penjadwalan** - permintaan radiologi langsung bisa diisi hasilnya dengan mencantumkan **dokter spesialis radiologi (radiolog)** yang melakukan pemeriksaan.

## 📋 Perubahan Workflow

### ❌ **Workflow LAMA:**

```
1. Dokter Request Radiologi (Status: requested)
2. Admin/Radiografer Jadwalkan (Status: scheduled)
3. Pemeriksaan Dilakukan (Status: processing)
4. Input Hasil oleh Radiografer (Status: completed)
```

### ✅ **Workflow BARU (Simplified):**

```
1. Dokter Request Radiologi (Status: processing) ← Langsung bisa dikerjakan
2. Input Hasil + Pilih Radiolog (Status: completed)
```

## 🗃️ Database Changes

### Migration 1: `add_radiologist_to_radiology_results`

```php
Schema::table('radiology_results', function (Blueprint $table) {
    $table->unsignedBigInteger('radiologist_id')->nullable()->after('radiology_request_id');
    $table->foreign('radiologist_id')->references('id')->on('users')->nullOnDelete();
});
```

### Migration 2: `change_radiology_status_default`

```php
Schema::table('radiology_requests', function (Blueprint $table) {
    $table->string('status')->default('processing')->change();
});
```

## 📊 Model Changes

### RadiologyResult Model

**Tambahan:**

-   Field: `radiologist_id` (foreign key to users)
-   Relationship: `radiologist()` → belongsTo(User)

```php
protected $fillable = [
    'radiology_request_id',
    'radiologist_id',  // ← NEW
    'findings',
    'impression',
    'payload',
    'files',
    'reported_by',
    'reported_at',
];

public function radiologist()
{
    return $this->belongsTo(User::class, 'radiologist_id');
}
```

## 🎯 Controller Changes

### RadiologiController

#### `resultsStore()` - Update Validation

**Tambahan:**

```php
$rules = [
    'radiologist_id' => 'required|exists:users,id',  // ← NEW: Required!
    'findings'   => 'required|string',
    'impression' => 'required|string',
    'attachments.*' => 'nullable|file|max:10240',
];

$result->radiologist_id = $data['radiologist_id'];  // ← Save radiologist
```

#### `requestsShow()` - Eager Load Radiologist

```php
$req = RadiologyRequest::with(['pasien', 'jenis', 'dokter', 'results' => function ($q) {
    $q->with(['radiologist', 'reporter'])->orderByDesc('created_at');
}])->findOrFail($id);
```

#### `resultsIndex()` - Include Radiologist

```php
$results = RadiologyResult::with(['request.pasien', 'request.jenis', 'radiologist', 'reporter'])
    ->whereHas('request', function ($query) {
        $query->where('status', 'completed');
    })
    ->orderByDesc('reported_at')->paginate(15);
```

### ObservasiController

#### Status Default = Processing

```php
if (strtolower($jp->type) === 'radiologi') {
    \App\Models\RadiologyRequest::create([
        'encounter_id' => $id,
        'pasien_id' => $encounter->pasien->id,
        'jenis_pemeriksaan_id' => $jp->id,
        'dokter_id' => $dokter->id,
        'status' => 'processing',  // ← Langsung processing!
        'price' => (float) $jp->harga,
        'created_by' => $dokter->id,
    ]);
}
```

#### API Response - Include Radiologist

```php
'latest' => $latest ? [
    'findings' => $latest->findings,
    'impression' => $latest->impression,
    'payload' => $latest->payload,
    'radiologist_name' => optional($latest->radiologist)->name,  // ← NEW
] : null,
```

## 🖥️ View Changes

### Form Input Hasil (`results.blade.php`)

**Dropdown Radiolog (Required):**

```blade
<div class="mb-3">
    <label class="form-label">Dokter Spesialis Radiologi <span class="text-danger">*</span></label>
    <select name="radiologist_id" class="form-select" required>
        <option value="">-- Pilih Radiolog --</option>
        @php
            $radiologists = \App\Models\User::where('role', 2)
                                            ->where('is_active', 1)
                                            ->orderBy('name')->get();
        @endphp
        @foreach($radiologists as $radiolog)
            <option value="{{ $radiolog->id }}">{{ $radiolog->name }}</option>
        @endforeach
    </select>
    <div class="form-text">Pilih dokter spesialis radiologi yang melakukan pemeriksaan</div>
</div>
```

### Detail Hasil (`show.blade.php`)

**Display Radiolog:**

```blade
<div class="mb-2">
    <div class="small text-muted">Dokter Spesialis Radiologi</div>
    <div class="fw-semibold">{{ optional($latestResult->radiologist)->name ?? '-' }}</div>
</div>
<div class="mb-2">
    <div class="small text-muted">Dilaporkan</div>
    <div class="fw-semibold">
        {{ optional($latestResult->reported_at)->format('d M Y H:i') }}
        oleh {{ optional($latestResult->reporter)->name ?? '-' }}
    </div>
</div>
```

**Action Buttons - Updated:**

```blade
@if ($st === 'processing')
    <a href="{{ route('radiologi.requests.results.edit', $req->id) }}"
       class="btn btn-success">
        <i class="bi bi-pencil-square me-1"></i>Input Hasil Pemeriksaan
    </a>
@elseif($st === 'completed')
    <a href="{{ route('radiologi.requests.print', $req->id) }}"
       target="_blank"
       class="btn btn-outline-primary">
        <i class="bi bi-printer me-1"></i>Cetak Hasil
    </a>
@endif
```

**Removed:**

-   ❌ Tombol "Jadwalkan" (tidak diperlukan lagi)
-   ❌ Routes untuk schedule create/store
-   ❌ Schedule management UI

### Observasi View (`_penunjang_request.blade.php`)

**Display Radiologist in Results:**

```javascript
${r.latest ? `
    ${r.latest.radiologist_name ?
        `<div class="small mb-1">
            <span class="text-muted">Radiolog:</span>
            <strong>${r.latest.radiologist_name}</strong>
        </div>`
    : ''}
    ${payloadHtml}
    <div class="small">
        <div><span class="text-muted">Findings:</span> ${r.latest.findings}</div>
        <div><span class="text-muted">Impression:</span> ${r.latest.impression}</div>
    </div>
` : '<div class="text-muted small">Belum ada hasil.</div>'}
```

## 🔄 Updated Flow Diagram

```
┌─────────────────────────────────────────────────────────┐
│ DOKTER (Observasi/EMR)                                  │
│ - Request pemeriksaan radiologi                         │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ↓
            [Status: PROCESSING]
                   │
                   ↓
┌─────────────────────────────────────────────────────────┐
│ PETUGAS RADIOLOGI                                       │
│ - Lakukan pemeriksaan                                   │
│ - Input hasil pemeriksaan                               │
│ - Pilih Dokter Spesialis Radiologi (RADIOLOG)          │
│ - Upload gambar/file (opsional)                         │
│ - Input custom fields (sesuai jenis pemeriksaan)       │
│ - Input findings & impression                           │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ↓
            [Status: COMPLETED]
                   │
                   ↓
┌─────────────────────────────────────────────────────────┐
│ HASIL TERSEDIA                                          │
│ - Dokter bisa lihat hasil di Observasi                 │
│ - Hasil bisa dicetak                                    │
│ - Data radiolog tercatat                                │
└─────────────────────────────────────────────────────────┘
```

## 📝 Field Mapping

| Field            | Required | Type        | Description                                           |
| ---------------- | -------- | ----------- | ----------------------------------------------------- |
| `radiologist_id` | ✅ Yes   | Foreign Key | Dokter spesialis radiologi yang melakukan pemeriksaan |
| `findings`       | ✅ Yes   | Long Text   | Temuan radiologi                                      |
| `impression`     | ✅ Yes   | Long Text   | Kesimpulan/diagnosis radiologi                        |
| `payload`        | ❌ No    | JSON        | Custom fields sesuai jenis pemeriksaan                |
| `attachments`    | ❌ No    | Files       | Gambar hasil pemeriksaan (X-ray, CT, MRI, dll)        |
| `reported_by`    | Auto     | Foreign Key | User yang input hasil (auto dari Auth)                |
| `reported_at`    | Auto     | Timestamp   | Waktu input hasil (auto now())                        |

## 🎯 Benefits

✅ **Simplified Workflow**: Tidak perlu penjadwalan, langsung input hasil
✅ **Faster Processing**: Mengurangi 1 step dalam workflow
✅ **Better Accountability**: Radiolog yang melakukan pemeriksaan tercatat jelas
✅ **Professional Documentation**: Nama radiolog muncul di hasil pemeriksaan
✅ **Flexible**: Tetap support custom fields untuk data terstruktur
✅ **Audit Trail**: Tetap track siapa yang input (`reported_by`) dan siapa yang periksa (`radiologist_id`)

## ⚠️ Important Notes

1. **Radiologist Selection is REQUIRED**: User harus memilih radiolog saat input hasil
2. **Default Status = Processing**: Semua permintaan baru langsung status `processing`
3. **Schedule Feature Removed**: Routes dan UI untuk scheduling tidak digunakan lagi
4. **Backward Compatible**: Data lama tetap bisa diakses, hanya field `radiologist_id` yang null

## 🔍 Validation

**Form Validation:**

```php
'radiologist_id' => 'required|exists:users,id'
```

**Error Message:**

-   "The radiologist id field is required."
-   "The selected radiologist id is invalid."

## 📊 Data Example

**Before (Old Data):**

```json
{
    "radiology_result_id": "uuid",
    "radiology_request_id": "uuid",
    "radiologist_id": null, // ← Tidak ada
    "findings": "...",
    "impression": "...",
    "reported_by": 123,
    "reported_at": "2025-10-25 10:00:00"
}
```

**After (New Data):**

```json
{
    "radiology_result_id": "uuid",
    "radiology_request_id": "uuid",
    "radiologist_id": 456, // ← Ada! (Dr. Radiolog)
    "findings": "...",
    "impression": "...",
    "payload": { "lvef": "65", "wall_motion": "Normal" },
    "reported_by": 123,
    "reported_at": "2025-10-26 14:30:00"
}
```

## 🚀 Migration Commands

```bash
# Run migrations
php artisan migrate

# Rollback if needed
php artisan migrate:rollback --step=2
```

## ✅ Testing Checklist

-   [ ] Request radiologi dari Observasi → Status langsung `processing`
-   [ ] Input hasil radiologi → Field radiolog muncul dan required
-   [ ] Pilih radiolog dari dropdown → Data tersimpan
-   [ ] View detail hasil → Nama radiolog muncul
-   [ ] View di Observasi → Radiolog muncul di hasil
-   [ ] Cetak hasil → Include nama radiolog
-   [ ] Data lama (tanpa radiolog) → Tetap bisa diakses, tampil "-"

---

**Updated:** 26 Oktober 2025  
**Status:** ✅ Implemented & Migrated  
**Impact:** High - Workflow Simplification
