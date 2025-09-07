<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasUuids;
    protected $table = 'salary_payments';
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'amount',
        'status',
        'paid_at',
    ];
}
