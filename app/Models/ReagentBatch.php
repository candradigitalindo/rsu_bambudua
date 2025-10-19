<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReagentBatch extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'reagent_id',
        'quantity',
        'remaining_quantity',
        'expiry_date',
    ];

    public function reagent(): BelongsTo
    {
        return $this->belongsTo(Reagent::class);
    }
}
