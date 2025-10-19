<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Berita;
use App\Models\User;
use App\Models\Encounter;
use App\Models\Pasien;
use App\Models\InpatientAdmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // 1. Berita Terbaru
            $beritaTerbaru = $this->getBeritaTerbaru();

            // 2. Statistik Pengguna per Role
            $userStats = $this->getUserStats();

            // 3. Statistik Umum
            $generalStats = $this->getGeneralStats();

            // 4. Grafik Pendaftaran Pengguna Baru (3 Bulan Terakhir)
            $registrationChart = $this->getRegistrationChart();

            // 5. Grafik Encounter/Kunjungan (3 Bulan Terakhir)
            $encounterChart = $this->getEncounterChart();

            // 6. Recent Activities (menggunakan data yang ada)
            $recentActivities = $this->getRecentActivities();

            return view('pages.dashboard.admin', compact(
                'beritaTerbaru',
                'userStats',
                'generalStats',
                'registrationChart',
                'encounterChart',
                'recentActivities'
            ));
        } catch (\Exception $e) {
            Log::error('Error in AdminDashboardController: ' . $e->getMessage());

            // Return with fallback data
            return view('pages.dashboard.admin', [
                'beritaTerbaru' => collect(),
                'userStats' => collect(),
                'generalStats' => $this->getDefaultStats(),
                'registrationChart' => $this->getEmptyChart(),
                'encounterChart' => $this->getEmptyChart(),
                'recentActivities' => collect()
            ])->with('error', 'Terjadi kesalahan saat memuat dashboard');
        }
    }

    /**
     * Get berita terbaru
     */
    private function getBeritaTerbaru()
    {
        try {
            return Berita::where('is_published', true)
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Jika tabel berita tidak ada, return empty collection
            return collect();
        }
    }

    /**
     * Get user statistics by role
     */
    private function getUserStats()
    {
        try {
            $userCounts = User::select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->pluck('total', 'role');

            // Check if UserRole enum exists
            if (class_exists('App\Enums\UserRole')) {
                return collect(UserRole::cases())->map(function ($role) use ($userCounts) {
                    return [
                        'label' => $role->label(),
                        'count' => $userCounts[$role->value] ?? 0,
                    ];
                });
            } else {
                // Fallback ke hard-coded roles
                $roleLabels = [
                    1 => 'Owner',
                    2 => 'Dokter',
                    3 => 'Perawat',
                    4 => 'Admin',
                    5 => 'Resepsionis'
                ];

                return collect($roleLabels)->map(function ($label, $roleId) use ($userCounts) {
                    return [
                        'label' => $label,
                        'count' => $userCounts[$roleId] ?? 0,
                    ];
                });
            }
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get general statistics
     */
    private function getGeneralStats()
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();

            return [
                'total_users' => User::count(),
                'total_patients' => $this->safeCount(Pasien::class),
                'today_encounters' => $this->safeCount(Encounter::class, ['created_at' => $today]),
                'monthly_encounters' => $this->safeCount(Encounter::class, ['created_at' => $thisMonth]),
                'active_admissions' => $this->safeCount(InpatientAdmission::class, ['discharge_date' => null]),
            ];
        } catch (\Exception $e) {
            return $this->getDefaultStats();
        }
    }

    /**
     * Safe count method that handles non-existent tables
     */
    private function safeCount($model, $conditions = [])
    {
        try {
            $query = $model::query();

            foreach ($conditions as $field => $value) {
                if ($value === null) {
                    $query->whereNull($field);
                } elseif ($value instanceof Carbon) {
                    if ($field === 'created_at') {
                        if ($value->isToday()) {
                            $query->whereDate($field, $value);
                        } else {
                            $query->where($field, '>=', $value);
                        }
                    }
                } else {
                    $query->where($field, $value);
                }
            }

            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get registration chart data
     */
    private function getRegistrationChart()
    {
        try {
            $registrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', now()->subMonths(3))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            return [
                'series' => [[
                    'name' => 'Pengguna Baru',
                    'data' => $registrations->pluck('count')->all(),
                ]],
                'categories' => $registrations->pluck('date')
                    ->map(fn($date) => Carbon::parse($date)->format('d M'))
                    ->all(),
            ];
        } catch (\Exception $e) {
            return $this->getEmptyChart();
        }
    }

    /**
     * Get encounter chart data (menggantikan login chart)
     */
    private function getEncounterChart()
    {
        try {
            $encounters = Encounter::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', now()->subMonths(3))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            return [
                'series' => [[
                    'name' => 'Kunjungan Pasien',
                    'data' => $encounters->pluck('count')->all(),
                ]],
                'categories' => $encounters->pluck('date')
                    ->map(fn($date) => Carbon::parse($date)->format('d M'))
                    ->all(),
            ];
        } catch (\Exception $e) {
            return $this->getEmptyChart();
        }
    }

    /**
     * Get recent activities (menggunakan data yang ada)
     */
    private function getRecentActivities()
    {
        try {
            $activities = collect();

            // Recent users
            $recentUsers = User::latest()->take(5)->get();
            foreach ($recentUsers as $user) {
                $activities->push([
                    'type' => 'user',
                    'message' => "Pengguna baru: {$user->name}",
                    'time' => $user->created_at->diffForHumans(),
                    'icon' => 'ri-user-add-line',
                    'color' => 'success'
                ]);
            }

            // Recent encounters (jika ada)
            try {
                $recentEncounters = Encounter::latest()->take(3)->get();
                foreach ($recentEncounters as $encounter) {
                    $activities->push([
                        'type' => 'encounter',
                        'message' => "Kunjungan baru: {$encounter->name_pasien}",
                        'time' => $encounter->created_at->diffForHumans(),
                        'icon' => 'ri-calendar-check-line',
                        'color' => 'info'
                    ]);
                }
            } catch (\Exception $e) {
                // Skip if encounter table doesn't exist
            }

            return $activities->sortByDesc('time')->take(10);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get default stats when error occurs
     */
    private function getDefaultStats()
    {
        return [
            'total_users' => 0,
            'total_patients' => 0,
            'today_encounters' => 0,
            'monthly_encounters' => 0,
            'active_admissions' => 0,
        ];
    }

    /**
     * Get empty chart data
     */
    private function getEmptyChart()
    {
        return [
            'series' => [[
                'name' => 'Data',
                'data' => [],
            ]],
            'categories' => [],
        ];
    }
}
