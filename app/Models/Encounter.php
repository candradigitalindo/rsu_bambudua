<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    use HasUuids;
    protected $fillable = [
        'no_encounter',
        'rekam_medis',
        'name_pasien',
        'pasien_satusehat_id',
        'status',
        'type'
    ];

    public function practitioner()
    {
        return $this->hasMany(Practitioner::class);
    }
}
