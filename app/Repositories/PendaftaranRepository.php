<?php

namespace App\Repositories;

use App\Models\Pasien;

class PendaftaranRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function cariPasien($request)
    {
        $pasiens = Pasien::where('name', 'like', '%' . $request->q . '%')->orWhere('rekam_medis', $request->q)->orWhere('no_identitas', $request->q)->orWhere('no_hp', $request->q)->orWhere('mr_lama', $request->q)->get();
        return $pasiens;
    }

    public function rawatJalan($request)
    {
        //
    }
}
