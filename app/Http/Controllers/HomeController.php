<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\OperationalExpense;
use App\Models\OtherIncome;
use App\Models\SalaryPayment;
use App\Models\User;
use App\Repositories\HomeRepository; // Tetap digunakan untuk profile
use App\Repositories\WilayahRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public $homeRepository;
    public $wilayahRepository;
    public function __construct(HomeRepository $homeRepository, WilayahRepository $wilayahRepository)
    {
        $this->homeRepository = $homeRepository;
        $this->wilayahRepository = $wilayahRepository;
    }
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Pendapatan Bulan Ini (Dipisahkan)
        $pendapatanOperasional = Encounter::where(function ($query) {
            $query->where('status_bayar_tindakan', 1)
                ->orWhere('status_bayar_resep', 1);
        })
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->selectRaw('SUM(total_bayar_tindakan) as pendapatan_tindakan, SUM(total_bayar_resep) as pendapatan_farmasi')
            ->first();

        $pendapatanTindakanBulanIni = $pendapatanOperasional->pendapatan_tindakan ?? 0;
        $pendapatanFarmasiBulanIni = $pendapatanOperasional->pendapatan_farmasi ?? 0;

        $pendapatanLainnya = OtherIncome::whereMonth('income_date', $currentMonth)
            ->whereYear('income_date', $currentYear)
            ->sum('amount');

        $pendapatanTindakanDanLainnya = $pendapatanTindakanBulanIni + $pendapatanLainnya;
        $totalPendapatanBulanIni = $pendapatanTindakanBulanIni + $pendapatanFarmasiBulanIni + $pendapatanLainnya;

        // 2. Pengeluaran Bulan Ini
        $pengeluaranOperasional = OperationalExpense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        $gajiInsentif = SalaryPayment::where('status', 'paid')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('amount');

        $totalPengeluaranBulanIni = $pengeluaranOperasional + $gajiInsentif;

        // 3. Laba/Rugi & Total Pasien
        $labaRugiBulanIni = $totalPendapatanBulanIni - $totalPengeluaranBulanIni;
        $totalPasienBulanIni = Encounter::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->distinct('rekam_medis')
            ->count();

        // 4. Data untuk Grafik
        $grafikData = $this->getGrafikDataTahunan($currentYear);

        return view('pages.dashboard.owner', compact(
            'totalPendapatanBulanIni',
            'pendapatanTindakanBulanIni',
            'pendapatanTindakanDanLainnya',
            'pendapatanFarmasiBulanIni',
            'totalPengeluaranBulanIni',
            'labaRugiBulanIni',
            'totalPasienBulanIni',
            'grafikData'
        ));
    }

    public function getProfile($id)
    {
        $user = $this->homeRepository->getProfile($id);
        $provinces = $this->wilayahRepository->getProvinces();
        return view('pages.dashboard.profile', compact('user', 'provinces'));
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string',
            'nik'       => 'required|string',
            'tgl_lahir' => 'required|string',
            'gender'    => 'required|string',
            'email'     => 'required|string',
            'no_hp'     => 'required|string',
            'status_menikah'    => 'nullable|string',
            'gol_darah' => 'required|string',
            'alamat'    => 'nullable|string',
            'provinsi'  => 'nullable|string',
            'kota'      => 'nullable|string',
            'foto'      => 'nullable|file|mimes:jpeg,jpg,png',
            'username'  => 'required|string|unique:users,username,' . $id,
            'new_password'  => 'nullable|string'
        ], [
            'name.required'     => 'Kolom masih kosong',
            'nik.required'      => 'Kolom masih kosong',
            'tgl_lahir.required' => 'Kolom masih kosong',
            'email.required'    => 'Kolom masih kosong',
            'gol_darah.required' => 'Kolom masih kosong',
            'gender.required'   => 'Pilih jenis kelamin',
            'foto.mimes'        => 'File harus berupa jpeg, jpg, png',
            'no_hp.required'    => 'Kolom masih kosong'
        ]);
        $profile =  $this->homeRepository->updateProfile($request, $id);
        return redirect()->route('home.profile', $id);
    }

    private function getGrafikDataTahunan($year)
    {
        $namaBulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];

        // Helper untuk mengambil data bulanan
        $fetchMonthlyData = function ($model, $dateColumn, $sumColumn, $year) {
            $data = $model->newQuery()->selectRaw("MONTH({$dateColumn}) as bulan, SUM({$sumColumn}) as total")
                ->whereYear($dateColumn, $year)
                ->groupBy('bulan')
                ->pluck('total', 'bulan')
                ->all();

            $result = [];
            for ($i = 1; $i <= 12; $i++) {
                $result[] = (int) ($data[$i] ?? 0);
            }
            return $result;
        };

        // Ambil data pendapatan dan pengeluaran
        $pendapatanTindakan = $fetchMonthlyData(new Encounter, 'updated_at', 'total_bayar_tindakan', $year);
        $pendapatanFarmasi = $fetchMonthlyData(new Encounter, 'updated_at', 'total_bayar_resep', $year);

        $pendapatanLainnya = $fetchMonthlyData(new OtherIncome, 'income_date', 'amount', $year);
        $pengeluaranOperasional = $fetchMonthlyData(new OperationalExpense, 'expense_date', 'amount', $year);
        $gajiInsentif = SalaryPayment::selectRaw('month as bulan, SUM(amount) as total')
            ->where('status', 'paid')->where('year', $year)->groupBy('bulan')->pluck('total', 'bulan')->all();

        $pengeluaranGaji = array_map(fn($i) => (int) ($gajiInsentif[$i] ?? 0), range(1, 12));

        $totalPendapatan = array_map(fn($t, $f, $l) => $t + $f + $l, $pendapatanTindakan, $pendapatanFarmasi, $pendapatanLainnya);
        $totalPengeluaran = array_map(fn($o, $g) => $o + $g, $pengeluaranOperasional, $pengeluaranGaji);

        // Hitung laba/rugi per bulan
        $labaRugi = array_map(fn($p, $k) => $p - $k, $totalPendapatan, $totalPengeluaran);

        return [
            'series' => [
                ['name' => 'Pendapatan Tindakan', 'data' => $pendapatanTindakan],
                ['name' => 'Pendapatan Farmasi', 'data' => $pendapatanFarmasi],
                ['name' => 'Pendapatan Lainnya', 'data' => $pendapatanLainnya],
                ['name' => 'Pengeluaran Operasional', 'data' => $pengeluaranOperasional],
                ['name' => 'Gaji & Insentif', 'data' => $pengeluaranGaji],
                ['name' => 'Laba/Rugi', 'data' => $labaRugi],
            ],
            'categories' => $namaBulan,
        ];
    }
}
