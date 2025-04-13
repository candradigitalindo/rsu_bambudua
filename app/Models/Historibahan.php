<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Historibahan extends Model
{
    use HasUuids;
    protected $fillable = ['quantity', 'expired_at', 'description', 'bahan_id', 'status'];


    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
}
