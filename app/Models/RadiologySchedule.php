<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RadiologySchedule extends Model
{
    use HasUuids;

    protected $fillable = [
        'radiology_request_id',
        'scheduled_start',
        'scheduled_end',
        'modality',
        'room',
        'radiographer_id',
        'preparation',
        'priority',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(RadiologyRequest::class, 'radiology_request_id');
    }

    public function radiographer()
    {
        return $this->belongsTo(User::class, 'radiographer_id');
    }
}
