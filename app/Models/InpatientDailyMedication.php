<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InpatientDailyMedication extends Model
{
    use HasUuids;
    protected $fillable = [
        'id',
        'inpatient_admission_id',
        'medication_code',
        'medication_name',
        'harga',
        'jumlah',
        'total',
        'dosage_instructions',
        'satuan',
        'route',
        'frequency',
        'expiration_date',
        'notes',
        'status',
        'authorized_by',
        'authorized_name',
        'administered_by',
        'administered_name',
        'administered_at',
        'is_billing',
        'medicine_date',
    ];
    public function authorized()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function administered()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
