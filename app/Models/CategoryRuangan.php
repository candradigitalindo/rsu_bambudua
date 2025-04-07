<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CategoryRuangan extends Model
{
    use HasUuids;
    protected $table = 'category_ruangans';
    protected $fillable = ['name', 'description'];
    
}
