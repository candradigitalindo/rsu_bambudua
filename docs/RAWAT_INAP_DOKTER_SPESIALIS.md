# Rawat Inap - Dokter Spesialis (DPJP) Implementation

## Overview

Implementasi field Dokter Spesialis (DPJP - Dokter Penanggung Jawab Pelayanan) pada formulir pendaftaran rawat inap. Setiap pasien rawat inap harus memiliki DPJP yang bertanggung jawab atas pelayanan medis mereka.

## Problem Statement

Sebelumnya, formulir pendaftaran rawat inap memiliki field "Dokter" yang:

-   Label tidak jelas (generic "Dokter")
-   Tidak menyimpan `dpjp_id` pada encounter
-   Menggunakan validasi array padahal field single select
-   Tidak konsisten dengan flow IGD rujukan yang sudah menggunakan dokter spesialis

## Solution Implemented

### 1. Frontend Changes

**File:** `resources/views/pages/pendaftaran/index.blade.php`

#### Label Update (Line ~808)

```blade
<!-- BEFORE -->
<label class="form-label" for="dokter_rawatRinap">Dokter</label>

<!-- AFTER -->
<label class="form-label" for="dokter_rawatRinap">
    <i class="ri-stethoscope-line me-1 text-primary"></i>
    Dokter Spesialis (DPJP)
</label>
```

#### Placeholder Update

```blade
<!-- BEFORE -->
<option value="">-- Pilih Dokter --</option>

<!-- AFTER -->
<option value="">-- Pilih Dokter Spesialis --</option>
```

**Features:**

-   Icon stethoscope untuk visual clarity
-   Label jelas menyebutkan "Dokter Spesialis (DPJP)"
-   Placeholder lebih descriptive
-   Single select (sudah benar)
-   Select2 enabled untuk searchability

### 2. Controller Validation

**File:** `app/Http/Controllers/PendaftaranController.php`

```php
// BEFORE
'dokter' => 'required|array',
'dokter.required' => 'Kolom Dokter harus dipilih',

// AFTER
'dokter' => 'required|exists:users,id',
'dokter.required' => 'Kolom Dokter Spesialis (DPJP) harus dipilih',
'dokter.exists' => 'Dokter Spesialis yang dipilih tidak valid',
```

**Changes:**

-   Validation berubah dari `array` ke single value dengan `exists:users,id`
-   Error message lebih spesifik
-   Added validation untuk memastikan dokter exists di database

### 3. Repository Logic

**File:** `app/Repositories/PendaftaranRepository.php`

```php
public function postRawatInap($request, $id)
{
    $encounter = Encounter::findOrFail($id);
    Practitioner::where('encounter_id', $encounter->id)->delete();

    // Get the selected dokter spesialis (DPJP)
    $dokterSpesialisId = $request->dokter;
    $dokterSpesialis = null;

    if ($dokterSpesialisId) {
        $dokterSpesialis = User::find($dokterSpesialisId);

        if ($dokterSpesialis) {
            // ✅ Set DPJP on encounter
            $encounter->update([
                'dpjp_id' => $dokterSpesialis->id
            ]);

            // ✅ Create Practitioner entry
            Practitioner::create([
                'encounter_id' => $encounter->id,
                'name'         => $dokterSpesialis->name,
                'id_petugas'   => $dokterSpesialis->id,
                'satusehat_id' => $dokterSpesialis->satusehat_id
            ]);
        }
    }

    // Update admission with dokter name
    $encounter->admission->update([
        'ruang_id' => $request->ruang_id,
        'ruangan_id' => $request->ruangan,
        'nama_dokter' => $dokterSpesialis ? $dokterSpesialis->name : null,
    ]);

    // ... (companion handling remains same)
}
```

**Key Changes:**

1. **`dpjp_id` Assignment:** Encounter now stores the DPJP ID
2. **Single Dokter Handling:** Changed from array loop to single dokter
3. **Practitioner Creation:** Automatically creates practitioner entry
4. **Admission Update:** Uses selected dokter's name

## Data Flow

### Before Fix

```
User selects dokter → AJAX sends dokter ID → Validation expects array ❌
→ Creates Practitioners but NO dpjp_id set ❌
```

### After Fix

```
User selects dokter spesialis → AJAX sends dokter ID → Validation checks exists ✅
→ Sets encounter.dpjp_id ✅ → Creates Practitioner entry ✅
```

## Database Impact

### Encounter Table Updates

```sql
-- Each rawat inap encounter will now have:
UPDATE encounters
SET dpjp_id = <selected_dokter_id>
WHERE id = <encounter_id>;
```

