<?php

namespace App\Repositories;

use App\Models\LokasiLoket;
use App\Models\Loket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        $userRole = Auth::user()->role;
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
    public function getReminderEncounter()
    {
        $today = now()->toDateString();

        // Ambil semua reminder settings yang aktif
        $reminderSettings = \App\Models\ReminderSetting::where('is_active', true)->get();

        if ($reminderSettings->isEmpty()) {
            return collect();
        }

        $encounters = collect();

        foreach ($reminderSettings as $setting) {
            $daysAfter = $setting->days_before; // Rename untuk clarity
            $reminderEncounters = collect(); // Inisialisasi di awal

            if ($setting->type === 'obat') {
                // REMINDER BELI OBAT: X hari SETELAH resep habis
                // Formula: created_at + masa_pemakaian_hari + days_after = today
                $reminderEncounters = \App\Models\Encounter::with('resep')
                    ->whereHas('resep', function ($q) use ($today, $daysAfter) {
                        // Tanggal resep + masa pemakaian + X hari setelah habis = hari ini
                        $q->whereRaw("DATE_ADD(DATE_ADD(DATE(created_at), INTERVAL masa_pemakaian_hari DAY), INTERVAL ? DAY) = ?", [$daysAfter, $today]);
                    })
                    ->orderByDesc('created_at')
                    ->get();

                foreach ($reminderEncounters as $encounter) {
                    $encounter->reminder_setting = $setting;
                    $encounter->days_after_empty = $daysAfter;
                    $encounter->reminder_type = 'obat';

                    // Hitung tanggal habis dan tanggal reminder
                    if ($encounter->resep) {
                        $tanggalResep = \Carbon\Carbon::parse($encounter->resep->created_at);
                        $tanggalHabis = $tanggalResep->copy()->addDays($encounter->resep->masa_pemakaian_hari);
                        $encounter->tanggal_habis = $tanggalHabis->format('d-m-Y');
                        $encounter->tanggal_reminder = $tanggalHabis->copy()->addDays($daysAfter)->format('d-m-Y');
                    }
                }
            } elseif ($setting->type === 'checkup') {
                // REMINDER CHECK UP: X hari DARI tanggal kunjungan terakhir
                // Formula: created_at + days_after = today
                $reminderEncounters = \App\Models\Encounter::query()
                    ->whereRaw("DATE_ADD(DATE(created_at), INTERVAL ? DAY) = ?", [$daysAfter, $today])
                    ->orderByDesc('created_at')
                    ->get();

                foreach ($reminderEncounters as $encounter) {
                    $encounter->reminder_setting = $setting;
                    $encounter->days_after_visit = $daysAfter;
                    $encounter->reminder_type = 'checkup';

                    $tanggalKunjungan = \Carbon\Carbon::parse($encounter->created_at);
                    $encounter->tanggal_kunjungan = $tanggalKunjungan->format('d-m-Y');
                    $encounter->tanggal_reminder = $tanggalKunjungan->copy()->addDays($daysAfter)->format('d-m-Y');
                }
            }

            $encounters = $encounters->merge($reminderEncounters);
        }

        // Ambil semua no_hp pasien
        $rekamMedisList = $encounters->pluck('rekam_medis')->unique()->toArray();
        $pasienHp = \App\Models\Pasien::whereIn('rekam_medis', $rekamMedisList)
            ->pluck('no_hp', 'rekam_medis');

        // Generate message untuk setiap encounter
        foreach ($encounters as $encounter) {
            $encounter->no_hp = $pasienHp[$encounter->rekam_medis] ?? null;

            if ($encounter->reminder_setting && $encounter->reminder_setting->message_template) {
                $message = $encounter->reminder_setting->message_template;

                // Replace variables
                $message = str_replace('{nama_pasien}', $encounter->name_pasien, $message);
                $message = str_replace('{rekam_medis}', $encounter->rekam_medis, $message);

                if ($encounter->reminder_type === 'obat') {
                    $message = str_replace('{hari}', $encounter->days_after_empty, $message);
                    $message = str_replace('{tanggal}', $encounter->tanggal_habis ?? '-', $message);
                } elseif ($encounter->reminder_type === 'checkup') {
                    $message = str_replace('{hari}', $encounter->days_after_visit, $message);
                    $message = str_replace('{tanggal}', $encounter->tanggal_kunjungan ?? '-', $message);
                }

                $encounter->reminder_message = $message;

                // Generate WhatsApp URL
                if ($encounter->no_hp) {
                    $phoneNumber = preg_replace('/[^0-9]/', '', $encounter->no_hp);
                    if (substr($phoneNumber, 0, 1) === '0') {
                        $phoneNumber = '62' . substr($phoneNumber, 1);
                    }
                    $encodedMessage = urlencode($message);
                    $encounter->whatsapp_url = "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
                } else {
                    $encounter->whatsapp_url = null;
                }
            } else {
                $encounter->reminder_message = null;
                $encounter->whatsapp_url = null;
            }
        }

        // Create/update reminder logs dan filter yang sudah diklik
        $filteredEncounters = $encounters->filter(function ($encounter) use ($today) {
            // Create or get existing log untuk hari ini
            $log = \App\Models\ReminderLog::firstOrCreate(
                [
                    'rekam_medis' => $encounter->rekam_medis,
                    'reminder_type' => $encounter->reminder_type,
                    'reminder_date' => $today,
                ],
                [
                    'encounter_id' => $encounter->id,
                    'wa_clicked' => false,
                ]
            );

            // Attach log ke encounter untuk tracking
            $encounter->reminder_log_id = $log->id;
            $encounter->wa_clicked = $log->wa_clicked;

            // Filter: hanya tampilkan yang belum diklik
            return !$log->wa_clicked;
        });

        // Return unique encounters per rekam_medis + reminder_type
        // Pasien bisa punya 2 reminder: obat DAN checkup
        return $filteredEncounters->unique(function ($item) {
            return $item->rekam_medis . '-' . $item->reminder_type;
        })->values();
    }
}
