<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RadiologySupply extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'unit',
        'stock',
        'warning_stock',
    ];

    protected $casts = [
        'stock' => 'integer',
        'warning_stock' => 'integer',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function batches(): HasMany
    {
        return $this->hasMany(RadiologySupplyBatch::class, 'supply_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(RadiologySupplyTransaction::class, 'supply_id');
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->warning_stock;
    }

    public function hasExpiredBatches(): bool
    {
        return $this->batches()
            ->where('expiry_date', '<=', now())
            ->where('remaining_quantity', '>', 0)
            ->exists();
    }
}
