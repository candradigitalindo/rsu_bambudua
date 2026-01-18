# Kasir Payment Methods Testing Checklist

## Pre-Testing Setup

-   [ ] Migration sudah dijalankan (`2025_11_28_074632_add_payment_fee_fields_to_encounters_table`)
-   [ ] Seeder PaymentMethodSeeder sudah dijalankan (7 metode pembayaran tersedia)
-   [ ] Database connection aktif
-   [ ] Server Laravel berjalan di http://127.0.0.1:8000

## Test 1: Payment Method Display

**URL**: http://127.0.0.1:8000/kasir/pembayaran/{pasien_id}

### Checklist:

-   [ ] Dropdown metode pembayaran menampilkan 7 metode
-   [ ] Setiap metode menampilkan info fee di nama (contoh: "QRIS (Fee 0.7%)")
-   [ ] Dropdown bisa dipilih tanpa error

**Screenshot**: Dropdown metode pembayaran

---

## Test 2: CASH Payment (No Fee)

**Scenario**: Bayar dengan CASH yang tidak ada fee

### Steps:

1. Pilih tagihan (contoh: Rp 100,000)
2. Pilih metode pembayaran: CASH
3. Masukkan jumlah: Rp 100,000
4. Perhatikan ringkasan pembayaran

### Expected Results:

-   [ ] Total Tagihan: Rp 100,000
-   [ ] Biaya Admin/Fee: **TIDAK TAMPIL** (karena Rp 0)
-   [ ] Grand Total: **TIDAK TAMPIL** (karena Rp 0)
-   [ ] Total Dibayar: Rp 100,000
-   [ ] Kembalian: Rp 0
-   [ ] Tombol "Proses Pembayaran" aktif (enabled)

**Screenshot**: Summary dengan CASH payment

---

## Test 3: DEBIT Payment (1.5% Fee)

**Scenario**: Bayar dengan DEBIT yang ada fee persentase

### Steps:

1. Pilih tagihan: Rp 100,000
2. Pilih metode pembayaran: Kartu Debit
3. Masukkan jumlah: Rp 101,500

### Expected Results:

-   [ ] Total Tagihan: Rp 100,000
-   [ ] Biaya Admin/Fee: Rp 1,500 (muncul dengan warna warning)
-   [ ] Grand Total: Rp 101,500 (muncul dengan warna danger/merah)
-   [ ] Total Dibayar: Rp 101,500
-   [ ] Kembalian: Rp 0
-   [ ] Fee info display muncul: "Fee: Rp 1,500 | Total: Rp 101,500"

**Screenshot**: Summary dengan DEBIT payment

---

## Test 4: TRANSFER Payment (Fixed Rp 6,500 Fee)

**Scenario**: Bayar dengan TRANSFER yang ada fee tetap

### Steps:

1. Pilih tagihan: Rp 100,000
2. Pilih metode pembayaran: Transfer Bank
3. Masukkan jumlah: Rp 106,500

### Expected Results:

-   [ ] Total Tagihan: Rp 100,000
-   [ ] Biaya Admin/Fee: Rp 6,500
-   [ ] Grand Total: Rp 106,500
-   [ ] Total Dibayar: Rp 106,500
-   [ ] Kembalian: Rp 0
-   [ ] Fee info display: "Fee: Rp 6,500 | Total: Rp 106,500"

**Screenshot**: Summary dengan TRANSFER payment

---

## Test 5: QRIS Payment (0.7% Fee)

**Scenario**: Bayar dengan QRIS

### Steps:

1. Pilih tagihan: Rp 100,000
2. Pilih metode pembayaran: QRIS
3. Masukkan jumlah: Rp 100,700

### Expected Results:

-   [ ] Total Tagihan: Rp 100,000
-   [ ] Biaya Admin/Fee: Rp 700
-   [ ] Grand Total: Rp 100,700
-   [ ] Total Dibayar: Rp 100,700
-   [ ] Kembalian: Rp 0
-   [ ] Fee info display: "Fee: Rp 700 | Total: Rp 100,700"

**Screenshot**: Summary dengan QRIS payment

---

## Test 6: Split Payment - CASH + DEBIT

**Scenario**: Bayar dengan 2 metode sekaligus

### Steps:

1. Pilih tagihan: Rp 100,000
2. Klik "Tambah Metode Pembayaran"
3. Metode 1: CASH, Jumlah: Rp 50,000
4. Metode 2: Kartu Debit, Jumlah: Rp 51,500

### Expected Results:

-   [ ] Total Tagihan: Rp 100,000
-   [ ] Biaya Admin/Fee: Rp 773 (1.5% dari Rp 51,500)
-   [ ] Grand Total: Rp 100,773
-   [ ] Total Dibayar: Rp 101,500
-   [ ] Kembalian: Rp 727
-   [ ] Fee info untuk CASH: tidak ada (fee = 0)
-   [ ] Fee info untuk DEBIT: "Fee: Rp 773 | Total: Rp 52,273"

