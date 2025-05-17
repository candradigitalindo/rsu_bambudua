<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RequestBahan extends Model
{
    use HasUuids;
    protected $fillable = [
        'encounter_id',
        'bahan_id',
        'qty',
        'nama_bahan',
        'status',
        'keterangan'
    ];
    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
