<?php

namespace App\Repositories;

use App\Models\JenisPemeriksaanPenunjang;

class JenisPemeriksaanPenunjangRepository
{
    public function getAll($perPage = 15)
    {
        $query = JenisPemeriksaanPenunjang::query();

        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        return $query->orderBy('name', 'asc')->paginate($perPage);
    }

    public function findById($id)
    {
        return JenisPemeriksaanPenunjang::findOrFail($id);
    }

    public function create(array $data)
    {
        // Hapus format non-numerik dari harga
        $data['harga'] = preg_replace('/[^\d]/', '', $data['harga']);
        return JenisPemeriksaanPenunjang::create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->findById($id);
        // Hapus format non-numerik dari harga
        $data['harga'] = preg_replace('/[^\d]/', '', $data['harga']);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        $item = $this->findById($id);

        // Cek apakah jenis pemeriksaan sudah digunakan di LabRequestItem atau RadiologyRequest
        if ($item->labRequestItems()->exists() || $item->radiologyRequests()->exists()) {
            return false;
        }

        $item->delete();
        return true;
    }
}
