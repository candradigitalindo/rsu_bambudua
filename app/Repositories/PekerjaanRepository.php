<?php

namespace App\Repositories;

use App\Models\Pekerjaan;

class PekerjaanRepository
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
        $pekerjaan = Pekerjaan::orderBy('created_at', 'ASC')->get();
        return $pekerjaan;
    }

    public function store($request)
    {
        $pekerjaan = Pekerjaan::create(['name' => ucfirst($request->name)]);
        return $pekerjaan;
    }

    public function destroy($id)
    {
        $pekerjaan = Pekerjaan::findOrFail($id);
        $pekerjaan->delete();
        return $pekerjaan;
    }
}
