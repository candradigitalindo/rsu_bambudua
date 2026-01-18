# Kasir Payment Methods with Fees Implementation

## Overview

Sistem pembayaran kasir telah diintegrasikan dengan metode pembayaran yang memiliki fitur biaya admin/fee. Sistem mendukung 3 jenis fee:

-   **Percentage**: Fee berdasarkan persentase dari jumlah bayar (contoh: 2.5%)
-   **Fixed**: Fee tetap dalam Rupiah (contoh: Rp 6,500)
-   **Both**: Kombinasi percentage + fixed (contoh: 2.5% + Rp 5,000)

## Database Changes

### Migration: `2025_11_28_074632_add_payment_fee_fields_to_encounters_table`

Menambahkan field baru ke tabel `encounters`:

-   `payment_fee_tindakan` (decimal 15,2) - Biaya fee untuk pembayaran tindakan
-   `payment_fee_resep` (decimal 15,2) - Biaya fee untuk pembayaran resep
-   `grand_total_tindakan` (decimal 15,2) - Total tindakan + fee
-   `grand_total_resep` (decimal 15,2) - Total resep + fee

### Encounter Model Update

Menambahkan field ke `$fillable`:

-   `payment_fee_tindakan`
-   `payment_fee_resep`
-   `grand_total_tindakan`
-   `grand_total_resep`

## Default Payment Methods

7 metode pembayaran telah di-seed dengan fee yang realistis:

| Code        | Name          | Fee Type   | Fee % | Fee Fixed | Example (Rp 100,000) |
| ----------- | ------------- | ---------- | ----- | --------- | -------------------- |
| CASH        | Tunai         | fixed      | 0%    | Rp 0      | Rp 100,000           |
| DEBIT       | Kartu Debit   | percentage | 1.5%  | -         | Rp 101,500           |
| CREDIT_CARD | Kartu Kredit  | percentage | 2.5%  | -         | Rp 102,500           |
| QRIS        | QRIS          | percentage | 0.7%  | -         | Rp 100,700           |
| TRANSFER    | Transfer Bank | fixed      | -     | Rp 6,500  | Rp 106,500           |
| EWALLET     | E-Wallet      | percentage | 1%    | -         | Rp 101,000           |
| INSURANCE   | Asuransi/BPJS | fixed      | 0%    | Rp 0      | Rp 100,000           |

## Backend Implementation

### KasirController::processPayment()

1. **Fee Calculation**: Menghitung fee untuk setiap metode pembayaran yang dipilih

    ```php
    $totalFee = 0;
    $paymentMethodsWithFee = collect($paymentMethods)
        ->filter(fn($pm) => isset($pm['method']) && $pm['amount_raw'] > 0)
        ->map(function ($pm) use (&$totalFee) {
            $paymentMethodModel = PaymentMethod::where('code', $pm['method'])->first();
            $fee = $paymentMethodModel->calculateFee($pm['amount_raw']);
            $totalFee += $fee;
            return [...];
        });
    ```

2. **Fee Distribution**: Fee didistribusikan proporsional ke tindakan dan resep

    ```php
    // Jika total tindakan = 70,000 dan total resep = 30,000 (total = 100,000)
    // Dan total fee = 2,000
    // Maka fee tindakan = 70% * 2,000 = 1,400
    // Dan fee resep = 30% * 2,000 = 600
    ```

3. **Validation**: Validasi pembayaran terhadap grand total (tagihan + fee)

    ```php
    $grandTotalForValidation = $totalBill + $totalFee;
    if ($totalPaymentReceived < $grandTotalForValidation) {
        // Error: pembayaran kurang
    }
    ```

4. **Storage**: Menyimpan fee dan grand total ke database
    ```php
    $encounter->payment_fee_tindakan = $feeTindakan;
    $encounter->grand_total_tindakan = $encounter->total_bayar_tindakan + $feeTindakan;
    ```

## Frontend Implementation

### Payment Form (show.blade.php)

#### Real-time Fee Calculation

JavaScript menghitung fee secara real-time berdasarkan metode pembayaran yang dipilih:

