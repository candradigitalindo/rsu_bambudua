<?php

namespace App\Repositories;

use App\Models\Etnis;

class EtnisRepository
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
        $etnis = Etnis::latest()->get();
        return $etnis;
    }

    public function store($request)
    {
        $etnis = Etnis::create(['name' => ucfirst($request->name)]);
        return $etnis;
    }

    public function destroy($id)
    {
        $etnis = Etnis::findOrFail($id);
        $etnis->delete();
        return $etnis;
    }
}
