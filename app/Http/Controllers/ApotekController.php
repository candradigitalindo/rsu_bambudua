<?php

namespace App\Http\Controllers;

use App\Models\InpatientBilling;
use App\Repositories\ApotekRepository;
use Illuminate\Http\Request;
use App\Models\Encounter;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiResepExport;
use Illuminate\Support\Facades\Auth;

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
        return $this->apotekRepository->exportPdf($start, request('end_date'));
    }
    // Excel
    public function exportExcel($start = null, $end = null)
    {
        $start = request('start_date');
        $end = request('end_date');
        // getPaidPrescriptionQuery now returns a collection directly.
        list($data, $warning) = $this->apotekRepository->getPaidPrescriptionQuery($start, $end);

        return Excel::download(new TransaksiResepExport($data), 'transaksi_resep.xlsx');
    }

    // Halaman Antrian/Penyiapan Resep (Rawat Jalan & Pulang)
    public function penyiapanResepIndex()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        $query = \App\Models\Resep::with(['encounter.patient', 'encounter.clinic', 'details'])
            // Ambil resep yang statusnya 'Diajukan' atau 'Disiapkan'
            ->whereIn('status', ['Diajukan', 'Disiapkan']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        } else {
            // Default: tampilkan data 7 hari terakhir jika tidak ada filter tanggal
            $query->where('created_at', '>=', now()->subDays(7));
        }

        // Urutkan berdasarkan status ('Diajukan' dulu) lalu tanggal terbaru
        $reseps = $query->orderByRaw("FIELD(status, 'Diajukan', 'Disiapkan')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.apotek.penyiapan_resep', compact('reseps'));
    }

    // Permintaan Obat Rawat Inap
    public function permintaanObatInap()
    {
        // Ambil semua permintaan obat rawat inap, lalu kelompokkan berdasarkan admisi
        $startDate = request('start_date');
        $endDate = request('end_date');

        $query = \App\Models\InpatientDailyMedication::with('admission.encounter', 'authorized');

        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        } else {
            // Default: tampilkan data 30 hari terakhir jika tidak ada filter tanggal
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $permintaanGrouped = $query
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('inpatient_admission_id');

        // Paginate hasil yang sudah dikelompokkan secara manual
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $currentItems = $permintaanGrouped->slice(($currentPage - 1) * $perPage, $perPage);
        $permintaan = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $permintaanGrouped->count(), $perPage, $currentPage, ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]);

        return view('pages.apotek.permintaan_inap', compact('permintaan'));
    }

    // Detail Permintaan Obat Inap (untuk modal)
    public function permintaanObatInapDetail($id)
    {
        $permintaan = \App\Models\InpatientDailyMedication::with('authorized')->findOrFail($id);
        return view('pages.apotek._permintaan_inap_detail', compact('permintaan'))->render();
    }

    // Detail Permintaan Obat Inap (untuk modal, dikelompokkan berdasarkan admisi)
    public function permintaanObatInapDetailGrouped($admissionId)
    {
        $permintaan = \App\Models\InpatientDailyMedication::with('authorized', 'administered')
            ->where('inpatient_admission_id', $admissionId)
            ->orderBy('created_at', 'asc')
            ->get();
        return view('pages.apotek._permintaan_inap_detail_grouped', compact('permintaan'))->render();
    }

    // Siapkan Obat Rawat Inap
    public function siapkanObatInap($id)
    {
        $permintaan = \App\Models\InpatientDailyMedication::find($id);

        if (!$permintaan) {
            return response()->json(['success' => false, 'message' => 'Permintaan tidak ditemukan.'], 404);
        }
        if ($permintaan->status !== \App\Models\InpatientDailyMedication::STATUS_DIAJUKAN) {
            return response()->json(['status' => 'error', 'message' => 'Permintaan tidak valid atau sudah diproses.'], 400);
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($permintaan) {
                // Menggunakan metode update untuk memastikan penanganan data yang konsisten oleh Eloquent
                $permintaan->update(['status' => \App\Models\InpatientDailyMedication::STATUS_DISIAPKAN]);

                // --- LOGIKA PENGURANGAN STOK APOTEK ---
                $product = \App\Models\ProductApotek::where('code', $permintaan->medication_code)->first();
                if ($product) {
                    // 1. Kurangi stok utama
                    $product->decrement('stok', $permintaan->jumlah);

                    // 2. Ambil stok spesifik yang akan dikurangi (berdasarkan expired date jika ada)
                    $stokIds = \App\Models\ApotekStok::where('product_apotek_id', $product->id)
                        ->where('status', 0) // Hanya yang tersedia (0 = Available)
                        ->orderBy('expired_at', 'asc') // Prioritaskan yang lebih dulu expired
                        ->limit($permintaan->jumlah)
                        ->pluck('id')
                        ->toArray();

                    // 3. Update status stok menjadi terpakai (status = 1)
                    if (!empty($stokIds)) {
                        \App\Models\ApotekStok::whereIn('id', $stokIds)->update(['status' => 1]);
                    }

                    // 4. Catat histori stok keluar
                    \App\Models\HistoriApotek::create([
                        'product_apotek_id' => $product->id,
                        'jumlah'            => -$permintaan->jumlah, // Stok keluar harus negatif
                        'type'              => 1, // Tipe 1 untuk stok keluar
                        'keterangan'        => 'Permintaan obat rawat inap untuk ' . $permintaan->admission->encounter->name_pasien,
                    ]);
                } else {
                    // Opsional: Log jika produk tidak ditemukan
                    \Illuminate\Support\Facades\Log::warning('Produk tidak ditemukan saat siapkan obat inap.', ['code' => $permintaan->medication_code]);
                }
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal siapkan obat inap: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menyiapkan obat karena kesalahan server.'], 500);
        }

        return response()->json(['status' => 'success', 'message' => 'Obat berhasil disiapkan.']);
    }

    // Detail Resep (untuk modal)
    public function penyiapanResepDetail($id)
    {
        $resep = \App\Models\Resep::with('details')->findOrFail($id);
        return view('pages.apotek._penyiapan_resep_detail', compact('resep'))->render();
    }

    // Proses siapkan resep
    public function siapkanResep(Request $request, $id)
    {
        try {
            $result = $this->apotekRepository->siapkanResep($id);

            if (!$result['success']) {
                return response()->json(['status' => 'error', 'message' => $result['message']], 400);
            }

            return response()->json(['status' => 'success', 'message' => $result['message']]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error siapkan resep: ' . $e->getMessage(), [
                'resep_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    // Proses siapkan satu item resep
    public function siapkanItemResep(Request $request, $id)
    {
        try {
            $result = $this->apotekRepository->siapkanItemResep($id); // $id adalah resepDetailId
            if (!$result['success']) {
                return response()->json(['status' => 'error', 'message' => $result['message']], 400);
            }
            return response()->json(['status' => 'success', 'message' => $result['message']]);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['status' => 'error', 'message' => 'Gagal menyiapkan item resep.'], 500);
        }
    }
}
