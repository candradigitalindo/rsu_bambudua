<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RadiologySupplyBatch extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'supply_id',
        'batch_number',
        'quantity',
        'remaining_quantity',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'expiry_date' => 'date',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function supply(): BelongsTo
    {
        return $this->belongsTo(RadiologySupply::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(RadiologySupplyTransaction::class, 'batch_id');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && $this->expiry_date->isBetween(now(), now()->addDays($days));
    }
}
