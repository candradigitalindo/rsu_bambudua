<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasUuids;

    protected $fillable = [
        'rekam_medis',
        'name',
        'is_identitas',
        'jenis_identitas',
        'no_identitas',
        'tgl_lahir',
        'golongan_darah',
        'jenis_kelamin',
        'email',
        'no_telepon',
        'no_hp',
        'status_menikah',
        'etnis',
        'agama',
        'kewarganegaraan',
        'pendidikan',
        'pekerjaan',
        'mr_lama',
        'alamat',
        'province_code',
        'province',
        'city_code',
        'city',
        'satusehat_id',
        'status'
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'is_identitas' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function riwayatPenyakit()
    {
        return $this->hasOne(RiwayatPenyakit::class);
    }

    public function encounters()
    {
        return $this->hasMany(Encounter::class, 'rekam_medis', 'rekam_medis');
    }
}
