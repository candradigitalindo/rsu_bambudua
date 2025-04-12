<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasUuids;
    protected $fillable = [
        'name',
        'description',
        'is_expired',
        'is_active'
    ];
    public function stokbahan()
    {
        return $this->hasMany(Stokbahan::class);
    }

}
