<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TemplateField;
use App\Models\LabRequestItem;
use App\Models\RadiologyRequest;

class JenisPemeriksaanPenunjang extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'type', 'harga'];

    public function templateFields()
    {
        return $this->hasMany(TemplateField::class, 'jenis_pemeriksaan_id')->orderBy('order');
    }

    // Relasi ke item permintaan lab (lab_request_items.test_id -> jenis_pemeriksaan_penunjangs.id)
    public function labRequestItems()
    {
        return $this->hasMany(LabRequestItem::class, 'test_id');
    }

    // Relasi ke permintaan radiologi (radiology_requests.jenis_pemeriksaan_id -> jenis_pemeriksaan_penunjangs.id)
    public function radiologyRequests()
    {
        return $this->hasMany(RadiologyRequest::class, 'jenis_pemeriksaan_id');
    }
}
