<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialistConsultation extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'encounter_id',
        'requested_by',
        'specialist_id',
        'assigned_doctor_id',
        'reason',
        'status',
        'scheduled_at',
        'result_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_doctor_id');
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Spesialis::class, 'specialist_id');
    }
}