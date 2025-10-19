<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reagent extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'unit', 'stock', 'warning_stock'];

    protected $casts = ['stock' => 'integer', 'warning_stock' => 'integer'];

    public function transactions(): HasMany
    {
        return $this->hasMany(ReagentTransaction::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ReagentBatch::class);
    }
}
