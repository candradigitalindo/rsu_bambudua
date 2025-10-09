<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Practitioner extends Model
{
    use HasUuids;
    protected $fillable = [
        'encounter_id', 'name', 'satusehat_id', 'id_petugas'
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_petugas', 'id');
    }
}
