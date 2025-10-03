<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name', 'contact', 'phone', 'email', 'address', 'npwp', 'payment_terms', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}