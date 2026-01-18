<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PrescriptionOrder extends Model
{
    protected $fillable = [
        'encounter_id',
        'doctor_id',
        'status',
        'pharmacy_status',
        'notes',
        'pharmacy_notes',
        'pharmacy_processed_at',
        'pharmacy_processed_by'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medications(): HasMany
    {
        return $this->hasMany(PrescriptionMedication::class);
    }
}
