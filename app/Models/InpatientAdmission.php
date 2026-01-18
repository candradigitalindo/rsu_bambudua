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
        'discharge_date',
        'transfer_date',
        'transfer_from',
        'transfer_to',
        'transfer_notes',
    ];
    protected $dates = [
        'admission_date',
        'discharge_date',
        'transfer_date',
    ];
    public function encounter()
    {
        return $this->belongsTo(Encounter::class, 'encounter_id');
    }
    public function patient()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }
    public function room()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'id');
    }

    public function treatments()
    {
        return $this->hasMany(InpatientTreatment::class, 'admission_id');
    }

    public function companions()
    {
        return $this->hasOne(PatientCompanion::class, 'admission_id');
    }
    public function visits()
    {
        return $this->hasMany(InpatientVisit::class, 'inpatient_admission_id');
    }
}
