<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PrescriptionMedication extends Model
{
    protected $fillable = [
        'prescription_order_id',
        'medication_name',
        'dosage',
        'route',
        'frequency',
        'scheduled_times',
        'instructions',
        'duration_days'
    ];

    protected $casts = [
        'scheduled_times' => 'array'
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

    public function prescriptionOrder(): BelongsTo
    {
        return $this->belongsTo(PrescriptionOrder::class);
    }

    public function administrations(): HasMany
    {
        return $this->hasMany(MedicationAdministration::class);
    }
}
