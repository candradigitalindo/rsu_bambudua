<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InpatientAdmission extends Model
{
    use HasUuids, SoftDeletes;
    protected $fillable = [
        'encounter_id',
        'pasien_id',
        'dokter_id',
        'nama_dokter',
        'ruangan_id',
        'bed_number',
        'admission_reason',
        'admission_date',
        'status',
    ];
    public function encounter()
    {
        return $this->belongsTo(Encounter::class, 'encounter_id');
    }
    public function patient()
    {
        return $this->belongsTo(Pasien::class);
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }
    public function room()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
    public function treatments()
    {
        return $this->hasMany(InpatientTreatment::class, 'admission_id');
    }
    public function medications()
    {
        return $this->hasMany(InpatientMedication::class, 'admission_id');
    }
    public function billings()
    {
        return $this->hasMany(InpatientBilling::class, 'admission_id');
    }
    public function companions()
    {
        return $this->hasMany(PatientCompanion::class, 'admission_id');
    }
}
