<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaketPasienUsage extends Model
{
    use HasUuids;

    protected $fillable = [
        'paket_pasien_id',
        'encounter_id',
        'sesi_ke',
        'used_by',
        'catatan',
    ];

    public function paketPasien()
    {
        return $this->belongsTo(PaketPasien::class);
    }

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
