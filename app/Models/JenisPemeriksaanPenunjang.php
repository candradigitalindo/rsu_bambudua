<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TemplateField;

class JenisPemeriksaanPenunjang extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'harga'];

    public function templateFields()
    {
        return $this->hasMany(TemplateField::class, 'jenis_pemeriksaan_id')->orderBy('order');
    }
}
