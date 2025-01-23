<?php

namespace App\Repositories;

use App\Models\Agama;

class AgamaRepository
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
        $agama = Agama::orderBy('created_at', 'ASC')->get();
        return $agama;
    }

    public function store($request)
    {
        $agama = Agama::create(['name' => ucfirst($request->name)]);
        return $agama;
    }

    public function destroy($id)
    {
        $agama = Agama::findOrFail($id);
        $agama->delete();
        return $agama;
    }
}
