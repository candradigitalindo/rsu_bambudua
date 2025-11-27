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
        // Hapus format non-numerik dari harga dan fee
        $data['harga'] = preg_replace('/[^\d]/', '', $data['harga']);
        $data['fee_dokter_penunjang'] = isset($data['fee_dokter_penunjang']) ? preg_replace('/[^\d]/', '', $data['fee_dokter_penunjang']) : 0;
        $data['fee_perawat_penunjang'] = isset($data['fee_perawat_penunjang']) ? preg_replace('/[^\d]/', '', $data['fee_perawat_penunjang']) : 0;
        $data['fee_pelaksana'] = isset($data['fee_pelaksana']) ? preg_replace('/[^\d]/', '', $data['fee_pelaksana']) : 0;
        $data['biaya_bahan'] = isset($data['biaya_bahan']) ? preg_replace('/[^\d]/', '', $data['biaya_bahan']) : 0;
        $data['jasa_sarana'] = isset($data['jasa_sarana']) ? preg_replace('/[^\d]/', '', $data['jasa_sarana']) : 0;
        return JenisPemeriksaanPenunjang::create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->findById($id);
        // Hapus format non-numerik dari harga dan fee
        $data['harga'] = preg_replace('/[^\d]/', '', $data['harga']);
        $data['fee_dokter_penunjang'] = isset($data['fee_dokter_penunjang']) ? preg_replace('/[^\d]/', '', $data['fee_dokter_penunjang']) : 0;
        $data['fee_perawat_penunjang'] = isset($data['fee_perawat_penunjang']) ? preg_replace('/[^\d]/', '', $data['fee_perawat_penunjang']) : 0;
        $data['fee_pelaksana'] = isset($data['fee_pelaksana']) ? preg_replace('/[^\d]/', '', $data['fee_pelaksana']) : 0;
        $data['biaya_bahan'] = isset($data['biaya_bahan']) ? preg_replace('/[^\d]/', '', $data['biaya_bahan']) : 0;
        $data['jasa_sarana'] = isset($data['jasa_sarana']) ? preg_replace('/[^\d]/', '', $data['jasa_sarana']) : 0;
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
