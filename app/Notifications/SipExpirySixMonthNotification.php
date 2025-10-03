<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SipExpirySixMonthNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var array<int, array{user_name:string, profession:string, sip_number:?string, sip_expiry_date:string}>
     */
    protected array $items;

    /**
     * Create a new notification instance.
     * @param array $items list of entries with keys: user_name, profession, sip_number, sip_expiry_date (Y-m-d)
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Pengingat: SIP Tenaga Kesehatan kedaluwarsa 6 bulan lagi')
            ->greeting('Halo,')
            ->line('Berikut daftar tenaga kesehatan yang masa berlaku SIP-nya akan kedaluwarsa dalam 6 bulan ke depan:');

        foreach ($this->items as $row) {
            $line = sprintf('- %s (%s)%s — Expired: %s',
                $row['user_name'],
                $row['profession'],
                $row['sip_number'] ? ' | SIP: '.$row['sip_number'] : '',
                $row['sip_expiry_date']
            );
            $mail->line($line);
        }

        return $mail->line('Mohon lakukan tindak lanjut perpanjangan.');
    }
}