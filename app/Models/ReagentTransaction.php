<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReagentTransaction extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['reagent_id', 'user_id', 'type', 'qty', 'notes', 'lab_request_item_id', 'expiry_date'];

    public function reagent(): BelongsTo
    {
        return $this->belongsTo(Reagent::class);
    }
    public function labItem(): BelongsTo
    {
        return $this->belongsTo(LabRequestItem::class, 'lab_request_item_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
