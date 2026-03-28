<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasUuids;
    protected $fillable = [
        'diskon_tindakan',
        'diskon_resep',
        'diskon_tindakan_nominal',
        'diskon_resep_nominal',
    ];
}
