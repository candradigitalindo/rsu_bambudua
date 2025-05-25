<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasUuids;
    protected $fillable = [
        'id',
        'encounter_id',
        'kode_resep',
        'masa_pemakaian_hari',
        'dokter',
        'catatan',
    ];
    public function details()
    {
        return $this->hasMany(ResepDetail::class);
    }
}
