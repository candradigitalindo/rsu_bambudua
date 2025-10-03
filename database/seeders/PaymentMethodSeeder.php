<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Tunai',         'code' => 'CASH',     'active' => true],
            ['name' => 'Debit',         'code' => 'DEBIT',    'active' => true],
            ['name' => 'QRIS',          'code' => 'QRIS',     'active' => true],
            ['name' => 'Transfer Bank', 'code' => 'TRANSFER', 'active' => true],
            ['name' => 'Asuransi',      'code' => 'INSURANCE','active' => true],
        ];

        foreach ($data as $row) {
            PaymentMethod::firstOrCreate(['code' => $row['code']], $row);
        }
    }
}