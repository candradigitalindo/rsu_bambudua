<?php

namespace App\Http\Controllers;

use App\Models\InpatientAdmission;
use App\Models\InpatientTreatment;
use App\Models\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DokterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        // Helper closure untuk query Practitioner by encounter type
        $countPractitioner = function ($type, $start, $end) use ($user) {
            return Practitioner::where('id_petugas', $user->id)
                ->whereHas('encounter', function ($q) use ($type) {
                    $q->where('type', $type);
                })
                ->whereBetween('created_at', [$start, $end])
                ->count();
        };

        // Helper closure untuk query Practitioner by encounter type per bulan
        $countPractitionerMonth = function ($type) use ($user) {
            return Practitioner::where('id_petugas', $user->id)
                ->whereHas('encounter', function ($q) use ($type) {
                    $q->where('type', $type);
                })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        };

        // Helper closure untuk query InpatientTreatment

        $countInpatient = function ($start, $end) use ($user) {
            return InpatientTreatment::where('performed_by', $user->id)
                ->where('request_type', 'Visit')
                ->whereBetween('created_at', [$start, $end])
                ->count();
        };

        $countInpatientMonth = function () use ($user) {
            return InpatientTreatment::where('performed_by', $user->id)
                ->where('request_type', 'Visit')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        };

        // Rawat Jalan (type 1)
        $thisWeek_rawatJalan = $countPractitioner(1, now()->startOfWeek(), now()->endOfWeek());
        $lastWeek_rawatJalan = $countPractitioner(1, now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek());
        $thisMonth_rawatJalan = $countPractitionerMonth(1);

        // Rawat Darurat (type 3)
        $thisWeek_rawatDarurat = $countPractitioner(3, now()->startOfWeek(), now()->endOfWeek());
        $lastWeek_rawatDarurat = $countPractitioner(3, now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek());
        $thisMonth_rawatDarurat = $countPractitionerMonth(3);

        // Rawat Inap
        $thisWeek_inpatient = $countInpatient(now()->startOfWeek(), now()->endOfWeek());
        $lastWeek_inpatient = $countInpatient(now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek());
        $thisMonth_inpatient = $countInpatientMonth();

        // Persentase kenaikan/penurunan
        $percent = function ($thisWeek, $lastWeek) {
            if ($lastWeek > 0) {
                return (($thisWeek - $lastWeek) / $lastWeek) * 100;
            }
            return $thisWeek > 0 ? 100 : 0;
        };

        $percent_rawatJalan = $percent($thisWeek_rawatJalan, $lastWeek_rawatJalan);
        $percent_rawatDarurat = $percent($thisWeek_rawatDarurat, $lastWeek_rawatDarurat);
        $percent_inpatient = $percent($thisWeek_inpatient, $lastWeek_inpatient);
        $grafik = $this->grafikTahunan(); // hasil: ['rawat_jalan'=>[...], 'rawat_darurat'=>[...], 'rawat_inap'=>[...]]
        $grafikKunjungan = $this->getGrafikKunjungan();
        $grafikPendapatan = $this->getGrafikPendapatan();

        // Histori pasien yang ditangani (10 terakhir)
        $historiPasien = $this->getHistoriPasien();

        // Histori pendapatan detail (10 terakhir)
        $historiPendapatan = $this->getHistoriPendapatan();

        return view('pages.dokter.index', compact(
            'user',
            'thisWeek_rawatJalan',
            'lastWeek_rawatJalan',
            'thisMonth_rawatJalan',
            'percent_rawatJalan',
            'thisWeek_rawatDarurat',
            'lastWeek_rawatDarurat',
            'thisMonth_rawatDarurat',
            'percent_rawatDarurat',
            'thisWeek_inpatient',
            'lastWeek_inpatient',
            'thisMonth_inpatient',
            'percent_inpatient',
            'grafik',
            'grafikKunjungan',
            'grafikPendapatan',
            'historiPasien',
            'historiPendapatan'
        ));
    }

    public function grafikTahunan()
    {
        $user = Auth::user();
        $year = now()->year;

        // Helper untuk rekap per bulan (index 1-12)
        $rekapPerBulan = function ($query, $year) {
            $data = $query
                ->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan')
                ->toArray();

            return array_map(
                fn($i) => $data[$i] ?? 0,
                range(1, 12)
            );
        };

        // Helper untuk total encounter per tahun
        $totalTahun = function ($query, $year) {
            return $query->whereYear('created_at', $year)->count();
        };

        // Rawat Jalan (type 1)
        $queryRawatJalan = Practitioner::where('id_petugas', $user->id)
            ->whereHas('encounter', fn($q) => $q->where('type', 1));
        $rawatJalan = $rekapPerBulan(clone $queryRawatJalan, $year);
        $totalRawatJalanTahunIni = $totalTahun(clone $queryRawatJalan, $year);
        $totalRawatJalanTahunLalu = $totalTahun(clone $queryRawatJalan, $year - 1);

        // Rawat Darurat (type 3)
        $queryRawatDarurat = Practitioner::where('id_petugas', $user->id)
            ->whereHas('encounter', fn($q) => $q->where('type', 3));
        $rawatDarurat = $rekapPerBulan(clone $queryRawatDarurat, $year);
        $totalRawatDaruratTahunIni = $totalTahun(clone $queryRawatDarurat, $year);
        $totalRawatDaruratTahunLalu = $totalTahun(clone $queryRawatDarurat, $year - 1);

        // Rawat Inap
        $queryRawatInap = InpatientAdmission::where('dokter_id', $user->id);
        $rawatInap = $rekapPerBulan(clone $queryRawatInap, $year);
        $totalRawatInapTahunIni = $totalTahun(clone $queryRawatInap, $year);
        $totalRawatInapTahunLalu = $totalTahun(clone $queryRawatInap, $year - 1);

        // Total encounter tahun ini & tahun lalu (semua jenis)
        $totalTahunIni = $totalRawatJalanTahunIni + $totalRawatDaruratTahunIni + $totalRawatInapTahunIni;
        $totalTahunLalu = $totalRawatJalanTahunLalu + $totalRawatDaruratTahunLalu + $totalRawatInapTahunLalu;

        // Fungsi persentase kenaikan/penurunan total
        $persen = function ($now, $last) {
            if ($last > 0) {
                return (($now - $last) / $last) * 100;
            }
            return $now > 0 ? 100 : 0;
        };

        return [
            'rawat_jalan'   => $rawatJalan,
            'rawat_darurat' => $rawatDarurat,
            'rawat_inap'    => $rawatInap,
            'total_encounter_tahun_ini' => $totalTahunIni,
            'total_encounter_tahun_lalu' => $totalTahunLalu,
            'persen_total_encounter' => $persen($totalTahunIni, $totalTahunLalu),
        ];
    }

    private function getGrafikKunjungan()
    {
        $user = Auth::user();
        $year = now()->year;
        $month = now()->month;
        $daysInMonth = now()->daysInMonth;

        // Helper untuk rekap bulanan (index 1-12)
        $rekapBulanan = function ($query, $year) {
            $data = $query
                ->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan')
                ->toArray();

            return array_map(fn($i) => $data[$i] ?? 0, range(1, 12));
        };

        // Helper untuk rekap harian dalam sebulan
        $rekapHarian = function ($query, $year, $month) use ($daysInMonth) {
            $data = $query
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->selectRaw('DAY(created_at) as tanggal, COUNT(*) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal')
                ->toArray();

            return array_map(fn($i) => $data[$i] ?? 0, range(1, $daysInMonth));
        };

        // Query dasar untuk practitioner
        $practitionerQuery = Practitioner::where('id_petugas', $user->id);

        // Data untuk Grafik Bulanan (1 Tahun)
        $bulanan = [
            'series' => [
                ['name' => 'Rawat Jalan', 'data' => $rekapBulanan(clone $practitionerQuery->whereHas('encounter', fn($q) => $q->where('type', 1)), $year)],
                ['name' => 'IGD', 'data' => $rekapBulanan(clone $practitionerQuery->whereHas('encounter', fn($q) => $q->where('type', 3)), $year)],
            ],
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];

        // Data untuk Grafik Harian (1 Bulan)
        $harian = [
            'series' => [
                ['name' => 'Rawat Jalan', 'data' => $rekapHarian(clone $practitionerQuery->whereHas('encounter', fn($q) => $q->where('type', 1)), $year, $month)],
                ['name' => 'IGD', 'data' => $rekapHarian(clone $practitionerQuery->whereHas('encounter', fn($q) => $q->where('type', 3)), $year, $month)],
            ],
            'categories' => range(1, $daysInMonth),
        ];

        return ['bulanan' => $bulanan, 'harian' => $harian];
    }

    private function getGrafikPendapatan()
    {
        $user = Auth::user();
        $year = now()->year;

        // Helper untuk rekap pendapatan bulanan dari incentives
        $rekapInsentifBulanan = function ($year, $userId) {
            $data = \App\Models\Incentive::where('user_id', $userId)
                ->where('year', $year)
                ->selectRaw('month as bulan, SUM(amount) as total')
                ->groupBy('month')
                ->pluck('total', 'bulan')
                ->toArray();

            return array_map(fn($i) => (int)($data[$i] ?? 0), range(1, 12));
        };

        // Helper untuk rekap pendapatan bulanan dari InpatientTreatment
        $rekapPendapatanBulanan = function ($query, $year) {
            $data = $query
                ->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as bulan, SUM(total) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan')
                ->toArray();

            return array_map(fn($i) => (int)($data[$i] ?? 0), range(1, 12));
        };

        // 1. Pendapatan dari Insentif (encounter, fee_penunjang, fee_obat)
        $pendapatanInsentif = $rekapInsentifBulanan($year, $user->id);

        // 2. Query untuk pendapatan dari tindakan rawat inap
        $queryPendapatanTindakan = InpatientTreatment::where('performed_by', $user->id)
            ->where('request_type', 'Tindakan');
        $pendapatanTindakan = $rekapPendapatanBulanan(clone $queryPendapatanTindakan, $year);

        // 3. Query untuk pendapatan dari visit rawat inap
        $queryPendapatanVisit = InpatientTreatment::where('performed_by', $user->id)
            ->where('request_type', 'Visit');
        $pendapatanVisit = $rekapPendapatanBulanan(clone $queryPendapatanVisit, $year);

        // Gabungkan semua pendapatan (insentif + tindakan + visit)
        $totalPendapatan = array_map(
            fn($insentif, $tindakan, $visit) => $insentif + $tindakan + $visit,
            $pendapatanInsentif,
            $pendapatanTindakan,
            $pendapatanVisit
        );

        return [
            'series' => [
                ['name' => 'Pendapatan', 'data' => $totalPendapatan],
            ],
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'total_tahun_ini' => array_sum($totalPendapatan)
        ];
    }

    /**
     * Get histori pasien yang ditangani (10 terakhir)
     */
    private function getHistoriPasien()
    {
        $user = Auth::user();

        // Ambil encounters dari practitioner (rawat jalan & IGD)
        $encountersFromPractitioner = Practitioner::where('id_petugas', $user->id)
            ->with(['encounter.pasien', 'encounter.diagnosis'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($practitioner) {
                $encounter = $practitioner->encounter;
                if (!$encounter) return null;

                return [
                    'tanggal' => $encounter->created_at,
                    'no_encounter' => $encounter->no_encounter,
                    'rekam_medis' => $encounter->rekam_medis,
                    'nama_pasien' => $encounter->name_pasien,
                    'type' => $encounter->type, // 1=RJ, 2=RI, 3=IGD
                    'type_text' => $encounter->type == 1 ? 'Rawat Jalan' : ($encounter->type == 3 ? 'IGD' : 'Rawat Inap'),
                    'diagnosis' => $encounter->diagnosis->first()->diagnosis_description ?? '-',
                    'status' => $encounter->status == 2 ? 'Selesai' : 'Sedang Dirawat',
                ];
            })
            ->filter(); // Remove null values

        // Ambil visit rawat inap
        $visitsFromInpatient = InpatientTreatment::where('performed_by', $user->id)
            ->where('request_type', 'Visit')
            ->with(['admission.encounter.pasien', 'admission.encounter.diagnosis'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($treatment) {
                $encounter = $treatment->admission->encounter ?? null;
                if (!$encounter) return null;

                return [
                    'tanggal' => $treatment->treatment_date,
                    'no_encounter' => $encounter->no_encounter,
                    'rekam_medis' => $encounter->rekam_medis,
                    'nama_pasien' => $encounter->name_pasien,
                    'type' => 2, // Rawat Inap
                    'type_text' => 'Rawat Inap (Visit)',
                    'diagnosis' => $encounter->diagnosis->first()->diagnosis_description ?? '-',
                    'status' => $encounter->status == 2 ? 'Selesai' : 'Sedang Dirawat',
                ];
            })
            ->filter();

        // Gabungkan dan sort berdasarkan tanggal terbaru
        return $encountersFromPractitioner->merge($visitsFromInpatient)
            ->sortByDesc('tanggal')
            ->take(10)
            ->values();
    }

    /**
     * Get histori pendapatan detail (10 terakhir)
     */
    private function getHistoriPendapatan()
    {
        $user = Auth::user();

        // 1. Ambil incentives (fee encounter, fee penunjang, fee pelaksana, fee obat)
        $incentives = \App\Models\Incentive::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($incentive) {
                // Parse type untuk label yang lebih friendly
                $typeLabels = [
                    'encounter' => 'Fee Kunjungan',
                    'treatment_inap' => 'Fee Tindakan Rawat Inap',
                    'visit_inap' => 'Fee Visit Rawat Inap',
                    'fee_penunjang' => 'Fee Penunjang',
                    'fee_pelaksana_lab' => 'Fee Pelaksana Lab',
                    'fee_pelaksana_radiologi' => 'Fee Pelaksana Radiologi',
                    'fee_obat_rj' => 'Fee Obat Rawat Jalan',
                    'fee_obat_inap' => 'Fee Obat Rawat Inap',
                ];

                return [
                    'tanggal' => $incentive->created_at,
                    'bulan' => $incentive->month,
                    'tahun' => $incentive->year,
                    'jenis' => $typeLabels[$incentive->type] ?? ucfirst(str_replace('_', ' ', $incentive->type)),
                    'keterangan' => $incentive->description,
                    'amount' => $incentive->amount,
                    'status' => $incentive->status == 'paid' ? 'Dibayar' : 'Pending',
                    'status_class' => $incentive->status == 'paid' ? 'success' : 'warning',
                ];
            });

        // 2. Ambil pendapatan dari tindakan rawat inap
        $inpatientTreatments = InpatientTreatment::where('performed_by', $user->id)
            ->whereIn('request_type', ['Tindakan', 'Visit'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($treatment) {
                return [
                    'tanggal' => $treatment->treatment_date,
                    'bulan' => $treatment->treatment_date->month,
                    'tahun' => $treatment->treatment_date->year,
                    'jenis' => $treatment->request_type == 'Tindakan' ? 'Tindakan Rawat Inap' : 'Visit Rawat Inap',
                    'keterangan' => $treatment->tindakan_name ?? 'Visit Pasien',
                    'amount' => $treatment->total,
                    'status' => 'Selesai',
                    'status_class' => 'success',
                ];
            });

        // Gabungkan dan sort berdasarkan tanggal terbaru
        return $incentives->merge($inpatientTreatments)
            ->sortByDesc('tanggal')
            ->take(10)
            ->values();
    }

    /**
     * Halaman histori pasien lengkap dengan filter tanggal
     */
    public function historiPasien(Request $request)
    {
        $user = Auth::user();

        // Default 30 hari terakhir
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Query untuk encounters dari practitioner (rawat jalan & IGD)
        $encountersQuery = Practitioner::where('id_petugas', $user->id)
            ->with(['encounter.pasien', 'encounter.diagnosis'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Query untuk visit rawat inap
        $visitsQuery = InpatientTreatment::where('performed_by', $user->id)
            ->where('request_type', 'Visit')
            ->with(['admission.encounter.pasien', 'admission.encounter.diagnosis'])
            ->whereBetween('treatment_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Get data
        $encountersFromPractitioner = $encountersQuery->get()
            ->map(function ($practitioner) {
                $encounter = $practitioner->encounter;
                if (!$encounter) return null;

                return [
                    'tanggal' => $encounter->created_at,
                    'no_encounter' => $encounter->no_encounter,
                    'rekam_medis' => $encounter->rekam_medis,
                    'nama_pasien' => $encounter->name_pasien,
                    'type' => $encounter->type,
                    'type_text' => $encounter->type == 1 ? 'Rawat Jalan' : ($encounter->type == 3 ? 'IGD' : 'Rawat Inap'),
                    'diagnosis' => $encounter->diagnosis->first()->diagnosis_description ?? '-',
                    'status' => $encounter->status == 2 ? 'Selesai' : 'Sedang Dirawat',
                ];
            })
            ->filter();

        $visitsFromInpatient = $visitsQuery->get()
            ->map(function ($treatment) {
                $encounter = $treatment->admission->encounter ?? null;
                if (!$encounter) return null;

                return [
                    'tanggal' => $treatment->treatment_date,
                    'no_encounter' => $encounter->no_encounter,
                    'rekam_medis' => $encounter->rekam_medis,
                    'nama_pasien' => $encounter->name_pasien,
                    'type' => 2,
                    'type_text' => 'Rawat Inap (Visit)',
                    'diagnosis' => $encounter->diagnosis->first()->diagnosis_description ?? '-',
                    'status' => $encounter->status == 2 ? 'Selesai' : 'Sedang Dirawat',
                ];
            })
            ->filter();

        // Gabungkan dan sort
        $historiPasien = $encountersFromPractitioner->merge($visitsFromInpatient)
            ->sortByDesc('tanggal')
            ->values();

        // Hitung statistik
        $totalPasien = $historiPasien->count();
        $totalRawatJalan = $historiPasien->where('type', 1)->count();
        $totalIGD = $historiPasien->where('type', 3)->count();
        $totalRawatInap = $historiPasien->where('type', 2)->count();

        return view('pages.dokter.histori-pasien', compact(
            'historiPasien',
            'startDate',
            'endDate',
            'totalPasien',
            'totalRawatJalan',
            'totalIGD',
            'totalRawatInap'
        ));
    }

    /**
     * Halaman histori pendapatan lengkap dengan filter tanggal
     */
    public function historiPendapatan(Request $request)
    {
        $user = Auth::user();

        // Default 30 hari terakhir
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Query incentives
        $incentivesQuery = \App\Models\Incentive::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Query inpatient treatments
        $inpatientTreatmentsQuery = InpatientTreatment::where('performed_by', $user->id)
            ->whereIn('request_type', ['Tindakan', 'Visit'])
            ->whereBetween('treatment_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Type labels
        $typeLabels = [
            'encounter' => 'Fee Kunjungan',
            'treatment_inap' => 'Fee Tindakan Rawat Inap',
            'visit_inap' => 'Fee Visit Rawat Inap',
            'fee_penunjang' => 'Fee Penunjang',
            'fee_pelaksana_lab' => 'Fee Pelaksana Lab',
            'fee_pelaksana_radiologi' => 'Fee Pelaksana Radiologi',
            'fee_obat_rj' => 'Fee Obat Rawat Jalan',
            'fee_obat_inap' => 'Fee Obat Rawat Inap',
        ];

        // Get incentives
        $incentives = $incentivesQuery->get()
            ->map(function ($incentive) use ($typeLabels) {
                return [
                    'tanggal' => $incentive->created_at,
                    'bulan' => $incentive->month,
                    'tahun' => $incentive->year,
                    'jenis' => $typeLabels[$incentive->type] ?? ucfirst(str_replace('_', ' ', $incentive->type)),
                    'keterangan' => $incentive->description,
                    'amount' => $incentive->amount,
                    'status' => $incentive->status == 'paid' ? 'Dibayar' : 'Pending',
                    'status_class' => $incentive->status == 'paid' ? 'success' : 'warning',
                ];
            });

        // Get inpatient treatments
        $inpatientTreatments = $inpatientTreatmentsQuery->get()
            ->map(function ($treatment) {
                return [
                    'tanggal' => $treatment->treatment_date,
                    'bulan' => $treatment->treatment_date->month,
                    'tahun' => $treatment->treatment_date->year,
                    'jenis' => $treatment->request_type == 'Tindakan' ? 'Tindakan Rawat Inap' : 'Visit Rawat Inap',
                    'keterangan' => $treatment->tindakan_name ?? 'Visit Pasien',
                    'amount' => $treatment->total,
                    'status' => 'Selesai',
                    'status_class' => 'success',
                ];
            });

        // Gabungkan dan sort
        $historiPendapatan = $incentives->merge($inpatientTreatments)
            ->sortByDesc('tanggal')
            ->values();

        // Hitung statistik
        $totalPendapatan = $historiPendapatan->sum('amount');
        $totalTransaksi = $historiPendapatan->count();
        $totalDibayar = $historiPendapatan->where('status', 'Dibayar')->sum('amount');
        $totalPending = $historiPendapatan->where('status', 'Pending')->sum('amount');

        // Group by jenis untuk breakdown
        $breakdownByJenis = $historiPendapatan->groupBy('jenis')->map(function ($items, $jenis) {
            return [
                'jenis' => $jenis,
                'jumlah' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        })->values();

        return view('pages.dokter.histori-pendapatan', compact(
            'historiPendapatan',
            'startDate',
            'endDate',
            'totalPendapatan',
            'totalTransaksi',
            'totalDibayar',
            'totalPending',
            'breakdownByJenis'
        ));
    }
}
