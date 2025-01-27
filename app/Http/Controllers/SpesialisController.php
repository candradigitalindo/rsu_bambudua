<?php

namespace App\Http\Controllers;

use App\Repositories\SpesialisRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SpesialisController extends Controller
{
    public $spesialisRepository;
    public function __construct(SpesialisRepository $spesialisRepository)
    {
        $this->spesialisRepository = $spesialisRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $spesialis = $this->spesialisRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Spesialis ?";
        confirmDelete($title, $text);
        return view('pages.spesialis.index', compact('spesialis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:spesialis'],['name.required' => 'kolom masih kosong', 'name.unique' => 'Nama Spesialis '.ucfirst($request->name). ' sudah terdaftar']);
        $spesialis = $this->spesialisRepository->store($request);
        if ($spesialis) {
            Alert::success('Berhasil', 'Data Spesialis Tersimpan!');
        }else {
            Alert::error('Error', 'Data Spesialis Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
        }
        return back();
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->spesialisRepository->destroy($id);
        Alert::info('Berhasil', 'Data Spesialis dihapus!');
        return back();
    }
}
