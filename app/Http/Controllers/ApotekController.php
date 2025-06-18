<?php

namespace App\Http\Controllers;

use App\Repositories\ApotekRepository;
use Illuminate\Http\Request;
use App\Models\Encounter;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiResepExport;

class ApotekController extends Controller
{
    public $apotekRepository;
    // buat constructor untuk inisialisasi repository
    public function __construct(ApotekRepository $apotekRepository)
    {
        $this->apotekRepository = $apotekRepository;
    }

    // dashboard
    public function dashboard()
    {
        $data = $this->apotekRepository->dashboard();
        return view('pages.apotek.dashboard', compact('data'));
    }
    // PDF
    public function exportPdf()
    {
        $start = request('start_date');
        $end = request('end_date');
        $query = \App\Models\Encounter::where('status_bayar_resep', 1);

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->endOfDay();
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        } else {
            $query->where('updated_at', '>=', now()->subYear());
        }

        $data = $query->orderByDesc('updated_at')->get();

        $pdf = Pdf::loadView('pages.apotek.transaksi_resep_pdf', ['data' => $data]);
        return $pdf->download('transaksi_resep.pdf');
    }
    // Excel
    public function exportExcel($start = null, $end = null)
    {
        $query = \App\Models\Encounter::where('status_bayar_resep', 1);

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->endOfDay();
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        } else {
            $query->where('updated_at', '>=', now()->subYear());
        }

        $data = $query->orderByDesc('updated_at')->get();

        return Excel::download(new TransaksiResepExport($data), 'transaksi_resep.xlsx');
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
