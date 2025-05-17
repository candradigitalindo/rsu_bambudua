<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductApotek extends Model
{
    use HasUuids;
    protected $fillable = [
        'category_id',
        'name',
        'code',
        'satuan',
        'harga',
        'type',
        'status',
        'stok',
        'expired',
        'warning_stok',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function apotekStok()
    {
        return $this->hasMany(ApotekStok::class, 'product_apotek_id');
    }
    public function historiApotek()
    {
        return $this->hasMany(HistoriApotek::class, 'product_apotek_id');
    }
}
