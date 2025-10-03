<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\InpatientBilling;
use App\Repositories\ApotekRepository;
use Illuminate\Http\Request;
use App\Models\Encounter;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiResepExport;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Concerns\LogsActivity;

class ApotekController extends Controller
{
    use LogsActivity;
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
        // Ambil Berita Terbaru
        $beritaTerbaru = Berita::where('is_published', true)
            ->where('created_at', '>=', now()->subMonth())
            ->latest()
            ->take(5)->get();
        return view('pages.apotek.dashboard', compact('data', 'beritaTerbaru'));
    }
    // PDF
    public function exportPdf()
    {
        $start = request('start_date');
        return $this->apotekRepository->exportPdf($start, request('end_date'));
    }
    // Excel
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $start = $request->get('start_date');
        $end = $request->get('end_date');

        // getPaidPrescriptionQuery now returns a collection directly.
        list($data, $warning) = $this->apotekRepository->getPaidPrescriptionQuery($start, $end);

        // Jika ada warning, bisa ditambahkan ke response header atau flash message
        if ($warning) {
            // Log warning untuk monitoring
            \Illuminate\Support\Facades\Log::info('Export Excel warning: ' . $warning);
        }

        return Excel::download(new TransaksiResepExport($data), 'transaksi_resep.xlsx');
    }

    // Halaman Antrian/Penyiapan Resep (Rawat Jalan & Pulang)
    public function penyiapanResepIndex()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        $query = \App\Models\Resep::with(['encounter.patient', 'encounter.clinic', 'details']);

        // Filter hanya resep yang memiliki item (details)
        $query->whereHas('details');

        if ($startDate && $endDate) {
            // Filter berdasarkan tanggal update jika ada rentang tanggal
            $query->whereBetween('updated_at', [$startDate, $endDate . ' 23:59:59']);
        } else {
            // Default: tampilkan resep yang memiliki item 'Diajukan'
            // ATAU resep yang semua itemnya 'Disiapkan' dan diupdate dalam 7 hari terakhir
            $query->where(function ($q) {
                $q->whereHas('details', function ($detailQuery) {
                    $detailQuery->where('status', 'Diajukan');
                })->orWhere('updated_at', '>=', now()->subDays(7));
            });
        }

        // Urutkan resep yang masih memiliki item 'Diajukan' di paling atas
        $reseps = $query->orderByRaw(
            "EXISTS (SELECT 1 FROM resep_details WHERE resep_details.resep_id = reseps.id AND resep_details.status = 'Diajukan') DESC"
        )
            ->orderByDesc('updated_at')
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
                // --- LOGIKA PENGURANGAN STOK APOTEK ---
                $product = \App\Models\ProductApotek::where('code', $permintaan->medication_code)
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new \RuntimeException('Produk dengan kode ' . $permintaan->medication_code . ' tidak ditemukan.');
                }

                // Validasi stok mencukupi
                if ($product->stok < $permintaan->jumlah) {
                    throw new \RuntimeException('Stok tidak mencukupi. Tersedia: ' . $product->stok . ', Diminta: ' . $permintaan->jumlah);
                }

                // Update status permintaan
                $permintaan->update(['status' => \App\Models\InpatientDailyMedication::STATUS_DISIAPKAN]);

                // 1. Kurangi stok utama
                $product->decrement('stok', $permintaan->jumlah);

                // 2. Ambil stok spesifik yang akan dikurangi (berdasarkan expired date jika ada)
                $stokIds = \App\Models\ApotekStok::where('product_apotek_id', $product->id)
                    ->where('status', 0) // Hanya yang tersedia (0 = Available)
                    ->orderBy('expired_at', 'asc') // Prioritaskan yang lebih dulu expired
                    ->limit($permintaan->jumlah)
                    ->pluck('id')
                    ->toArray();

                // Validasi apakah jumlah batch stok yang tersedia mencukupi
                if (count($stokIds) < $permintaan->jumlah) {
                    throw new \RuntimeException('Jumlah batch stok tidak mencukupi untuk memenuhi permintaan. Tersedia: ' . count($stokIds) . ', Diminta: ' . $permintaan->jumlah);
                }

                // 3. Update status stok menjadi terpakai (status = 1)
                if (count($stokIds) < $permintaan->jumlah) {
                    \Illuminate\Support\Facades\Log::warning('Jumlah batch stok tidak sesuai dengan permintaan', [
                        'product_id' => $product->id,
                        'requested' => $permintaan->jumlah,
                        'available_batches' => count($stokIds)
                    ]);
                }

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

                // Setelah obat disiapkan, update status encounter agar muncul di kasir
                if ($permintaan->admission && $permintaan->admission->encounter) {
                    $permintaan->admission->encounter->update(['status' => 2]);
                }
            });
        } catch (\RuntimeException $e) {
            // Error bisnis (stok tidak cukup, produk tidak ditemukan, dll)
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal siapkan obat inap: ' . $e->getMessage(), [
                'permintaan_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Gagal menyiapkan obat karena kesalahan server.'], 500);
        }

        $this->activity('Menyiapkan Obat Inap', [
            'permintaan_id' => $id,
            'medication_code' => $permintaan->medication_code ?? null,
            'jumlah' => $permintaan->jumlah ?? null,
        ], 'apotek');
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

            // Setelah semua item disiapkan, update status encounter ke 'Selesai Diperiksa' (2)
            $resep = \App\Models\Resep::with('encounter')->find($id);
            if ($resep && $resep->encounter) {
                $resep->encounter->update(['status' => 2]);
            }

            $this->activity('Menyiapkan Resep', ['resep_id' => $id], 'apotek');
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

            // Jika semua item sudah disiapkan, update status encounter ke 'Selesai Diperiksa' (2)
            if ($result['all_prepared']) {
                $resepDetail = \App\Models\ResepDetail::with('resep.encounter')->find($id);
                if ($resepDetail?->resep?->encounter) {
                    $resepDetail->resep->encounter->update(['status' => 2]);
                }
            }

            $this->activity('Menyiapkan Item Resep', ['resep_detail_id' => $id], 'apotek');
            return response()->json(['status' => 'success', 'message' => $result['message'], 'all_prepared' => $result['all_prepared']]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error siapkan item resep: ' . $e->getMessage(), [
                'resep_detail_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Gagal menyiapkan item resep.'], 500);
        }
    }

    // Halaman untuk menampilkan resep yang bisa di-reorder
    public function reorderList()
    {
        $search = request('search');

        if ($search) {
            $query = \App\Models\Resep::with(['encounter.patient', 'encounter.clinic'])
                // Hanya ambil resep yang statusnya 'Disiapkan'
                ->where('status', 'Disiapkan');

            $query->where(function ($q) use ($search) {
                $q->where('kode_resep', 'like', '%' . $search . '%')
                    ->orWhereHas('encounter', function ($encounterQuery) use ($search) {
                        $encounterQuery->where('name_pasien', 'like', '%' . $search . '%');
                    });
            });
            $reseps = $query->orderByDesc('created_at')->paginate(15);
        } else {
            // Jika tidak ada pencarian, kirim paginator kosong
            $reseps = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        return view('pages.apotek.reorder_resep_list', compact('reseps'));
    }

    // Proses siapkan ulang resep
    public function reorder(Request $request, $id)
    {
        try {
            $resep = \App\Models\Resep::with('details')->findOrFail($id);

            // Ubah status semua detail yang 'Disiapkan' menjadi 'Diajukan'
            $resep->details()->where('status', 'Disiapkan')->update(['status' => 'Diajukan']);

            // Ubah status resep utama menjadi 'Diajukan'
            $resep->update(['status' => 'Diajukan', 'updated_at' => now()]);

            $this->activity('Mengajukan Ulang Resep', ['resep_id' => $id], 'apotek');
            return redirect()->route('apotek.penyiapan-resep')->with('success', 'Resep berhasil diajukan ulang untuk penyiapan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error reorder resep: ' . $e->getMessage(), [
                'resep_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat mengajukan ulang resep.');
        }
    }
}
