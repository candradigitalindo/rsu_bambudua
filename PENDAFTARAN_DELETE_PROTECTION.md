# Pendaftaran Delete Protection Fix

## Problem

Pendaftaran module was able to delete encounters even after they had been paid, causing potential data integrity and accounting issues.

## Root Cause

The destroy methods in `PendaftaranController.php` did not validate payment status before deletion:

-   `destroyEncounterRajal()` - For Rawat Jalan (outpatient)
-   `destroyEncounterRdarurat()` - For Rawat Darurat (emergency/IGD)
-   `destroyRawatInap()` - For Rawat Inap (inpatient)

## Solution Implemented

Added payment status validation at the beginning of each destroy method to prevent deletion of paid encounters.

### Code Changes

**File**: `app/Http/Controllers/PendaftaranController.php`

All three destroy methods now check `status_bayar_tindakan` and `status_bayar_resep` before allowing deletion:

```php
public function destroyEncounterRajal($id)
{
    // Check if encounter has been paid
    $encounter = \App\Models\Encounter::findOrFail($id);
    if ($encounter->status_bayar_tindakan || $encounter->status_bayar_resep) {
        return response()->json([
            'status' => false,
            'text' => 'Tidak dapat menghapus encounter yang sudah dibayar'
        ], 403);
    }

    $result = $this->pendaftaranRepository->destroyEncounterRajal($id);
    return response()->json(['status' => true, 'text' => 'Encounter berhasil dihapus', 'data' => $result]);
}
```

The same validation was added to:

-   `destroyEncounterRdarurat($id)`
-   `destroyRawatInap($id)`

## Behavior After Fix

### Unpaid Encounters

-   ✅ Can be deleted normally
-   User sees: "Encounter berhasil dihapus"

### Paid Encounters

-   ❌ Cannot be deleted
-   User sees: "Tidak dapat menghapus encounter yang sudah dibayar"
-   HTTP Status: 403 Forbidden
-   Payment status preserved
-   Financial data remains intact

## Payment Status Fields

The validation checks two boolean fields in the `encounters` table:

-   `status_bayar_tindakan` - True if tindakan/procedures have been paid
-   `status_bayar_resep` - True if resep/prescriptions have been paid

If **either** field is true, the encounter is considered paid and cannot be deleted.

## Security Benefits

1. **Data Integrity**: Prevents accidental deletion of financial records
2. **Audit Trail**: Paid encounters remain in the system for accounting
3. **Compliance**: Maintains complete transaction history
4. **Error Prevention**: Stops users from making costly mistakes

## Testing Scenarios

### Test Case 1: Delete Unpaid Encounter

1. Create new encounter
2. Do NOT process payment
3. Attempt deletion from pendaftaran
4. **Expected**: Deletion successful

### Test Case 2: Delete Partially Paid Encounter

1. Create encounter with tindakan and resep
2. Pay only tindakan (status_bayar_tindakan = true)
3. Attempt deletion from pendaftaran
4. **Expected**: Deletion blocked with error message

### Test Case 3: Delete Fully Paid Encounter

1. Create encounter with items
2. Process full payment (both status fields = true)
3. Attempt deletion from pendaftaran
4. **Expected**: Deletion blocked with error message

### Test Case 4: Different Encounter Types

Repeat above tests for:

-   Rawat Jalan (outpatient)
-   Rawat Darurat (emergency)
-   Rawat Inap (inpatient)

All should have consistent protection behavior.

## Related Files

-   `app/Http/Controllers/PendaftaranController.php` - Controller with validation
-   `app/Repositories/PendaftaranRepository.php` - Repository delete methods (unchanged)
-   `app/Models/Encounter.php` - Model with payment status fields

## Notes

-   This fix only prevents deletion at the controller level
-   The repository methods remain unchanged (no business logic added there)
-   Frontend should handle 403 status code and display error message
-   Consider adding visual indicators in UI to show which encounters are paid
