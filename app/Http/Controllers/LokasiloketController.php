<?php

namespace App\Http\Controllers;

use App\Repositories\LokasiloketRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class LokasiloketController extends Controller
{
    public $lokasiloketRepository;
    public function __construct(LokasiloketRepository $lokasiloketRepository)
    {
        $this->lokasiloketRepository = $lokasiloketRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lokasis = $this->lokasiloketRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Lokasi Loket ?";
        confirmDelete($title, $text);
        return view('pages.lokasiloket.index', compact('lokasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.lokasiloket.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lokasi_loket'  => 'required|string',
            'prefix_antrian'=> 'required|string|unique:lokasi_lokets'
        ],[
            'lokasi_loket.required'     => 'Kolom masih kosong',
            'prefix_antrian.required'   => 'Kolom masih kosong',
            'prefix_antrian.unique'     => 'Prefix Antrian '.$request->prefix_antrian. ' sudah terdaftar',
        ]);

        $lokasi = $this->lokasiloketRepository->store($request);
        if ($lokasi) {
            Alert::success('Berhasil', 'Data Lokasi Loket Tersimpan!');
        }else {
            Alert::error('Error', 'Data Lokasi Loket Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
        }
        return redirect()->route('lokasiloket.index');
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
        $lokasi = $this->lokasiloketRepository->edit($id);
        return view('pages.lokasiloket.edit', compact('lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'lokasi_loket'  => 'required|string',
            'prefix_antrian'=> 'required|string|unique:lokasi_lokets,prefix_antrian,'.$id
        ],[
            'lokasi_loket.required'     => 'Kolom masih kosong',
            'prefix_antrian.required'   => 'Kolom masih kosong',
            'prefix_antrian.unique'     => 'Prefix Antrian '.$request->prefix_antrian. ' sudah terdaftar',
        ]);

        $lokasi = $this->lokasiloketRepository->update($request, $id);
        if ($lokasi) {
            Alert::success('Berhasil', 'Data Lokasi Loket Terupdate!');
        }else {
            Alert::error('Error', 'Data Lokasi Loket Gagal Terupdate, silahkan coba secara berkala atau hubungi Developer');
        }
        return redirect()->route('lokasiloket.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->lokasiloketRepository->destroy($id);
        Alert::info('Berhasil', 'Data Lokasi Loket dihapus!');
        return redirect()->route('lokasiloket.index');
    }
}
