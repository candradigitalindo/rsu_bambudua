<?php

namespace App\Http\Controllers;

use App\Repositories\PenggunaRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PenggunaController extends Controller
{
    public $penggunaRepository;
    public function __construct(PenggunaRepository $penggunaRepository)
    {
        $this->penggunaRepository = $penggunaRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->penggunaRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Pengguna ?";
        confirmDelete($title, $text);
        return view('pages.pengguna.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $spesialis = $this->penggunaRepository->create();
        return view('pages.pengguna.create', compact('spesialis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'username'  => 'required|string|unique:users',
            'role'      => 'required|string',
            'password'  => 'required|min:8'
        ], [
            'name.required'         => 'Kolom masih kosong',
            'username.required'     => 'Kolom masih kosong',
            'username.unique'       => 'Username ' . $request->username . ' sudah terdaftar',
            'role.required'         => 'Pilih Hak Akses Pengguna',
            'password.required'     => 'Kolom masih kosong',
            'password.min'          => 'Minimal Password 8 karakter'
        ]);

        $pengguna = $this->penggunaRepository->store($request);
        if ($pengguna) {
            Alert::success('Berhasil', 'Data Pengguna Tersimpan!');
        } else {
            Alert::error('Error', 'Data Pengguna Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
        }
        return redirect()->route('pengguna.index');
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
        $data = $this->penggunaRepository->edit($id);
        return view('pages.pengguna.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'      => 'required|string',
            'username'  => 'required|string|unique:users,username,' . $id,
            'role'      => 'required|string',
            'password'  => 'nullable|min:8'
        ], [
            'name.required'         => 'Kolom masih kosong',
            'username.required'     => 'Kolom masih kosong',
            'username.unique'       => 'Username ' . $request->username . ' sudah terdaftar',
            'role.required'         => 'Pilih Hak Akses Pengguna',
            'password.min'          => 'Minimal Password 8 karakter'
        ]);

        $user = $this->penggunaRepository->update($request, $id);
        if ($user) {
            Alert::success('Berhasil', 'Data Pengguna terupdate!');
        } else {
            Alert::error('Error', 'Data Pengguna Gagal terupdate, silahkan coba secara berkala atau hubungi Developer');
        }
        return redirect()->route('pengguna.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->penggunaRepository->destroy($id);
        Alert::info('Berhasil', 'Data Pengguna dihapus!');
        return back();
    }
}
