<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Tindakan extends Model
{
    use HasUuids;
    protected $fillable = [
        'name',
        'description',
        'harga',
        'status'
    ];
    public function getHargaFormattedAttribute()
    {
        return number_format($this->harga, 0, ',', '.');
    }
}
