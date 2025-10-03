<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabRequestItem extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'lab_request_id',
        'test_id',
        'test_name',
        'price',
        'result_value',
        'result_unit',
        'result_reference',
        'result_notes',
        'result_payload',
    ];

    protected $casts = [
        'result_payload' => 'array',
    ];

    public function request(): BelongsTo { return $this->belongsTo(LabRequest::class, 'lab_request_id'); }
    public function jenisPemeriksaan(): BelongsTo { return $this->belongsTo(\App\Models\JenisPemeriksaanPenunjang::class, 'test_id'); }
}
