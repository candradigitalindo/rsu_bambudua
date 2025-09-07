<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncentiveSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description'
    ];
}
