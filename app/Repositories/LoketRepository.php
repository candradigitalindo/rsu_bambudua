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
        $lokets->map(function ($loket) {
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
        $userRole = auth()->user()->role;
        $isOwner = ($userRole == 1);

        // Tentukan rentang waktu default berdasarkan role
        $defaultStartDate = $isOwner ? now()->subYear() : now()->subMonths(3);

        // Statistik transaksi encounter per bulan (tindakan sudah terbayar) dalam 1 tahun
        $year = date('Y');
        $transaksiPerBulan = \App\Models\Encounter::selectRaw('MONTH(updated_at) as bulan, COUNT(*) as total')
            ->where('updated_at', '>=', $defaultStartDate)
            ->where('status_bayar_tindakan', 1)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Siapkan label dan data untuk 12 bulan jika Owner, atau 3 bulan jika bukan
        $monthRange = $isOwner ? 12 : 3;
        $startLoop = $isOwner ? 1 : now()->subMonths($monthRange - 1)->month;

        $dataBulan = [];
        for ($i = 0; $i < $monthRange; $i++) {
            $currentMonth = now()->subMonths($i)->month;
            $dataBulan[$currentMonth] = $transaksiPerBulan[$currentMonth] ?? 0;
        }

        // Urutkan array berdasarkan key (nomor bulan) untuk konsistensi di grafik
        ksort($dataBulan);

        // Statistik nominal encounter per bulan (tindakan sudah terbayar) dalam 1 tahun
        $nominalPerBulan = \App\Models\Encounter::selectRaw('MONTH(updated_at) as bulan, SUM(total_bayar_tindakan) as total')
            ->where('updated_at', '>=', $defaultStartDate)
            ->where('status_bayar_tindakan', 1)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $dataNominalBulan = [];
        foreach ($dataBulan as $month => $value) {
            $dataNominalBulan[$month] = $nominalPerBulan[$month] ?? 0;
        }

        // Urutkan juga data nominal
        ksort($dataNominalBulan);

        // Encounter tindakan terbayar (paginate 50, filter tanggal, warning jika > 1 tahun)
        $query = \App\Models\Encounter::where('status_bayar_tindakan', 1);
        $start = request('start_date');
        $end = request('end_date');
        $warning = null;

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

            $query->whereBetween('updated_at', [$startDate, $endDate]);
        } else {
            $query->where('updated_at', '>=', $defaultStartDate);
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
        $typeList = [
            1 => 'Rawat Jalan',
            2 => 'Rawat Inap',
            3 => 'IGD'
        ];

        $encounters = \App\Models\Encounter::with(['tindakan', 'practitioner.user'])
            ->where('status', $status)
            ->whereYear('created_at', date('Y'))
            ->when(request('search'), function ($query, $search) {
                $query->where('name_pasien', 'like', '%' . $search . '%');
            })
            ->orderBy('status_bayar_tindakan', 'asc')
            ->orderByDesc('updated_at')
            ->paginate(50);

        // Tambahkan label type pada setiap encounter
        $encounters->getCollection()->transform(function ($item) use ($typeList) {
            $item->type_label = $typeList[$item->type] ?? '-';
            return $item;
        });

        return $encounters;
    }
    // cetak struk tindakan
    public function cetakEncounter($id)
    {
        $encounter = \App\Models\Encounter::with(['tindakan', 'practitioner.user'])
            ->findOrFail($id);
        return $encounter;
    }
    public function getReminderEncounter($condition = 2)
    {
        $today = now()->toDateString();

        // Ambil encounter yang masa resepnya akan habis dalam 7 hari
        $encounters = \App\Models\Encounter::with('resep')
            ->where('condition', $condition)
            ->whereHas('resep', function ($q) use ($today) {
                $q->whereRaw("DATE_SUB(DATE_ADD(DATE(created_at), INTERVAL masa_pemakaian_hari DAY), INTERVAL 2 DAY) = ?", [$today]);
            })
            ->orderByDesc('created_at')
            ->get();

        // Ambil semua no_hp pasien sekaligus (hindari query di dalam loop)
        $rekamMedisList = $encounters->pluck('rekam_medis')->unique()->toArray();
        $pasienHp = \App\Models\Pasien::whereIn('rekam_medis', $rekamMedisList)
            ->pluck('no_hp', 'rekam_medis');

        // Tambahkan no_hp ke setiap encounter
        foreach ($encounters as $encounter) {
            $encounter->no_hp = $pasienHp[$encounter->rekam_medis] ?? null;
        }

        // Encounter terbaru per rekam_medis
        $uniqueEncounters = $encounters->unique('rekam_medis')->values();

        return $uniqueEncounters;
    }
}
