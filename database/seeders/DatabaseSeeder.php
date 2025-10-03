<?php

namespace Database\Seeders;

use App\Models\Etnis;
use App\Models\Profile;
use App\Models\User;
use App\Models\Agama;
use App\Models\Tindakan;
use App\Models\ProductApotek;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User Seeder
        $user = \App\Models\User::firstOrCreate(
            ['username' => 'owner'],
            [
                'name'      => 'dr. Owner',
                'password'  => \Illuminate\Support\Facades\Hash::make('12345678'),
                'role'      => 1
            ]
        );
        \App\Models\Profile::firstOrCreate(['user_id' => $user->id]);

        // Agama Seeder
        $agamas = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
        foreach ($agamas as $agama) {
            Agama::firstOrCreate(['name' => $agama]);
        }

        // Etnis Seeder
        $etnisList = ['Jawa', 'Sunda', 'Batak', 'Minang', 'Bugis', 'Madura', 'Bali', 'Betawi', 'Tionghoa', 'Lainnya'];
        foreach ($etnisList as $etnis) {
            Etnis::firstOrCreate(['name' => $etnis]);
        }

        // Tindakan Seeder
        $tindakans = [
            ['name' => 'Pemeriksaan Dokter', 'code' => 'TDK001', 'harga' => 50000],
            ['name' => 'Infus', 'code' => 'TDK002', 'harga' => 25000],
            ['name' => 'Suntik', 'code' => 'TDK003', 'harga' => 15000],
        ];
        foreach ($tindakans as $tindakan) {
            Tindakan::firstOrCreate(['code' => $tindakan['code']], $tindakan);
        }

        // Category Seeder (pastikan ada minimal 1 kategori)
        $category = DB::table('categories')->first();
        if (!$category) {
            $uuid = (string) Str::uuid();
            DB::table('categories')->insert([
                'id' => $uuid,
                'name' => 'Obat Umum',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoryId = $uuid;
        } else {
            $categoryId = $category->id;
        }

        // Payment Method Seeder
        $this->call(PaymentMethodSeeder::class);

        // Product Apotek Seeder
        $products = [
            ['code' => 'OBT001', 'name' => 'Paracetamol', 'satuan' => 'tablet', 'harga' => 2000, 'stok' => 100, 'category_id' => $categoryId],
            ['code' => 'OBT002', 'name' => 'Amoxicillin', 'satuan' => 'kapsul', 'harga' => 3000, 'stok' => 80, 'category_id' => $categoryId],
            ['code' => 'OBT003', 'name' => 'Vitamin C', 'satuan' => 'tablet', 'harga' => 1500, 'stok' => 120, 'category_id' => $categoryId],
        ];
        foreach ($products as $product) {
            ProductApotek::firstOrCreate(['code' => $product['code']], $product);
        }

        // Incentive Setting Seeder
        $this->call(IncentiveSettingSeeder::class);
    }
}
