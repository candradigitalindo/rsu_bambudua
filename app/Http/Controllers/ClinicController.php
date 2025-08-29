<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ClinicRepository;

class ClinicController extends Controller
{
    protected $clinicRepository;

    public function __construct(ClinicRepository $clinicRepository)
    {
        $this->clinicRepository = $clinicRepository;
    }
    public function index()
    {
        $clinics = $this->clinicRepository->all();
        return view('pages.clinics.index', compact('clinics'));
    }
    public function show($id)
    {
        $clinic = $this->clinicRepository->find($id);
        if (!$clinic) {
            abort(404);
        }
        return view('pages.clinics.show', compact('clinic'));
    }
    // post
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255|unique:clinics,nama',
            'alamat' => 'required|string|max:255',
            'telepon' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama klinik harus diisi.',
            'alamat.required' => 'Alamat klinik harus diisi.',
            'telepon.required' => 'Nomor telepon klinik harus diisi.',
        ]);
        $clinic = $this->clinicRepository->create($data);
        return redirect()->route('clinics.index')->with('success', 'Poliklinik ' . $clinic->nama . ' berhasil ditambahkan.');
    }
    // put
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255|unique:clinics,nama,' . $id,
            'alamat' => 'required|string|max:255',
            'telepon' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.required' => 'Nama klinik harus diisi.',
            'alamat.required' => 'Alamat klinik harus diisi.',
            'telepon.required' => 'Nomor telepon klinik harus diisi.',
        ]);
        $clinic = $this->clinicRepository->update($id, $data);
        if (!$clinic) {
            abort(404);
        }
        return redirect()->route('clinics.index')->with('success', 'Poliklinik ' . $clinic->nama . ' berhasil diperbarui.');
    }

    // delete
    public function destroy($id)
    {
        $deleted = $this->clinicRepository->delete($id);
        if (!$deleted) {
            abort(404);
        }
        return redirect()->route('clinics.index')->with('success', 'Poliklinik berhasil dihapus.');
    }

}
