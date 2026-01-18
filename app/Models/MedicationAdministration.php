<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MedicationAdministration extends Model
{
    protected $fillable = [
        'prescription_medication_id',
        'admission_id',
        'nurse_id',
        'administered_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'administered_at' => 'datetime'
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

    public function prescriptionMedication(): BelongsTo
    {
        return $this->belongsTo(PrescriptionMedication::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(InpatientAdmission::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
}
