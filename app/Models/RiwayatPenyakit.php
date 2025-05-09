<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenyakit extends Model
{
    use HasUuids;
    protected $fillable = ['pasien_id', 'riwayat_penyakit', 'riwayat_penyakit_lain', 'riwayat_penyakit_keluarga'];
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }
}
