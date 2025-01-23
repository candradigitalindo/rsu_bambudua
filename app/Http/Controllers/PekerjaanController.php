<?php

namespace App\Http\Controllers;

use App\Repositories\PekerjaanRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PekerjaanController extends Controller
{
    public $pekerjaanRepository;
    public function __construct(PekerjaanRepository $pekerjaanRepository)
    {
        $this->pekerjaanRepository = $pekerjaanRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pekerjaan = $this->pekerjaanRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Pekerjaan ?";
        confirmDelete($title, $text);
        return view('pages.pekerjaan.index', compact('pekerjaan'));
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
        $request->validate(['name' => 'required|string'],['name.required' => 'kolom masih kosong']);
        $pekerjaan = $this->pekerjaanRepository->store($request);
        if ($pekerjaan) {
            Alert::success('Berhasil', 'Data Pekerjaan Tersimpan!');
        }else {
            Alert::error('Error', 'Data Pekerjaan Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
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
        $this->pekerjaanRepository->destroy($id);
        Alert::info('Berhasil', 'Data Pekerjaan dihapus!');
        return back();
    }
}
