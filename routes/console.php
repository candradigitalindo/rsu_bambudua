<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;

use App\Models\ProfessionalLicense;
use App\Models\User;
use App\Notifications\SipExpirySixMonthNotification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Cek SIP kedaluwarsa 6 bulan lagi dan kirim reminder ke Admin & Superadmin
Artisan::command('sip:remind-expiring', function () {
    $today = now();
    $targetStart = $today->copy()->addMonthsNoOverflow(6)->startOfMonth()->startOfDay();
    $targetEnd = $today->copy()->addMonthsNoOverflow(6)->endOfMonth()->endOfDay();

    $licenses = ProfessionalLicense::with('user.profile')
        ->whereNull('six_month_reminder_sent_at')
        ->whereBetween('sip_expiry_date', [$targetStart->toDateString(), $targetEnd->toDateString()])
        ->get();

    if ($licenses->isEmpty()) {
        $this->info('No new SIPs found expiring in the next 6-month window.');
        return 0;
    }

    $items = $licenses->map(function ($lic) {
        return [
            'user_name' => $lic->user?->name ?: 'N/A',
            'profession' => $lic->profession,
            'sip_number' => $lic->sip_number,
            'sip_expiry_date' => optional($lic->sip_expiry_date)->toDateString(),
        ];
    })->values()->all();

    // Role asumsi: 1 = Owner/Superadmin, 4 = Admin
    $admins = User::with('profile')->whereIn('role', [1, 4])->get();

    if ($admins->isNotEmpty()) {
        Notification::send($admins, new SipExpirySixMonthNotification($items));
        // tandai sudah dikirim agar tidak berulang setiap hari
        $licenses->each(function ($lic) {
            $lic->six_month_reminder_sent_at = now();
            $lic->save();
        });
        $this->info('Reminder sent to ' . $admins->count() . ' admin(s).');
    } else {
        $this->warn('No admins found to notify.');
    }

    return 0;
})->purpose('Remind Admin/Superadmin about SIP expiring in 6 months')->dailyAt('08:00');
