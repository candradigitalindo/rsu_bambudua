# 📋 Custom Fields untuk Hasil Radiologi

## Overview

Sistem radiologi sekarang mendukung **custom fields** atau kolom dinamis yang dapat disesuaikan untuk setiap jenis pemeriksaan radiologi. Fitur ini memungkinkan petugas radiologi untuk menginput data terstruktur sesuai dengan template yang sudah didefinisikan.

## Implementasi

### 1. Database Schema

#### Table: `template_fields`

```sql
- id (bigint, PK)
- jenis_pemeriksaan_id (uuid, FK to jenis_pemeriksaan_penunjangs)
- field_name (varchar) - nama field untuk backend (snake_case)
- field_label (varchar) - label yang ditampilkan ke user
- field_type (varchar) - tipe input: text, number, textarea, select
- placeholder (varchar) - placeholder atau opsi untuk select (dipisah |)
- order (int) - urutan tampilan field
```

#### Table: `radiology_results`

```sql
- payload (json) - menyimpan data custom fields dalam format JSON
```

### 2. Model Relationships

**JenisPemeriksaanPenunjang Model:**

```php
public function templateFields()
{
    return $this->hasMany(TemplateField::class, 'jenis_pemeriksaan_id')
                ->orderBy('order');
}
```

### 3. Controller Logic

**RadiologiController::resultsEdit()**

-   Load template fields: `with(['pasien', 'jenis.templateFields', 'dokter'])`
-   Pass ke view untuk render form dinamis

**RadiologiController::resultsStore()**

-   Validasi dinamis untuk setiap custom field
-   Simpan ke kolom `payload` sebagai JSON
-   Format: `['field_name' => 'value', ...]`

### 4. View Implementation

#### Form Input (`results.blade.php`)

```blade
@if($req->jenis && $req->jenis->templateFields && $req->jenis->templateFields->isNotEmpty())
  <div class="card bg-light mb-3">
    <div class="card-header">
      <h6>Data Pemeriksaan {{ $req->jenis->name }}</h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        @foreach($req->jenis->templateFields as $field)
          <div class="col-md-6">
            <label>{{ $field->field_label }}</label>

            @if($field->field_type === 'textarea')
              <textarea name="payload[{{ $field->field_name }}]"
                        class="form-control"
                        placeholder="{{ $field->placeholder }}">
              </textarea>

            @elseif($field->field_type === 'number')
              <input type="number" step="0.01"
                     name="payload[{{ $field->field_name }}]"
                     class="form-control"
                     placeholder="{{ $field->placeholder }}">

            @elseif($field->field_type === 'select')
              <select name="payload[{{ $field->field_name }}]"
                      class="form-select">
                <option value="">-- Pilih --</option>
                @foreach(explode('|', $field->placeholder) as $option)
                  <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
              </select>

            @else
              <input type="text"
                     name="payload[{{ $field->field_name }}]"
                     class="form-control"
                     placeholder="{{ $field->placeholder }}">
            @endif
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif
```

#### Display Results (`show.blade.php`)

```blade
@if(!empty($latestResult->payload) && is_array($latestResult->payload))
  <div class="card bg-light mb-3">
    <div class="card-header py-2">
      <h6>Data Pemeriksaan</h6>
    </div>
    <div class="card-body">
      <div class="row g-2">
        @foreach($latestResult->payload as $key => $value)
          <div class="col-md-6">
            <div class="small text-muted">
              {{ ucwords(str_replace('_', ' ', $key)) }}
            </div>
            <div class="fw-semibold">{{ $value ?: '-' }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif
```

### 5. API Response (Observasi Module)

**ObservasiController::radiologyRequests()**

```php
'latest' => $latest ? [
    'findings' => $latest->findings,
    'impression' => $latest->impression,
    'payload' => $latest->payload, // Custom fields
] : null,
```

**JavaScript Display:**

```javascript
// Build payload/custom fields display if exists
let payloadHtml = "";
if (r.latest && r.latest.payload && typeof r.latest.payload === "object") {
    payloadHtml = '<div class="card bg-light mt-2 mb-2">...';
    for (let [key, value] of Object.entries(r.latest.payload)) {
        if (value) {
            let label = key
                .replace(/_/g, " ")
                .replace(/\b\w/g, (l) => l.toUpperCase());
            payloadHtml += `<div class="col-md-6">
                <small class="text-muted">${label}</small>
                <div class="fw-semibold small">${value}</div>
            </div>`;
        }
    }
    payloadHtml += "</div>";
}
```

