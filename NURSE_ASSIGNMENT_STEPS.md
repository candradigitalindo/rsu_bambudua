# 🩺 STEP-BY-STEP ASSIGNMENT PERAWAT

Dokumentasi lengkap tentang cara assign perawat di berbagai layanan (Rawat Jalan, IGD, Rawat Inap, dan Radiologi).

---

## 📋 DAFTAR ISI

1. [Rawat Jalan](#1-rawat-jalan)
2. [IGD (Instalasi Gawat Darurat)](#2-igd-instalasi-gawat-darurat)
3. [Rawat Inap](#3-rawat-inap)
4. [Radiologi](#4-radiologi)
5. [Database Structure](#5-database-structure)
6. [FAQ](#6-faq)

---

## 1. RAWAT JALAN

### 📍 Lokasi Assignment

**SAAT MENYELESAIKAN ENCOUNTER** di halaman Observasi

### 🔄 Alur Lengkap

#### Step 1: Pendaftaran (Tidak Ada Assignment Perawat)

**Lokasi**: `Pendaftaran > Rawat Jalan`

```
┌─────────────────────────────────────┐
│  FORM PENDAFTARAN RAWAT JALAN       │
├─────────────────────────────────────┤
│  ✓ Pilih Pasien                     │
│  ✓ Jenis Jaminan                    │
│  ✓ Pilih Dokter (multiple)          │
│  ✓ Tujuan Kunjungan                 │
│  ✓ Klinik                           │
│                                     │
│  ❌ TIDAK ADA PILIHAN PERAWAT       │
└─────────────────────────────────────┘
```

**File**: `app/Repositories/PendaftaranRepository.php`

```php
public function postRawatJalan($request, $id)
{
    // Buat encounter
    $encounter = Encounter::create([
        'type' => 1, // Rawat Jalan
        // ... data lainnya
    ]);

    // Simpan dokter ke tabel practitioner
    if ($request->filled('dokter') && is_array($request->dokter)) {
        foreach ($dokters as $dokter) {
            Practitioner::create([
                'encounter_id' => $encounter->id,
                'id_petugas'   => $dokter->id,
            ]);
        }
    }

    // ❌ TIDAK ADA assignment perawat di sini
}
```

#### Step 2: Dokter Melakukan Pemeriksaan

**Lokasi**: `Dashboard Dokter > Layanan Medis > Observasi`

Dokter melakukan:

-   Anamnesis
-   Pemeriksaan Fisik
-   Diagnosis
-   Tindakan
-   Resep Obat
-   Pemeriksaan Penunjang

**❌ BELUM ADA ASSIGNMENT PERAWAT**

#### Step 3: Menyelesaikan Encounter (Assignment Perawat)

**Lokasi**: `Observasi > Tab Catatan`

```
┌─────────────────────────────────────────┐
│  🏁 SELESAIKAN ENCOUNTER                │
├─────────────────────────────────────────┤
│  📝 Catatan (opsional)                  │
│                                         │
│  👨‍⚕️ PILIH PERAWAT (WAJIB) *           │
│  ┌───────────────────────────────────┐ │
│  │ ☐ Ns. Budi Santoso                │ │
│  │ ☐ Ns. Siti Aminah                 │ │
│  │ ☐ Ns. Ahmad Fauzi                 │ │
│  └───────────────────────────────────┘ │
│                                         │
│  🏥 Status Pulang:                      │
│  • Sembuh                               │
│  • Pulang Paksa                         │
│  • Rujuk ke Rawat Inap                  │
│                                         │
│  [💾 Simpan & Selesai]                  │
└─────────────────────────────────────────┘
```

**File**: `resources/views/pages/observasi/partials/_catatan.blade.php`

```blade
<div class="mb-3">
    <label class="form-label">Pilih Perawat yang Menangani <span class="text-danger">*</span></label>
    @php
        $perawatsData = $perawats['perawats'] ?? [];
        $terpilih = $perawats['perawat_terpilih'] ?? [];
    @endphp
    @foreach($perawatsData as $p)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="perawat_ids[]"
                   value="{{ $p->id }}" id="perawat{{ $p->id }}"
                   {{ in_array($p->id, $terpilih) ? 'checked' : '' }}>
            <label class="form-check-label" for="perawat{{ $p->id }}">
                {{ $p->name }}
            </label>
        </div>
    @endforeach
</div>
```

#### Step 4: Submit & Proses Insentif

**File**: `app/Repositories/ObservasiRepository.php`

```php
public function postCatatanEncounter($request, $id)
{
    $encounter = Encounter::find($id);

    // 1. Update status encounter
    $encounter->status = 2; // Selesai
    $encounter->condition = $request->status_pulang;
    $encounter->save();

    // 2. Simpan perawat ke pivot table encounter_nurse
    $perawatIds = $request->input('perawat_ids', []);
    if ($request->has('perawat_ids')) {
        $encounter->nurses()->sync($perawatIds);
    }

    // 3. Buat insentif perawat
    if ($encounter->status == 2) {
        $this->processIncentives($encounter, $perawatIds);
    }
}

private function processIncentives(Encounter $encounter, array $perawatIds)
{
    // Ambil setting insentif
    $settings = IncentiveSetting::whereIn('setting_key', [
        'perawat_per_encounter_rawat_jalan'
    ])->pluck('setting_value', 'setting_key');

    $amount = $settings['perawat_per_encounter_rawat_jalan'] ?? 0;

    // Insentif Perawat Rawat Jalan (type 1)
    if ($encounter->type == 1 && $amount > 0 && !empty($perawatIds)) {
        foreach ($perawatIds as $perawatId) {
            Incentive::create([
                'user_id' => $perawatId,
                'encounter_id' => $encounter->id,
                'amount' => $amount, // Rp 10,000
                'type' => 'encounter_rawat_jalan',
                'status' => 'pending',
            ]);
        }
    }
}
```

### 📊 Database Records

**Tabel: `encounter_nurse` (Pivot Table)**

```
id | encounter_id | user_id | created_at          | updated_at
---|--------------|---------|---------------------|-------------------
1  | uuid-123     | 15      | 2025-11-14 10:00:00 | 2025-11-14 10:00:00
2  | uuid-123     | 18      | 2025-11-14 10:00:00 | 2025-11-14 10:00:00
```

**Tabel: `incentives`**

```
id | user_id | encounter_id | amount  | type                      | status
---|---------|--------------|---------|---------------------------|--------
1  | 15      | uuid-123     | 10000   | encounter_rawat_jalan     | pending
2  | 18      | uuid-123     | 10000   | encounter_rawat_jalan     | pending
```

### ⚠️ Validasi

**File**: `app/Http/Controllers/ObservasiController.php`

```php
$request->validate([
    'perawat_ids' => 'required_if:encounter.type,1|array',
    'perawat_ids.*' => 'exists:users,id',
], [
    'perawat_ids.required_if' => 'Perawat harus dipilih untuk Rawat Jalan.',
]);
```

---

## 2. IGD (INSTALASI GAWAT DARURAT)

### 📍 Lokasi Assignment

**SAAT MENYELESAIKAN ENCOUNTER** di halaman Observasi (sama seperti Rawat Jalan)

### 🔄 Alur Lengkap

#### Step 1: Pendaftaran IGD

**Lokasi**: `Pendaftaran > IGD`

```
┌─────────────────────────────────────┐
│  FORM PENDAFTARAN IGD               │
├─────────────────────────────────────┤
│  ✓ Pilih Pasien                     │
│  ✓ Jenis Jaminan                    │
│  ✓ Pilih Dokter IGD (multiple)      │
│  ✓ Tujuan Kunjungan                 │
│  ✓ Tingkat Kegawatan (1-5)          │
│  ✓ Cara Datang                      │
│  ✓ Keluhan Utama                    │
│                                     │
│  ❌ TIDAK ADA PILIHAN PERAWAT       │
└─────────────────────────────────────┘
```

**File**: `app/Repositories/PendaftaranRepository.php`

```php
public function postRawatDarurat($request, $id)
{
    $encounter = Encounter::create([
        'type' => 3, // IGD
        // ... data lainnya
    ]);

    // Simpan dokter
    if ($request->filled('dokter') && is_array($request->dokter)) {
        foreach ($dokters as $dokter) {
            Practitioner::create([
                'encounter_id' => $encounter->id,
                'id_petugas'   => $dokter->id,
            ]);
        }
    }

    // ❌ TIDAK ADA assignment perawat
}
```

#### Step 2: Pemeriksaan & Step 3: Selesaikan Encounter

**SAMA PERSIS dengan Rawat Jalan** - Assignment perawat dilakukan saat menyelesaikan encounter di tab Catatan.

#### Step 4: Proses Insentif (Berbeda Jumlahnya)

```php
private function processIncentives(Encounter $encounter, array $perawatIds)
{
    $settings = IncentiveSetting::whereIn('setting_key', [
        'perawat_per_encounter_igd'
    ])->pluck('setting_value', 'setting_key');

    $amount = $settings['perawat_per_encounter_igd'] ?? 0;

    // Insentif Perawat IGD (type 3)
    if ($encounter->type == 3 && $amount > 0 && !empty($perawatIds)) {
        foreach ($perawatIds as $perawatId) {
            Incentive::create([
                'user_id' => $perawatId,
                'amount' => $amount, // Rp 15,000 (lebih tinggi dari rawat jalan)
                'type' => 'encounter_igd',
                'status' => 'pending',
            ]);
        }
    }
}
```

### 📊 Perbedaan dengan Rawat Jalan

| Aspek             | Rawat Jalan             | IGD                    |
| ----------------- | ----------------------- | ---------------------- |
| Type              | 1                       | 3                      |
| Waktu Assignment  | Saat selesai encounter  | Saat selesai encounter |
| Lokasi Assignment | Tab Catatan             | Tab Catatan            |
| Jumlah Insentif   | Rp 10,000               | Rp 15,000              |
| Type Insentif     | `encounter_rawat_jalan` | `encounter_igd`        |

---

## 3. RAWAT INAP

### 📍 Lokasi Assignment

**OTOMATIS dari Visit/Treatment yang dilakukan dokter**

### 🔄 Alur Lengkap

#### Step 1: Admisi Rawat Inap

**Lokasi**: `Pendaftaran > Rawat Inap` atau `Rujukan dari RJ/IGD`

```
┌─────────────────────────────────────┐
│  FORM ADMISI RAWAT INAP             │
├─────────────────────────────────────┤
│  ✓ Pilih Encounter (dari RJ/IGD)    │
│  ✓ Pilih Dokter Penanggung Jawab    │
│  ✓ Pilih Ruangan & Kamar            │
│  ✓ Data Keluarga Pendamping         │
│                                     │
│  ❌ TIDAK ADA PILIHAN PERAWAT       │
└─────────────────────────────────────┘
```

**Catatan**: Pasien rawat inap TIDAK memilih perawat secara manual. Perawat mendapatkan insentif dari **setiap visit/tindakan** yang dilakukan.

#### Step 2: Dokter Melakukan Visit/Tindakan

**Lokasi**: `Observasi Rawat Inap > Tab Tindakan`

```
┌─────────────────────────────────────────┐
│  ➕ TAMBAH TINDAKAN RAWAT INAP          │
├─────────────────────────────────────────┤
│  📅 Tanggal: 14/11/2025 10:00          │
│                                         │
│  🏥 Jenis Request:                      │
│  • Visit Dokter                         │
│  • Tindakan                             │
│  • Konsultasi                           │
│                                         │
│  👨‍⚕️ Dokter Pelaksana: Dr. Ahmad       │
│  🩺 Tindakan: Visite Harian            │
│  💵 Harga: Rp 50,000                    │
│                                         │
│  📝 Hasil/Catatan: (opsional)           │
│                                         │
│  [💾 Simpan Tindakan]                   │
└─────────────────────────────────────────┘
```

**File**: `app/Repositories/ObservasiRepository.php`

```php
public function postInpatientTreatment($request, $id)
{
    // Buat record tindakan
    $treatment = InpatientTreatment::create([
        'admission_id' => $id,
        'request_type' => $request->type, // 'Visit'
        'performed_by' => $request->dokter_id,
        'tindakan_id' => $request->tindakan_id,
        'treatment_date' => $request->treatment_date,
        'result' => $request->result,
    ]);

    // ❌ TIDAK ADA assignment perawat di sini
}
```

#### Step 3: Selesaikan Encounter (Proses Insentif)

**Lokasi**: `Observasi Rawat Inap > Tab Catatan`

```php
private function processIncentives(Encounter $encounter, array $perawatIds)
{
    // Insentif Rawat Inap (type 2)
    if ($encounter->type == 2) {
        $inpatientAdmission = InpatientAdmission::where('encounter_id', $encounter->id)->first();

        if ($inpatientAdmission) {
            // Ambil semua treatment/visit yang sudah dilakukan
            $treatments = InpatientTreatment::where('admission_id', $inpatientAdmission->id)
                ->where('request_type', 'Visit')
                ->get();

            $amount = $settings['perawat_per_encounter_rawat_inap'] ?? 0;

            // Buat insentif per tindakan
            foreach ($treatments as $treatment) {
                // Ambil perawat yang assigned ke ruangan pasien
                $nurses = $this->getNursesForRoom($inpatientAdmission->room_id);

                foreach ($nurses as $nurse) {
                    Incentive::create([
                        'user_id' => $nurse->id,
                        'encounter_id' => $encounter->id,
                        'inpatient_treatment_id' => $treatment->id,
                        'amount' => $amount, // Rp 20,000 per treatment
                        'type' => 'treatment_inap',
                        'description' => 'Insentif Perawat Rawat Inap - ' . $treatment->request_type,
                        'status' => 'pending',
                    ]);
                }
            }
        }
    }
}
```

### 🆚 Perbedaan Rawat Inap

| Aspek            | Rawat Jalan/IGD                          | Rawat Inap                          |
| ---------------- | ---------------------------------------- | ----------------------------------- |
| Cara Assignment  | **Manual** - pilih checkbox saat selesai | **Otomatis** - dari room assignment |
| Waktu Assignment | 1x saat encounter selesai                | **Multiple** - per treatment/visit  |
| Basis Insentif   | Per encounter                            | **Per treatment**                   |
| Jumlah Insentif  | 1x per encounter                         | **Nx per N treatments**             |

**Contoh**:

```
Pasien rawat inap 5 hari dengan 5 visit dokter:
- Visit 1 → Insentif Rp 20,000
- Visit 2 → Insentif Rp 20,000
- Visit 3 → Insentif Rp 20,000
- Visit 4 → Insentif Rp 20,000
- Visit 5 → Insentif Rp 20,000
Total: Rp 100,000
```

---

## 4. RADIOLOGI

### 📍 Lokasi Assignment

**SAAT INPUT HASIL PEMERIKSAAN RADIOLOGI**

### 🔄 Alur Lengkap

#### Step 1: Permintaan Radiologi

**Lokasi**: `Observasi > Tab Pemeriksaan Penunjang` atau `Radiologi > Permintaan Baru`

```
┌─────────────────────────────────────┐
│  PERMINTAAN RADIOLOGI               │
├─────────────────────────────────────┤
│  ✓ Pilih Pasien/Encounter           │
│  ✓ Jenis Pemeriksaan (X-Ray, CT)    │
│  ✓ Dokter Perujuk                   │
│  ✓ Indikasi Klinis                  │
│                                     │
│  ❌ TIDAK ADA PILIHAN PERAWAT       │
└─────────────────────────────────────┘
```

**File**: `app/Http/Controllers/RadiologiController.php`

```php
public function store(Request $request)
{
    $radiologyRequest = RadiologyRequest::create([
        'encounter_id' => $encounter->id,
        'jenis_pemeriksaan_id' => $request->pemeriksaan,
        'dokter_id' => $request->dokter_perujuk,
        'status' => 'pending',
        'price' => $jenisPemeriksaan->harga,
    ]);

    // ❌ TIDAK ADA assignment perawat
}
```

#### Step 2: Input Hasil Radiologi (Assignment Perawat)

**Lokasi**: `Radiologi > Daftar Permintaan > Input Hasil`

```
┌─────────────────────────────────────────┐
│  📋 INPUT HASIL RADIOLOGI               │
├─────────────────────────────────────────┤
│  📷 Jenis: Chest X-Ray                  │
│  💵 Harga: Rp 150,000                   │
│                                         │
│  👨‍⚕️ Dokter Radiologi (WAJIB) *        │
│  ┌───────────────────────────────────┐ │
│  │ Dr. Radiologi A, Sp.Rad          │ │
│  └───────────────────────────────────┘ │
│                                         │
│  👨‍⚕️ Perawat / Nurse (WAJIB) *         │
│  ┌───────────────────────────────────┐ │
│  │ -- Pilih Perawat --              │ │
│  │ Ns. Budi Santoso                 │ │
│  │ Ns. Siti Aminah                  │ │
│  └───────────────────────────────────┘ │
│                                         │
│  📄 Hasil Pemeriksaan:                  │
│  ┌───────────────────────────────────┐ │
│  │ Cor dan pulmo dalam batas normal │ │
│  │ Tidak tampak infiltrat...        │ │
│  └───────────────────────────────────┘ │
│                                         │
│  📎 Upload Gambar (opsional)            │
│                                         │
│  [💾 Simpan Hasil]                      │
└─────────────────────────────────────────┘
```

**File**: `resources/views/pages/radiologi/permintaan/results.blade.php`

```blade
<div class="mb-3">
    <label class="form-label">Dokter Spesialis Radiologi <span class="text-danger">*</span></label>
    <select name="performer_id" class="form-select" required>
        <option value="">-- Pilih Dokter Radiologi --</option>
        @foreach ($radiologists as $rad)
            <option value="{{ $rad->id }}">{{ $rad->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Perawat / Nurse <span class="text-danger">*</span></label>
    <select name="reporter_id" class="form-select" required>
        <option value="">-- Pilih Perawat --</option>
        @foreach ($nurses as $nurse)
            <option value="{{ $nurse->id }}">{{ $nurse->name }}</option>
        @endforeach
    </select>
    <div class="form-text">Pilih perawat yang membantu pemeriksaan</div>
</div>
```

#### Step 3: Simpan Hasil (Tidak Ada Insentif Langsung)

**File**: `app/Http/Controllers/RadiologiController.php`

```php
public function storeResults(Request $request, $id)
{
    $radiologyRequest = RadiologyRequest::findOrFail($id);

    // Simpan hasil
    RadiologyResult::create([
        'radiology_request_id' => $id,
        'performer_id' => $request->performer_id, // Dokter radiologi
        'reporter_id' => $request->reporter_id,   // Perawat ✓
        'result_text' => $request->result_text,
    ]);

    // Update status
    $radiologyRequest->update([
        'status' => 'completed'
    ]);

    // ⚠️ INSENTIF BELUM DIBUAT DI SINI
}
```

#### Step 4: Pembayaran (Insentif Dibuat)

**Lokasi**: `Kasir > Pembayaran`

```
TRIGGER: Saat kasir memproses pembayaran
FILE: app/Http/Controllers/KasirController.php
```

```php
public function bayarEncounter(Request $request, $id)
{
    // 1. Proses pembayaran
    $encounter = Encounter::findOrFail($id);
    $encounter->update(['status_bayar_tindakan' => 1]);

    // 2. Buat insentif radiologi
    $this->createLabRadiologiIncentives($encounter);
}

private function createLabRadiologiIncentives($encounter)
{
    // Ambil semua permintaan radiologi yang sudah selesai
    $radiologyRequests = RadiologyRequest::with('results')
        ->where('encounter_id', $encounter->id)
        ->where('status', 'completed')
        ->get();

    foreach ($radiologyRequests as $request) {
        // 1. Insentif Dokter Perujuk (5%)
        if ($request->dokter_id) {
            $dokterPerujuk = User::find($request->dokter_id);
            Incentive::create([
                'user_id' => $dokterPerujuk->id,
                'amount' => $request->price * 0.05, // 5%
                'type' => 'fee_dokter_penunjang',
            ]);
        }

        // 2. Insentif Dokter Radiologi
        $result = $request->results()->first();
        if ($result && $result->performer_id) {
            $dokterRadiologi = User::find($result->performer_id);

            $feeMode = IncentiveSetting::where('setting_key', 'fee_spesialis_radiologi_mode')->value('setting_value');
            $feeValue = IncentiveSetting::where('setting_key', 'fee_spesialis_radiologi_value')->value('setting_value');

            $amount = ($feeMode == 1)
                ? ($request->price * ($feeValue / 100)) // Percentage
                : $feeValue; // Flat

            Incentive::create([
                'user_id' => $dokterRadiologi->id,
                'amount' => $amount,
                'type' => 'fee_spesialis_radiologi',
            ]);
        }

        // 3. Insentif Perawat Radiologi ✓
        if ($result && $result->reporter_id) {
            $perawat = User::find($result->reporter_id);

            $feeMode = IncentiveSetting::where('setting_key', 'perawat_fee_radiologi_mode')->value('setting_value');
            $feeValue = IncentiveSetting::where('setting_key', 'perawat_fee_radiologi_value')->value('setting_value');

            $amount = ($feeMode == 1)
                ? ($request->price * ($feeValue / 100)) // Percentage: Rp 150,000 x 5% = Rp 7,500
                : $feeValue; // Flat

            Incentive::create([
                'user_id' => $perawat->id,
                'encounter_id' => $encounter->id,
                'radiology_request_id' => $request->id,
                'amount' => $amount,
                'type' => 'fee_perawat_radiologi',
                'description' => 'Fee Perawat Radiologi - ' . $request->jenis->name,
                'status' => 'pending',
            ]);
        }
    }
}
```

### 📊 Flow Lengkap Radiologi

```
┌──────────────────┐
│ 1. PERMINTAAN    │  ❌ Tidak assign perawat
│    Radiologi     │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ 2. INPUT HASIL   │  ✓ ASSIGN PERAWAT (dropdown)
│    Radiologi     │  ✓ Simpan ke radiology_results.reporter_id
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ 3. PEMBAYARAN    │  ✓ BUAT INSENTIF PERAWAT
│    di Kasir      │  ✓ Berdasarkan reporter_id
└──────────────────┘
```

---

## 5. DATABASE STRUCTURE

### Tabel: `encounter_nurse` (Rawat Jalan & IGD)

```sql
CREATE TABLE encounter_nurse (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    encounter_id CHAR(36) NOT NULL,
    user_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (encounter_id) REFERENCES encounters(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Tabel: `radiology_results` (Radiologi)

```sql
CREATE TABLE radiology_results (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    radiology_request_id BIGINT NOT NULL,
    performer_id BIGINT NOT NULL,      -- Dokter Radiologi
    reporter_id BIGINT NULL,           -- Perawat ✓
    result_text TEXT,
    result_image VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (radiology_request_id) REFERENCES radiology_requests(id),
    FOREIGN KEY (performer_id) REFERENCES users(id),
    FOREIGN KEY (reporter_id) REFERENCES users(id)
);
```

### Tabel: `inpatient_treatments` (Rawat Inap)

```sql
CREATE TABLE inpatient_treatments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    admission_id BIGINT NOT NULL,
    request_type VARCHAR(50),          -- 'Visit', 'Tindakan', 'Konsultasi'
    performed_by BIGINT NOT NULL,      -- Dokter
    tindakan_id BIGINT,
    treatment_date DATETIME,
    result TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- ❌ Tidak ada kolom nurse_id karena perawat di-assign ke ruangan, bukan per treatment
```

### Relasi Model

**Encounter Model** (`app/Models/Encounter.php`)

```php
public function nurses()
{
    return $this->belongsToMany(
        User::class,
        'encounter_nurse',
        'encounter_id',
        'user_id'
    )->withTimestamps();
}
```

**User Model** (`app/Models/User.php`)

```php
public function encounters()
{
    return $this->belongsToMany(
        Encounter::class,
        'encounter_nurse',
        'user_id',
        'encounter_id'
    )->withTimestamps();
}
```

---

## 6. FAQ

### ❓ Kenapa Rawat Jalan & IGD tidak assign perawat saat pendaftaran?

**Jawab**: Karena pada saat pendaftaran, belum diketahui perawat mana yang akan menangani. Perawat baru di-assign setelah pemeriksaan selesai dan dokter tahu siapa yang membantu.

### ❓ Bagaimana jika lupa memilih perawat saat menyelesaikan encounter?

**Jawab**: Sistem akan menolak submit dengan validasi:

```php
'perawat_ids' => 'required_if:encounter.type,1,3|array'
```

Pesan error: "Perawat harus dipilih untuk Rawat Jalan atau Rawat Darurat."

### ❓ Apakah bisa mengubah perawat setelah encounter selesai?

**Jawab**: **TIDAK**. Setelah encounter status = 2 (Selesai), assignment perawat sudah di-sync ke pivot table dan insentif sudah dibuat. Untuk mengubah, harus:

1. Edit manual di database tabel `encounter_nurse`
2. Hapus dan buat ulang record di tabel `incentives`

### ❓ Kenapa Rawat Inap tidak assign perawat manual?

**Jawab**: Karena sistem rawat inap berbasis **ruangan**. Perawat yang bertugas di ruangan tersebut otomatis menangani pasien. Insentif diberikan per treatment/visit, bukan per encounter.

### ❓ Kapan insentif radiologi dibuat?

**Jawab**: Insentif radiologi dibuat **SAAT PEMBAYARAN**, bukan saat input hasil. Ini karena insentif baru valid setelah pasien membayar.

### ❓ Apakah bisa 1 encounter ditangani multiple perawat?

**Jawab**: **YA**. Sistem mendukung multiple nurses per encounter (checkbox, bukan radio button). Setiap perawat akan mendapat insentif sesuai jumlah yang di-setting.

### ❓ Bagaimana cara melihat daftar perawat yang pernah menangani pasien tertentu?

**Jawab**: Query via relasi:

```php
$encounter = Encounter::with('nurses')->find($id);
$nurses = $encounter->nurses; // Collection of User model
```

### ❓ Apakah insentif langsung dibayarkan?

**Jawab**: **TIDAK**. Insentif dibuat dengan `status = 'pending'`. Pembayaran dilakukan melalui menu **Keuangan > Pembayaran Gaji/Insentif** pada akhir bulan.

---

## 📝 SUMMARY TABLE

| Layanan         | Waktu Assignment       | Lokasi Assignment      | Jumlah Perawat      | Basis Insentif  | Jumlah Insentif |
| --------------- | ---------------------- | ---------------------- | ------------------- | --------------- | --------------- |
| **Rawat Jalan** | Saat selesai encounter | Tab Catatan (checkbox) | Multiple            | Per encounter   | Rp 10,000       |
| **IGD**         | Saat selesai encounter | Tab Catatan (checkbox) | Multiple            | Per encounter   | Rp 15,000       |
| **Rawat Inap**  | Otomatis dari ruangan  | Tidak ada UI           | Multiple (per room) | Per treatment   | Rp 20,000       |
| **Radiologi**   | Saat input hasil       | Form Hasil (dropdown)  | 1 (single)          | Per pemeriksaan | 5% dari harga   |

---

## 🎯 KEY TAKEAWAYS

1. **Rawat Jalan & IGD**: Assignment manual via **checkbox** saat menyelesaikan encounter
2. **Rawat Inap**: Assignment **otomatis** berdasarkan ruangan, insentif per treatment
3. **Radiologi**: Assignment via **dropdown** saat input hasil, insentif dibuat saat pembayaran
4. **Pivot Table**: `encounter_nurse` menyimpan relasi many-to-many untuk RJ & IGD
5. **Validasi**: Wajib pilih perawat untuk type 1 (RJ) dan 3 (IGD)
6. **Insentif**: Dibuat otomatis dengan status `pending`, dibayar akhir bulan

---

**Dibuat**: 14 November 2025  
**Update Terakhir**: 14 November 2025  
**Versi**: 1.0