**Screenshot**: Summary dengan split payment

---

## Test 7: Insufficient Payment (Kurang Bayar)

**Scenario**: Customer bayar kurang dari grand total

### Steps:

1. Pilih tagihan: Rp 100,000
2. Pilih metode pembayaran: QRIS (fee 0.7%)
3. Masukkan jumlah: Rp 100,000 (kurang Rp 700)

### Expected Results:

-   [ ] Total Tagihan: Rp 100,000
-   [ ] Biaya Admin/Fee: Rp 700
-   [ ] Grand Total: Rp 100,700
-   [ ] Total Dibayar: Rp 100,000
-   [ ] Kembalian/Kurang: **-Rp 700** (warna merah)
-   [ ] Display "PEMBAYARAN KURANG" muncul dengan warna merah
-   [ ] Tombol "Proses Pembayaran" **DISABLED**
-   [ ] Saat di-submit, muncul SweetAlert error dengan detail kurang bayar

**Screenshot**: Display pembayaran kurang

---

## Test 8: Payment Processing & Database

**Scenario**: Submit pembayaran dan cek database

### Steps:

1. Pilih tagihan: Rp 100,000 (Tindakan Rp 70,000 + Resep Rp 30,000)
2. Pilih metode pembayaran: Kartu Kredit (2.5% fee)
3. Masukkan jumlah: Rp 102,500
4. Klik "Proses Pembayaran"
5. Konfirmasi di SweetAlert

### Expected Results:

-   [ ] SweetAlert konfirmasi menampilkan:
    -   Total Tagihan: Rp 100,000
    -   Biaya Admin/Fee: Rp 2,500
    -   Grand Total: Rp 102,500
    -   Total Dibayar: Rp 102,500
    -   Kembalian: Rp 0
-   [ ] Redirect ke halaman kasir index
-   [ ] Success message menampilkan detail pembayaran
-   [ ] Tombol "Cetak Struk" muncul

### Database Check:

Query database untuk cek encounter yang baru dibayar:

```sql
SELECT
    id,
    total_bayar_tindakan,
    payment_fee_tindakan,
    grand_total_tindakan,
    total_bayar_resep,
    payment_fee_resep,
    grand_total_resep,
    metode_pembayaran_tindakan,
    metode_pembayaran_resep
FROM encounters
WHERE id = '{encounter_id}'
```

**Expected Database Values**:

-   [ ] `payment_fee_tindakan` = 1750.00 (70% × 2500)
-   [ ] `payment_fee_resep` = 750.00 (30% × 2500)
-   [ ] `grand_total_tindakan` = 71750.00 (70000 + 1750)
-   [ ] `grand_total_resep` = 30750.00 (30000 + 750)
-   [ ] `metode_pembayaran_tindakan` = "Kartu Kredit: Rp 102,500 (Fee: Rp 2,500)"
-   [ ] `metode_pembayaran_resep` = "Kartu Kredit: Rp 102,500 (Fee: Rp 2,500)"

**Screenshot**: Database record

---

## Test 9: Receipt/Struk Display

**Scenario**: Cetak struk dan cek tampilan fee

### Steps:

1. Setelah pembayaran sukses, klik "Cetak Struk"
2. Atau dari histori, klik "Cetak Struk" pada transaksi

### Expected Results:

-   [ ] Struk menampilkan semua item yang dibayar
-   [ ] Section "SUBTOTAL" menampilkan total sebelum fee
-   [ ] Section "BIAYA ADMIN/FEE" menampilkan total fee (jika ada)
-   [ ] Section "GRAND TOTAL" menampilkan subtotal + fee (jika ada)
-   [ ] Section "METODE PEMBAYARAN" menampilkan metode dengan detail fee
    -   Contoh: "Kartu Kredit: Rp 102,500 (Fee: Rp 2,500)"
-   [ ] Jika fee = 0 (CASH), tidak ada section BIAYA ADMIN/FEE dan GRAND TOTAL

**Screenshot**: Struk dengan fee

---

## Test 10: Split Payment Multiple Methods

**Scenario**: Bayar dengan 3 metode berbeda

### Steps:

1. Pilih tagihan: Rp 200,000
2. Klik "Tambah Metode Pembayaran" 2x (total 3 metode)
3. Metode 1: CASH Rp 50,000 (fee: 0)
4. Metode 2: QRIS Rp 50,700 (fee: 0.7% = Rp 355)
5. Metode 3: DEBIT Rp 101,500 (fee: 1.5% = Rp 1,523)

### Expected Results:

-   [ ] Total Tagihan: Rp 200,000
-   [ ] Biaya Admin/Fee: Rp 1,878 (355 + 1,523)
-   [ ] Grand Total: Rp 201,878
-   [ ] Total Dibayar: Rp 202,200
-   [ ] Kembalian: Rp 322
-   [ ] Setiap metode menampilkan fee info masing-masing
-   [ ] Struk menampilkan 3 metode pembayaran dengan detail fee

