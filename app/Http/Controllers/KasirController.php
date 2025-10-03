<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsActivity;
use App\Models\Encounter;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Pasien;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    use LogsActivity;

    /**
     * Menampilkan halaman utama kasir dengan daftar encounter yang perlu dibayar.
     */
    public function index(Request $request)
    {
        $typeList = [
            1 => 'Rawat Jalan',
            2 => 'Rawat Inap',
            3 => 'IGD',
        ];

        // Default ke 30 hari terakhir jika tidak ada filter tanggal
        $startDate = $request->input('start_date', now()->subMonth()->startOfDay()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        // 1. Dapatkan daftar rekam medis unik yang memiliki tagihan dan lakukan paginasi.
        $baseQuery = Encounter::query()
            ->where('status', 2) // Selesai Diperiksa
            ->where(function ($query) {
                // Ambil encounter yang memiliki tagihan tindakan ATAU tagihan resep yang BELUM LUNAS
                $query->where(fn($q) => $q->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 0))
                    ->orWhere(fn($q) => $q->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 0));
            })
            // Batasi rentang waktu menjadi 1 bulan terakhir
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

        // Ambil semua encounter yang relevan untuk rekam medis di halaman ini, gunakan query dasar yang sudah difilter
        $allEncounters = (clone $baseQuery)
            ->whereIn('rekam_medis', $rekamMedisList)
            ->with('pasien')
            ->get();

        // 3. Kelompokkan dan proses data untuk ditampilkan.
        $patientsWithBills = $allEncounters->groupBy('rekam_medis')->map(function ($encounters, $rekamMedis) use ($typeList) {
            $firstEncounter = $encounters->first();

            // Filter tagihan yang belum lunas
            $unpaidTindakan = $encounters->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 0);
            $unpaidResep    = $encounters->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 0);

            // Hitung total tagihan hanya dari yang belum lunas
            $totalTagihan = $unpaidTindakan->sum('total_bayar_tindakan') + $unpaidResep->sum('total_bayar_resep');

            // Cek apakah ada tagihan yang belum lunas
            $hasUnpaidBills = $totalTagihan > 0;

            // Kumpulkan jenis encounter yang unik
            $relevantEncounters = $hasUnpaidBills ? $unpaidTindakan->merge($unpaidResep) : $encounters;
            $encounterTypes = $relevantEncounters->pluck('type')->unique()->map(fn($type) => $typeList[$type] ?? '-')->implode(', ');

            return (object) [
                'pasien_id'       => optional($firstEncounter->pasien)->id,
                'rekam_medis'     => $rekamMedis,
                'name_pasien'     => $firstEncounter->name_pasien,
                'last_visit'      => $encounters->max('updated_at'),
                'total_tagihan'   => $totalTagihan, // Ini adalah total tagihan yang BELUM LUNAS
                'has_unpaid_bills' => $hasUnpaidBills,
                'unpaid_tindakan' => $unpaidTindakan->count(),
                'unpaid_resep'    => $unpaidResep->count(),
                'jenis_kunjungan' => $encounterTypes,
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
        $unpaidEncounters = Encounter::with([
            'tindakan',
            'resep.details',
            'labRequests.items',
            'radiologyRequests'
        ])
            ->where('rekam_medis', $pasien->rekam_medis)
            ->where('status', 2) // Selesai diperiksa
            ->where(function ($query) {
                // Ambil encounter yang memiliki tagihan tindakan ATAU resep yang belum lunas
                $query->where(fn($q) => $q->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 0))
                    ->orWhere(fn($q) => $q->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 0));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = \App\Models\PaymentMethod::where('active', true)->orderBy('name')->get();
        return view('pages.kasir.show', compact('pasien', 'unpaidEncounters', 'paymentMethods'));
    }

    public function processPayment(Request $request, $pasien_id)
    {
        $request->validate([
            'payment_method' => 'required|string|exists:payment_methods,code',
            'items_to_pay'   => 'required|array|min:1',
            'items_to_pay.*' => 'string',
        ], [
            'items_to_pay.required' => 'Pilih setidaknya satu item untuk dibayar.',
            'items_to_pay.*.regex'  => 'Format item pembayaran tidak valid.',
            'payment_method.exists' => 'Metode pembayaran tidak valid.',
        ]);

        $itemsToPay       = $request->input('items_to_pay');
        $paymentMethod    = $request->input('payment_method');
        $totalPaidAmount  = 0;
        $paidItemsInfo    = [];

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
                [$type, $encounterId] = $parts;

                // Gunakan encounter yang sudah di-load
                $encounter = $encounters->get($encounterId);

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

        // Simpan juga ID pasien untuk cetak struk dari halaman index
        $pasien = \App\Models\Pasien::find($pasien_id);
        session(['last_paid_patient_id' => $pasien_id]);
        session(['last_paid_patient_name' => $pasien->name]);

        $successMessage = 'Pembayaran sebesar ' . 'Rp ' . number_format($totalPaidAmount, 0, ',', '.') . ' berhasil diproses.';

        $pasien = \App\Models\Pasien::find($pasien_id);
        $this->activity(
            'Memproses Pembayaran — ' . ($pasien->name ?? '-') . ' (RM ' . ($pasien->rekam_medis ?? '-') . ')',
            [
                'pasien_id'   => $pasien_id,
                'rekam_medis' => $pasien->rekam_medis ?? null,
                'metode'      => $paymentMethod,
                'total'       => $totalPaidAmount,
                'items'       => $paidItemsInfo,
            ],
            'kasir'
        );

        return redirect()->route('kasir.index')->with('success', $successMessage)->with('show_print_button', true);
    }

    /**
     * Cetak struk transaksi terakhir berdasarkan session 'paid_items_info'
     */
    public function cetakStrukTerakhir(Request $request)
    {
        $paid = session('paid_items_info', []);
        $pasienId = $request->query('pasien_id', session('last_paid_patient_id'));

        if (empty($paid) && !$pasienId) {
            return redirect()->route('kasir.index')->with('error', 'Tidak ada data transaksi untuk dicetak.');
        }

        $pasien = Pasien::find($pasienId);
        if (!$pasien) {
            return redirect()->route('kasir.index')->with('error', 'Pasien tidak ditemukan.');
        }

        // Jika mencetak dari halaman index, ambil semua encounter lunas terakhir pasien
        if (empty($paid)) {
            $encounters = Encounter::where('rekam_medis', $pasien->rekam_medis)->where('status', 2)->where(fn($q) => $q->where('status_bayar_tindakan', 1)->orWhere('status_bayar_resep', 1))->orderByDesc('updated_at')->get();
        } else {
            $encounterIds = array_keys($paid);
            $encounters = Encounter::whereIn('id', $encounterIds)->get();
        }

        $total = 0;
        foreach ($paid as $eid => $items) {
            foreach ($items as $type => $amount) {
                $total += (float)$amount;
            }
        }
        return view('pages.kasir.struk', compact('encounters', 'paid', 'pasien', 'total'));
    }
}
