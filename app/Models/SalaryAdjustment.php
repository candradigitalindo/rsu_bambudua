<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'bonus',
        'deduction',
        'notes',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