## Contoh Use Case

### Pemeriksaan USG Jantung (Echocardiography)

**Template Fields:**

```
1. field_name: lvef
   field_label: LVEF (%)
   field_type: number
   placeholder: Masukkan nilai LVEF

2. field_name: aorta_root_diam
   field_label: Aorta Root Diameter (mm)
   field_type: number
   placeholder: Ukuran diameter aorta

3. field_name: mitral_regurgitation
   field_label: Mitral Regurgitation
   field_type: select
   placeholder: None|Trivial|Mild|Moderate|Severe

4. field_name: wall_motion
   field_label: Wall Motion
   field_type: textarea
   placeholder: Deskripsi gerakan dinding jantung
```

**Hasil yang tersimpan di payload:**

```json
{
    "lvef": "65",
    "aorta_root_diam": "32.5",
    "mitral_regurgitation": "Mild",
    "wall_motion": "Normokinetic"
}
```

### Pemeriksaan CT Scan Brain

**Template Fields:**

```
1. field_name: contrast_used
   field_label: Contrast Enhancement
   field_type: select
   placeholder: No|Yes - Pre and Post Contrast

2. field_name: brain_parenchyma
   field_label: Brain Parenchyma
   field_type: textarea
   placeholder: Deskripsi parenkim otak

3. field_name: ventricles
   field_label: Ventricles
   field_type: select
   placeholder: Normal|Dilated|Compressed

4. field_name: hemorrhage
   field_label: Hemorrhage
   field_type: select
   placeholder: None|Epidural|Subdural|Subarachnoid|Intracerebral
```

## Keuntungan Sistem Custom Fields

✅ **Structured Data**: Data terstruktur untuk analisis dan reporting
✅ **Flexibility**: Setiap jenis pemeriksaan bisa punya template berbeda
✅ **Standardization**: Memastikan konsistensi input data
✅ **Searchable**: Data JSON bisa di-query untuk analytics
✅ **Extensible**: Mudah menambah field baru tanpa ubah schema database

## Field Types Supported

| Type       | Description                | Use Case                   |
| ---------- | -------------------------- | -------------------------- |
| `text`     | Single line text input     | Nama, singkatan            |
| `number`   | Numeric input with decimal | Ukuran, dimensi, nilai lab |
| `textarea` | Multi-line text            | Deskripsi panjang          |
| `select`   | Dropdown selection         | Kategori, severity level   |

**Note:** Untuk select, gunakan `|` sebagai separator di placeholder:

```
Normal|Abnormal|Critical
```

## Migration untuk Template Fields

Sudah ada: `/database/migrations/2025_07_30_010101_create_template_fields_table.php`

## Cara Menambah Template Fields

### Via Database Seeder:

```php
use App\Models\JenisPemeriksaanPenunjang;
use App\Models\TemplateField;

$usgJantung = JenisPemeriksaanPenunjang::where('name', 'USG Jantung')->first();

TemplateField::create([
    'jenis_pemeriksaan_id' => $usgJantung->id,
    'field_name' => 'lvef',
    'field_label' => 'LVEF (%)',
    'field_type' => 'number',
    'placeholder' => 'Masukkan nilai LVEF',
    'order' => 1,
]);
```

### Via Admin Interface (Recommended - Future):

Buat CRUD untuk master data Template Fields di menu Admin.

## Compliance dengan Standar

✅ **Structured Reporting**: Mendukung template pelaporan terstruktur
✅ **Data Quality**: Validasi input sesuai tipe field
✅ **Interoperability**: Format JSON mudah untuk integrasi HL7/FHIR

## Next Steps

1. **✅ DONE**: Implementasi custom fields di form hasil radiologi
2. **✅ DONE**: Tampilkan custom fields di detail hasil
3. **✅ DONE**: Integrasi dengan modul Observasi
4. **🔜 TODO**: Buat CRUD untuk manage Template Fields
5. **🔜 TODO**: Export hasil radiologi ke PDF dengan custom fields
6. **🔜 TODO**: Implementasi search/filter berdasarkan custom fields
7. **🔜 TODO**: Analytics dashboard based on custom fields data

---

**Updated:** 26 Oktober 2025
**Status:** ✅ Implemented & Production Ready
