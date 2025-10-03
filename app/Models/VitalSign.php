<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VitalSign extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'vital_signs';

    protected $fillable = [
        'admission_id',
        'recorded_by_id',
        'measurement_time',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'respiratory_rate',
        'oxygen_saturation',
        'consciousness_level',
        'notes',
    ];

    protected $casts = [
        'measurement_time' => 'datetime',
        'temperature' => 'decimal:1',
    ];

    /**
     * Get the admission that owns the vital sign.
     */
    public function admission()
    {
        return $this->belongsTo(InpatientAdmission::class, 'admission_id');
    }

    /**
     * Get the user who recorded the vital sign.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}