```javascript
function calculateFee(paymentCode, amount) {
    const method = paymentMethodsData[paymentCode];
    let fee = 0;

    if (method.fee_type === "percentage") {
        fee = amount * (method.fee_percentage / 100);
    } else if (method.fee_type === "fixed") {
        fee = method.fee_fixed;
    } else if (method.fee_type === "both") {
        fee = amount * (method.fee_percentage / 100) + method.fee_fixed;
    }

    return Math.round(fee);
}
```

#### Payment Summary Display

Menampilkan breakdown pembayaran:

-   **Total Tagihan**: Subtotal dari semua item yang dipilih
-   **Biaya Admin/Fee**: Total fee dari semua metode pembayaran
-   **Grand Total**: Total Tagihan + Biaya Admin/Fee
-   **Total Dibayar**: Jumlah yang dibayarkan customer
-   **Kembalian/Kurang**: Selisih antara Total Dibayar dan Grand Total

#### Split Payment Support

Mendukung pembayaran dengan multiple metode:

-   Customer bisa membayar dengan 2+ metode sekaligus
-   Contoh: 50% CASH + 50% DEBIT
-   Fee dihitung untuk setiap metode secara terpisah

#### Fee Info Display

Setiap metode pembayaran menampilkan informasi fee:

-   Dropdown menunjukkan fee di samping nama metode
-   Real-time display menunjukkan: "Fee: Rp X | Total: Rp Y"

### Receipt (struk.blade.php)

#### Payment Breakdown

Struk menampilkan:

1. **Detail Items**: Semua item yang dibayar dengan harga satuan dan subtotal
2. **Subtotal**: Total tagihan sebelum fee
3. **Biaya Admin/Fee**: Total fee yang dikenakan (jika ada)
4. **Grand Total**: Subtotal + Biaya Admin/Fee
5. **Metode Pembayaran**: Daftar metode pembayaran yang digunakan dengan detail fee

Contoh tampilan:

```
DETAIL PEMBAYARAN
Item                  Qty    Harga Satuan    Subtotal
------------------------------------------------
Konsultasi Dokter      1     Rp 100,000     Rp 100,000
Obat Amoxicillin       2     Rp 15,000      Rp 30,000
------------------------------------------------
SUBTOTAL                                    Rp 130,000
BIAYA ADMIN/FEE                             Rp 3,250
GRAND TOTAL                                 Rp 133,250

METODE PEMBAYARAN
QRIS: Rp 133,250 (Fee: Rp 3,250)
```

## Testing Scenarios

### Test 1: Single Payment Method - CASH (No Fee)

1. Pilih tagihan: Rp 100,000
2. Pilih metode: CASH
3. Masukkan jumlah: Rp 100,000
4. **Expected**:
    - Total Tagihan: Rp 100,000
    - Biaya Admin/Fee: Rp 0 (tidak ditampilkan)
    - Grand Total: Rp 100,000 (tidak ditampilkan)
    - Kembalian: Rp 0

### Test 2: Single Payment Method - DEBIT (1.5% Fee)

1. Pilih tagihan: Rp 100,000
2. Pilih metode: DEBIT
3. Masukkan jumlah: Rp 101,500
4. **Expected**:
    - Total Tagihan: Rp 100,000
    - Biaya Admin/Fee: Rp 1,500
    - Grand Total: Rp 101,500
    - Kembalian: Rp 0

### Test 3: Single Payment Method - TRANSFER (Fixed Rp 6,500 Fee)

1. Pilih tagihan: Rp 100,000
2. Pilih metode: TRANSFER
3. Masukkan jumlah: Rp 106,500
4. **Expected**:
    - Total Tagihan: Rp 100,000
    - Biaya Admin/Fee: Rp 6,500
    - Grand Total: Rp 106,500
    - Kembalian: Rp 0

### Test 4: Split Payment - CASH + DEBIT

1. Pilih tagihan: Rp 100,000
2. Tambah metode pembayaran
3. Metode 1: CASH, Jumlah: Rp 50,000 (Fee: Rp 0)
4. Metode 2: DEBIT, Jumlah: Rp 51,500 (Fee: Rp 772.50 ≈ Rp 773)
5. **Expected**:
    - Total Tagihan: Rp 100,000
    - Biaya Admin/Fee: Rp 773
    - Grand Total: Rp 100,773
    - Total Dibayar: Rp 101,500
    - Kembalian: Rp 727

