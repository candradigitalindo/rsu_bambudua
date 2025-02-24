<?php

namespace App\Repositories;

use App\Models\Antrian;
use App\Models\LokasiLoket;

class AntrianRepository
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

    public function show($id)
    {
        $lokasi = LokasiLoket::findOrFail($id);
        return $lokasi;
    }

    public function store($id)
    {
        $lokasi = LokasiLoket::findOrFail($id);
        $cek    = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $id)->count();
        $antrian= Antrian::create([
            'lokasiloket_id'    => $lokasi->id,
            'prefix'            => $lokasi->prefix_antrian,
            'nomor'             => $cek == 0 ? 1 : $cek + 1
        ]);
        return $antrian;
    }
}
