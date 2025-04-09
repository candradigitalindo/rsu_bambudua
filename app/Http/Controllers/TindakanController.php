<?php

namespace App\Http\Controllers;

use App\Repositories\TindakanRepository;
use Illuminate\Http\Request;

class TindakanController extends Controller
{
    public $tindakanRepository;
    public function __construct(TindakanRepository $tindakanRepository) {
        $this->tindakanRepository = $tindakanRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tindakans = $this->tindakanRepository->index();
        return view('pages.tindakan.index', compact('tindakans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.tindakan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'harga' => 'required|string',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Nama tindakan tidak boleh kosong.',
            'name.string' => 'Nama tindakan harus berupa string.',
            'name.max' => 'Nama tindakan tidak boleh lebih dari 255 karakter.',
            'description.string' => 'Deskripsi harus berupa string.',
            'harga.required' => 'Harga harus diisi.',
            'harga.string' => 'Harga harus berupa string.',
            'status.required' => 'Status harus diisi.',
            'status.boolean' => 'Status harus berupa boolean.',
        ]);
        $data = [
            'name' => $request->name,
            'status' => $request->status,
            'description' => $request->description,
            'harga' => str_replace(".", "", $request->harga),
        ];

        $tindakan = $this->tindakanRepository->create($data);

        return redirect()->route('tindakan.index')->with('success', 'Tindakan '.$tindakan->name.' berhasil dibuat.');
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
        $tindakan = $this->tindakanRepository->show($id);
        return view('pages.tindakan.edit', compact('tindakan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'harga' => 'required|string',
            'status' => 'required|numeric',
        ], [
            'name.required' => 'Nama tindakan tidak boleh kosong.',
            'name.string' => 'Nama tindakan harus berupa string.',
            'name.max' => 'Nama tindakan tidak boleh lebih dari 255 karakter.',
            'description.string' => 'Deskripsi harus berupa string.',
            'harga.required' => 'Harga harus diisi.',
            'harga.string' => 'Harga harus berupa string.',
            'status.required' => 'Status harus diisi.',
            'status.numeric' => 'Status harus berupa angka.',
        ]);
        $data = [
            'name' => $request->name,
            'status' => (int) $request->status,
            'description' => $request->description,
            'harga' => str_replace(".", "", $request->harga),
        ];

        $tindakan = $this->tindakanRepository->update($id, $data);

        return redirect()->route('tindakan.index')->with('success', 'Tindakan '.$tindakan->name.' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tindakan = $this->tindakanRepository->destroy($id);
        return redirect()->route('tindakan.index')->with('success', 'Tindakan '.$tindakan->name.' berhasil dihapus.');
    }
}
