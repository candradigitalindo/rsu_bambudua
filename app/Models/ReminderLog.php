<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'rekam_medis',
        'encounter_id',
        'reminder_type',
        'reminder_date',
        'wa_clicked',
        'clicked_at',
        'clicked_by',
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'wa_clicked' => 'boolean',
        'clicked_at' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'rekam_medis', 'rekam_medis');
    }

    public function encounter()
    {
        return $this->belongsTo(Encounter::class, 'encounter_id');
    }

    public function clickedByUser()
    {
        return $this->belongsTo(User::class, 'clicked_by');
    }
}
