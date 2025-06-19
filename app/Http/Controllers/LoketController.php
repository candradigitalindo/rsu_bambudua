<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Repositories\LoketRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiTindakanExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LoketController extends Controller
{
    public $loketRepository;
    public function __construct(LoketRepository $loketRepository)
    {
        $this->loketRepository = $loketRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->loketRepository->index();
        $title = 'Delete Data!';
        $text = "Apakah yakin hapus data Loket ?";
        confirmDelete($title, $text);
        return view('pages.loket.index', compact('data'));
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
            'lokasi'      => 'required|string',
            'kode_loket' => 'required|string|unique:lokets',
            'user'       => 'required|string'
        ],[
            'kode_loket.required' => 'kolom masih kosong',
            'kode_loket.unique' => 'Kode loket '.$request->kode_loket. ' sudah terdaftar',
            'lokasi.required'    => 'Pilih Lokasi Loket',
            'user.required'     => 'Pilih Petugas'
        ]);
        $loket = $this->loketRepository->store($request);
        if ($loket) {
            Alert::success('Berhasil', 'Data Loket Tersimpan!');
        }else {
            Alert::error('Error', 'Data Loket Gagal Tersimpan, silahkan coba secara berkala atau hubungi Developer');
        }
        return redirect()->route('loket.index');
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
        $this->loketRepository->destroy($id);
        Alert::info('Berhasil', 'Data Loket dihapus!');
        return back();
    }
    public function dashboard()
    {
        $data = $this->loketRepository->dashboard();
        return view('pages.loket.dashboard', compact('data'));
    }
    public function getEncounter()
    {
        $encounters = $this->loketRepository->getEncounter();
        return view('pages.loket.encounter', compact('encounters'));
    }
    public function tindakanAjax($id)
    {
        $encounter = Encounter::with('tindakan')->findOrFail($id);
        return view('pages.loket._tindakan_detail_table', compact('encounter'))->render();
    }
    // Bayar Tindakan
    public function bayarTindakan(Request $request, $id)
    {
        $request->validate([
            'metode_pembayaran' => 'required|string',
        ]);
        $encounter = $this->loketRepository->bayarTindakan($request, $id);
        return response()->json([
            'status' => 'success',
            'message' => 'Tindakan berhasil dibayar.',
            'encounter' => $encounter
        ]);
    }
    // Cetak struk tindakan
    public function cetakEncounter($id)
    {
        $encounter = $this->loketRepository->cetakEncounter($id);
        return view('pages.loket.cetak_struk_tindakan', compact('encounter'));
    }
    public function exportPdf($start = null, $end = null)
    {
        $start = request('start_date');
        $end = request('end_date');
        $query = \App\Models\Encounter::where('status_bayar_tindakan', 1);

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->endOfDay();
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        } else {
            $query->where('updated_at', '>=', now()->subYear());
        }

        $data = $query->orderByDesc('updated_at')->get();

        $pdf = Pdf::loadView('pages.loket.transaksi_tindakan_pdf', ['data' => $data]);
        return $pdf->download('transaksi_tindakan.pdf');
    }
    // Excel
    public function exportExcel($start = null, $end = null)
    {
        $start = request('start_date');
        $end = request('end_date');
        $query = \App\Models\Encounter::where('status_bayar_tindakan', 1);

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->endOfDay();
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        } else {
            $query->where('updated_at', '>=', now()->subYear());
        }

        $data = $query->orderByDesc('updated_at')->get();

        return Excel::download(new TransaksiTindakanExport($data), 'transaksi_tindakan.xlsx');
    }

    // getReminderEncounter
    public function getReminderEncounter()
    {
        $encounters = $this->loketRepository->getReminderEncounter();
        return view('pages.loket.reminder_encounter', compact('encounters'));
    }
}
