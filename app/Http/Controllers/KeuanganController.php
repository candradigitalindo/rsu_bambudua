<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Encounter;
use App\Models\Incentive;
use App\Models\OtherIncome;
use App\Models\OperationalExpense;
use App\Models\IncentiveSetting;
use App\Models\SalaryAdjustment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SalaryPayment;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class KeuanganController extends Controller
{
    public function index()
    {
        $currentYear = now()->year;
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // 1. Pendapatan Bulan Ini (Optimized Query)
        $pendapatan = Encounter::where(function ($query) {
            $query->where('status_bayar_tindakan', 1)
                ->orWhere('status_bayar_resep', 1);
        })
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('SUM(total_bayar_tindakan) as total_tindakan, SUM(total_bayar_resep) as total_resep')
            ->first();

        $pendapatanOperasionalBulanIni = ($pendapatan->total_tindakan ?? 0) + ($pendapatan->total_resep ?? 0);

        // Ambil pendapatan lainnya bulan ini
        $pendapatanLainnyaBulanIni = OtherIncome::whereBetween('income_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalPendapatanBulanIni = $pendapatanOperasionalBulanIni + $pendapatanLainnyaBulanIni;

        // 2. Pengeluaran, Gaji, Laba/Rugi
        $pengeluaranOperasionalBulanIni = OperationalExpense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Mengembalikan perhitungan Gaji & Insentif hanya untuk yang sudah dibayar bulan ini.
        $gajiInsentifBulanIni = SalaryPayment::where('status', 'paid')->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $totalPengeluaranBulanIni = $pengeluaranOperasionalBulanIni + $gajiInsentifBulanIni;

        $labaRugiBulanIni = $totalPendapatanBulanIni - $totalPengeluaranBulanIni;

        // 3. Data untuk Grafik Tahunan
        $grafikData = $this->getGrafikDataTahunan($currentYear);

        // 4. Ambil Berita Terbaru
        $beritaTerbaru = Berita::where('is_published', true)
            ->where('created_at', '>=', now()->subMonth())
            ->latest()
            ->take(5)->get();

        return view('pages.keuangan.index', compact(
            'totalPendapatanBulanIni',
            'totalPengeluaranBulanIni',
            'gajiInsentifBulanIni',
            'labaRugiBulanIni',
            'pengeluaranOperasionalBulanIni',
            'pendapatanLainnyaBulanIni',
            'grafikData',
            'beritaTerbaru'
        ));
    }

    private function getGrafikDataTahunan($year)
    {
        $data = [];
        $namaBulan = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        $seriesNames = [
            'pendapatan_tindakan' => 'Pendapatan Tindakan',
            'pendapatan_farmasi' => 'Pendapatan Farmasi',
            'pendapatan_lainnya' => 'Pendapatan Lainnya',
            'pengeluaran_operasional' => 'Pengeluaran Operasional',
            'gaji_dan_insentif' => 'Gaji & Insentif',
        ];

        // Inisialisasi semua data dengan 0
        foreach ($seriesNames as $key => $name) {
            $data[$key] = array_fill(0, 12, 0);
        }

        // Ambil data dari DB
        $pendapatanTindakan = Encounter::selectRaw('MONTH(updated_at) as bulan, SUM(total_bayar_tindakan) as total')
            ->where('status_bayar_tindakan', 1)
            ->whereYear('updated_at', $year)
            ->groupBy('bulan')
            ->get();

        $pendapatanFarmasi = Encounter::selectRaw('MONTH(updated_at) as bulan, SUM(total_bayar_resep) as total')
            ->where('status_bayar_resep', 1)
            ->whereYear('updated_at', $year)
            ->groupBy('bulan')
            ->get();

        $pendapatanLainnya = OtherIncome::selectRaw('MONTH(income_date) as bulan, SUM(amount) as total')
            ->whereYear('income_date', $year)
            ->groupBy('bulan')
            ->get();

        $pengeluaranOperasional = OperationalExpense::selectRaw('MONTH(expense_date) as bulan, SUM(amount) as total')
            ->whereYear('expense_date', $year)
            ->groupBy('bulan')
            ->get();

        $gajiTahunan = SalaryPayment::selectRaw('month as bulan, SUM(amount) as total')
            ->where('status', 'paid')
            ->where('year', $year)
            ->groupBy('bulan')
            ->get();

        // Isi data yang ada dari DB
        foreach ($pendapatanTindakan as $result) {
            if ($result->bulan) {
                $data['pendapatan_tindakan'][$result->bulan - 1] = (float)$result->total;
            }
        }

        foreach ($pendapatanFarmasi as $result) {
            if ($result->bulan) {
                $data['pendapatan_farmasi'][$result->bulan - 1] = (float)$result->total;
            }
        }

        foreach ($pendapatanLainnya as $result) {
            if ($result->bulan) {
                $data['pendapatan_lainnya'][$result->bulan - 1] = (float)$result->total;
            }
        }

        foreach ($pengeluaranOperasional as $result) {
            if ($result->bulan) {
                $data['pengeluaran_operasional'][$result->bulan - 1] = (float)$result->total;
            }
        }

        foreach ($gajiTahunan as $result) {
            if ($result->bulan) {
                $data['gaji_dan_insentif'][$result->bulan - 1] = (float)$result->total;
            }
        }

        // Buat series untuk chart
        $series = [];
        foreach ($seriesNames as $key => $name) {
            $series[] = [
                'name' => $name,
                'data' => $data[$key]
            ];
        }

        return [
            'series' => $series,
            'categories' => $namaBulan,
        ];
    }

    public function gaji()
    {
        // Panggil helper untuk mendapatkan periode penggajian yang benar
        $payrollPeriod = $this->getPayrollPeriod();
        $startDate = $payrollPeriod['start_date'];
        $endDate = $payrollPeriod['end_date'];
        $paymentMonth = $payrollPeriod['payment_month'];
        $paymentYear = $payrollPeriod['payment_year'];

        // Ambil semua pengguna dengan relasi yang diperlukan
        $employees = User::with([
            'salary',
            'salaryPayments' => fn($q) => $q->where('year', $paymentYear)->where('month', $paymentMonth),
            'incentives' => fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]),
            'salaryAdjustments' => fn($q) => $q->where('year', $paymentYear)->where('month', $paymentMonth)
        ])->where('role', '!=', 1)->orderBy('name')->get(); // Asumsikan role 1 adalah owner/admin

        // Hitung total insentif dan penyesuaian untuk setiap karyawan
        $employees->each(function ($employee) {
            $employee->total_incentive = $employee->incentives->sum('amount');
            // Ambil penyesuaian untuk periode ini, jika ada
            $adjustment = $employee->salaryAdjustments->first();
            $employee->bonus = $adjustment->bonus ?? 0;
            $employee->deduction = $adjustment->deduction ?? 0;
        });

        return view('pages.keuangan.gaji', [
            'employees' => $employees,
            'current_month_name' => \Carbon\Carbon::createFromDate($paymentYear, $paymentMonth)->translatedFormat('F Y')
        ]);
    }

    public function paySalary(Request $request, User $user)
    {
        // Panggil helper untuk mendapatkan periode penggajian yang benar
        $payrollPeriod = $this->getPayrollPeriod();
        $startDate = $payrollPeriod['start_date'];
        $endDate = $payrollPeriod['end_date'];
        $paymentMonth = $payrollPeriod['payment_month'];
        $paymentYear = $payrollPeriod['payment_year'];

        // Validasi apakah gaji untuk bulan ini sudah dibayar
        $existingPayment = SalaryPayment::where('user_id', $user->id)
            ->where('year', $paymentYear)
            ->where('month', $paymentMonth)
            ->where('status', 'paid')
            ->first();

        if ($existingPayment) {
            return response()->json(['message' => 'Gaji untuk bulan ini sudah dibayar.'], 422);
        }

        $gajiPokok = $user->salary->base_salary ?? 0;

        $totalIncentive = $user->incentives()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $adjustment = SalaryAdjustment::where('user_id', $user->id)
            ->where('year', $paymentYear)->where('month', $paymentMonth)->first();
        $bonus = $adjustment->bonus ?? 0;
        $deduction = $adjustment->deduction ?? 0;

        $totalGaji = ($gajiPokok + $totalIncentive + $bonus) - $deduction;

        DB::transaction(function () use ($user, $paymentYear, $paymentMonth, $totalGaji, $startDate, $endDate) {
            // 1. Catat pembayaran gaji
            SalaryPayment::create([
                'user_id' => $user->id,
                'year' => $paymentYear,
                'month' => $paymentMonth,
                'amount' => $totalGaji,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // 2. Update status semua insentif di bulan ini menjadi 'paid'
            Incentive::where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->update(['status' => 'paid', 'paid_at' => now()]);
        });

        return response()->json(['message' => 'Pembayaran gaji untuk ' . $user->name . ' berhasil diproses.']);
    }

    public function pengaturanIncentive()
    {
        $settings = IncentiveSetting::pluck('setting_value', 'setting_key')->toArray();

        // Cast cutoff_day to integer to remove decimal
        if (isset($settings['cutoff_day'])) {
            $settings['cutoff_day'] = (int) $settings['cutoff_day'];
        }

        return view('pages.keuangan.pengaturan_insentif', compact('settings'));
    }

    public function simpanPengaturanIncentive(Request $request)
    {
        $request->validate([
            'cutoff_day' => 'required|numeric|min:1|max:28',
            'fee_lab_mode' => 'nullable|in:0,1',
            'fee_lab_value' => 'nullable|numeric|min:0',
            'fee_radiologi_mode' => 'nullable|in:0,1',
            'fee_radiologi_value' => 'nullable|numeric|min:0',
            'perawat_fee_radiologi_mode' => 'nullable|in:0,1',
            'perawat_fee_radiologi_value' => 'nullable|numeric|min:0',
            'perawat_fee_radiologi_pertindakan_value' => 'nullable|numeric|min:0',
            'fee_obat_mode' => 'nullable|in:0,1',
            'fee_obat_value' => 'nullable|numeric|min:0',
            'fee_obat_target_mode' => 'nullable|in:0,1',
            'fee_dokter_penunjang' => 'nullable|numeric|min:0',
        ], [
            'cutoff_day.required' => 'Tanggal cut-off harus diisi.',
            'cutoff_day.numeric' => 'Tanggal cut-off harus berupa angka bulat.',
            'cutoff_day.min' => 'Tanggal cut-off minimal adalah 1.',
            'cutoff_day.max' => 'Tanggal cut-off maksimal adalah 28.',
        ]);

        // Honor Dokter dan Bonus Perawat sekarang diatur di Master Data Tindakan
        // Setting ini tidak lagi digunakan

        IncentiveSetting::updateOrCreate(
            ['setting_key' => 'cutoff_day'],
            [
                'setting_value' => (int) $request->cutoff_day,
                'description' => 'Tanggal batas (cut-off) untuk perhitungan gaji dan insentif bulanan.'
            ]
        );

        // Simpan pengaturan fee penunjang & obat
        $keys = [
            'fee_lab_mode',
            'fee_lab_value',
            'fee_radiologi_mode',
            'fee_radiologi_value',
            'perawat_fee_radiologi_mode',
            'perawat_fee_radiologi_value',
            'perawat_fee_radiologi_pertindakan_value',
            'fee_obat_mode',
            'fee_obat_value',
            'fee_obat_target_mode',
            'fee_dokter_penunjang'
        ];
        foreach ($keys as $key) {
            if (!is_null($request->$key)) {
                IncentiveSetting::updateOrCreate(
                    ['setting_key' => $key],
                    [
                        'setting_value' => $request->$key,
                        'description' => 'Pengaturan otomatis: ' . $key,
                    ]
                );
            }
        }

        Alert::success('Berhasil', 'Pengaturan insentif berhasil disimpan.');
        return redirect()->route('keuangan.incentive.settings');
    }

    public function gajiDetail(User $user)
    {
        // Panggil helper untuk mendapatkan periode penggajian yang benar
        $payrollPeriod = $this->getPayrollPeriod();
        $startDate = $payrollPeriod['start_date'];
        $endDate = $payrollPeriod['end_date'];
        $paymentMonth = $payrollPeriod['payment_month'];
        $paymentYear = $payrollPeriod['payment_year'];

        // Ambil data user, gaji pokok, dan pembayaran bulan ini
        $user->load(['salary', 'profile.spesialis']);

        // Ambil semua insentif untuk bulan ini
        $incentives = Incentive::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil data penyesuaian untuk bulan ini
        $adjustment = SalaryAdjustment::where('user_id', $user->id)
            ->where('year', $paymentYear)
            ->where('month', $paymentMonth)
            ->first();

        return view('pages.keuangan.gaji_detail', [
            'employee' => $user,
            'incentives' => $incentives,
            'gaji_pokok' => $user->salary->base_salary ?? 0,
            'adjustment' => $adjustment,
            'current_month_name' => \Carbon\Carbon::createFromDate($paymentYear, $paymentMonth)->translatedFormat('F Y')
        ]);
    }

    public function storeSalaryAdjustment(Request $request, User $user)
    {
        $request->validate([
            'bonus' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'bonus.numeric' => 'Bonus harus berupa angka.',
            'deduction.numeric' => 'Potongan harus berupa angka.',
        ]);

        // Panggil helper untuk mendapatkan periode penggajian yang benar
        $payrollPeriod = $this->getPayrollPeriod();
        $paymentMonth = $payrollPeriod['payment_month'];
        $paymentYear = $payrollPeriod['payment_year'];

        // Cek apakah gaji sudah dibayar untuk periode ini
        $isPaid = SalaryPayment::where('user_id', $user->id)
            ->where('year', $paymentYear)
            ->where('month', $paymentMonth)
            ->where('status', 'paid')
            ->exists();

        if ($isPaid) {
            Alert::error('Gagal', 'Gaji untuk periode ini sudah dibayar. Penyesuaian tidak dapat disimpan.');
            return back();
        }

        SalaryAdjustment::updateOrCreate(
            [
                'user_id' => $user->id,
                'year' => $paymentYear,
                'month' => $paymentMonth,
            ],
            [
                'bonus' => $request->input('bonus', 0),
                'deduction' => $request->input('deduction', 0),
                'notes' => $request->input('notes'),
                'created_by' => Auth::id(),
            ]
        );

        Alert::success('Berhasil', 'Penyesuaian gaji berhasil disimpan.');
        return back();
    }

    /**
     * Helper function to determine the current payroll period based on the cutoff date.
     * Handles edge cases for end-of-month dates.
     *
     * @return array
     */
    private function getPayrollPeriod(): array
    {
        $settingCutoff = IncentiveSetting::where('setting_key', 'cutoff_day')->first();
        $cutOffDay = $settingCutoff ? (int) $settingCutoff->setting_value : 25;
        $today = now();

        // Tentukan tanggal akhir periode saat ini
        $currentPeriodEnd = $today->copy()->day($cutOffDay)->endOfDay();
        if ($cutOffDay > $today->daysInMonth) {
            $currentPeriodEnd = $today->copy()->endOfMonth()->endOfDay();
        }

        if ($today->gt($currentPeriodEnd)) {
            // Jika hari ini sudah melewati cut-off bulan ini, periode gaji adalah untuk bulan depan
            $startDate = $currentPeriodEnd->copy()->addSecond();
            $endDate = $currentPeriodEnd->copy()->addMonth()->day($cutOffDay)->endOfDay();
            if ($cutOffDay > $endDate->daysInMonth) {
                $endDate = $endDate->copy()->endOfMonth()->endOfDay();
            }
            $paymentMonth = $today->copy()->addMonth()->month;
            $paymentYear = $today->copy()->addMonth()->year;
        } else {
            // Jika hari ini belum melewati cut-off, periode gaji adalah untuk bulan ini
            $endDate = $currentPeriodEnd;
            $previousPeriodEnd = $today->copy()->subMonth()->day($cutOffDay)->endOfDay();
            if ($cutOffDay > $previousPeriodEnd->daysInMonth) {
                $previousPeriodEnd = $previousPeriodEnd->copy()->endOfMonth()->endOfDay();
            }
            $startDate = $previousPeriodEnd->copy()->addSecond();
            $paymentMonth = $today->month;
            $paymentYear = $today->year;
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_month' => $paymentMonth,
            'payment_year' => $paymentYear,
        ];
    }
}
