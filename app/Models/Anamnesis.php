<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Anamnesis extends Model
{
    use HasUuids;
    protected $fillable = [
        'encounter_id',
        'keluhan_utama',
    ];
    // belongsTo
    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
