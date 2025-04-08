<?php

namespace App\Http\Controllers;

use App\Repositories\RuanganRepository;
use Illuminate\Http\Request;

class CategoryRuanganController extends Controller
{
    public $ruanganRepository;
    public function __construct(RuanganRepository $ruanganRepository)
    {
        $this->ruanganRepository = $ruanganRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->ruanganRepository->AllCategory();
        return view('pages.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:category_ruangans',
            'description' => 'nullable|string|max:255',
        ],
        [
            'name.required' => 'Nama kategori ruangan tidak boleh kosong.',
            'name.unique' => 'Nama kategori ruangan sudah ada.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        $this->ruanganRepository->createCategory($data);

        return redirect()->route('category.index')->with('success', 'Kategori ruangan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = $this->ruanganRepository->showCategory($id);
        return view('pages.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:category_ruangans,name,' . $id,
            'description' => 'nullable|string|max:255',
        ],
        [
            'name.required' => 'Nama kategori ruangan tidak boleh kosong.',
            'name.unique' => 'Nama kategori ruangan sudah ada.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        $this->ruanganRepository->updateCategory($id, $data);

        return redirect()->route('category.index')->with('success', 'Kategori ruangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->ruanganRepository->destroyCategory($id);

        return redirect()->route('category.index')->with('success', 'Kategori ruangan berhasil dihapus.');
    }
}
