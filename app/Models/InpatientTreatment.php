<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InpatientTreatment extends Model
{
    use HasUuids, SoftDeletes;
    protected $fillable = [
        'admission_id',
        'tindakan_name',
        'harga',
        'total',
        'treatment_date',
        'quantity',
        'result',
        'performed_by',
        'tindakan_id',
        'request_type',
        'document',
        'is_billing',
    ];
    public function admission()
    {
        return $this->belongsTo(InpatientAdmission::class, 'admission_id');
    }
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
    public function tindakan()
    {
        return $this->belongsTo(Tindakan::class, 'tindakan_id');
    }
}
