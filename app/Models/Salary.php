<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasUuids;
    protected $table = 'salaries';
    protected $fillable = [
        'user_id',
        'base_salary',
        'notes',
    ];
}
