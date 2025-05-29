<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    use HasUuids;
    protected $fillable = [
        'no_encounter',
        'rekam_medis',
        'name_pasien',
        'pasien_satusehat_id',
        'status',
        'type',
        'jenis_jaminan',
        'tujuan_kunjungan',
        'diskon_tindakan',
        'diskon_persen_tindakan',
        'total_tindakan',
        'total_bayar_tindakan',
        'diskon_resep',
        'diskon_persen_resep',
        'total_resep',
        'total_bayar_resep',
    ];

    public function practitioner()
    {
        return $this->hasMany(Practitioner::class);
    }
    public function anamnesis()
    {
        return $this->hasOne(Anamnesis::class);
    }
    public function tandaVital()
    {
        return $this->hasOne(TandaVital::class);
    }
    public function pemeriksaanPenunjang()
    {
        return $this->hasMany(PemeriksaanPenunjang::class);
    }
    public function tindakan()
    {
        return $this->hasMany(TindakanEncounter::class);
    }
    public function requestBahan()
    {
        return $this->hasMany(RequestBahan::class);
    }
    public function diagnosis()
    {
        return $this->hasMany(Diagnosis::class);
    }
}
