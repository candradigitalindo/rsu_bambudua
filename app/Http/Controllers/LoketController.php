<?php

namespace App\Http\Controllers;

use App\Repositories\LoketRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class LoketController extends Controller
{
    public $loketRepository;
    public function __construct(LoketRepository $loketRepository)
    {
        $this->loketRepository = $loketRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lokets = $this->loketRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Loket ?";
        confirmDelete($title, $text);
        return view('pages.loket.index', compact('lokets'));
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
        $request->validate(['kode_loket' => 'required|string|unique:lokets', 'keterangan' => 'nullable|string'],['kode_loket.required' => 'kolom masih kosong', 'loket.unique' => 'Kode loket '.$request->kode_loket. ' sudah terdaftar']);
        $loket = $this->loketRepository->store($request);
        if ($loket) {
            Alert::success('Berhasil', 'Data Loket Tersimpan!');
        }else {
            Alert::error('Error', 'Data Loket Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
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
        $this->loketRepository->destroy($id);
        Alert::info('Berhasil', 'Data Loket dihapus!');
        return back();
    }
}
