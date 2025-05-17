<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Icd10 extends Model
{
    use HasUuids;
    protected $fillable = ['code', 'description', 'version'];
}
