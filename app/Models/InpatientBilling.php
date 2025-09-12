<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InpatientBilling extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'inpatient_admission_id',
        'billing_type',
        'description',
        'amount',
        'payment_method',
        'paid_at',
        'created_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function admission()
    {
        return $this->belongsTo(InpatientAdmission::class, 'inpatient_admission_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