### Practitioners Table

```sql
-- One practitioner entry per DPJP
INSERT INTO practitioners (encounter_id, name, id_petugas, satusehat_id)
VALUES (<encounter_id>, '<dokter_name>', <dokter_id>, '<satusehat_id>');
```

## Consistency with IGD Flow

### IGD Rujukan → Rawat Inap

**File:** `app/Repositories/ObservasiRepository.php`

```php
private function handleRujukanRawatInap($encounter, $dokterSpesialisId)
{
    // Creates new encounter with dpjp_id
    $newEncounter = Encounter::create([
        'dpjp_id' => $dokterSpesialisId, // ✅ Already set
        // ...
    ]);
}
```

### Direct Rawat Inap Registration

**File:** `app/Repositories/PendaftaranRepository.php`

```php
public function postRawatInap($request, $id)
{
    // Updates existing encounter with dpjp_id
    $encounter->update([
        'dpjp_id' => $dokterSpesialisId // ✅ Now set
    ]);
}
```

**Result:** Both paths (IGD rujukan and direct registration) now consistently set `dpjp_id`.

## Testing Checklist

### Functional Testing

-   [ ] Open pendaftaran page
-   [ ] Select a patient and click "Rawat Inap"
-   [ ] Verify field label shows "Dokter Spesialis (DPJP)" with icon
-   [ ] Select a dokter from dropdown
-   [ ] Fill in other required fields (jaminan, ruangan, pendamping)
-   [ ] Submit form
-   [ ] Verify success message

### Database Verification

```sql
-- Check encounter has dpjp_id
SELECT id, no_encounter, dpjp_id, type
FROM encounters
WHERE type = 2
ORDER BY created_at DESC
LIMIT 1;

-- Check practitioner entry created
SELECT * FROM practitioners
WHERE encounter_id = <encounter_id>;

-- Check admission has dokter name
SELECT * FROM admissions
WHERE encounter_id = <encounter_id>;
```

### Edge Cases

-   [ ] Try submitting without selecting dokter → Should show validation error
-   [ ] Select non-existent dokter ID → Should show "tidak valid" error
-   [ ] Update existing rawat inap → Should replace old practitioners

## Benefits

1. **Data Consistency:** All rawat inap encounters now have DPJP assigned
2. **Clear UI/UX:** Users understand they're selecting DPJP, not generic dokter
3. **Validation:** Ensures dokter exists and is valid
4. **Traceability:** Easy to track which dokter is responsible for each patient
5. **Integration Ready:** Consistent structure for reporting and SATUSEHAT integration

## Files Modified

1. `resources/views/pages/pendaftaran/index.blade.php` - Form label and placeholder
2. `app/Http/Controllers/PendaftaranController.php` - Validation rules
3. `app/Repositories/PendaftaranRepository.php` - Repository logic
4. `docs/RAWAT_INAP_DOKTER_SPESIALIS.md` - This documentation

## Related Documents

-   `docs/IGD_RUJUKAN_SPESIALIS.md` - IGD rujukan dengan dokter spesialis
-   `docs/IGD_INCENTIVE_FIX.md` - IGD incentive calculation
-   `docs/IMPLEMENTASI-BERTAHAP.md` - Overall implementation plan

## Notes

-   All users with `role = 2` (DOKTER) are shown in the dropdown
-   The dropdown already uses Select2 for searchability
-   JavaScript validation happens client-side, server validation is final
-   Existing rawat inap data (before this fix) may have NULL dpjp_id - consider data migration if needed

## Migration Consideration (Optional)

If you want to backfill existing rawat inap encounters:

```sql
-- Find rawat inap encounters without dpjp_id
SELECT e.id, e.no_encounter, p.id_petugas, u.name
FROM encounters e
LEFT JOIN practitioners p ON p.encounter_id = e.id
LEFT JOIN users u ON u.id = p.id_petugas
WHERE e.type = 2 AND e.dpjp_id IS NULL
AND u.role = 2
LIMIT 10;

-- Backfill dpjp_id from first practitioner with role=2
UPDATE encounters e
SET dpjp_id = (
    SELECT p.id_petugas
    FROM practitioners p
    JOIN users u ON u.id = p.id_petugas
    WHERE p.encounter_id = e.id
    AND u.role = 2
    LIMIT 1
)
WHERE e.type = 2 AND e.dpjp_id IS NULL;
```

---

**Implementation Date:** 2024  
**Status:** ✅ Completed  
**Tested:** Awaiting user verification
