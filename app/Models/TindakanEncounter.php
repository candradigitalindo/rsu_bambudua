<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TindakanEncounter extends Model
{
    use HasUuids;
    protected $fillable = [
        'encounter_id',
        'tindakan_id',
        'tindakan_name',
        'tindakan_description',
        'tindakan_harga',
        'qty',
        'total_harga',
        'id_petugas',
        'petugas_name',
    ];
    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
