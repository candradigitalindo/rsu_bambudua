<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaketPasien extends Model
{
    use HasUuids;

    protected $fillable = [
        'paket_pemeriksaan_id',
        'pasien_id',
        'total_sesi',
        'sesi_terpakai',
        'harga_bayar',
        'status_bayar',
        'metode_pembayaran',
        'payment_fee',
        'grand_total',
        'paid_at',
        'tanggal_mulai',
        'tanggal_expired',
        'status',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_expired' => 'date',
        'paid_at' => 'datetime',
        'harga_bayar' => 'integer',
        'payment_fee' => 'integer',
        'grand_total' => 'integer',
        'status_bayar' => 'boolean',
    ];

    public function paketPemeriksaan()
    {
        return $this->belongsTo(PaketPemeriksaan::class);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function usages()
    {
        return $this->hasMany(PaketPasienUsage::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sisaSesi()
    {
        return $this->total_sesi - $this->sesi_terpakai;
    }

    public function sisaHari()
    {
        return max(0, Carbon::now()->diffInDays($this->tanggal_expired, false));
    }

    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->tanggal_expired);
    }
}
