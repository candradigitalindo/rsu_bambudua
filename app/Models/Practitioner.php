<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Practitioner extends Model
{
    use HasUuids;
    protected $fillable = [
        'encounter_id', 'name', 'satusehat_id'
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
