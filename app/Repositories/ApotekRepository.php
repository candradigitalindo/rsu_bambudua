<?php

namespace App\Repositories;

use Barryvdh\DomPDF\Facade\Pdf;

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

        // Obat Kadaluarsa (status 0 = stok aktif)
        $obatKadaluarsa = \App\Models\ApotekStok::where('expired_at', '<=', now())
            ->where('status', 0)
            ->count();

        // Statistik transaksi encounter per bulan (resep sudah terbayar) dalam 1 tahun
        $year = date('Y');
        $transaksiPerBulan = \App\Models\Encounter::selectRaw('MONTH(updated_at) as bulan, COUNT(*) as total')
            ->whereYear('updated_at', $year)
            ->where('status_bayar_resep', 1)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $dataBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulan[$i] = $transaksiPerBulan[$i] ?? 0;
        }

        // Statistik nominal encounter per bulan (resep sudah terbayar) dalam 1 tahun
        $nominalPerBulan = \App\Models\Encounter::selectRaw('MONTH(updated_at) as bulan, SUM(total_bayar_resep) as total')
            ->whereYear('updated_at', $year)
            ->where('status_bayar_resep', 1)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $dataNominalBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataNominalBulan[$i] = $nominalPerBulan[$i] ?? 0;
        }

        // Encounter resep terbayar (paginate 50, filter tanggal, warning jika > 1 tahun)
        $query = \App\Models\Encounter::where('status_bayar_resep', 1);
        $start = request('start_date');
        $end = request('end_date');
        $warning = null;

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay();
            $endDate = \Carbon\Carbon::parse($end)->endOfDay();

            if ($startDate->diffInDays($endDate) > 366) {
                $warning = 'Rentang tanggal maksimal 1 tahun!';
                $endDate = $startDate->copy()->addYear();
            }

            $query->whereBetween('updated_at', [$startDate, $endDate]);
        } else {
            $query->where('updated_at', '>=', now()->subYear());
        }

        $encounterTerbayar = $query->orderByDesc('updated_at')->paginate(50);

        return [
            'total_obat' => $stokSummary->total ?? 0,
            'obat_tersedia' => $stokSummary->tersedia ?? 0,
            'obat_habis' => $stokSummary->habis ?? 0,
            'obat_kadaluarsa' => $obatKadaluarsa,
            'transaksi_per_bulan' => $dataBulan,
            'nominal_transaksi_per_bulan' => $dataNominalBulan,
            'encounter_terbayar' => $encounterTerbayar,
            'warning' => $warning,
        ];
    }

    // ambil encounter yang status 2 beserta resep dan resep detailnya
    public function getEncounter($status = 2)
    {
        $typeList = [
            1 => 'Rawat Jalan',
            2 => 'Rawat Inap',
            3 => 'IGD'
        ];

        $encounters = \App\Models\Encounter::with(['resep.details', 'practitioner.user'])
            ->where('status', $status)
            ->whereYear('created_at', date('Y'))
            ->when(request('search'), function ($query, $search) {
                $query->where('name_pasien', 'like', '%' . $search . '%');
            })
            ->orderBy('status_bayar_resep', 'asc')
            ->orderByDesc('updated_at')
            ->paginate(50);

        // Tambahkan label type pada setiap encounter
        $encounters->getCollection()->transform(function ($item) use ($typeList) {
            $item->type_label = $typeList[$item->type] ?? '-';
            return $item;
        });

        return $encounters;
    }
    // bayar resep
    public function bayarResep($request, $id)
    {
        $encounter = \App\Models\Encounter::findOrFail($id);
        $encounter->status_bayar_resep = 1;
        $encounter->metode_pembayaran_resep = $request->metode_pembayaran;
        $encounter->save();

        $details = $encounter->resep && $encounter->resep->details ? $encounter->resep->details : [];
        foreach ($details as $detail) {
            // Update stok ProductApotek
            \App\Models\ProductApotek::where('id', $detail->product_apotek_id)
                ->decrement('stok', $detail->qty);

            // Ambil stok yang akan diupdate statusnya (expired_at dulu)
            $stokIds = \App\Models\ApotekStok::where('product_apotek_id', $detail->product_apotek_id)
                ->where('expired_at', $detail->expired_at)
                ->where('status', 0)
                ->orderBy('id')
                ->limit($detail->qty)
                ->pluck('id')
                ->toArray();

            // Jika stokIds kosong, ambil stok terlama tanpa filter expired_at
            if (empty($stokIds)) {
                $stokIds = \App\Models\ApotekStok::where('product_apotek_id', $detail->product_apotek_id)
                    ->where('status', 0)
                    ->orderBy('expired_at', 'asc')
                    ->orderBy('id')
                    ->limit($detail->qty)
                    ->pluck('id')
                    ->toArray();
            }

            // Update status stok sekaligus (lebih cepat)
            if (!empty($stokIds)) {
                \App\Models\ApotekStok::whereIn('id', $stokIds)->update(['status' => 1]);
            }

            // Catat histori stok keluar
            \App\Models\HistoriApotek::create([
                'product_apotek_id' => $detail->product_apotek_id,
                'jumlah' => -$detail->qty,
                'expired_at' => $detail->expired_at,
                'type' => 1,
                'keterangan' => 'Pengurangan stok karena resep ' . ($encounter->resep->kode_resep ?? ''),
            ]);
        }

        return $encounter;
    }
    // cetak resep dari encounter
    public function cetakResep($id)
    {
        $encounter = \App\Models\Encounter::with(['resep.details', 'practitioner.user'])
            ->findOrFail($id);
        return $encounter;
    }
    // export transaksi resep pdf
    public function exportPdf($start = null, $end = null)
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

        $pdf = Pdf::loadView('pages.apotek.transaksi_resep_pdf', ['data' => $data]);
        return $pdf->download('transaksi_resep.pdf');
    }
}
