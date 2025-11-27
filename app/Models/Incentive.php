<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incentive extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'encounter_id',
        'year',
        'month',
        'amount',
        'type',
        'description',
        'status',
        'paid_at'
    ];

    /**
     * Get the user that owns the incentive.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
