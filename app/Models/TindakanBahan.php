<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TindakanBahan extends Model
{
    use HasUuids;
    protected $table = 'tindakan_bahan';
    protected $fillable = [
        'tindakan_id',
        'bahan_id',
        'quantity',
    ];
}
