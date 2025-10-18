<?php

namespace App\Repositories;

use App\Models\Spesialis;

class SpesialisRepository
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
        $spesialis = Spesialis::latest()->get();
        return $spesialis;
    }

    public function store($request)
    {
        // Ambil 3 huruf pertama dari nama dan ubah menjadi uppercase
        $baseKode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $request->name), 0, 3));

        // Cek apakah kode sudah ada
        $counter = 0;
        $kode = $baseKode;
        while (Spesialis::where('kode', $kode)->exists()) {
            $counter++;
            // Jika 'PEN' sudah ada, coba 'PE1', 'PE2', dst.
            $nextKode = substr($baseKode, 0, 2) . $counter;
            $kode = $nextKode;
        }

        $spesialis = Spesialis::create([
            'name' => ucfirst($request->name),
            'kode' => $kode
        ]);
        return $spesialis;
    }

    public function destroy($id)
    {
        $spesialis = Spesialis::findOrFail($id);
        $spesialis->delete();
        return $spesialis;
    }
}
