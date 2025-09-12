<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Pasien;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    /**
     * Menampilkan halaman utama kasir dengan daftar encounter yang perlu dibayar.
     */
    public function index(Request $request)
    {
        $typeList = [
            1 => 'Rawat Jalan',
            2 => 'Rawat Inap',
            3 => 'IGD'
        ];

        // Default ke 30 hari terakhir jika tidak ada filter tanggal
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        // 1. Dapatkan daftar rekam medis unik yang memiliki tagihan dan lakukan paginasi.
        $baseQuery = Encounter::query()
            ->where('status', 2) // Selesai Diperiksa
            ->where(function ($query) {
                // Ambil encounter yang memiliki tagihan tindakan ATAU tagihan resep yang belum lunas
                $query->where(fn($q) => $q->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 0))
                    ->orWhere(fn($q) => $q->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 0));
            })
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->when($request->search, function ($q, $search) {
                $q->where('name_pasien', 'like', "%{$search}%")
                    ->orWhere('rekam_medis', 'like', "%{$search}%");
            });

        // Clone query dasar untuk paginasi
        $rekamMedisPaginated = (clone $baseQuery)
            ->select('rekam_medis')
            ->distinct()
            ->orderBy('rekam_medis')
            ->paginate(15);

        // 2. Ambil semua encounter yang relevan untuk rekam medis di halaman ini.
        $rekamMedisList = $rekamMedisPaginated->pluck('rekam_medis');

        // Gunakan query dasar yang sudah difilter, lalu filter berdasarkan rekam medis di halaman saat ini
        $allEncounters = (clone $baseQuery)
            ->with('pasien')
            ->whereIn('rekam_medis', $rekamMedisList)
            ->get();

        // 3. Kelompokkan dan proses data untuk ditampilkan.
        $patientsWithBills = $allEncounters->groupBy('rekam_medis')->map(function ($encounters, $rekamMedis) use ($typeList) {
            $firstEncounter = $encounters->first();

            // Filter hanya tagihan yang belum lunas untuk perhitungan
            $unpaidTindakan = $encounters->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 0);
            $unpaidResep = $encounters->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 0);

            // Hitung total tagihan hanya dari yang belum lunas
            $totalTagihan = $unpaidTindakan->sum('total_bayar_tindakan') + $unpaidResep->sum('total_bayar_resep');

            // Kumpulkan jenis encounter yang unik
            $unpaidTypes = $encounters->pluck('type')->unique()->map(fn($type) => $typeList[$type] ?? '-')->implode(', ');

            return (object) [
                'pasien_id' => optional($firstEncounter->pasien)->id,
                'rekam_medis' => $rekamMedis,
                'name_pasien' => $firstEncounter->name_pasien,
                'last_visit' => $encounters->max('updated_at'),
                'total_tagihan' => $totalTagihan,
                'unpaid_tindakan' => $unpaidTindakan->count(),
                'unpaid_resep' => $unpaidResep->count(),
                'jenis_kunjungan' => $unpaidTypes,
            ];
        })->sortByDesc('last_visit');

        // 4. Buat instance Paginator baru dengan data yang sudah diproses.
        $paginatedPatients = new LengthAwarePaginator(
            $patientsWithBills->values(), // Gunakan ->values() untuk mereset key
            $rekamMedisPaginated->total(),
            $rekamMedisPaginated->perPage(),
            $rekamMedisPaginated->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.kasir.index', ['patients' => $paginatedPatients, 'startDate' => $startDate, 'endDate' => $endDate]);
    }

    public function show($pasien_id)
    {
        $pasien = Pasien::findOrFail($pasien_id);

        // Ambil semua encounter yang belum lunas untuk pasien ini
        $unpaidEncounters = Encounter::with(['tindakan', 'resep.details'])
            ->where('rekam_medis', $pasien->rekam_medis)
            ->where('status', 2) // Selesai diperiksa
            ->where(function ($query) {
                // Ambil encounter yang memiliki tagihan tindakan ATAU tagihan resep yang belum lunas
                $query->where(fn($q) => $q->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 0))
                    ->orWhere(fn($q) => $q->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 0));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.kasir.show', compact('pasien', 'unpaidEncounters'));
    }

    public function processPayment(Request $request, $pasien_id)
    {
        $request->validate([
            'payment_method' => 'required|string|in:Tunai,Debit,QRIS,Transfer Bank,Asuransi',
            'items_to_pay'   => 'required|array|min:1',
            // Validasi format UUID yang lebih ketat
            'items_to_pay.*' => 'string',
        ], [
            'items_to_pay.required' => 'Pilih setidaknya satu item untuk dibayar.',
            'items_to_pay.*.regex'  => 'Format item pembayaran tidak valid.',
            'payment_method.in'     => 'Metode pembayaran tidak valid.',
        ]);

        $itemsToPay    = $request->input('items_to_pay');
        $paymentMethod = $request->input('payment_method');
        $totalPaidAmount = 0;
        $paidItemsInfo = []; // Untuk menyimpan info item yang dibayar untuk struk

        // Ekstrak semua ID encounter dari input untuk di-query sekaligus
        $encounterIds = collect($itemsToPay)->map(function ($item) {
            // Ambil bagian UUID dari string 'tipe-uuid'
            $parts = explode('-', $item, 2);
            return $parts[1] ?? null;
        })->filter()->unique()->all();

        // Ambil semua encounter yang relevan dalam satu query
        $encounters = Encounter::whereIn('id', $encounterIds)->get()->keyBy('id');

        DB::transaction(function () use ($itemsToPay, $paymentMethod, $encounters, &$totalPaidAmount, &$paidItemsInfo) {
            foreach ($itemsToPay as $item) {
                $parts = explode('-', $item, 2);
                if (count($parts) !== 2) {
                    continue; // Lewati item dengan format tidak valid
                }
                list($type, $encounterId) = $parts;

                // Gunakan encounter yang sudah di-load
                $encounter = $encounters->get(strtolower($encounterId));

                if (!$encounter) {
                    continue; // Lewati jika encounter tidak ditemukan
                }

                if ($type === 'tindakan' && !$encounter->status_bayar_tindakan) {
                    $encounter->status_bayar_tindakan      = 1;
                    $encounter->metode_pembayaran_tindakan = $paymentMethod;
                    $totalPaidAmount += $encounter->total_bayar_tindakan;
                    $paidItemsInfo[$encounterId]['tindakan'] = $encounter->total_bayar_tindakan;
                } elseif ($type === 'resep' && !$encounter->status_bayar_resep) {
                    $encounter->status_bayar_resep      = 1;
                    $encounter->metode_pembayaran_resep = $paymentMethod;
                    $totalPaidAmount += $encounter->total_bayar_resep;
                    $paidItemsInfo[$encounterId]['resep'] = $encounter->total_bayar_resep;
                }

                $encounter->save();
            }
        });

        // Simpan ID encounter yang dibayar ke session untuk dicetak
        session(['paid_items_info' => $paidItemsInfo]);

        $successMessage = 'Pembayaran sebesar ' . 'Rp ' . number_format($totalPaidAmount, 0, ',', '.') . ' berhasil diproses.';

        return redirect()->route('kasir.index')->with('success', $successMessage)->with('show_print_button', true);
    }
}
