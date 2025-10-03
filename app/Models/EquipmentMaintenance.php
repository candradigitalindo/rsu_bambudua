<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentMaintenance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'equipment_id',
        'date',
        'type',
        'performed_by',
        'notes',
        'cost',
        'operational_expense_id',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function equipment()
    {
        return $this->belongsTo(MedicalEquipment::class, 'equipment_id');
    }
}
