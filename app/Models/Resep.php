<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasUuids;
    protected $fillable = [
        'id',
        'encounter_id',
        'kode_resep',
        'dokter',
        'catatan',
    ];
    // encounter
    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
    public function details()
    {
        return $this->hasMany(ResepDetail::class);
    }

    /**
     * Get the overall status of the prescription.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        // Gunakan loadMissing untuk efisiensi, hanya load jika belum ada.
        $this->loadMissing('details');

        // Cek apakah ada detail yang masih 'Diajukan'
        if ($this->details->contains('status', 'Diajukan')) {
            return 'Diajukan';
        }

        // Jika tidak ada yang 'Diajukan' dan ada detail, berarti semua sudah diproses
        if ($this->details->isNotEmpty()) {
            return 'Disiapkan';
        }

        // Default jika tidak ada detail atau kondisi lain
        return 'Tidak Ada Item';
    }
}
