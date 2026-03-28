<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaketPemeriksaan extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'tindakan_ids',
        'lab_ids',
        'radiologi_ids',
        'obat_ids',
        'jumlah_sesi',
        'harga',
        'is_gratis',
        'masa_berlaku_hari',
        'status',
    ];

    protected $casts = [
        'tindakan_ids' => 'array',
        'lab_ids' => 'array',
        'radiologi_ids' => 'array',
        'obat_ids' => 'array',
        'is_gratis' => 'boolean',
        'status' => 'boolean',
        'harga' => 'integer',
    ];

    public function paketPasiens()
    {
        return $this->hasMany(PaketPasien::class);
    }

    public function getItemIds($field)
    {
        $items = $this->$field ?? [];
        return collect($items)->map(fn($i) => is_array($i) ? $i['id'] : $i)->toArray();
    }

    public function getItemQtyMap($field)
    {
        $items = $this->$field ?? [];
        return collect($items)->mapWithKeys(fn($i) => is_array($i) ? [$i['id'] => $i['qty'] ?? 1] : [$i => 1])->toArray();
    }

    private function resolveItems($field, $modelClass, $filters = [])
    {
        if (empty($this->$field)) return collect();
        $items = collect($this->$field);
        $isObj = $items->first() && is_array($items->first());
        $ids = $isObj ? $items->pluck('id') : $items;
        $qtyMap = $isObj ? $items->pluck('qty', 'id') : $items->mapWithKeys(fn($id) => [$id => 1]);
        $query = $modelClass::whereIn('id', $ids);
        foreach ($filters as $k => $v) $query->where($k, $v);
        return $query->get()->map(function ($m) use ($qtyMap) {
            $m->qty = $qtyMap[$m->id] ?? 1;
            return $m;
        });
    }

    public function tindakanList()
    {
        return $this->resolveItems('tindakan_ids', Tindakan::class);
    }

    public function labList()
    {
        return $this->resolveItems('lab_ids', JenisPemeriksaanPenunjang::class, ['type' => 'lab']);
    }

    public function radiologiList()
    {
        return $this->resolveItems('radiologi_ids', JenisPemeriksaanPenunjang::class, ['type' => 'radiologi']);
    }

    public function obatList()
    {
        return $this->resolveItems('obat_ids', ProductApotek::class);
    }
}
