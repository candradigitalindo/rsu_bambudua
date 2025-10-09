<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one clinic exists
        $clinic = Clinic::first();
        if (!$clinic) {
            // If there is no clinic yet, create a minimal one
            $clinic = Clinic::create([
                'nama' => 'Klinik Umum',
                'alamat' => 'Alamat Klinik',
            ]);
        }

        $doctors = [
            [
                'username' => 'dokter_a',
                'name' => 'Dokter A',
            ],
            [
                'username' => 'dokter_b',
                'name' => 'Dokter B',
            ],
        ];

        foreach ($doctors as $doc) {
            $user = User::firstOrCreate(
                ['username' => $doc['username']],
                [
                    'name' => $doc['name'],
                    'password' => Hash::make('12345678'),
                    'role' => 2, // 2 = Dokter
                    'is_active' => 1,
                ]
            );

            // Ensure the user has a profile
            Profile::firstOrCreate(['user_id' => $user->id]);

            // Attach to clinic if not already attached
            if (method_exists($user, 'clinics')) {
                if (!$user->clinics()->where('clinic_id', $clinic->id)->exists()) {
                    $user->clinics()->attach($clinic->id);
                }
            }
        }
    }
}
