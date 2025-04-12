<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stokbahan extends Model
{
    use HasUuids;
    protected $fillable = [
        'bahan_id',
        'is_available',
        'expired_at',
        'date_used',
        'description'
    ];
    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }   
}
