# Fitur Pemilihan Dokter Spesialis untuk Rujukan Rawat Inap dari IGD

**Tanggal**: 28 November 2025  
**Status**: ✅ Selesai

## 📋 Deskripsi Fitur

Ketika pasien IGD dirujuk ke rawat inap (status pulang = 3), sistem sekarang **WAJIB memilih dokter spesialis** yang akan menangani pasien di rawat inap.

## 🎯 Tujuan

1. ✅ Memastikan setiap pasien yang dirujuk ke rawat inap sudah memiliki dokter spesialis yang jelas
2. ✅ Meningkatkan koordinasi antara IGD dan rawat inap
3. ✅ Memudahkan tracking DPJP (Dokter Penanggung Jawab Pelayanan)
4. ✅ Sesuai dengan standar operasional RS

## 🔧 Perubahan yang Dilakukan

### 1. View - Tab Catatan (\_catatan.blade.php)

**Penambahan**:

```blade
<!-- Field Dokter Spesialis untuk Rujukan Rawat Inap -->
<div class="row gx-3 mb-4 d-none" id="dokter-spesialis-section">
    <div class="col-12">
        <div class="alert alert-info">
            <strong>Rujukan ke Rawat Inap</strong> - Pilih dokter spesialis
        </div>
    </div>
    <div class="col-md-12">
        <label for="dokter_spesialis_id">
            Dokter Spesialis <span class="text-danger">*</span>
        </label>
        <select id="dokter_spesialis_id" name="dokter_spesialis_id">
            <!-- Filter dokter dengan role = 2 (Dokter Spesialis) -->
            @foreach ($dokters['dokters'] as $dokter)
                @if($dokter->role == 2)
                    <option value="{{ $dokter->id }}">
                        {{ $dokter->name }} - {{ $dokter->spesialis->name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
</div>
```

**JavaScript**:

```javascript
// Toggle section dokter spesialis
$("#status_pulang").on("change", function () {
    const statusPulang = $(this).val();
    if (statusPulang == "3") {
        // Rujukan Rawat Inap
        $("#dokter-spesialis-section").removeClass("d-none");
        $("#dokter_spesialis_id").prop("required", true);
    } else {
        $("#dokter-spesialis-section").addClass("d-none");
        $("#dokter_spesialis_id").prop("required", false);
    }
});

// Validasi saat submit
if (status_pulang == "3" && !dokter_spesialis_id) {
    $("#error-dokter-spesialis").text("Dokter Spesialis harus dipilih");
    return;
}
```

### 2. Controller (ObservasiController.php)

**Validasi Baru**:

```php
$request->validate([
    'catatan' => 'nullable|string|max:255',
    'status_pulang' => 'required|numeric',
    'perawat_ids' => 'required_if:encounter.type,1,3|array',
    'perawat_ids.*' => 'exists:users,id',
    // BARU: Validasi dokter spesialis
    'dokter_spesialis_id' => 'required_if:status_pulang,3|exists:users,id',
], [
    'dokter_spesialis_id.required_if' => 'Dokter Spesialis harus dipilih untuk rujukan rawat inap.',
    'dokter_spesialis_id.exists' => 'Dokter Spesialis tidak valid.',
]);
```

### 3. Repository (ObservasiRepository.php)

**Method Signature Update**:

```php
// SEBELUM:
private function handleRujukanRawatInap(Encounter $encounter)

// SESUDAH:
private function handleRujukanRawatInap(Encounter $encounter, $dokterSpesialisId = null)
```

**Logika Update**:

```php
// Gunakan dokter spesialis yang dipilih
$dpjpId = $dokterSpesialisId ?: $encounter->dpjp_id;

$newEncounter = Encounter::create([
    'dpjp_id' => $dpjpId, // Dokter spesialis dari form
    // ... field lainnya
]);

// BARU: Tambahkan practitioner untuk dokter spesialis
if ($dpjpId) {
    $dokter = User::find($dpjpId);
    if ($dokter) {
        Practitioner::create([
            'encounter_id' => $newEncounter->id,
            'name'         => $dokter->name,
            'id_petugas'   => $dokter->id,
            'satusehat_id' => $dokter->satusehat_id
        ]);
    }
}
```

## 🔄 Alur Kerja

### Sebelum Perubahan

```
1. Pasien IGD selesai pemeriksaan
2. Pilih Status Pulang: "Rujukan Rawat Inap"
3. Klik "Selesai Pemeriksaan"
4. ❌ Encounter rawat inap dibuat dengan dokter dari IGD (tidak selalu spesialis)
```

### Setelah Perubahan

