<?php

namespace App\Repositories;

use App\Models\Pendidikan;

class PendidikanRepository
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
        $pendidikan = Pendidikan::latest()->get();
        return $pendidikan;
    }

    public function store($request)
    {
        $pendidikan = Pendidikan::create(['name' => ucfirst($request->name)]);
        return $pendidikan;
    }

    public function destroy($id)
    {
        $pendidikan = Pendidikan::findOrFail($id);
        $pendidikan->delete();
        return $pendidikan;
    }
}
