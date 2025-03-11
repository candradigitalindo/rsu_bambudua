<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasUuids;
    protected $fillable = [
        'rekam_medis', 'name', 'is_identitas', 'jenis_identitas', 'no_identitas', 'tgl_lahir', 'golongan_darah', 'jenis_kelamin', 'email', 'no_telepon', 'no_hp', 'status_menikah', 'etnis', 'agama', 'kewarganegaraan', 'pendidikan', 'pekerjaan', 'mr_lama','alamat', 'province_code','province', 'city_code', 'city', 'satusehat_id'
    ];
}
