<?php

namespace App\Http\Controllers;

use App\Repositories\JaminanRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class JaminanController extends Controller
{
    public $jaminanRepository;
    public function __construct(JaminanRepository $jaminanRepository)
    {
        $this->jaminanRepository = $jaminanRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jaminans = $this->jaminanRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Jaminan ?";
        confirmDelete($title, $text);
        return view('pages.jenisjaminan.index', compact('jaminans'));
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
        $request->validate([
            'name'      => 'required|string',
            'status'    => 'required|string'
        ],[
            'name.required'     => 'Kolom masih kosong',
            'status.required'   => 'Pilih Status'
        ]);

        $jenis = $this->jaminanRepository->store($request);

        if ($jenis) {
            Alert::success('Berhasil', 'Jenis Jaminan Tersimpan!');
        }else {
            Alert::error('Error', 'Jenis Jaminan Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
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
        $jaminan = $this->jaminanRepository->edit($id);
        Alert::success('Berhasil', 'Jenis Jaminan Terupdate!');
        return back();
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
        $jaminan = $this->jaminanRepository->destroy($id);
        Alert::info('Berhasil', 'Data Jaminan dihapus!');
        return back();
    }
}
