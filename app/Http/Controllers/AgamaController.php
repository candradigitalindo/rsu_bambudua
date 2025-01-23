<?php

namespace App\Http\Controllers;

use App\Repositories\AgamaRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AgamaController extends Controller
{
    public $agamaRepository;
    public function __construct(AgamaRepository $agamaRepository)
    {
        $this->agamaRepository = $agamaRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agama = $this->agamaRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Agama ?";
        confirmDelete($title, $text);
        return view('pages.agama.index', compact('agama'));
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
        $agama = $this->agamaRepository->store($request);
        if ($agama) {
            Alert::success('Berhasil', 'Data Agama Tersimpan!');
        }else {
            Alert::error('Error', 'Data Agama Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
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
        $this->agamaRepository->destroy($id);
        Alert::info('Berhasil', 'Data Agama dihapus!');
        return back();
    }
}
