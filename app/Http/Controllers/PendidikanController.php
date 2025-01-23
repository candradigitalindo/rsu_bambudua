<?php

namespace App\Http\Controllers;

use App\Repositories\PendidikanRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PendidikanController extends Controller
{
    public $pendidikanRepository;
    public function __construct(PendidikanRepository $pendidikanRepository)
    {
        $this->pendidikanRepository = $pendidikanRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendidikans = $this->pendidikanRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Pendidikan ?";
        confirmDelete($title, $text);
        return view('pages.pendidikan.index', compact('pendidikans'));
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
        $request->validate(['name' => 'required|string']);
        $pendidikan = $this->pendidikanRepository->store($request);
        if ($pendidikan) {
            Alert::success('Berhasil', 'Data Pendidikan Tersimpan!');
        }else {
            Alert::error('Error', 'Data Pendidikan Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
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
        $this->pendidikanRepository->destroy($id);
        Alert::info('Berhasil', 'Data Pendidikan dihapus!');
        return back();
    }
}
