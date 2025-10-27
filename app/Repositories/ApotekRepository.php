<?php

namespace App\Repositories;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApotekRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function dashboard()
    {
        // Statistik stok obat (total, tersedia, habis) dalam 1 query
        $stokSummary = \App\Models\ProductApotek::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN stok > 0 THEN 1 ELSE 0 END) as tersedia,
            SUM(CASE WHEN stok = 0 THEN 1 ELSE 0 END) as habis
        ")->first();

        // Obat Kadaluarsa (status 0 = stok aktif, bukan terpakai)
        $obatKadaluarsa = \App\Models\ApotekStok::where('expired_at', '<=', now())
            ->where('status', 0)
            ->count();

        // Tentukan rentang waktu berdasarkan role
        $userRole = Auth::user()->role;
        $isOwner = ($userRole == 1);
        $defaultStartDate = $isOwner ? now()->subYear()->startOfDay() : now()->subMonths(3)->startOfDay();

        // Statistik transaksi encounter per bulan (resep sudah terbayar)
        $transaksiPerBulan = \App\Models\Encounter::selectRaw('YEAR(updated_at) as tahun, MONTH(updated_at) as bulan, COUNT(*) as total')
            ->where('updated_at', '>=', $defaultStartDate)
            ->where('status_bayar_resep', 1)
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')->orderBy('bulan', 'asc')
            ->get()
            ->keyBy(fn($item) => $item->tahun . '-' . $item->bulan)
            ->map(fn($item) => $item->total)
            ->toArray();

        // Statistik nominal encounter per bulan (resep sudah terbayar)
        $nominalResep = \App\Models\Encounter::selectRaw('YEAR(updated_at) as tahun, MONTH(updated_at) as bulan, SUM(total_bayar_resep) as total')
            ->where('updated_at', '>=', $defaultStartDate)
            ->where('status_bayar_resep', 1)
            ->groupBy('tahun', 'bulan')
            ->get()
            ->keyBy(fn($item) => $item->tahun . '-' . $item->bulan)
            ->map(fn($item) => $item->total)
            ->toArray();

        // Statistik nominal dari InpatientBilling (Obat) per bulan
        $nominalInap = \App\Models\InpatientBilling::selectRaw('YEAR(paid_at) as tahun, MONTH(paid_at) as bulan, SUM(amount) as total')
            ->where('paid_at', '>=', $defaultStartDate)
            ->where('billing_type', 'Obat')
            ->groupBy('tahun', 'bulan')
            ->get()
            ->keyBy(fn($item) => $item->tahun . '-' . $item->bulan)
            ->map(fn($item) => $item->total)
            ->toArray();

        // Gabungkan kedua nominal
        $dataNominalBulan = [];
        $allKeys = array_unique(array_merge(array_keys($nominalResep), array_keys($nominalInap)));
        foreach ($allKeys as $key) {
            $dataNominalBulan[$key] = ($nominalResep[$key] ?? 0) + ($nominalInap[$key] ?? 0);
        }

        // Encounter resep terbayar (paginate 50, filter tanggal, warning jika > 1 tahun)
        list($query, $warning) = $this->getPaidPrescriptionQuery(request('start_date'), request('end_date'));

        // Manual pagination for the combined collection
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 50;
        $currentItems = $query->slice(($currentPage - 1) * $perPage, $perPage);
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $query->count(), $perPage, $currentPage, ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]);

        return [
            'total_obat' => $stokSummary->total ?? 0,
            'obat_tersedia' => $stokSummary->tersedia ?? 0,
            'obat_habis' => $stokSummary->habis ?? 0,
            'obat_kadaluarsa' => $obatKadaluarsa,
            'transaksi_per_bulan' => $transaksiPerBulan, // Kirim data mentah
            'nominal_transaksi_per_bulan' => $dataNominalBulan, // Kirim data mentah
            'encounter_terbayar' => $paginatedData,
            'warning' => $warning,
        ];
    }

    // export transaksi resep pdf
    public function exportPdf($start = null, $end = null)
    {
        list($data, $warning) = $this->getPaidPrescriptionQuery($start, $end);
        // $data is already a sorted collection, no need for further queries.
        $pdf = Pdf::loadView('pages.apotek.transaksi_resep_pdf', ['data' => $data]);
        $pdf->loadView('pages.apotek.transaksi_resep_pdf', ['data' => $data]);
        return $pdf->download('transaksi_resep.pdf');
    }

    public function getPaidPrescriptionQuery($start, $end)
    {
        $warning = null;
        $userRole = Auth::user()->role;
        $isOwner = ($userRole == 1);

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->endOfDay();
            if (!$isOwner && $startDate->diffInMonths($endDate) > 3) {
                $warning = 'Rentang tanggal maksimal 3 bulan!';
                $endDate = $startDate->copy()->addMonths(3);
            } elseif ($isOwner && $startDate->diffInDays($endDate) > 366) {
                $warning = 'Rentang tanggal maksimal 1 tahun!';
                $endDate = $startDate->copy()->addYear();
            }
        } else {
            // Default ke 1 tahun terakhir jika tidak ada rentang tanggal
            $startDate = now()->subYear()->startOfDay();
            $endDate = now()->endOfDay();
        }

        // 1. Ambil data transaksi resep rawat jalan (Encounter)
        $resepJalan = \App\Models\Encounter::where('status_bayar_resep', 1)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->with('resep')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'kode_transaksi' => $item->resep->kode_resep ?? 'N/A',
                    'name_pasien' => $item->name_pasien,
                    'tanggal_transaksi' => $item->updated_at,
                    'nominal' => $item->total_resep,
                    'diskon_rp' => $item->diskon_resep,
                    'diskon_persen_resep' => $item->diskon_persen_resep,
                    'total_bayar' => $item->total_bayar_resep,
                    'metode_pembayaran' => $item->metode_pembayaran_resep,
                    'tipe' => 'Resep Rawat Jalan',
                ];
            });

        // 2. Ambil data transaksi obat rawat inap (InpatientBilling)
        $resepInap = \App\Models\InpatientBilling::where('billing_type', 'Obat')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->with('admission.encounter')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'kode_transaksi' => 'BILL-' . substr($item->admission->id, 0, 8),
                    'name_pasien' => $item->admission->encounter->name_pasien ?? 'N/A',
                    'tanggal_transaksi' => $item->paid_at,
                    'nominal' => $item->amount,
                    'diskon_rp' => 0,
                    'diskon_persen_resep' => 0,
                    'total_bayar' => $item->amount,
                    'metode_pembayaran' => $item->payment_method,
                    'tipe' => 'Obat Rawat Inap',
                ];
            });

        // 3. Gabungkan kedua koleksi dan urutkan berdasarkan tanggal
        $combinedData = $resepJalan->merge($resepInap)->sortByDesc('tanggal_transaksi');

        return [$combinedData, $warning];
    }


    public function siapkanResep($resepId): array
    {
        $resep = \App\Models\Resep::with('details.productApotek')->findOrFail($resepId);

        // Ambil semua detail yang masih 'Diajukan'
        $detailsToPrepare = $resep->details()->where('status', 'Diajukan')->get();

        if ($detailsToPrepare->isEmpty()) {
            return ['success' => false, 'message' => 'Tidak ada item resep yang perlu disiapkan.'];
        }

        // Validasi stok untuk semua item sekaligus
        foreach ($detailsToPrepare as $detail) {
            if (!$detail->productApotek || $detail->productApotek->stok < $detail->qty) {
                return ['success' => false, 'message' => 'Stok untuk obat "' . ($detail->productApotek->name ?? $detail->nama_obat) . '" tidak mencukupi.'];
            }
        }

        // Lakukan semua operasi dalam satu transaksi database
        \Illuminate\Support\Facades\DB::transaction(function () use ($detailsToPrepare, $resep) {
            foreach ($detailsToPrepare as $detail) {
                $this->processStockReduction($detail, $resep);
                $detail->update(['status' => 'Disiapkan']);
            }

            // Cek apakah semua item sudah disiapkan
            // Reload relasi untuk mendapatkan data terbaru setelah update di loop
            $resep->load('details');

            // Update status resep utama menjadi 'Disiapkan' jika semua item sudah disiapkan
            $allPrepared = !$resep->details()->where('status', 'Diajukan')->exists();
            if ($allPrepared) {
                $resep->update(['status' => 'Disiapkan']);

                // Hitung total tagihan resep dan update ke encounter
                $totalTagihan = $resep->details()->sum('total_harga');
                $encounter = $resep->encounter;
                if ($encounter) {
                    $encounter->update([
                        'total_resep' => $totalTagihan,
                        'total_bayar_resep' => $totalTagihan
                    ]);
                }
            }
        });

        return ['success' => true, 'message' => 'Semua item resep berhasil disiapkan.'];
    }


    public function siapkanItemResep($resepDetailId): array
    {
        $detail = \App\Models\ResepDetail::with('productApotek', 'resep.details')->findOrFail($resepDetailId);

        if ($detail->status !== 'Diajukan') {
            return ['success' => false, 'message' => 'Item ini sudah diproses sebelumnya.'];
        }

        if (!$detail->productApotek || $detail->productApotek->stok < $detail->qty) {
            return ['success' => false, 'message' => 'Stok untuk obat "' . $detail->productApotek->name . '" tidak mencukupi.'];
        }

        DB::transaction(function () use ($detail) {
            $this->processStockReduction($detail, $detail->resep);
            $detail->update(['status' => 'Disiapkan']);
        });

        // Cek di luar transaksi untuk memastikan data konsisten
        $resep = $detail->resep;
        $resep->refresh(); // Muat ulang model resep dari database

        // Cek apakah masih ada item yang berstatus 'Diajukan'
        $allPrepared = !$resep->details()->where('status', 'Diajukan')->exists();

        // Update status resep utama jika semua item sudah disiapkan
        if ($allPrepared) {
            $resep->update(['status' => 'Disiapkan']);

            // Hitung total tagihan resep dan update ke encounter
            $totalTagihan = $resep->details()->sum('total_harga');
            $encounter = $resep->encounter;
            if ($encounter) {
                $encounter->update([
                    'total_resep' => $totalTagihan,
                    'total_bayar_resep' => $totalTagihan
                ]);
            }
        }

        return [
            'success' => true,
            'message' => 'Item resep berhasil disiapkan.',
            'all_prepared' => $allPrepared
        ];
    }

    private function processStockReduction(\App\Models\ResepDetail $detail, \App\Models\Resep $resep): void
    {
        $product = $detail->productApotek;
        if (!$product) {
            // Throw exception jika produk tidak terhubung, ini akan menghentikan transaksi
            throw new \Exception('Produk untuk item resep "' . $detail->nama_obat . '" tidak ditemukan.');
        }

        $quantity = $detail->qty;

        // 1. Kurangi stok utama di produk
        $product->decrement('stok', $quantity);

        // 2. Ambil ID stok yang akan diupdate (FIFO berdasarkan expired date)
        $stokIds = \App\Models\ApotekStok::where('product_apotek_id', $product->id)
            ->where('status', 0) // Hanya yang tersedia
            ->orderBy('expired_at', 'asc')
            ->limit($quantity)
            ->pluck('id')
            ->toArray();

        // 3. Update status stok menjadi 'Terpakai' (status = 1)
        if (!empty($stokIds)) {
            \App\Models\ApotekStok::whereIn('id', $stokIds)->update(['status' => 1]);
        }

        // 4. Catat histori stok keluar
        \App\Models\HistoriApotek::create([
            'product_apotek_id' => $product->id,
            'jumlah'            => -$quantity, // Stok keluar bernilai negatif
            'type'              => 1, // Tipe 1 untuk stok keluar
            'keterangan'        => 'Pengurangan stok dari resep ' . ($resep->kode_resep ?? 'N/A'),
        ]);
    }
}