### Test 5: Insufficient Payment

1. Pilih tagihan: Rp 100,000
2. Pilih metode: QRIS (0.7% fee)
3. Masukkan jumlah: Rp 100,000 (kurang dari grand total Rp 100,700)
4. **Expected**:
    - Tombol "Proses Pembayaran" disabled
    - Display menunjukkan: "Pembayaran Kurang: Rp 700"

### Test 6: Mixed Tindakan & Resep Payment

1. Pilih tagihan: Tindakan Rp 70,000 + Resep Rp 30,000 = Rp 100,000
2. Pilih metode: CREDIT_CARD (2.5% fee = Rp 2,500)
3. **Expected**:
    - Fee Tindakan: 70% × Rp 2,500 = Rp 1,750
    - Fee Resep: 30% × Rp 2,500 = Rp 750
    - Grand Total Tindakan: Rp 71,750
    - Grand Total Resep: Rp 30,750
    - Total: Rp 102,500

## User Flow

### Kasir Payment Process

1. **Access**: Navigate to Kasir → Pilih Pasien
2. **Select Items**: Checklist item tagihan yang akan dibayar
3. **Choose Payment Method**: Pilih metode pembayaran dari dropdown
4. **Enter Amount**: Masukkan jumlah bayar
    - Sistem otomatis menghitung dan menampilkan fee
    - Sistem menampilkan grand total (tagihan + fee)
5. **Add More Methods** (Optional): Klik "Tambah Metode Pembayaran" untuk split payment
6. **Review Summary**: Cek ringkasan pembayaran di panel kanan
    - Total Tagihan
    - Biaya Admin/Fee
    - Grand Total
    - Total Dibayar
    - Kembalian/Kurang
7. **Submit**: Klik "Proses Pembayaran"
8. **Confirmation**: Review dialog konfirmasi dengan detail lengkap
9. **Print Receipt**: Cetak struk pembayaran dengan detail fee

### Payment Method Management (Admin Only)

1. **Access**: Navigate to Master Data → Metode Pembayaran
2. **Create**: Tambah metode pembayaran baru
    - Nama metode
    - Kode unik
    - Tipe fee (percentage/fixed/both)
    - Nilai fee
    - Status aktif/nonaktif
3. **Edit**: Update fee atau status metode pembayaran
4. **View**: Lihat daftar metode dengan badge fee

## Error Handling

### Validation Errors

1. **No items selected**: "Pilih minimal satu item yang akan dibayar"
2. **No payment method selected**: Tombol disabled sampai metode dipilih
3. **Insufficient payment**: "Total pembayaran kurang dari grand total"
4. **Invalid payment method**: "Metode pembayaran tidak valid"

### Backend Errors

-   Logged dengan detail untuk debugging
-   User-friendly error messages
-   Transaction rollback pada error

## Activity Logging

Setiap pembayaran dicatat dengan detail:

```php
[
    'pasien_id' => $pasien_id,
    'rekam_medis' => $pasien->rekam_medis,
    'metode' => "QRIS: Rp 100,000 (Fee: Rp 700)",
    'total_tagihan' => 100000,
    'total_fee' => 700,
    'grand_total' => 100700,
    'total_bayar' => 100700,
    'kembalian' => 0,
    'items' => [...],
    'payment_methods_detail' => [...]
]
```

## Notes

-   Fee dihitung berdasarkan jumlah yang dibayar per metode, bukan total tagihan
-   Untuk split payment, fee dihitung terpisah untuk setiap metode
-   Fee didistribusikan proporsional ke tindakan dan resep
-   Metode pembayaran CASH dan INSURANCE biasanya tidak dikenakan fee
-   Format fee di struk: "Metode: Rp X (Fee: Rp Y)" jika fee > 0

## Future Enhancements

1. Support untuk maximum fee amount
2. Fee discount berdasarkan total transaksi
3. Fee history tracking per payment method
4. Monthly fee report
5. Integration dengan payment gateway untuk auto-calculate fee
