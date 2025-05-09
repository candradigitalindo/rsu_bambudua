<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TandaVital extends Model
{
    use HasUuids;
    protected $fillable = [
        'encounter_id',
        'nadi',
        'pernapasan',
        'sistolik',
        'diastolik',
        'suhu',
        'berat_badan',
        'tinggi_badan',
        'kesadaran'
    ];
    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
