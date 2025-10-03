<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RadiologyRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'pasien_id',
        'jenis_pemeriksaan_id',
        'dokter_id',
        'notes',
        'status',
        'price',
        'created_by',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function jenis()
    {
        return $this->belongsTo(JenisPemeriksaanPenunjang::class, 'jenis_pemeriksaan_id');
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function results()
    {
        return $this->hasMany(RadiologyResult::class, 'radiology_request_id');
    }

    public function schedule()
    {
        return $this->hasOne(RadiologySchedule::class, 'radiology_request_id');
    }
}
