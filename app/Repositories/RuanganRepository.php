<?php

namespace App\Repositories;

use App\Models\CategoryRuangan;
use App\Models\Ruangan;

class RuanganRepository
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
        $ruangans = Ruangan::with('category')->orderBy('no_kamar', 'asc')->paginate(10);
        return $ruangans;
    }
    public function show($id)
    {
        return Ruangan::findOrFail($id);
    }

    public function create(array $data)
    {
        return Ruangan::create($data);
    }
    public function update($id, array $data)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update($data);
        return $ruangan;
    }
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();
        return $ruangan;
    }

    public function AllCategory()
    {
        return CategoryRuangan::orderBy('created_at', 'desc')->get();
    }
    public function showCategory($id)
    {
        return CategoryRuangan::findOrFail($id);
    }
    public function createCategory(array $data)
    {
        return CategoryRuangan::create($data);
    }
    public function updateCategory($id, array $data)
    {
        $category = CategoryRuangan::findOrFail($id);
        $category->update($data);
        return $category;
    }
    public function destroyCategory($id)
    {
        $category = CategoryRuangan::findOrFail($id);
        $category->delete();
        return $category;
    }
}
