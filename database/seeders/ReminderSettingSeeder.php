<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReminderSetting;
use Illuminate\Support\Str;

class ReminderSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template Reminder Beli Obat Lagi
        $templateObat = "*Bambu Dua Clinic* – *Pengingat Resep Obat*

Halo *{nama_pasien}*,
Kami mengingatkan bahwa obat Anda sudah habis sejak *{hari} hari* yang lalu (habis tanggal *{tanggal}*).

Untuk menjaga kelangsungan pengobatan, kami sarankan Anda melakukan *kunjungan ulang* untuk mendapatkan resep baru.

*Jadwal praktek:* Senin - Sabtu Pukul 17.00 - 21.00 WIB
*Lokasi:* Bambu Dua Clinic, Jl. Bambu II No.20
*Konfirmasi kedatangan:* 0811-6311-378

Kami siap membantu Anda menjaga kesehatan secara berkelanjutan.

Terima kasih
*Salam sehat*
*Bambu Dua Clinic*";

        // Template Reminder Check Up
        $templateCheckup = "*Bambu Dua Clinic* – *Pengingat Kontrol*

Halo *{nama_pasien}*,
Sudah *{hari} hari* sejak kunjungan terakhir Anda (tanggal *{tanggal}*).

Untuk menjaga kesehatan Anda secara optimal, kami sarankan untuk melakukan *kontrol kesehatan rutin*.

*Jadwal praktek:* Senin - Sabtu Pukul 17.00 - 21.00 WIB
*Lokasi:* Bambu Dua Clinic, Jl. Bambu II No.20
*Konfirmasi kedatangan:* 0811-6311-378

Kami siap membantu Anda menjaga kesehatan secara berkelanjutan.

Terima kasih
*Salam sehat*
*Bambu Dua Clinic*";

        // Hapus data lama jika ada
        ReminderSetting::truncate();

        // Insert Reminder Settings
        ReminderSetting::create([
            'id' => Str::uuid(),
            'name' => 'Reminder Beli Obat Lagi',
            'type' => 'obat',
            'days_before' => 1, // 1 hari setelah obat habis
            'message_template' => $templateObat,
            'is_active' => true,
        ]);

        ReminderSetting::create([
            'id' => Str::uuid(),
            'name' => 'Reminder Check Up',
            'type' => 'checkup',
            'days_before' => 7, // 7 hari dari kunjungan terakhir
            'message_template' => $templateCheckup,
            'is_active' => true,
        ]);

        $this->command->info('Reminder settings seeded successfully!');
    }
}
