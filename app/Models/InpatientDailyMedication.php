<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InpatientDailyMedication extends Model
{
    use HasFactory, HasUuids;

    const STATUS_DIAJUKAN = 'Diajukan';
    const STATUS_DISIAPKAN = 'Disiapkan';
    const STATUS_DIBERIKAN = 'Diberikan';

    protected $fillable = [
        'inpatient_admission_id',
        'medication_code',
        'medication_name',
        'jumlah',
        'satuan',
        'harga',
        'total',
        'dosage_instructions',
        'authorized_by',
        'status',
        'is_billing',
        'administered_by',
        'administered_name',
        'administered_at',
    ];

    public function admission()
    {
        return $this->belongsTo(InpatientAdmission::class, 'inpatient_admission_id');
    }

    public function authorized()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function administered()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
