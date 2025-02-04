<?php

namespace App\Repositories;

use App\Models\LokasiLoket;

class LokasiloketRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $lokasis = LokasiLoket::latest()->get();
        return $lokasis;
    }

    public function store($request)
    {
        $lokasi = LokasiLoket::create(['lokasi_loket' => ucfirst($request->lokasi_loket), 'prefix_antrian' => strtoupper($request->prefix_antrian)]);
        return $lokasi;
    }

    public function edit($id)
    {
        $lokasi = LokasiLoket::findOrFail($id);
        return $lokasi;
    }

    public function update($request, $id)
    {
        $lokasi = LokasiLoket::findOrFail($id);
        $lokasi->update(['lokasi_loket' => ucfirst($request->lokasi_loket), 'prefix_antrian' => strtoupper($request->prefix_antrian)]);
        return $lokasi;
    }

    public function destroy($id)
    {
        $lokasi = LokasiLoket::findOrFail($id);
        $lokasi->delete();
        return $lokasi;
    }
}
