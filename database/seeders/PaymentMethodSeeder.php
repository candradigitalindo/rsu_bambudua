<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Tunai',
                'code' => 'CASH',
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'fee_type' => 'fixed',
                'description' => 'Pembayaran tunai langsung',
                'active' => true
            ],
            [
                'name' => 'Kartu Debit',
                'code' => 'DEBIT',
                'fee_percentage' => 1.5,
                'fee_fixed' => 0,
                'fee_type' => 'percentage',
                'description' => 'Pembayaran menggunakan kartu debit dengan fee 1.5%',
                'active' => true
            ],
            [
                'name' => 'Kartu Kredit',
                'code' => 'CREDIT_CARD',
                'fee_percentage' => 2.5,
                'fee_fixed' => 0,
                'fee_type' => 'percentage',
                'description' => 'Pembayaran menggunakan kartu kredit dengan fee 2.5%',
                'active' => true
            ],
            [
                'name' => 'QRIS',
                'code' => 'QRIS',
                'fee_percentage' => 0.7,
                'fee_fixed' => 0,
                'fee_type' => 'percentage',
                'description' => 'Pembayaran menggunakan QRIS dengan fee 0.7%',
                'active' => true
            ],
            [
                'name' => 'Transfer Bank',
                'code' => 'TRANSFER',
                'fee_percentage' => 0,
                'fee_fixed' => 6500,
                'fee_type' => 'fixed',
                'description' => 'Transfer bank antar bank dengan biaya administrasi Rp 6.500',
                'active' => true
            ],
            [
                'name' => 'E-Wallet (GoPay, OVO, Dana)',
                'code' => 'EWALLET',
                'fee_percentage' => 1.0,
                'fee_fixed' => 0,
                'fee_type' => 'percentage',
                'description' => 'Pembayaran melalui dompet digital dengan fee 1%',
                'active' => true
            ],
            [
                'name' => 'Asuransi/BPJS',
                'code' => 'INSURANCE',
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'fee_type' => 'fixed',
                'description' => 'Pembayaran melalui asuransi kesehatan atau BPJS',
                'active' => true
            ],
        ];

        foreach ($data as $row) {
            PaymentMethod::updateOrCreate(
                ['code' => $row['code']],
                $row
            );
        }
    }
}
