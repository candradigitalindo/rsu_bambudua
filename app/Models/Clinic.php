<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasUuids;
    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'deskripsi',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
