<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'tgl_lahir',
        'gender',
        'email',
        'no_hp',
        'status_menikah',
        'gol_darah',
        'alamat',
        'kode_provinsi',
        'provinsi',
        'kode_kota',
        'kota',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