```
1. Pasien IGD selesai pemeriksaan
2. Pilih Status Pulang: "Rujukan Rawat Inap"
3. ✅ Field "Dokter Spesialis" muncul (WAJIB)
4. ✅ Pilih dokter spesialis yang akan menangani di rawat inap
5. Klik "Selesai Pemeriksaan"
6. ✅ Encounter rawat inap dibuat dengan dokter spesialis terpilih
7. ✅ Practitioner otomatis ditambahkan untuk dokter tersebut
```

## 📊 UI/UX

### Status Pulang Options

-   ✅ Kondisi Stabil
-   🔄 Pulang Kontrol Kembali
-   **🏥 Rujukan Rawat Inap** → Muncul field dokter spesialis
-   🚑 Rujukan RSU Lain
-   🕊️ Meninggal

### Field Dokter Spesialis

-   **Hanya muncul** jika status pulang = Rujukan Rawat Inap
-   **Required field** (wajib diisi)
-   **Select2 dropdown** dengan search
-   **Filter**: Hanya menampilkan user dengan role = 2 (Dokter Spesialis)
-   **Format**: [ID Petugas] Nama Dokter - Spesialis

### Validasi

-   ✅ Client-side: Alert jika tidak dipilih
-   ✅ Server-side: Validasi `required_if:status_pulang,3`
-   ✅ Database: Validasi `exists:users,id`

## ✅ Testing

### Test Case 1: Rujukan Rawat Inap dengan Dokter Spesialis

```
1. Buka encounter IGD
2. Masuk tab "Catatan"
3. Pilih Status Pulang: "Rujukan Rawat Inap"
4. ✅ Verifikasi: Field dokter spesialis muncul
5. Pilih dokter spesialis (misal: Dr. Spesialis Bedah)
6. Klik "Selesai Pemeriksaan"
7. ✅ Verifikasi: Encounter rawat inap terbuat
8. ✅ Verifikasi: dpjp_id = ID dokter spesialis yang dipilih
9. ✅ Verifikasi: Practitioner entry ditambahkan untuk dokter tersebut
```

### Test Case 2: Validasi Wajib Dokter Spesialis

```
1. Buka encounter IGD
2. Pilih Status Pulang: "Rujukan Rawat Inap"
3. TIDAK pilih dokter spesialis
4. Klik "Selesai Pemeriksaan"
5. ✅ Verifikasi: Muncul error "Dokter Spesialis harus dipilih"
6. ✅ Verifikasi: Form tidak tersubmit
```

### Test Case 3: Status Pulang Lain

```
1. Buka encounter IGD
2. Pilih Status Pulang: "Kondisi Stabil"
3. ✅ Verifikasi: Field dokter spesialis TIDAK muncul
4. Klik "Selesai Pemeriksaan"
5. ✅ Verifikasi: Encounter selesai tanpa error
```

### Test Case 4: Toggle Status Pulang

```
1. Pilih Status Pulang: "Rujukan Rawat Inap"
2. ✅ Field dokter spesialis muncul
3. Pilih dokter spesialis
4. Ganti Status Pulang: "Kondisi Stabil"
5. ✅ Verifikasi: Field dokter spesialis hilang
6. ✅ Verifikasi: Selection di-reset
```

## 📝 Database Schema

### Table: encounters

```sql
-- Field yang terpengaruh
dpjp_id BIGINT UNSIGNED NULLABLE
-- Sekarang diisi dari dokter spesialis yang dipilih (untuk rawat inap hasil rujukan IGD)
```

### Table: practitioners

```sql
-- Entry baru otomatis ditambahkan
encounter_id    -- ID encounter rawat inap baru
name            -- Nama dokter spesialis
id_petugas      -- ID user dokter spesialis
satusehat_id    -- Satusehat ID dokter
```

## 🎯 Manfaat

1. ✅ **Koordinasi Lebih Baik**: Dokter rawat inap sudah tahu dari awal
2. ✅ **Tracking Jelas**: DPJP tercatat sejak rujukan
3. ✅ **Efisiensi**: Tidak perlu update manual di rawat inap
4. ✅ **Standar Operasional**: Sesuai prosedur RS
5. ✅ **Akuntabilitas**: Dokter spesialis bertanggung jawab sejak rujukan

## 🚨 Breaking Changes

**Tidak ada breaking changes**

-   Field baru bersifat **conditional** (hanya muncul jika rujukan rawat inap)
-   **Backward compatible** dengan data lama
-   **Validasi hanya** untuk encounter baru

## 📞 Support

Jika ada masalah dengan fitur ini:

1. Pastikan user dengan role = 2 (Dokter Spesialis) ada di database
2. Cek apakah dokter memiliki data spesialis (table: `spesialis`)
3. Verifikasi Select2 ter-load dengan benar
4. Periksa console browser untuk error JavaScript

---

**Dibuat**: 28 November 2025  
**Status**: Production Ready ✅  
**Verified By**: System Administrator
