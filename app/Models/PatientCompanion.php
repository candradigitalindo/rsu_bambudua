<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientCompanion extends Model
{
    use HasUuids, SoftDeletes;
    protected $fillable = [
        'admission_id',
        'name',
        'nik',
        'phone',
        'relation',
        'is_primary',
    ];
    public function admission()
    {
        return $this->belongsTo(InpatientAdmission::class, 'admission_id');
    }
}
