<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalEquipment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'medical_equipments';

    protected $fillable = [
        'name',
        'category',
        'serial_number',
        'asset_tag',
        'location',
        'vendor',
        'status',
        'purchase_date',
        'warranty_expiry',
        'last_calibration_date',
        'next_calibration_due',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'last_calibration_date' => 'date',
        'next_calibration_due' => 'date',
        'is_active' => 'boolean',
    ];

    public function maintenances()
    {
        return $this->hasMany(EquipmentMaintenance::class, 'equipment_id');
    }
}
