<?php

namespace App\Repositories;

use App\Models\LokasiLoket;
use App\Models\Loket;
use App\Models\User;

class LoketRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $lokets     = Loket::orderBy('created_at', 'DESC')->get();
        $lokasis    = LokasiLoket::latest()->get();
        $users      = User::where('role', 5)->latest()->get();
        $lokets->map( function ($loket) {
            $loket['lokasi'] = $loket->lokasiloket->lokasi_loket;
            $user   = User::where('id', $loket->user_id)->first();
            $loket['user']   = $user == null ? null : $user->name;
        });
        return ['lokets' => $lokets, 'lokasis' => $lokasis, 'users' => $users];
    }

    public function store($request)
    {
        $loket = Loket::create(['lokasiloket_id' => $request->lokasi, 'kode_loket' => strtoupper($request->kode_loket), 'user_id' => $request->user]);
        return $loket;
    }

    public function destroy($id)
    {
        $loket = Loket::findOrFail($id);
        $loket->delete();
        return $loket;
    }
    public function dashboard()
    {
        // Statistik transaksi encounter per bulan (tindakan sudah terbayar) dalam 1 tahun
        $year = date('Y');
        $transaksiPerBulan = \App\Models\Encounter::selectRaw('MONTH(updated_at) as bulan, COUNT(*) as total')
            ->whereYear('updated_at', $year)
            ->where('status_bayar_tindakan', 1)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $dataBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulan[$i] = $transaksiPerBulan[$i] ?? 0;
        }

        // Statistik nominal encounter per bulan (tindakan sudah terbayar) dalam 1 tahun
        $nominalPerBulan = \App\Models\Encounter::selectRaw('MONTH(updated_at) as bulan, SUM(total_bayar_tindakan) as total')
            ->whereYear('updated_at', $year)
            ->where('status_bayar_tindakan', 1)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $dataNominalBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataNominalBulan[$i] = $nominalPerBulan[$i] ?? 0;
        }

        // Encounter tindakan terbayar (paginate 50, filter tanggal, warning jika > 1 tahun)
        $query = \App\Models\Encounter::where('status_bayar_tindakan', 1);
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
            'transaksi_per_bulan' => $dataBulan,
            'nominal_transaksi_per_bulan' => $dataNominalBulan,
            'encounter_terbayar' => $encounterTerbayar,
            'warning' => $warning,
        ];
    }
    public function getEncounter($status = 2)
    {
        return \App\Models\Encounter::with(['tindakan', 'practitioner.user'])
            ->where('status', $status)
            ->whereYear('created_at', date('Y'))
            ->when(request('search'), function ($query, $search) {
                $query->where('name_pasien', 'like', '%' . $search . '%');
            })
            ->orderBy('status_bayar_tindakan', 'asc')      // status_bayar 0 di atas
            ->orderByDesc('updated_at')           // lalu urut terbaru
            ->paginate(50);
    }
    // Bayar tindakan
    public function bayarTindakan($request, $id)
    {
        $encounter = \App\Models\Encounter::findOrFail($id);
        $encounter->status_bayar_tindakan = 1;
        $encounter->metode_pembayaran_tindakan = $request->metode_pembayaran;
        $encounter->save();
        return $encounter;
    }
    // cetak struk tindakan
    public function cetakEncounter($id)
    {
        $encounter = \App\Models\Encounter::with(['tindakan', 'practitioner.user'])
            ->findOrFail($id);
        return $encounter;
    }

}
