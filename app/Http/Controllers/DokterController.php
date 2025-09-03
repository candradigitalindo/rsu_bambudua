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
            return Practitioner::where('id_petugas', $user->id_petugas)
                ->whereHas('encounter', function ($q) use ($type) {
                    $q->where('type', $type);
                })
                ->whereBetween('created_at', [$start, $end])
                ->count();
        };

        // Helper closure untuk query Practitioner by encounter type per bulan
        $countPractitionerMonth = function ($type) use ($user) {
            return Practitioner::where('id_petugas', $user->id_petugas)
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
        $thisWeek_rawatDaurat = $countPractitioner(3, now()->startOfWeek(), now()->endOfWeek());
        $lastWeek_rawatDaurat = $countPractitioner(3, now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek());
        $thisMonth_rawatDaurat = $countPractitionerMonth(3);

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
        $percent_rawatDaurat = $percent($thisWeek_rawatDaurat, $lastWeek_rawatDaurat);
        $percent_inpatient = $percent($thisWeek_inpatient, $lastWeek_inpatient);
        $grafik = $this->grafikTahunan(); // hasil: ['rawat_jalan'=>[...], 'rawat_darurat'=>[...], 'rawat_inap'=>[...]]

        return view('pages.dokter.index', compact(
            'user',
            'thisWeek_rawatJalan',
            'lastWeek_rawatJalan',
            'thisMonth_rawatJalan',
            'percent_rawatJalan',
            'thisWeek_rawatDaurat',
            'lastWeek_rawatDaurat',
            'thisMonth_rawatDaurat',
            'percent_rawatDaurat',
            'thisWeek_inpatient',
            'lastWeek_inpatient',
            'thisMonth_inpatient',
            'percent_inpatient',
            'grafik'
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
        $queryRawatJalan = Practitioner::where('id_petugas', $user->id_petugas)
            ->whereHas('encounter', fn($q) => $q->where('type', 1));
        $rawatJalan = $rekapPerBulan(clone $queryRawatJalan, $year);
        $totalRawatJalanTahunIni = $totalTahun(clone $queryRawatJalan, $year);
        $totalRawatJalanTahunLalu = $totalTahun(clone $queryRawatJalan, $year - 1);

        // Rawat Darurat (type 3)
        $queryRawatDarurat = Practitioner::where('id_petugas', $user->id_petugas)
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
}
