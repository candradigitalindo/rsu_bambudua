<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanPenunjang extends Model
{
    use HasUuids;
    protected $fillable = [
        'encounter_id',
        'jenis_pemeriksaan',
        'hasil_pemeriksaan',
        'dokumen_pemeriksaan',
        'harga',
        'qty',
        'total_harga',
    ];
    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
