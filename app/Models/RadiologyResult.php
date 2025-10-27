<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RadiologyResult extends Model
{
    use HasUuids;

    protected $fillable = [
        'radiology_request_id',
        'radiologist_id',
        'findings',
        'impression',
        'payload',
        'files',
        'reported_by',
        'reported_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'files' => 'array',
        'reported_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(RadiologyRequest::class, 'radiology_request_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function radiologist()
    {
        return $this->belongsTo(User::class, 'radiologist_id');
    }
}
