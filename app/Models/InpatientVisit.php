<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InpatientVisit extends Model
{
    protected $table = 'inpatient_visits';
    public $incrementing = false; // karena id UUID

    protected $fillable = [
        'inpatient_admission_id',
        'dokter_id',
        'perawat_id',
        'tanggal_visit',
        'catatan',
    ];

    public function admission()
    {
        return $this->belongsTo(InpatientAdmission::class, 'inpatient_admission_id');
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function perawat()
    {
        return $this->belongsTo(User::class, 'perawat_id');
    }
}
