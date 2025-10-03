<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Berita;
use App\Models\Encounter;
use App\Models\OperationalExpense;
use App\Models\OtherIncome;
use App\Models\SalaryPayment;
use App\Models\User;
use App\Repositories\HomeRepository; // Tetap digunakan untuk profile
use App\Repositories\WilayahRepository;
use App\Services\OwnerDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public $homeRepository;
    public $wilayahRepository;
    public $ownerDashboardService;
    
    public function __construct(
        HomeRepository $homeRepository, 
        WilayahRepository $wilayahRepository,
        OwnerDashboardService $ownerDashboardService
    ) {
        $this->homeRepository = $homeRepository;
        $this->wilayahRepository = $wilayahRepository;
        $this->ownerDashboardService = $ownerDashboardService;
    }
    public function index()
    {
        $userRole = UserRole::fromValue(Auth::user()->role);
        
        // Redirect ke dashboard sesuai role
        if ($userRole && !in_array($userRole, [UserRole::OWNER, UserRole::ADMIN])) {
            return redirect()->route($userRole->dashboardRoute());
        }
        
        // Jika bukan owner/admin atau role tidak dikenali, redirect ke profile
        if (!$userRole || !in_array($userRole, [UserRole::OWNER, UserRole::ADMIN])) {
            return redirect()->route('home.profile', Auth::id());
        }

        // Rentang tanggal bulan ini untuk optimasi query
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $currentYear = now()->year;

        // 1. Pendapatan Bulan Ini (Dipisahkan)
        $pendapatanOperasional = Encounter::where(function ($query) {
            $query->where('status_bayar_tindakan', 1)
                ->orWhere('status_bayar_resep', 1);
        })
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('SUM(total_bayar_tindakan) as pendapatan_tindakan, SUM(total_bayar_resep) as pendapatan_farmasi')
            ->first();

        $pendapatanTindakanBulanIni = $pendapatanOperasional->pendapatan_tindakan ?? 0;
        $pendapatanFarmasiBulanIni = $pendapatanOperasional->pendapatan_farmasi ?? 0;

        $pendapatanLainnya = OtherIncome::whereBetween('income_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $pendapatanTindakanDanLainnya = $pendapatanTindakanBulanIni + $pendapatanLainnya;
        $totalPendapatanBulanIni = $pendapatanTindakanBulanIni + $pendapatanFarmasiBulanIni + $pendapatanLainnya;

        // 2. Pengeluaran Bulan Ini
        $pengeluaranOperasional = OperationalExpense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $gajiInsentif = SalaryPayment::where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalPengeluaranBulanIni = $pengeluaranOperasional + $gajiInsentif;

        // 3. Laba/Rugi & Total Pasien
        $labaRugiBulanIni = $totalPendapatanBulanIni - $totalPengeluaranBulanIni;
        $totalPasienBulanIni = Encounter::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->distinct('rekam_medis')
            ->count();

        // 4. Data untuk Grafik
        $grafikData = $this->getGrafikDataTahunan($currentYear);
        $grafikKunjungan = $this->getGrafikKunjungan();

        // 5. Ambil Berita Terbaru
        $beritaTerbaru = Berita::where('is_published', true)
            ->where('created_at', '>=', now()->subMonth())
            ->latest()
            ->take(5)->get();

        // 6. Data Analisa Tambahan untuk Owner
        $kpiData = $this->ownerDashboardService->getKPIData();
        $operationalStatus = $this->ownerDashboardService->getOperationalStatus();
        $alerts = $this->ownerDashboardService->getAlerts();
        $topDiagnoses = $this->ownerDashboardService->getTopDiagnoses();
        $departmentPerformance = $this->ownerDashboardService->getDepartmentPerformance();
        $inventoryAlerts = $this->ownerDashboardService->getInventoryAlerts();
        $financialHealth = $this->ownerDashboardService->getFinancialHealth();
        $bedAnalytics = $this->ownerDashboardService->getBedAnalytics();

        return view('pages.dashboard.owner', compact(
            'totalPendapatanBulanIni',
            'pendapatanTindakanBulanIni',
            'pendapatanTindakanDanLainnya',
            'pendapatanFarmasiBulanIni',
            'totalPengeluaranBulanIni',
            'labaRugiBulanIni',
            'totalPasienBulanIni',
            'grafikData',
            'grafikKunjungan',
            'beritaTerbaru',
            'kpiData',
            'operationalStatus',
            'alerts',
            'topDiagnoses',
            'departmentPerformance',
            'inventoryAlerts',
            'financialHealth',
            'bedAnalytics'
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

    private function getGrafikKunjungan()
    {
        $year = now()->year;
        $month = now()->month;
        $daysInMonth = now()->daysInMonth;

        // Helper untuk rekap per bulan (index 1-12)
        $rekapBulanan = function ($type) use ($year) {
            $data = \App\Models\Encounter::where('type', $type)
                ->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
                ->groupBy('bulan')
                ->pluck('total', 'bulan')
                ->toArray();

            return array_map(fn($i) => $data[$i] ?? 0, range(1, 12));
        };

        // Helper untuk rekap per hari dalam sebulan
        $rekapHarian = function ($type) use ($year, $month) {
            $data = \App\Models\Encounter::where('type', $type)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->selectRaw('DAY(created_at) as tanggal, COUNT(*) as total')
                ->groupBy('tanggal')
                ->pluck('total', 'tanggal')
                ->toArray();

            return array_map(fn($i) => $data[$i] ?? 0, range(1, now()->daysInMonth));
        };

        // Data untuk Grafik Bulanan (1 Tahun)
        $bulanan = [
            'series' => [
                ['name' => 'Rawat Jalan', 'data' => $rekapBulanan(1)],
                ['name' => 'Rawat Inap', 'data' => $rekapBulanan(2)],
                ['name' => 'IGD', 'data' => $rekapBulanan(3)],
            ],
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];

        // Data untuk Grafik Harian (1 Bulan)
        $harian = [
            'series' => [
                ['name' => 'Rawat Jalan', 'data' => $rekapHarian(1)],
                ['name' => 'Rawat Inap', 'data' => $rekapHarian(2)],
                ['name' => 'IGD', 'data' => $rekapHarian(3)],
            ],
            'categories' => range(1, $daysInMonth),
        ];

        return ['bulanan' => $bulanan, 'harian' => $harian];
    }

    /**
     * Get real-time dashboard data for AJAX refresh
     */
    public function getRealTimeData()
    {
        if (!in_array(auth()->user()->role, [1, 4])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = [
            'operational_status' => $this->ownerDashboardService->getOperationalStatus(),
            'alerts' => $this->ownerDashboardService->getAlerts(),
            'inventory_alerts' => $this->ownerDashboardService->getInventoryAlerts()->take(5),
            'kpi_data' => $this->ownerDashboardService->getKPIData(),
            'timestamp' => now()->format('H:i:s')
        ];

        return response()->json($data);
    }
}
