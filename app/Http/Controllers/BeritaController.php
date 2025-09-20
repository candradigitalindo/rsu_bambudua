<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::with('user')->latest()->get();
        confirmDelete('Hapus Berita!', 'Apakah Anda yakin ingin menghapus berita ini?');
        return view('pages.berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('pages.berita.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'is_published' => 'required|boolean',
        ]);

        $data = [
            'judul' => $request->judul,
            'konten' => $request->konten,
            'is_published' => $request->is_published,
            'user_id' => Auth::id(),
        ];

        Berita::create($data);

        Alert::success('Berhasil', 'Berita berhasil ditambahkan.');
        return redirect()->route('berita.index');
    }

    public function edit(Berita $berita)
    {
        return view('pages.berita.edit', compact('berita'));
    }

    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'is_published' => 'required|boolean',
        ]);

        $data = [
            'judul' => $request->judul,
            'konten' => $request->konten,
            'is_published' => $request->is_published,
        ];


        $berita->update($data);

        Alert::success('Berhasil', 'Berita berhasil diperbarui.');
        return redirect()->route('berita.index');
    }

    public function destroy(Berita $berita)
    {
        $berita->delete();

        Alert::info('Berhasil', 'Berita berhasil dihapus.');
        return redirect()->route('berita.index');
    }
}
