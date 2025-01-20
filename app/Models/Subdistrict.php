<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
    use HasUuids;
    protected $fillable = ['code', 'parent_code', 'bps_code', 'name', 'province_code'];
}
