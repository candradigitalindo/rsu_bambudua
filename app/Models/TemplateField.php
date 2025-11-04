<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateField extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_pemeriksaan_id',
        'field_name',
        'field_label',
        'field_type',
        'placeholder',
        'order'
    ];

    public function jenisPemeriksaan()
    {
        return $this->belongsTo(JenisPemeriksaanPenunjang::class, 'jenis_pemeriksaan_id');
    }

    public function fieldItems()
    {
        return $this->hasMany(TemplateFieldItem::class, 'template_field_id')->orderBy('order');
    }

    // Check if this field is a group type with sub-items
    public function isGroup()
    {
        return $this->field_type === 'group';
    }
}
