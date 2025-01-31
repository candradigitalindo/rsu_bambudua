<?php

namespace App\Repositories;

use App\Models\Loket;

class LoketRepository
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
        $lokets = Loket::orderBy('created_at', 'ASC')->get();
        return $lokets;
    }

    public function store($request)
    {
        $loket = Loket::create(['kode_loket' => ucfirst($request->kode_loket), 'keterangan' => $request->keterangan]);
        return $loket;
    }

    public function destroy($id)
    {
        $loket = Loket::findOrFail($id);
        $loket->delete();
        return $loket;
    }
}
