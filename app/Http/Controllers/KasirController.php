<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsActivity;
use App\Models\Encounter;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Pasien;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethod;
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
            'radiologyRequests.jenis'
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

        // Ambil juga encounter yang sudah dibayar (riwayat) untuk ditampilkan
        $paidEncounters = Encounter::with([
            'tindakan',
            'resep.details',
            'labRequests.items',
            'radiologyRequests.jenis'
        ])
            ->where('rekam_medis', $pasien->rekam_medis)
            ->where('status', 2)
            ->where(function ($query) {
                $query->where(fn($q) => $q->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 1))
                    ->orWhere(fn($q) => $q->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 1));
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        $paymentMethods = PaymentMethod::where('active', true)->orderBy('name')->get();
        return view('pages.kasir.show', compact('pasien', 'unpaidEncounters', 'paymentMethods', 'paidEncounters'));
    }

    public function processPayment(Request $request, $pasien_id)
    {
        \Illuminate\Support\Facades\Log::info('=== START PROCESS PAYMENT ===', [
            'pasien_id' => $pasien_id,
            'items_to_pay' => $request->input('items_to_pay'),
            'payment_methods' => $request->input('payment_methods'),
        ]);

        $request->validate([
            'items_to_pay'   => 'required|array|min:1',
            'items_to_pay.*' => 'string',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*.method' => 'required|string|exists:payment_methods,code',
            'payment_methods.*.amount_raw' => 'required|numeric|min:0',
            'encounter_ids' => 'required|array|min:1', // Tambahkan validasi untuk encounter_ids
            'encounter_ids.*' => 'required|string',
        ], [
            'items_to_pay.required' => 'Pilih setidaknya satu item untuk dibayar.',
            'payment_methods.required' => 'Pilih minimal satu metode pembayaran.',
            'payment_methods.*.method.exists' => 'Metode pembayaran tidak valid.',
            'payment_methods.*.amount_raw.required' => 'Jumlah pembayaran harus diisi.',
        ]);

        \Illuminate\Support\Facades\Log::info('Validation passed');

        $itemsToPay       = $request->input('items_to_pay');
        $paymentMethods   = $request->input('payment_methods');
        $encounterIds     = $request->input('encounter_ids', []); // Ambil dari request langsung
        $totalPaidAmount  = 0;
        $paidItemsInfo    = [];

        // Hitung total pembayaran dari semua metode
        $totalPaymentReceived = collect($paymentMethods)->sum('amount_raw');

        \Illuminate\Support\Facades\Log::info('Total payment received', ['total' => $totalPaymentReceived]);

        // Ekstrak encounter IDs yang unique
        $encounterIds = collect($encounterIds)->unique()->values()->all();

        // Ambil semua encounter yang relevan dalam satu query
        $encounters = Encounter::whereIn('id', $encounterIds)->get()->keyBy('id');

        \Illuminate\Support\Facades\Log::info('Encounters loaded', [
            'encounterIds' => $encounterIds,
            'encounters_count' => $encounters->count(),
            'encounters_keys' => $encounters->keys()->toArray()
        ]);

        // Hitung total tagihan dari encounter yang dipilih
        // Group items by type untuk menentukan apakah ada tindakan atau resep
        $totalBill = 0;
        $itemsByType = collect($itemsToPay)->map(function ($item) {
            // Ambil type saja (bagian pertama sebelum dash pertama)
            $firstDash = strpos($item, '-');
            return $firstDash !== false ? substr($item, 0, $firstDash) : $item;
        });

        foreach ($encounters as $encounterId => $encounter) {
            $hasTindakan = $itemsByType->contains(function ($type) {
                return in_array($type, ['tindakan', 'lab', 'radiologi']);
            });

            $hasResep = $itemsByType->contains('resep');

            \Illuminate\Support\Facades\Log::info('Encounter check', [
                'encounter_id' => $encounterId,
                'hasTindakan' => $hasTindakan,
                'hasResep' => $hasResep,
                'status_bayar_tindakan' => $encounter->status_bayar_tindakan,
                'status_bayar_resep' => $encounter->status_bayar_resep,
                'total_bayar_tindakan' => $encounter->total_bayar_tindakan,
                'total_bayar_resep' => $encounter->total_bayar_resep,
            ]);

            if ($hasTindakan && !$encounter->status_bayar_tindakan) {
                $totalBill += $encounter->total_bayar_tindakan;
            }
            if ($hasResep && !$encounter->status_bayar_resep) {
                $totalBill += $encounter->total_bayar_resep;
            }
        }

        \Illuminate\Support\Facades\Log::info('Total bill calculated', [
            'totalBill' => $totalBill,
            'totalPaymentReceived' => $totalPaymentReceived
        ]);

        // Validasi: Pembayaran harus >= total tagihan
        if ($totalPaymentReceived < $totalBill) {
            \Illuminate\Support\Facades\Log::warning('Payment insufficient', [
                'totalBill' => $totalBill,
                'totalPaymentReceived' => $totalPaymentReceived
            ]);
            return redirect()->back()->with('error', 'Total pembayaran (Rp ' . number_format($totalPaymentReceived, 0, ',', '.') . ') kurang dari total tagihan (Rp ' . number_format($totalBill, 0, ',', '.') . ')')->withInput();
        }

        // Gabungkan metode pembayaran untuk disimpan
        $paymentMethodsCombined = collect($paymentMethods)
            ->filter(fn($pm) => isset($pm['method']) && $pm['amount_raw'] > 0)
            ->map(fn($pm) => $pm['method'] . ':' . number_format($pm['amount_raw'], 0, ',', '.'))
            ->implode('; ');

        \Illuminate\Support\Facades\Log::info('Starting DB transaction');

        DB::transaction(function () use ($itemsToPay, $paymentMethodsCombined, $encounterIds, &$totalPaidAmount, &$paidItemsInfo) {
            \Illuminate\Support\Facades\Log::info('Inside DB transaction');

            // Get types from items
            $itemsByType = collect($itemsToPay)->map(function ($item) {
                $firstDash = strpos($item, '-');
                return $firstDash !== false ? substr($item, 0, $firstDash) : $item;
            });

            $hasTindakan = $itemsByType->contains(function ($type) {
                return in_array($type, ['tindakan', 'lab', 'radiologi']);
            });

            $hasResep = $itemsByType->contains('resep');

            foreach ($encounterIds as $encounterId) {
                // Re-fetch encounter dalam transaction untuk memastikan data fresh
                $encounter = Encounter::find($encounterId);
                if (!$encounter) continue;

                // Process tindakan payment (includes lab & radiologi)
                if ($hasTindakan && !$encounter->status_bayar_tindakan) {
                    $encounter->status_bayar_tindakan      = 1;
                    $encounter->metode_pembayaran_tindakan = $paymentMethodsCombined;
                    $totalPaidAmount += $encounter->total_bayar_tindakan;
                    $paidItemsInfo[$encounterId]['tindakan'] = $encounter->total_bayar_tindakan;

                    \Illuminate\Support\Facades\Log::info('Updating tindakan payment', [
                        'encounter_id' => $encounterId,
                        'status_bayar_tindakan' => 1,
                        'total' => $encounter->total_bayar_tindakan
                    ]);

                    // [NEW] Buat insentif lab & radiologi saat pembayaran lunas
                    $this->createLabRadiologiIncentives($encounter);
                }

                // Process resep payment
                if ($hasResep && !$encounter->status_bayar_resep) {
                    $encounter->status_bayar_resep      = 1;
                    $encounter->metode_pembayaran_resep = $paymentMethodsCombined;
                    $totalPaidAmount += $encounter->total_bayar_resep;
                    $paidItemsInfo[$encounterId]['resep'] = $encounter->total_bayar_resep;

                    \Illuminate\Support\Facades\Log::info('Updating resep payment', [
                        'encounter_id' => $encounterId,
                        'status_bayar_resep' => 1,
                        'total' => $encounter->total_bayar_resep
                    ]);

                    // Buat insentif farmasi (obat) saat resep dibayar
                    try {
                        $mode = (int)(\App\Models\IncentiveSetting::where('setting_key', 'fee_obat_mode')->value('setting_value') ?? 1);
                        $val  = (float)(\App\Models\IncentiveSetting::where('setting_key', 'fee_obat_value')->value('setting_value') ?? 0);
                        $target = (int)(\App\Models\IncentiveSetting::where('setting_key', 'fee_obat_target_mode')->value('setting_value') ?? 0);
                        $base = (float)($encounter->total_bayar_resep ?? 0);
                        if ($base > 0 && $val > 0) {
                            $amount = $mode === 1 ? ($base * ($val / 100.0)) : $val;
                            $userId = null;
                            if ($target === 1) {
                                // Prescriber tidak terekam dengan id user pada model Resep, fallback ke DPJP
                                $userId = optional(optional($encounter->practitioner()->with('user')->first())->user)->id;
                            } else {
                                $userId = optional(optional($encounter->practitioner()->with('user')->first())->user)->id;
                            }
                            if ($userId) {
                                \App\Models\Incentive::create([
                                    'id' => \Illuminate\Support\Str::uuid(),
                                    'user_id' => $userId,
                                    'amount' => $amount,
                                    'type' => 'fee_obat_rj',
                                    'description' => 'Fee Obat (RJ/IGD) pasien ' . $encounter->name_pasien,
                                    'year' => now()->year,
                                    'month' => now()->month,
                                    'status' => 'pending',
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning('Gagal membuat insentif obat RJ: ' . $e->getMessage());
                    }
                }

                $encounter->save();

                \Illuminate\Support\Facades\Log::info('Encounter saved', [
                    'encounter_id' => $encounterId,
                    'status_bayar_tindakan' => $encounter->status_bayar_tindakan,
                    'status_bayar_resep' => $encounter->status_bayar_resep,
                ]);
            }
        });

        // Simpan ID encounter yang dibayar ke session untuk dicetak
        session(['paid_items_info' => $paidItemsInfo]);

        // Simpan juga ID pasien untuk cetak struk dari halaman index
        $pasien = \App\Models\Pasien::find($pasien_id);
        session(['last_paid_patient_id' => $pasien_id]);
        session(['last_paid_patient_name' => $pasien->name]);

        $this->activity(
            'Memproses Pembayaran — ' . ($pasien->name ?? '-') . ' (RM ' . ($pasien->rekam_medis ?? '-') . ')',
            [
                'pasien_id'   => $pasien_id,
                'rekam_medis' => $pasien->rekam_medis ?? null,
                'metode'      => $paymentMethodsCombined,
                'total_tagihan' => $totalBill,
                'total_bayar' => $totalPaymentReceived,
                'kembalian'   => $totalPaymentReceived - $totalBill,
                'items'       => $paidItemsInfo,
            ],
            'kasir'
        );

        $successMessage = 'Pembayaran berhasil diproses. Total tagihan: Rp ' . number_format($totalBill, 0, ',', '.') . ', Total dibayar: Rp ' . number_format($totalPaymentReceived, 0, ',', '.');
        if ($totalPaymentReceived > $totalBill) {
            $successMessage .= ' Kembalian: Rp ' . number_format($totalPaymentReceived - $totalBill, 0, ',', '.');
        }

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
            $encounters = Encounter::where('rekam_medis', $pasien->rekam_medis)
                ->where('status', 2)
                ->where(fn($q) => $q->where('status_bayar_tindakan', 1)->orWhere('status_bayar_resep', 1))
                ->orderByDesc('updated_at')
                ->get();
        } else {
            $encounterIds = array_keys($paid);
            $encounters = Encounter::whereIn('id', $encounterIds)
                ->with(['tindakan', 'labRequests.items', 'radiologyRequests.jenis', 'resep.details'])
                ->get();
        }

        // Hitung total dan ambil semua encounter yang belum lunas
        $total = 0;
        foreach ($paid as $eid => $items) {
            foreach ($items as $type => $amount) {
                $total += (float)$amount;
            }
        }

        // Ambil tagihan yang belum terbayar untuk pasien ini
        $unpaidEncounters = Encounter::where('rekam_medis', $pasien->rekam_medis)
            ->where('status', 2)
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 0);
                })->orWhere(function ($sq) {
                    $sq->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 0);
                });
            })
            ->with(['tindakan', 'labRequests.items', 'radiologyRequests.jenis', 'resep.details'])
            ->get();

        return view('pages.kasir.struk', compact('encounters', 'paid', 'pasien', 'total', 'unpaidEncounters'));
    }

    /**
     * Halaman histori transaksi pembayaran
     */
    public function histori(Request $request)
    {
        $query = Encounter::where('status', 2)
            ->where(function ($q) {
                $q->where('status_bayar_tindakan', 1)
                    ->orWhere('status_bayar_resep', 1);
            });

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('updated_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('updated_at', '<=', $request->tanggal_sampai);
        }

        // Filter berdasarkan pencarian pasien
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_pasien', 'like', "%{$search}%")
                    ->orWhere('no_encounter', 'like', "%{$search}%")
                    ->orWhereHas('pasien', function ($pq) use ($search) {
                        $pq->where('rekam_medis', 'like', "%{$search}%");
                    });
            });
        }

        $encounters = $query->with(['pasien'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('pages.kasir.histori', compact('encounters'));
    }

    /**
     * Halaman laporan pembayaran
     */
    public function laporan(Request $request)
    {
        $tanggalDari = $request->input('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->input('tanggal_sampai', now()->format('Y-m-d'));

        $query = Encounter::where('status', 2)
            ->where(function ($q) {
                $q->where('status_bayar_tindakan', 1)
                    ->orWhere('status_bayar_resep', 1);
            })
            ->whereDate('updated_at', '>=', $tanggalDari)
            ->whereDate('updated_at', '<=', $tanggalSampai);

        $encounters = $query->with(['pasien'])
            ->orderByDesc('updated_at')
            ->get();

        // Hitung statistik
        $totalTindakan = $encounters->sum(function ($enc) {
            return $enc->status_bayar_tindakan ? $enc->total_bayar_tindakan : 0;
        });

        $totalResep = $encounters->sum(function ($enc) {
            return $enc->status_bayar_resep ? $enc->total_bayar_resep : 0;
        });

        $totalPembayaran = $totalTindakan + $totalResep;
        $jumlahTransaksi = $encounters->count();

        // Group by payment method
        $byPaymentMethod = [];
        foreach ($encounters as $enc) {
            if ($enc->status_bayar_tindakan && $enc->metode_pembayaran_tindakan) {
                $methods = explode(';', $enc->metode_pembayaran_tindakan);
                foreach ($methods as $method) {
                    $parts = explode(':', trim($method));
                    if (count($parts) === 2) {
                        $methodName = trim($parts[0]);
                        $amount = (float) str_replace(['.', ','], ['', '.'], trim($parts[1]));
                        if (!isset($byPaymentMethod[$methodName])) {
                            $byPaymentMethod[$methodName] = 0;
                        }
                        $byPaymentMethod[$methodName] += $amount;
                    }
                }
            }
            if ($enc->status_bayar_resep && $enc->metode_pembayaran_resep) {
                $methods = explode(';', $enc->metode_pembayaran_resep);
                foreach ($methods as $method) {
                    $parts = explode(':', trim($method));
                    if (count($parts) === 2) {
                        $methodName = trim($parts[0]);
                        $amount = (float) str_replace(['.', ','], ['', '.'], trim($parts[1]));
                        if (!isset($byPaymentMethod[$methodName])) {
                            $byPaymentMethod[$methodName] = 0;
                        }
                        $byPaymentMethod[$methodName] += $amount;
                    }
                }
            }
        }

        return view('pages.kasir.laporan', compact(
            'encounters',
            'tanggalDari',
            'tanggalSampai',
            'totalPembayaran',
            'totalTindakan',
            'totalResep',
            'jumlahTransaksi',
            'byPaymentMethod'
        ));
    }

    /**
     * Cetak struk untuk encounter tertentu
     */
    public function cetakStruk($encounter_id)
    {
        $encounter = Encounter::with(['pasien', 'tindakan', 'labRequests.items', 'radiologyRequests.jenis', 'resep.details'])
            ->findOrFail($encounter_id);

        $pasien = $encounter->pasien;

        // Build paid items info
        $paidItemsInfo = [];
        if ($encounter->status_bayar_tindakan) {
            $paidItemsInfo[$encounter->id]['tindakan'] = $encounter->total_bayar_tindakan;
        }
        if ($encounter->status_bayar_resep) {
            $paidItemsInfo[$encounter->id]['resep'] = $encounter->total_bayar_resep;
        }

        $total = ($encounter->status_bayar_tindakan ? $encounter->total_bayar_tindakan : 0) +
            ($encounter->status_bayar_resep ? $encounter->total_bayar_resep : 0);

        $encounters = collect([$encounter]);
        $paid = $paidItemsInfo;

        // Get unpaid encounters for this patient
        $unpaidEncounters = Encounter::where('rekam_medis', $pasien->rekam_medis)
            ->where('status', 2)
            ->where('id', '!=', $encounter_id)
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('total_bayar_tindakan', '>', 0)->where('status_bayar_tindakan', 0);
                })->orWhere(function ($sq) {
                    $sq->where('total_bayar_resep', '>', 0)->where('status_bayar_resep', 0);
                });
            })
            ->with(['tindakan', 'labRequests.items', 'radiologyRequests.jenis', 'resep.details'])
            ->get();

        return view('pages.kasir.struk', compact('encounters', 'paid', 'pasien', 'total', 'unpaidEncounters'));
    }

    /**
     * Buat insentif lab dan radiologi saat pembayaran lunas
     * Mencakup fee penunjang (dokter perujuk) dan fee pelaksana (dokter lab/radiologi)
     */
    private function createLabRadiologiIncentives(Encounter $encounter)
    {
        try {
            $observasiRepo = new \App\Repositories\ObservasiRepository();

            // 1. Proses Lab Requests
            $labRequests = \App\Models\LabRequest::with('items')
                ->where('encounter_id', $encounter->id)
                ->where('status', 'completed')
                ->get();

            foreach ($labRequests as $labRequest) {
                // Fee Penunjang untuk dokter perujuk (requested_by atau dokter_id)
                $dokterPerujukId = $labRequest->dokter_id ?? $labRequest->requested_by;
                if ($dokterPerujukId) {
                    $dokterPerujuk = \App\Models\User::find($dokterPerujukId);
                    if ($dokterPerujuk) {
                        $observasiRepo->createPemeriksaanPenunjangIncentive(
                            $encounter,
                            $dokterPerujuk,
                            'Pemeriksaan Laboratorium',
                            (float)$labRequest->total_charge,
                            'lab'
                        );
                    }
                }

                // Fee Pelaksana untuk petugas lab yang menyelesaikan
                // Ambil dari user yang melakukan completed (biasanya Auth user saat update status)
                // Karena tidak ada field performed_by, gunakan updated_by atau cek dari activity log
                // Alternatif: gunakan user yang login terakhir saat completed_at
                if ($labRequest->completed_at) {
                    // Cari siapa yang menyelesaikan lab ini
                    // Untuk sementara, gunakan dokter_id atau first practitioner sebagai pelaksana
                    // Idealnya harus ada field performed_by di lab_requests
                    $pelaksanaLab = \App\Models\User::where('role', 8)->first(); // Ambil user lab pertama sebagai fallback

                    // Coba cari dari activity log siapa yang terakhir update status completed
                    $lastActivity = \App\Models\ActivityLog::where('subject_type', 'App\\Models\\LabRequest')
                        ->where('subject_id', $labRequest->id)
                        ->where('properties->status', 'completed')
                        ->latest()
                        ->first();

                    if ($lastActivity && $lastActivity->causer_id) {
                        $pelaksanaLab = \App\Models\User::find($lastActivity->causer_id);
                    }

                    if ($pelaksanaLab) {
                        $observasiRepo->createRadiologistIncentive(
                            $encounter,
                            $pelaksanaLab,
                            'Pemeriksaan Laboratorium',
                            (float)$labRequest->total_charge,
                            'lab'
                        );
                    }
                }
            }

            // 2. Proses Radiology Requests
            $radiologyRequests = \App\Models\RadiologyRequest::with(['jenis', 'results'])
                ->where('encounter_id', $encounter->id)
                ->where('status', 'completed')
                ->get();

            foreach ($radiologyRequests as $radiologyRequest) {
                // Fee Penunjang untuk dokter perujuk
                if ($radiologyRequest->dokter_id) {
                    $dokterPerujuk = \App\Models\User::find($radiologyRequest->dokter_id);
                    if ($dokterPerujuk) {
                        $observasiRepo->createPemeriksaanPenunjangIncentive(
                            $encounter,
                            $dokterPerujuk,
                            optional($radiologyRequest->jenis)->name ?? 'Radiologi',
                            (float)$radiologyRequest->price,
                            'radiologi'
                        );
                    }
                }

                // Fee Pelaksana untuk radiologist yang melakukan pemeriksaan
                $result = $radiologyRequest->results()->latest()->first();
                if ($result && $result->radiologist_id) {
                    $radiologist = \App\Models\User::find($result->radiologist_id);
                    if ($radiologist) {
                        $observasiRepo->createRadiologistIncentive(
                            $encounter,
                            $radiologist,
                            optional($radiologyRequest->jenis)->name ?? 'Radiologi',
                            (float)$radiologyRequest->price,
                            'radiologi'
                        );
                    }
                }
            }

            \Illuminate\Support\Facades\Log::info('Lab & Radiologi incentives created for encounter', [
                'encounter_id' => $encounter->id,
                'lab_count' => $labRequests->count(),
                'radio_count' => $radiologyRequests->count()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create lab/radiologi incentives', [
                'encounter_id' => $encounter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
