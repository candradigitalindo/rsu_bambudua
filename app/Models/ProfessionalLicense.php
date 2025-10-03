<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profession',
'sip_number',
        'str_number',
'sip_expiry_date',
'str_expiry_date',
        'sip_file_path',
        'str_file_path',
        'six_month_reminder_sent_at',
    ];

    protected $casts = [
        'sip_expiry_date' => 'date',
'six_month_reminder_sent_at' => 'datetime',
        'str_expiry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}