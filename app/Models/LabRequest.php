<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabRequest extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'encounter_id',
        'requested_by',
        'status',
        'requested_at',
        'collected_at',
        'completed_at',
        'notes',
        'total_charge',
        'charged',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'collected_at' => 'datetime',
        'completed_at' => 'datetime',
        'charged' => 'boolean',
    ];

    public function encounter(): BelongsTo { return $this->belongsTo(Encounter::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function items(): HasMany { return $this->hasMany(LabRequestItem::class); }
}