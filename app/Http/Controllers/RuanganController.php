<?php

namespace App\Http\Controllers;

use App\Repositories\RuanganRepository;
use Illuminate\Http\Request;

class RuanganController extends Controller
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
        $ruangans = $this->ruanganRepository->index(); // Fetch all ruangan data
        return view('pages.ruangan.index', compact('ruangans')); // Pass data to the view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->ruanganRepository->AllCategory(); // Fetch all categories
        return view('pages.ruangan.create', compact('categories')); // Pass categories to the view for creating a new ruangan
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'no_kamar' => 'required|unique:ruangans',
                'harga' => 'required|string',
                'category' => 'required|string',
                'description' => 'nullable|string',
                'class' => 'nullable|string',
                'capacity' => 'nullable|integer',
            ],
            [
                'no_kamar.required' => 'No Kamar tidak boleh kosong.',
                'no_kamar.unique' => 'No Kamar harus unik.',
                'harga.required' => 'Harga harus diisi.',
                'harga.string' => 'Harga harus berupa string.',
                'category.required' => 'Kategori harus diisi.',
                'description.string' => 'Deskripsi harus berupa string.',
            ]
        );

        $data = [
            'no_kamar' => $request->no_kamar,
            'category_id' => $request->category,
            'description' => $request->description,
            'harga' => str_replace(".", "", $request->harga),
            'class' => $request->class, // Optional field
            'capacity' => $request->capacity, // Optional field
        ];

        $this->ruanganRepository->create($data); // Create new ruangan
        return redirect()->route('ruangan.index')->with('success', 'Ruangan created successfully.'); // Redirect with success message
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
        $ruangan = $this->ruanganRepository->show($id); // Fetch ruangan by ID
        $categories = $this->ruanganRepository->AllCategory(); // Fetch all categories
        return view('pages.ruangan.edit', compact('ruangan', 'categories')); // Pass data to the view for editing
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'no_kamar' => 'required|unique:ruangans,no_kamar,' . $id,
                'harga' => 'required|string',
                'category' => 'required|string',
                'description' => 'nullable|string',
                'class' => 'nullable|string',
                'capacity' => 'nullable|integer',
            ],
            [
                'no_kamar.required' => 'No Kamar tidak boleh kosong.',
                'no_kamar.unique' => 'No Kamar harus unik.',
                'harga.required' => 'Harga harus diisi.',
                'harga.string' => 'Harga harus berupa string.',
                'category.required' => 'Kategori harus diisi.',
                'description.string' => 'Deskripsi harus berupa string.',
            ]
        );

        $data = [
            'no_kamar' => $request->no_kamar,
            'category_id' => $request->category,
            'description' => $request->description,
            'harga' => str_replace(".", "", $request->harga),
            'class' => $request->class, // Optional field
            'capacity' => $request->capacity, // Optional field
        ];

        $this->ruanganRepository->update($id, $data); // Update ruangan by ID
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui.'); // Redirect with success message
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->ruanganRepository->destroy($id); // Delete ruangan by ID
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus.'); // Redirect with success message
    }
}
