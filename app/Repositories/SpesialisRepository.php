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
        if (ucfirst($request->name) == "Owner") {
            $kode = 1;
        }else {
            $cek       = Spesialis::max('kode');
            $kode      = $cek == 0 ? 2 : $cek + 1;
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
