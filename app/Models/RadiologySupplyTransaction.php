<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadiologySupplyTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'supply_id',
        'batch_id',
        'type',
        'quantity',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function supply(): BelongsTo
    {
        return $this->belongsTo(RadiologySupply::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(RadiologySupplyBatch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
