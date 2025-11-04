<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateFieldItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'template_field_id',
        'item_name',
        'item_label',
        'item_type',
        'examination_name',
        'unit',
        'normal_range',
        'placeholder',
        'order'
    ];

    public function templateField()
    {
        return $this->belongsTo(TemplateField::class, 'template_field_id');
    }
}
