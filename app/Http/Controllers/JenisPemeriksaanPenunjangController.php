<?php

namespace App\Http\Controllers;

use App\Models\JenisPemeriksaanPenunjang;
use App\Models\TemplateField;
use Illuminate\Http\Request;
use App\Repositories\JenisPemeriksaanPenunjangRepository;
use RealRashid\SweetAlert\Facades\Alert;

class JenisPemeriksaanPenunjangController extends Controller
{
    protected $repository;

    public function __construct(JenisPemeriksaanPenunjangRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $jenisPemeriksaan = $this->repository->getAll();
        confirmDelete('Hapus Data!', 'Apakah Anda yakin ingin menghapus data ini?');
        return view('pages.master.jenis_pemeriksaan.index', compact('jenisPemeriksaan'));
    }

    public function create()
    {
        return view('pages.master.jenis_pemeriksaan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:jenis_pemeriksaan_penunjangs,name',
            'type' => 'required|in:lab,radiologi',
            'harga' => 'required|string',
        ], [
            'name.required' => 'Nama pemeriksaan harus diisi.',
            'name.unique' => 'Nama pemeriksaan sudah ada.',
            'type.required' => 'Tipe pemeriksaan harus dipilih.',
            'harga.required' => 'Harga harus diisi.',
        ]);

        $this->repository->create($request->all());

        Alert::success('Berhasil', 'Data berhasil ditambahkan.');
        return redirect()->route('jenis-pemeriksaan.index');
    }

    public function edit($id)
    {
        $item = $this->repository->findById($id);
        return view('pages.master.jenis_pemeriksaan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:jenis_pemeriksaan_penunjangs,name,' . $id,
            'type' => 'required|in:lab,radiologi',
            'harga' => 'required|string',
        ], [
            'name.required' => 'Nama pemeriksaan harus diisi.',
            'name.unique' => 'Nama pemeriksaan sudah ada.',
            'type.required' => 'Tipe pemeriksaan harus dipilih.',
            'harga.required' => 'Harga harus diisi.',
        ]);

        $this->repository->update($id, $request->all());

        Alert::success('Berhasil', 'Data berhasil diperbarui.');
        return redirect()->route('jenis-pemeriksaan.index');
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->repository->delete($id);
            if ($deleted) {
                Alert::success('Berhasil', 'Data berhasil dihapus.');
            } else {
                Alert::error('Gagal', 'Data tidak dapat dihapus karena sudah digunakan.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Menangkap error foreign key constraint
            if ($e->getCode() == 23000) {
                Alert::error('Gagal', 'Data tidak dapat dihapus karena sudah digunakan di transaksi lain.');
            } else {
                Alert::error('Gagal', 'Terjadi kesalahan pada database.');
            }
        }

        return back();
    }

    public function showFields($id)
    {
        $jenisPemeriksaan = JenisPemeriksaanPenunjang::with('templateFields')->findOrFail($id);
        confirmDelete('Hapus Kolom!', 'Apakah Anda yakin ingin menghapus kolom ini?');
        return view('pages.master.jenis_pemeriksaan.fields', compact('jenisPemeriksaan'));
    }

    public function storeField(Request $request, $id)
    {
        $request->validate([
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,number,textarea',
            'placeholder' => 'nullable|string|max:255',
        ], [
            'field_label.required' => 'Label Kolom harus diisi.',
            'field_type.required' => 'Tipe Kolom harus dipilih.',
        ]);

        $jenisPemeriksaan = JenisPemeriksaanPenunjang::findOrFail($id);

        $fieldName = \Illuminate\Support\Str::snake($request->field_label);

        $jenisPemeriksaan->templateFields()->create([
            'field_name' => $fieldName,
            'field_label' => $request->field_label,
            'field_type' => $request->field_type,
            'placeholder' => $request->placeholder,
        ]);

        Alert::success('Berhasil', 'Kolom berhasil ditambahkan.');
        return back();
    }

    public function destroyField($field_id)
    {
        TemplateField::findOrFail($field_id)->delete();
        Alert::success('Berhasil', 'Kolom berhasil dihapus.');
        return back();
    }
}
