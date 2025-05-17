<?php

namespace App\Repositories;

class CategoryRepository
{
    public function getAllCategories()
    {
        $query = \App\Models\Category::query();

        // Filter jika ada request (misal: nama)
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }
        // Count Jumlah produk apotek
        $query->withCount('products');
        // Paginate, misal 20 per halaman
        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getCategoryById($id)
    {
        return \App\Models\Category::find($id);
    }

    public function createCategory($data)
    {
        return \App\Models\Category::create($data);
    }

    public function updateCategory($id, $data)
    {
        $category = $this->getCategoryById($id);
        if ($category) {
            $category->update($data);
            return $category;
        }
        return null;
    }

    public function deleteCategory($id)
    {

        $category = $this->getCategoryById($id);
        if ($category) {
            $category->delete();
            return true;
        }
        return false;
    }
}
