<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NursingCareRecord extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'encounter_id',
        'nurse_id',
        'shift',
        'systolic',
        'diastolic',
        'heart_rate',
        'resp_rate',
        'temperature',
        'spo2',
        'pain_scale',
        'nursing_diagnosis',
        'interventions',
        'evaluation_notes',
    ];

    protected $casts = [
        'temperature' => 'decimal:1',
    ];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
}