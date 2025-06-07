<?php

namespace App\Http\Controllers;

use App\Repositories\ApotekRepository;
use Illuminate\Http\Request;
use App\Models\Encounter;

class ApotekController extends Controller
{
    public $apotekRepository;
    // buat constructor untuk inisialisasi repository
    public function __construct(ApotekRepository $apotekRepository)
    {
        $this->apotekRepository = $apotekRepository;
    }

    // get Encounter
    public function getEncounter()
    {
        $encounters = $this->apotekRepository->getEncounter();
        return view('pages.apotek.encounter', compact('encounters'));
    }

    public function resepDetailAjax($id)
    {
        $encounter = Encounter::with('resep.details')->findOrFail($id);
        return view('pages.apotek._resep_detail_table', compact('encounter'))->render();
    }
    // bayar resep
    public function bayarResep(Request $request, $id)
    {
        $request->validate([
            'metode_pembayaran' => 'required|string',
        ]);
        $encounter = $this->apotekRepository->bayarResep($request, $id);
        return response()->json([
            'status' => 'success',
            'message' => 'Resep berhasil dibayar.',
            'encounter' => $encounter
        ]);
    }
    // cetak resep
    public function cetakResep($id)
    {
        $encounter = $this->apotekRepository->cetakResep($id);
        return view('pages.apotek.cetak_resep', compact('encounter'));
    }
}
