<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasUuids;
    protected $fillable = [
        'name',
        'description',
        'is_expired',
        'is_active',
        'warning'
    ];
    public function stokbahan()
    {
        return $this->hasMany(Stokbahan::class);
    }
    public function historibahan()
    {
        return $this->hasMany(Historibahan::class);
    }

    public function getStockQuantityAttribute()
    {
        return number_format($this->stokbahan()->where('is_available', 1)->count(), 0, ',', '.');
    }
    public function getExpiredStockQuantityAttribute()
    {
        return number_format($this->stokbahan()->where('is_available', 1)->where('expired_at', '<', now())->count(), 0, ',', '.');
    }

    public function getWarningStockQuantityAttribute()
    {
        return number_format($this->stokbahan()->where('is_available', 1)->where('expired_at', '>', Carbon::now())->where('expired_at', '<=', Carbon::now()->subDays($this->warning))->count(), 0, ',', '.');
    }
    public function tindakan()
    {
        return $this->belongsToMany(Tindakan::class, 'tindakan_bahan')->withPivot('quantity', 'id')->withTimestamps();
    }
}
