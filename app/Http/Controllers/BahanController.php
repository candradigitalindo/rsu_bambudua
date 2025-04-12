<?php

namespace App\Http\Controllers;

use App\Repositories\BahanRepository;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public $bahanRepository;
    public function __construct(BahanRepository $bahanRepository)
    {
        $this->bahanRepository = $bahanRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bahans = $this->bahanRepository->index(); // Assuming a method to get all resources
        return view('pages.bahan.index', compact('bahans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Add logic to show the creation form
        return view('pages.bahan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_expired' => 'string|required',
            'is_active' => 'string|required',
        ],
        [
            'name.required' => 'Kolom Nama tidak boleh kosong',
            'name.string' => 'Kolom Nama Bahan harus berupa string',
            'name.max' => 'Kolom Nama Bahan tidak boleh lebih dari 255 karakter',
            'description.string' => 'Kolom Deskripsi harus berupa string',
            'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
            'is_expired.boolean' => 'Kolom Expired harus berupa boolean',
            'is_active.boolean' => 'Kolom Status harus berupa boolean',
            'is_expired.required' => 'Kolom Expired tidak boleh kosong',
            'is_active.required' => 'Kolom Status tidak boleh kosong',
        ]);
        $data = [
            'name' => $request->name,
            'is_expired' => (int) $request->is_expired,
            'description' => $request->description,
            'is_active' => (int) $request->is_active,
        ];

        $bahan = $this->bahanRepository->create($data); // Use $data instead of $request->all()
        return redirect()->route('bahans.index')->with('success', 'Bahan ' . $bahan->name . ' berhasil dibuat.');
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
        $bahan = $this->bahanRepository->show($id);
        return view('pages.bahan.edit', compact('bahan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_expired' => 'string|required',
            'is_active' => 'string|required',
        ],
        [
            'name.required' => 'Kolom Nama Bahan tidak boleh kosong',
            'name.string' => 'Kolom Nama Bahan harus berupa string',
            'name.max' => 'Kolom Nama Bahan tidak boleh lebih dari 255 karakter',
            'description.string' => 'Kolom Deskripsi harus berupa string',
            'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
            'is_expired.string' => 'Kolom Expired harus berupa string',
            'is_active.string' => 'Kolom Status harus berupa string',
            'is_expired.required' => 'Kolom Expired tidak boleh kosong',
            'is_active.required' => 'Kolom Status tidak boleh kosong',
        ]);
        $data = [
            'name' => $request->name,
            'is_expired' => (int) $request->is_expired,
            'description' => $request->description,
            'is_active' => (int) $request->is_active,
        ];

        $bahan = $this->bahanRepository->update($id, $data);
        return redirect()->route('bahans.index')->with('success', 'Bahan '.$bahan->name.' berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bahan = $this->bahanRepository->destroy($id);
        return redirect()->route('bahans.index')->with('success', 'Bahan '.$bahan->name.' berhasil dihapus.');
    }
}
