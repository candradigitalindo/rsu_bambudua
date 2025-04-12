<?php

namespace App\Repositories;

use App\Models\Bahan;

class BahanRepository
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
        $bahan = Bahan::where('name', 'like', '%' . request('name') . '%')->orderBy('updated_at', 'DESC')->paginate(10);
        return $bahan;
    }
    public function show($id)
    {
        return Bahan::findOrFail($id);
    }
    public function create(array $data)
    {
        return Bahan::create($data);
    }
    public function update($id, array $data)
    {
        $bahan = Bahan::findOrFail($id);
        $bahan->update($data);
        return $bahan;
    }
    public function destroy($id)
    {
        $bahan = Bahan::findOrFail($id);
        $bahan->delete();
        return $bahan;
    }
    public function getBahan()
    {
        return Bahan::where('is_active', true)->orderBy('name', 'ASC')->get();
    }
}
