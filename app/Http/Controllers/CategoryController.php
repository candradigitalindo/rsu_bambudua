<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;

class CategoryController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    // List & search
    public function index(Request $request)
    {
        $categories = $this->categoryRepo->getAllCategories();
        return view('pages.categories.index', compact('categories'));
    }

    // Show create form
    public function create()
    {
        return view('pages.categories.create');
    }

    // Store new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);
        $this->categoryRepo->createCategory($request->only('name'));
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Show edit form
    public function edit($id)
    {
        $category = $this->categoryRepo->getCategoryById($id);
        return view('pages.categories.edit', compact('category'));
    }

    // Update category
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $this->categoryRepo->updateCategory($id, $request->only('name'));
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diupdate.');
    }

    // Delete category
    public function destroy($id)
    {
        $this->categoryRepo->deleteCategory($id);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
