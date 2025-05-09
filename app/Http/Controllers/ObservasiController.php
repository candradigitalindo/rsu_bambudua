<?php

namespace App\Http\Controllers;

use App\Repositories\ObservasiRepository;
use Illuminate\Http\Request;

class ObservasiController extends Controller
{
    public $observasiRepository;
    public function __construct(ObservasiRepository $observasiRepository)
    {
        $this->observasiRepository = $observasiRepository;
    }

    public function index($id)
    {
        $observasi = $id;
        return view('pages.observasi.index', compact('observasi'));
    }
    public function riwayatPenyakit($id)
    {
        $riwayatPenyakit = $this->observasiRepository->riwayatPenyakit($id);
        if ($riwayatPenyakit) {
            return response()->json($riwayatPenyakit);
        } else {
            return response()->json(['message' => 'Riwayat penyakit tidak ditemukan'], 404);
        }
    }
    public function postAnemnesis(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'keluhan_utama' => 'required|string|max:255',
            'riwayat_penyakit' => 'required|string|max:255',
            'riwayat_penyakit_keluarga' => 'required|string|max:255',
        ]);
        $result = $this->observasiRepository->postAnemnesis($request, $id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Anamnesis berhasil disimpan.',
            'data' => $result
        ]);
    }
    // ambil tanda vital berdasarkan encounter_id
    public function tandaVital($id)
    {
        $tandaVital = $this->observasiRepository->tandaVital($id);
        if ($tandaVital) {
            return response()->json($tandaVital);
        } else {
            return response()->json(['message' => 'Tanda vital tidak ditemukan'], 404);
        }
    }
    public function postTandaVital(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nadi' => 'nullable|string|max:255',
            'pernapasan' => 'nullable|string',
            'sistolik' => 'nullable|string',
            'diastolik' => 'nullable|string',
            'suhu' => 'nullable|string',
            'berat_badan' => 'nullable|string',
            'tinggi_badan' => 'nullable|string',
            'kesadaran' => 'nullable|string|max:255',
        ]);
        $result = $this->observasiRepository->postTandaVital($request, $id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Tanda Vital berhasil disimpan.',
            'data' => $result
        ]);
    }
}
