<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasUuids;
    protected $table = 'ruangans';
    protected $fillable = ['no_kamar', 'description', 'category_id', 'harga'];

    public function category()
    {
        return $this->belongsTo(CategoryRuangan::class, 'category_id');
    }

    public function getHargaFormattedAttribute()
    {
        return number_format($this->harga, 0, ',', '.');
    }
}

