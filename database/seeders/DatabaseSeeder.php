<?php

namespace Database\Seeders;

use App\Models\Etnis;
use App\Models\Profile;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::create([
            'name'      => 'dr. Owner',
            'username'  => 'owner',
            'password'  => Hash::make('12345678'),
            'role'      => 1
        ]);

        Profile::create(['user_id'  => $user->id]);

    }
}
