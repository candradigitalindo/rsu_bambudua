<?php

namespace App\Http\Controllers;

use App\Repositories\EtnisRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EtnisController extends Controller
{
    public $etnisRepository;
    public function __construct(EtnisRepository $etnisRepository)
    {
        $this->etnisRepository = $etnisRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $etnis = $this->etnisRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Etnis ?";
        confirmDelete($title, $text);
        return view('pages.etnis.index', compact('etnis'));
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
        $etnis = $this->etnisRepository->store($request);
        if ($etnis) {
            Alert::success('Berhasil', 'Data Etnis Tersimpan!');
        }else {
            Alert::error('Error', 'Data Etnis Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
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
        $this->etnisRepository->destroy($id);
        Alert::info('Berhasil', 'Data Etnis dihapus!');
        return back();
    }
}
