<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasUuids;

    protected $fillable = [
        'encounter_id',
        'diagnosis_code',
        'diagnosis_description',
        'diagnosis_type',
        'id_petugas',
        'petugas_name',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