**Screenshot**: Split payment 3 metode

---

## Test 11: Real-time Calculation Update

**Scenario**: Cek apakah perhitungan update real-time

### Steps:

1. Pilih tagihan: Rp 100,000
2. Pilih metode: DEBIT
3. Ketik jumlah: 50000
4. Ubah jumlah: 100000
5. Ubah jumlah: 101500

### Expected Results:

-   [ ] Fee info display update otomatis setiap kali angka berubah
-   [ ] Pada jumlah 50,000: Fee: Rp 750 | Total: Rp 50,750
-   [ ] Pada jumlah 100,000: Fee: Rp 1,500 | Total: Rp 101,500
-   [ ] Pada jumlah 101,500: Fee: Rp 1,523 | Total: Rp 103,023
-   [ ] Ringkasan pembayaran di panel kanan update real-time
-   [ ] Tombol "Proses Pembayaran" disabled jika pembayaran kurang

**Screenshot**: Real-time update

---

## Test 12: Activity Log

**Scenario**: Cek activity log mencatat detail fee

### Steps:

1. Lakukan pembayaran dengan fee (contoh: DEBIT Rp 101,500)
2. Buka tabel `activity_logs` di database
3. Cari record terakhir dengan description "Memproses Pembayaran"

### Expected Database Record:

```json
{
  "pasien_id": "xxx",
  "rekam_medis": "RM-xxx",
  "metode": "Kartu Debit: Rp 101,500 (Fee: Rp 1,500)",
  "total_tagihan": 100000,
  "total_fee": 1500,
  "grand_total": 101500,
  "total_bayar": 101500,
  "kembalian": 0,
  "items": {...},
  "payment_methods_detail": [
    {
      "code": "DEBIT",
      "name": "Kartu Debit",
      "amount": 101500,
      "fee": 1500,
      "total_with_fee": 103000
    }
  ]
}
```

-   [ ] Record activity log tersimpan dengan lengkap
-   [ ] Field `total_fee` terisi dengan benar
-   [ ] Field `grand_total` = total_tagihan + total_fee
-   [ ] Field `payment_methods_detail` berisi array detail metode pembayaran

**Screenshot**: Activity log record

---

## Test 13: Edge Cases

### Test 13.1: Tagihan Rp 0

-   [ ] Tidak bisa submit (tombol disabled)
-   [ ] Alert: "Pilih minimal satu item yang akan dibayar"

### Test 13.2: Pembayaran Pas (Kembalian Rp 0)

-   [ ] Display kembalian tidak muncul
-   [ ] Konfirmasi dialog menunjukkan "Pembayaran Pas"

### Test 13.3: Pembayaran Lebih (Ada Kembalian)

-   [ ] Display kembalian muncul dengan warna hijau
-   [ ] Jumlah kembalian benar
-   [ ] Konfirmasi dialog menunjukkan kembalian

### Test 13.4: Multiple Encounters Payment

-   [ ] Fee dihitung untuk total semua encounters
-   [ ] Fee didistribusikan proporsional ke setiap encounter
-   [ ] Semua encounter tersimpan dengan fee yang benar

---

## Browser Compatibility Test

Test di berbagai browser:

### Chrome

-   [ ] Payment form berfungsi
-   [ ] Real-time calculation update
-   [ ] No console errors

### Firefox

-   [ ] Payment form berfungsi
-   [ ] Real-time calculation update
-   [ ] No console errors

### Safari

-   [ ] Payment form berfungsi
-   [ ] Real-time calculation update
-   [ ] No console errors

---

## Mobile Responsive Test

Test di ukuran layar mobile:

-   [ ] Form pembayaran tetap rapi
-   [ ] Dropdown metode pembayaran bisa dipilih
-   [ ] Input jumlah bisa diisi
-   [ ] Summary pembayaran tetap terlihat
-   [ ] Tombol-tombol bisa diklik

---

## Performance Test

-   [ ] Payment form load < 2 detik
-   [ ] Real-time calculation tidak lag
-   [ ] Submit pembayaran < 5 detik
-   [ ] Struk generation < 3 detik
-   [ ] No memory leaks di console

---

## Bug Reporting Template

Jika menemukan bug, catat dengan format:

```
**Bug Title**: [Deskripsi singkat]

**Steps to Reproduce**:
1. ...
2. ...
3. ...

**Expected Result**: ...

**Actual Result**: ...

**Screenshot/Video**: [attach]

**Browser**: Chrome 120 / Firefox 121 / Safari 17
**Device**: Desktop / Mobile
**OS**: Windows 11 / macOS / Android / iOS

**Console Errors**: [jika ada]

**Database State**: [jika relevan]
```

---

## Sign-off

Setelah semua test PASSED:

**Tested by**: ******\_\_\_******
**Date**: ******\_\_\_******
**Build Version**: ******\_\_\_******
**Status**: ☐ PASSED ☐ FAILED (with notes)

**Notes**:

---

---

---
