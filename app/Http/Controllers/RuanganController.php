<?php

namespace App\Http\Controllers;

use App\Models\InpatientAdmission;
use App\Models\NursingCareRecord;
use App\Models\User;
use App\Models\VitalSign;
use App\Models\Ruangan;
use App\Models\ActivityLog;
use App\Models\PrescriptionOrder;
use App\Models\PrescriptionMedication;
use App\Models\MedicationAdministration;
use App\Models\ProductApotek;
use App\Repositories\RuanganRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controller as BaseController;

class RuanganController extends BaseController
{
    private $ruanganRepository;

    // Constants untuk role dan encounter types
    const ROLE_OWNER = 1;
    const ROLE_DOCTOR = 2;
    const ROLE_NURSE = 3;
    const ROLE_ADMIN = 4;
    const ROLE_RECEPTIONIST = 5;

    const ENCOUNTER_RAWAT_JALAN = 1;
    const ENCOUNTER_RAWAT_INAP = 2;
    const ENCOUNTER_IGD = 3;

    const AUTHORIZED_ROLES = [self::ROLE_OWNER, self::ROLE_NURSE, self::ROLE_ADMIN];

    public function __construct(RuanganRepository $ruanganRepository)
    {
        $this->ruanganRepository = $ruanganRepository;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangans = $this->ruanganRepository->index(); // Fetch all ruangan data
        return view('pages.ruangan.index', compact('ruangans')); // Pass data to the view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->ruanganRepository->AllCategory(); // Fetch all categories
        return view('pages.ruangan.create', compact('categories')); // Pass categories to the view for creating a new ruangan
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'no_kamar' => 'required|unique:ruangans',
                'harga' => 'required|string',
                'category' => 'required|string',
                'description' => 'nullable|string',
                'class' => 'nullable|string',
                'capacity' => 'nullable|integer',
            ],
            [
                'no_kamar.required' => 'No Kamar tidak boleh kosong.',
                'no_kamar.unique' => 'No Kamar harus unik.',
                'harga.required' => 'Harga harus diisi.',
                'harga.string' => 'Harga harus berupa string.',
                'category.required' => 'Kategori harus diisi.',
                'description.string' => 'Deskripsi harus berupa string.',
            ]
        );

        $data = [
            'no_kamar' => $request->no_kamar,
            'category_id' => $request->category,
            'description' => $request->description,
            'harga' => str_replace(".", "", $request->harga),
            'class' => $request->class, // Optional field
            'capacity' => $request->capacity, // Optional field
        ];

        $this->ruanganRepository->create($data); // Create new ruangan
        return redirect()->route('ruangan.index')->with('success', 'Ruangan created successfully.'); // Redirect with success message
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ruangan = $this->ruanganRepository->show($id); // Fetch ruangan by ID
        $categories = $this->ruanganRepository->AllCategory(); // Fetch all categories
        return view('pages.ruangan.edit', compact('ruangan', 'categories')); // Pass data to the view for editing
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'no_kamar' => 'required|unique:ruangans,no_kamar,' . $id,
                'harga' => 'required|string',
                'category' => 'required|string',
                'description' => 'nullable|string',
                'class' => 'nullable|string',
                'capacity' => 'nullable|integer',
            ],
            [
                'no_kamar.required' => 'No Kamar tidak boleh kosong.',
                'no_kamar.unique' => 'No Kamar harus unik.',
                'harga.required' => 'Harga harus diisi.',
                'harga.string' => 'Harga harus berupa string.',
                'category.required' => 'Kategori harus diisi.',
                'description.string' => 'Deskripsi harus berupa string.',
            ]
        );

        $data = [
            'no_kamar' => $request->no_kamar,
            'category_id' => $request->category,
            'description' => $request->description,
            'harga' => str_replace(".", "", $request->harga),
            'class' => $request->class, // Optional field
            'capacity' => $request->capacity, // Optional field
        ];

        $this->ruanganRepository->update($id, $data); // Update ruangan by ID
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui.'); // Redirect with success message
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->ruanganRepository->destroy($id); // Delete ruangan by ID
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus.'); // Redirect with success message
    }

    public function getTodayNursingNotes(Request $request)
    {
        try {
            $notes = NursingCareRecord::with('nurse', 'encounter')
                ->whereDate('created_at', now()->toDateString())
                // Optionally filter by current nurse if needed
                // ->where('nurse_id', auth()->id())
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($note) {
                    return [
                        'time' => $note->created_at->format('H:i'),
                        'patient_name' => $note->encounter->name_pasien ?? 'N/A',
                        'note_preview' => \Illuminate\Support\Str::limit($note->interventions ?? $note->evaluation_notes, 100),
                        'nurse_name' => $note->nurse->name ?? 'N/A',
                    ];
                });
            return response()->json(['success' => true, 'data' => $notes]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load nursing notes.'], 500);
        }
    }

    /**
     * Get bed availability information for all room types (API endpoint)
     */
    public function getBedAvailability()
    {
        try {
            $availability = $this->ruanganRepository->getBedAvailability();
            $summary = $this->ruanganRepository->getBedAvailabilitySummary();

            return response()->json([
                'success' => true,
                'message' => 'Bed availability data retrieved successfully',
                'data' => [
                    'summary' => $summary,
                    'categories' => $availability,
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving bed availability data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bed availability summary (API endpoint)
     */
    public function getBedAvailabilitySummary()
    {
        try {
            $summary = $this->ruanganRepository->getBedAvailabilitySummary();

            return response()->json([
                'success' => true,
                'message' => 'Bed availability summary retrieved successfully',
                'data' => $summary
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving bed availability summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show bed availability dashboard page
     */
    public function bedAvailabilityDashboard()
    {
        $availability = $this->ruanganRepository->getBedAvailability();
        $summary = $this->ruanganRepository->getBedAvailabilitySummary();

        return view('pages.ruangan.bed-availability', compact('availability', 'summary'));
    }

    /**
     * Show nurse bed dashboard page
     */
    public function nurseBedDashboard()
    {
        try {
            $availability = $this->ruanganRepository->getBedAvailability();
            $summary = $this->ruanganRepository->getBedAvailabilitySummary();

            // Separate pending and assigned admissions
            $pendingAdmissions = $this->getPendingAdmissions();
            $assignedAdmissions = $this->getAssignedAdmissions();

            // Map data for display
            $pendingList = $this->mapPendingAdmissions($pendingAdmissions);
            $nurseAssignments = $this->mapNurseAssignments($assignedAdmissions);
            $roomPatients = $this->groupPatientsByRoom($assignedAdmissions);
            $urgentTasks = $this->generateUrgentTasks($nurseAssignments);
            $shiftInfo = $this->getCurrentShiftInfo();
            $allNurses = $this->getNurses();

            return view('pages.ruangan.nurse-bed-dashboard', compact(
                'availability',
                'summary',
                'pendingList',
                'nurseAssignments',
                'urgentTasks',
                'shiftInfo',
                'roomPatients',
                'allNurses'
            ));
        } catch (\Exception $e) {
            Log::error('Error in nurseBedDashboard: ' . $e->getMessage());
            return $this->returnFallbackDashboard($e->getMessage());
        }
    }

    /**
     * Refresh nurse dashboard data (AJAX endpoint)
     */
    public function refreshNurseDashboard()
    {
        try {
            $availability = $this->ruanganRepository->getBedAvailability();
            $summary = $this->ruanganRepository->getBedAvailabilitySummary();

            // Separate pending and assigned
            $pendingAdmissions = $this->getPendingAdmissions();
            $assignedAdmissions = $this->getAssignedAdmissions();

            $pendingList = $this->mapPendingAdmissions($pendingAdmissions);
            $nurseAssignments = $this->mapNurseAssignments($assignedAdmissions);
            $roomPatients = $this->groupPatientsByRoom($assignedAdmissions);
            $urgentTasks = $this->generateUrgentTasks($nurseAssignments);

            return $this->jsonResponse(true, 'Dashboard data refreshed successfully', [
                'summary' => $summary,
                'availability' => $availability,
                'pending_count' => $pendingList->count(),
                'pending_list' => $pendingList,
                'nurse_assignments' => $nurseAssignments,
                'room_patients' => $roomPatients,
                'urgent_tasks' => $urgentTasks,
                'last_updated' => now()->format('d/m/Y H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Error in refreshNurseDashboard: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error refreshing dashboard: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get all active admissions with optimized relationships
     */
    private function getActiveAdmissions()
    {
        return InpatientAdmission::with([
            'room:id,no_kamar,class,capacity,category_id',
            'room.category:id,name',
            'patient:id,name,rekam_medis,tgl_lahir,jenis_kelamin',
            'doctor:id,name',
            'encounter:id,type,status'
        ])
            ->whereNull('discharge_date')
            ->whereHas('encounter', function ($query) {
                $query->whereIn('type', [self::ENCOUNTER_RAWAT_INAP, self::ENCOUNTER_IGD]);
            })
            ->orderBy('admission_date', 'desc')
            ->get();
    }

    /**
     * Get pending admissions (tidak punya ruangan)
     */
    private function getPendingAdmissions()
    {
        return InpatientAdmission::with([
            'patient:id,name,rekam_medis,tgl_lahir,jenis_kelamin,alamat,no_hp,is_kerabat_dokter,is_kerabat_karyawan,is_kerabat_owner',
            'doctor:id,name',
            'encounter:id,type,status,created_at,jenis_jaminan',
            'encounter.jenisJaminan:id,name'
        ])
            ->whereNull('discharge_date')
            ->whereNull('ruangan_id') // Belum ada ruangan
            ->whereHas('encounter', function ($query) {
                $query->whereIn('type', [self::ENCOUNTER_RAWAT_INAP, self::ENCOUNTER_IGD]);
            })
            ->orderBy('created_at', 'asc') // First come first serve
            ->get();
    }

    /**
     * Get assigned admissions (sudah punya ruangan)
     */
    private function getAssignedAdmissions()
    {
        return InpatientAdmission::with([
            'room:id,no_kamar,class,capacity,category_id',
            'room.category:id,name',
            'patient:id,name,rekam_medis,tgl_lahir,jenis_kelamin',
            'doctor:id,name',
            'encounter:id,type,status'
        ])
            ->whereNull('discharge_date')
            ->whereNotNull('ruangan_id') // Sudah ada ruangan
            ->whereHas('encounter', function ($query) {
                $query->whereIn('type', [self::ENCOUNTER_RAWAT_INAP, self::ENCOUNTER_IGD]);
            })
            ->orderBy('admission_date', 'desc')
            ->get();
    }

    /**
     * Map pending admissions (belum dapat ruangan)
     */
    private function mapPendingAdmissions($admissions)
    {
        return $admissions->map(function ($admission) {
            $sourceType = $admission->encounter->type == self::ENCOUNTER_IGD ? 'IGD' : 'Pendaftaran Langsung';
            $priorityClass = $admission->encounter->type == self::ENCOUNTER_IGD ? 'danger' : 'warning';

            // Determine kerabat status
            $kerabatType = 'Reguler';
            if ($admission->patient) {
                if ($admission->patient->is_kerabat_owner) {
                    $kerabatType = 'Kerabat Owner';
                } elseif ($admission->patient->is_kerabat_dokter) {
                    $kerabatType = 'Kerabat Dokter';
                } elseif ($admission->patient->is_kerabat_karyawan) {
                    $kerabatType = 'Kerabat Karyawan';
                }
            }

            // Convert gender
            $genderText = 'N/A';
            if ($admission->patient && $admission->patient->jenis_kelamin) {
                $genderText = $admission->patient->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan';
            }

            return [
                'id' => $admission->id,
                'encounter_id' => $admission->encounter_id,
                'patient_name' => optional($admission->patient)->name ?? 'N/A',
                'medical_record' => optional($admission->patient)->rekam_medis ?? 'N/A',
                'age' => $this->calculateAge($admission->patient),
                'gender' => $genderText,
                'birth_date' => optional($admission->patient)->tgl_lahir ? Carbon::parse($admission->patient->tgl_lahir)->format('d/m/Y') : 'N/A',
                'address' => optional($admission->patient)->alamat ?? 'N/A',
                'phone' => optional($admission->patient)->no_hp ?? 'N/A',
                'doctor_name' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                'admission_reason' => $admission->admission_reason ?? 'N/A',
                'jenis_jaminan' => optional($admission->encounter->jenisJaminan)->name ?? 'N/A',
                'kerabat_type' => $kerabatType,
                'source_type' => $sourceType,
                'priority_class' => $priorityClass,
                'created_at' => $admission->created_at,
                'waiting_time' => $admission->created_at ? Carbon::parse($admission->created_at)->diffForHumans() : 'N/A'
            ];
        });
    }

    /**
     * Map admissions to nurse assignments format
     */
    private function mapNurseAssignments($admissions)
    {
        return $admissions->take(10)->map(function ($admission) {
            return [
                'id' => $admission->id,
                'patient_name' => optional($admission->patient)->name ?? 'N/A',
                'room' => optional($admission->room)->no_kamar ?? 'N/A',
                'room_category' => optional(optional($admission->room)->category)->name ?? 'N/A',
                'room_class' => optional($admission->room)->class ?? 'Umum',
                'condition' => $this->mapAdmissionStatus($admission->status ?? 'active'),
                'doctor_name' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'days_admitted' => $admission->admission_date ? floor(Carbon::parse($admission->admission_date)->diffInDays(now(), true)) : 0,
                'medical_record' => optional($admission->patient)->rekam_medis ?? 'N/A'
            ];
        });
    }

    /**
     * Group patients by room
     */
    private function groupPatientsByRoom($admissions)
    {
        return $admissions->groupBy(function ($admission) {
            return optional($admission->room)->no_kamar ?? 'Unknown';
        })->map(function ($roomAdmissions) {
            return $roomAdmissions->map(function ($admission) {
                return [
                    'id' => $admission->id,
                    'patient_name' => optional($admission->patient)->name ?? 'N/A',
                    'condition' => $this->mapAdmissionStatus($admission->status ?? 'active'),
                    'days_admitted' => $admission->admission_date ? floor(Carbon::parse($admission->admission_date)->diffInDays(now(), true)) : 0,
                    'medical_record' => optional($admission->patient)->rekam_medis ?? 'N/A'
                ];
            });
        });
    }

    /**
     * Generate urgent tasks based on patient data
     */
    private function generateUrgentTasks($nurseAssignments)
    {
        $urgentTasks = collect();

        // Long stay patients (>7 days)
        $longStayPatients = $nurseAssignments->filter(fn($patient) => ($patient['days_admitted'] ?? 0) > 7);
        foreach ($longStayPatients->take(2) as $patient) {
            $urgentTasks->push([
                'message' => "Review discharge plan untuk {$patient['patient_name']} di {$patient['room']} ({$patient['days_admitted']} hari)",
                'time' => $patient['days_admitted'] . ' hari rawat inap',
                'priority' => 'high',
                'type' => 'discharge_review'
            ]);
        }

        // Critical patients
        $criticalPatients = $nurseAssignments->filter(fn($patient) => strtolower($patient['condition']) === 'critical');
        foreach ($criticalPatients->take(3) as $patient) {
            $urgentTasks->push([
                'message' => "Monitor intensive {$patient['patient_name']} di {$patient['room']} - Kondisi Critical",
                'time' => 'Setiap 30 menit',
                'priority' => 'urgent',
                'type' => 'critical_monitoring'
            ]);
        }

        // New admissions
        $newAdmissions = $nurseAssignments->filter(fn($patient) => ($patient['days_admitted'] ?? 0) <= 1);
        foreach ($newAdmissions->take(2) as $patient) {
            $urgentTasks->push([
                'message' => "Initial assessment {$patient['patient_name']} di {$patient['room']} - Pasien baru",
                'time' => 'Dalam 2 jam',
                'priority' => 'normal',
                'type' => 'initial_assessment'
            ]);
        }

        // Default tasks if empty
        if ($urgentTasks->isEmpty()) {
            $urgentTasks->push([
                'message' => 'Lakukan round check semua pasien rawat inap',
                'time' => 'Setiap 2 jam',
                'priority' => 'normal',
                'type' => 'general_round'
            ]);
        }

        return $urgentTasks;
    }

    /**
     * Get current shift information
     */
    private function getCurrentShiftInfo()
    {
        $currentHour = now()->format('H');

        if ($currentHour >= 6 && $currentHour < 14) {
            $shift = ['current' => 'Pagi (06:00-14:00)', 'start' => '06:00', 'next' => '14:00 (Shift Sore)'];
        } elseif ($currentHour >= 14 && $currentHour < 22) {
            $shift = ['current' => 'Sore (14:00-22:00)', 'start' => '14:00', 'next' => '22:00 (Shift Malam)'];
        } else {
            $shift = ['current' => 'Malam (22:00-06:00)', 'start' => '22:00', 'next' => '06:00 (Shift Pagi)'];
        }

        return [
            'current_shift' => $shift['current'],
            'shift_start' => $shift['start'],
            'next_shift' => $shift['next'],
            'nurse_count' => 3,
            'patients_per_nurse' => 0
        ];
    }

    /**
     * Get nurses list optimized
     */
    private function getNurses()
    {
        return User::where('role', self::ROLE_NURSE)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Return fallback dashboard data
     */
    private function returnFallbackDashboard($errorMessage)
    {
        return view('pages.ruangan.nurse-bed-dashboard', [
            'availability' => [],
            'summary' => ['total_beds' => 0, 'occupied_beds' => 0, 'available_beds' => 0, 'occupancy_rate' => 0],
            'pendingList' => collect(),
            'nurseAssignments' => collect(),
            'urgentTasks' => collect([
                ['message' => 'Sistem sedang maintenance - Data akan segera tersedia', 'time' => 'Sekarang', 'priority' => 'normal', 'type' => 'system']
            ]),
            'shiftInfo' => ['current_shift' => 'N/A', 'shift_start' => 'N/A', 'next_shift' => 'N/A', 'nurse_count' => 0, 'patients_per_nurse' => 0],
            'roomPatients' => collect(),
            'allNurses' => collect()
        ])->with('error', 'Terjadi kesalahan saat memuat data: ' . $errorMessage);
    }

    /**
     * Optimized get occupied patients with single query
     */
    public function getOccupiedPatients()
    {
        try {
            $occupiedPatients = $this->getActiveAdmissions()->map(function ($admission) {
                return [
                    'room_number' => optional($admission->room)->no_kamar ?? 'N/A',
                    'category_name' => optional(optional($admission->room)->category)->name ?? 'N/A',
                    'class' => optional($admission->room)->class ?? 'Umum',
                    'patient_name' => optional($admission->patient)->name ?? 'N/A',
                    'gender' => optional($admission->patient)->jenis_kelamin ?? 'N/A',
                    'age' => $this->calculateAge($admission->patient),
                    'medical_record' => optional($admission->patient)->rekam_medis ?? 'N/A',
                    'doctor_name' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                    'admission_date' => $admission->admission_date,
                    'admission_reason' => $admission->admission_reason ?? 'N/A',
                    'status' => $this->mapAdmissionStatus($admission->status ?? 'active'),
                ];
            });

            return $this->jsonResponse(true, 'Data pasien berhasil diambil', $occupiedPatients);
        } catch (\Exception $e) {
            Log::error('Error in getOccupiedPatients: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error mengambil data pasien: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Optimized patient detail with eager loading
     */
    public function getPatientDetail($roomNumber, $patientId = null)
    {
        try {
            $query = InpatientAdmission::with([
                'room:id,no_kamar,class,capacity,category_id',
                'room.category:id,name',
                'patient:id,name,rekam_medis,tgl_lahir,jenis_kelamin',
                'doctor:id,name',
                'encounter:id,type,status'
            ])
                ->whereNull('discharge_date')
                ->whereHas('room', fn($q) => $q->where('no_kamar', $roomNumber));

            if ($patientId) {
                $query->where('id', $patientId);
            }

            $admission = $query->first();

            if (!$admission) {
                return $this->jsonResponse(false, 'Patient not found in room ' . $roomNumber, [], 404);
            }

            $patientDetail = [
                'id' => $admission->id,
                'patient_name' => optional($admission->patient)->name ?? 'N/A',
                'medical_record' => optional($admission->patient)->rekam_medis ?? 'N/A',
                'age' => $this->calculateAge($admission->patient),
                'gender' => optional($admission->patient)->jenis_kelamin ?? 'N/A',
                'room_number' => optional($admission->room)->no_kamar ?? 'N/A',
                'room_category' => optional(optional($admission->room)->category)->name ?? 'N/A',
                'room_class' => optional($admission->room)->class ?? 'Umum',
                'doctor_name' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'days_admitted' => $admission->admission_date ? floor(Carbon::parse($admission->admission_date)->diffInDays(now(), true)) : 0,
                'admission_reason' => $admission->admission_reason ?? 'N/A',
                'condition' => $this->mapAdmissionStatus($admission->status ?? 'active'),
                'vital_signs_history' => $this->getVitalSignsHistory($admission->id),
                'nursing_notes' => $this->getNursingNotesHistory($admission->id)
            ];

            return $this->jsonResponse(true, 'Patient detail retrieved successfully', $patientDetail);
        } catch (\Exception $e) {
            Log::error('Error in getPatientDetail: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error retrieving patient detail: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get admission data by ID
     */
    public function getAdmissionData($admissionId)
    {
        try {
            $admission = InpatientAdmission::with(['patient', 'encounter', 'room', 'doctor'])
                ->findOrFail($admissionId);

            return $this->jsonResponse(true, 'Admission data retrieved successfully', [
                'id' => $admission->id,
                'encounter_id' => $admission->encounter_id,
                'encounter_no' => $admission->encounter->no_encounter ?? 'N/A',
                'patient_name' => optional($admission->patient)->name ?? 'N/A',
                'medical_record' => optional($admission->patient)->rekam_medis ?? 'N/A',
                'room' => optional($admission->room)->no_kamar ?? 'N/A',
                'doctor' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                'admission_date' => $admission->admission_date
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getAdmissionData: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error retrieving admission data: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Assign room to pending admission (PERAWAT)
     */
    public function assignRoomToAdmission(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|exists:inpatient_admissions,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'bed_number' => 'nullable|string|max:10'
        ]);

        try {
            $admission = InpatientAdmission::findOrFail($request->admission_id);

            // Check if already assigned
            if ($admission->ruangan_id) {
                return $this->jsonResponse(false, 'Pasien sudah memiliki ruangan: ' . optional($admission->room)->no_kamar, [], 400);
            }

            // Check bed availability
            $room = Ruangan::findOrFail($request->ruangan_id);
            $currentOccupancy = InpatientAdmission::where('ruangan_id', $room->id)
                ->whereNull('discharge_date')
                ->count();

            if ($currentOccupancy >= $room->capacity) {
                return $this->jsonResponse(false, 'Ruangan ' . $room->no_kamar . ' sudah penuh (kapasitas: ' . $room->capacity . ')', [], 400);
            }

            // Assign room
            $admission->update([
                'ruangan_id' => $request->ruangan_id,
                'bed_number' => $request->bed_number ?: '-',
                'admission_date' => now() // Set tanggal masuk saat ditempat
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'subject' => 'Assign Ruangan Rawat Inap',
                'module' => 'rawat_inap',
                'method' => 'POST',
                'url' => $request->fullUrl(),
                'route_name' => $request->route()->getName(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 200,
                'payload' => [
                    'admission_id' => $admission->id,
                    'patient' => optional($admission->patient)->name,
                    'room' => $room->no_kamar,
                    'bed_number' => $request->bed_number,
                    'assigned_by' => Auth::user()->name
                ]
            ]);

            return $this->jsonResponse(true, 'Ruangan berhasil di-assign ke pasien ' . optional($admission->patient)->name, [
                'admission_id' => $admission->id,
                'room' => $room->no_kamar,
                'bed_number' => $request->bed_number
            ]);
        } catch (\Exception $e) {
            Log::error('Error in assignRoomToAdmission: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error assigning room: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get patients in a specific room
     */
    public function getRoomPatients($roomId)
    {
        try {
            $room = Ruangan::with('category')->findOrFail($roomId);

            $patients = InpatientAdmission::with([
                'patient:id,name,rekam_medis,tgl_lahir,jenis_kelamin,alamat,no_hp,is_kerabat_owner,is_kerabat_dokter,is_kerabat_karyawan',
                'doctor:id,name',
                'encounter:id,type,status,jenis_jaminan',
                'encounter.jenisJaminan:id,name'
            ])
                ->where('ruangan_id', $roomId)
                ->whereNull('discharge_date')
                ->get()
                ->map(function ($admission) {
                    $admissionDate = Carbon::parse($admission->admission_date);
                    $now = now();

                    // Calculate duration
                    $diffInHours = (int) $admissionDate->diffInHours($now);
                    $diffInDays = (int) $admissionDate->diffInDays($now);

                    if ($diffInHours < 24) {
                        $daysStayed = $diffInHours . ' jam';
                    } elseif ($diffInDays == 1) {
                        $daysStayed = '1 hari';
                    } else {
                        $daysStayed = $diffInDays . ' hari';
                    }

                    // Determine kerabat type
                    $kerabatType = 'Reguler';
                    if ($admission->patient->is_kerabat_owner) {
                        $kerabatType = 'Owner';
                    } elseif ($admission->patient->is_kerabat_dokter) {
                        $kerabatType = 'Dokter';
                    } elseif ($admission->patient->is_kerabat_karyawan) {
                        $kerabatType = 'Karyawan';
                    }

                    return [
                        'admission_id' => $admission->id,
                        'patient_name' => $admission->patient->name,
                        'rekam_medis' => $admission->patient->rekam_medis,
                        'gender' => $admission->patient->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan',
                        'age' => Carbon::parse($admission->patient->tgl_lahir)->age . ' tahun',
                        'birth_date' => Carbon::parse($admission->patient->tgl_lahir)->format('d/m/Y'),
                        'phone' => $admission->patient->no_hp,
                        'address' => $admission->patient->alamat,
                        'doctor' => $admission->doctor->name ?? '-',
                        'jaminan' => $admission->encounter->jenisJaminan->name ?? '-',
                        'bed_number' => $admission->bed_number,
                        'admission_date' => $admissionDate->format('d/m/Y H:i'),
                        'days_stayed' => $daysStayed,
                        'kerabat_type' => $kerabatType
                    ];
                });

            return $this->jsonResponse(true, 'Room patients retrieved', [
                'room' => [
                    'id' => $room->id,
                    'number' => $room->no_kamar,
                    'category' => $room->category->name,
                    'class' => 'Kelas ' . $room->class,
                    'capacity' => $room->capacity,
                    'occupied' => $patients->count()
                ],
                'patients' => $patients
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getRoomPatients: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error retrieving room patients: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get all inpatients for vital signs monitoring
     */
    public function getAllInpatients()
    {
        try {
            $query = InpatientAdmission::with([
                'patient:id,name,rekam_medis,tgl_lahir,jenis_kelamin,is_kerabat_owner,is_kerabat_dokter,is_kerabat_karyawan',
                'doctor:id,name',
                'room:id,no_kamar',
                'encounter:id,jenis_jaminan',
                'encounter.jenisJaminan:id,name'
            ])
                ->whereNotNull('ruangan_id')
                ->whereNull('discharge_date');

            // Filter berdasarkan role user
            $currentUser = Auth::user();
            if ($currentUser->role == 2) { // Dokter
                $query->where('dokter_id', $currentUser->id);
            }
            // Owner (role 1) bisa lihat semua pasien

            $patients = $query->orderBy('admission_date', 'desc')
                ->get()
                ->map(function ($admission) {
                    $admissionDate = Carbon::parse($admission->admission_date);
                    $now = now();

                    // Calculate duration
                    $diffInHours = (int) $admissionDate->diffInHours($now);
                    $diffInDays = (int) $admissionDate->diffInDays($now);

                    if ($diffInHours < 24) {
                        $daysStayed = $diffInHours . ' jam';
                    } elseif ($diffInDays == 1) {
                        $daysStayed = '1 hari';
                    } else {
                        $daysStayed = $diffInDays . ' hari';
                    }

                    // Determine kerabat type
                    $kerabatType = 'Reguler';
                    if ($admission->patient->is_kerabat_owner) {
                        $kerabatType = 'Owner';
                    } elseif ($admission->patient->is_kerabat_dokter) {
                        $kerabatType = 'Dokter';
                    } elseif ($admission->patient->is_kerabat_karyawan) {
                        $kerabatType = 'Karyawan';
                    }

                    return [
                        'admission_id' => $admission->id,
                        'encounter_id' => $admission->encounter_id,
                        'patient_name' => $admission->patient->name,
                        'rekam_medis' => $admission->patient->rekam_medis,
                        'gender' => $admission->patient->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan',
                        'age' => Carbon::parse($admission->patient->tgl_lahir)->age . ' tahun',
                        'doctor' => $admission->doctor->name ?? '-',
                        'doctor_name' => $admission->doctor->name ?? '-',
                        'jaminan' => $admission->encounter->jenisJaminan->name ?? '-',
                        'room_number' => $admission->room->no_kamar ?? '-',
                        'bed_number' => $admission->bed_number ?? '-',
                        'admission_date' => $admissionDate->format('d/m/Y H:i'),
                        'days_stayed' => $daysStayed,
                        'kerabat_type' => $kerabatType
                    ];
                });

            return $this->jsonResponse(true, 'All inpatients retrieved', $patients);
        } catch (\Exception $e) {
            Log::error('Error in getAllInpatients: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error retrieving inpatients: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Store vital signs
     */
    public function storeVitalSigns(Request $request)
    {
        try {
            $validated = $request->validate([
                'admission_id' => 'required|exists:inpatient_admissions,id',
                'blood_pressure_systolic' => 'required|numeric|min:0|max:300',
                'blood_pressure_diastolic' => 'required|numeric|min:0|max:200',
                'heart_rate' => 'required|numeric|min:0|max:300',
                'temperature' => 'required|numeric|min:30|max:45',
                'respiratory_rate' => 'required|numeric|min:0|max:100',
                'oxygen_saturation' => 'required|numeric|min:0|max:100',
                'consciousness_level' => 'required|string',
                'notes' => 'nullable|string'
            ]);

            // Create vital sign record
            $vitalSign = VitalSign::create([
                'admission_id' => $validated['admission_id'],
                'recorded_by_id' => Auth::id(),
                'measurement_time' => now(),
                'blood_pressure_systolic' => $validated['blood_pressure_systolic'],
                'blood_pressure_diastolic' => $validated['blood_pressure_diastolic'],
                'heart_rate' => $validated['heart_rate'],
                'temperature' => $validated['temperature'],
                'respiratory_rate' => $validated['respiratory_rate'],
                'oxygen_saturation' => $validated['oxygen_saturation'],
                'consciousness_level' => $validated['consciousness_level'],
                'notes' => $validated['notes']
            ]);

            // Check for abnormal values and prepare warnings
            $warnings = [];

            // Blood pressure
            if ($validated['blood_pressure_systolic'] < 90 || $validated['blood_pressure_systolic'] > 140) {
                $warnings[] = '⚠️ Tekanan sistolik abnormal: ' . $validated['blood_pressure_systolic'] . ' mmHg (Normal: 90-140)';
            }
            if ($validated['blood_pressure_diastolic'] < 60 || $validated['blood_pressure_diastolic'] > 90) {
                $warnings[] = '⚠️ Tekanan diastolik abnormal: ' . $validated['blood_pressure_diastolic'] . ' mmHg (Normal: 60-90)';
            }

            // Heart rate
            if ($validated['heart_rate'] < 60 || $validated['heart_rate'] > 100) {
                $warnings[] = '⚠️ Nadi abnormal: ' . $validated['heart_rate'] . ' x/menit (Normal: 60-100)';
            }

            // Temperature
            if ($validated['temperature'] < 36.0 || $validated['temperature'] > 37.5) {
                $warnings[] = '⚠️ Suhu abnormal: ' . $validated['temperature'] . ' °C (Normal: 36.0-37.5)';
            }

            // Respiratory rate
            if ($validated['respiratory_rate'] < 12 || $validated['respiratory_rate'] > 20) {
                $warnings[] = '⚠️ Pernapasan abnormal: ' . $validated['respiratory_rate'] . ' x/menit (Normal: 12-20)';
            }

            // Oxygen saturation
            if ($validated['oxygen_saturation'] < 95) {
                $warnings[] = '⚠️ Saturasi oksigen rendah: ' . $validated['oxygen_saturation'] . '% (Normal: >95)';
            }

            // Consciousness level
            if ($validated['consciousness_level'] !== 'Compos Mentis') {
                $warnings[] = '⚠️ Kesadaran menurun: ' . $validated['consciousness_level'];
            }

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'subject' => 'Vital Signs Recorded',
                'module' => 'Rawat Inap',
                'method' => 'POST',
                'url' => $request->fullUrl(),
                'route_name' => $request->route()->getName(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 200,
                'payload' => json_encode([
                    'admission_id' => $validated['admission_id'],
                    'vital_sign_id' => $vitalSign->id,
                    'abnormal_count' => count($warnings)
                ])
            ]);

            return $this->jsonResponse(true, 'Vital signs saved successfully', [
                'vital_sign' => $vitalSign,
                'warnings' => $warnings
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->jsonResponse(false, 'Validation error', ['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in storeVitalSigns: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error saving vital signs: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Optimized nursing note creation
     */
    public function addNursingNote(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|exists:inpatient_admissions,id',
            'note' => 'required|string|max:1000',
            'note_type' => 'nullable|string|in:observation,medication,procedure,general',
            'priority' => 'nullable|string|in:low,normal,high,urgent'
        ]);

        try {
            $nursingNote = NursingCareRecord::create([
                'admission_id' => $request->admission_id,
                'nurse_id' => Auth::id(),
                'note' => $request->note,
                'note_type' => $request->note_type ?? 'general',
                'priority' => $request->priority ?? 'normal',
                'recorded_at' => now()
            ]);

            return $this->jsonResponse(true, 'Nursing note added successfully', [
                'id' => $nursingNote->id,
                'nurse_name' => Auth::user()->name,
                'note' => $nursingNote->note,
                'recorded_at' => $nursingNote->recorded_at->format('Y-m-d H:i:s')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in addNursingNote: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error adding nursing note: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Optimized vital signs recording
     */
    public function recordVitalSigns(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|exists:inpatient_admissions,id',
            'measurement_time' => 'required|date',
            'blood_pressure_systolic' => 'nullable|numeric|min:50|max:300',
            'blood_pressure_diastolic' => 'nullable|numeric|min:30|max:200',
            'heart_rate' => 'nullable|numeric|min:30|max:200',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'respiratory_rate' => 'nullable|numeric|min:5|max:60',
            'oxygen_saturation' => 'nullable|numeric|min:70|max:100',
            'consciousness_level' => 'nullable|string|in:Compos Mentis,Apatis,Somnolent,Sopor,Coma',
            'notes' => 'nullable|string|max:1000'
        ]);

        if (!in_array(Auth::user()->role, self::AUTHORIZED_ROLES)) {
            return $this->jsonResponse(false, 'Unauthorized role.', [], 403);
        }

        try {
            $vitalSign = VitalSign::create(array_merge(
                $request->only([
                    'admission_id',
                    'measurement_time',
                    'blood_pressure_systolic',
                    'blood_pressure_diastolic',
                    'heart_rate',
                    'temperature',
                    'respiratory_rate',
                    'oxygen_saturation',
                    'consciousness_level',
                    'notes'
                ]),
                ['recorded_by_id' => Auth::id()]
            ));

            return $this->jsonResponse(true, 'Vital signs recorded successfully', [
                'blood_pressure' => $this->formatBloodPressure($vitalSign->blood_pressure_systolic, $vitalSign->blood_pressure_diastolic),
                'heart_rate' => $vitalSign->heart_rate ? $vitalSign->heart_rate . ' bpm' : null,
                'temperature' => $vitalSign->temperature ? $vitalSign->temperature . '°C' : null,
                'recorded_at' => now()->format('Y-m-d H:i:s')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in recordVitalSigns: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error recording vital signs: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Optimized patient transfer
     */
    public function transferPatient(Request $request)
    {
        if (!in_array(Auth::user()->role, self::AUTHORIZED_ROLES)) {
            return $this->jsonResponse(false, 'Unauthorized', [], 403);
        }

        $request->validate([
            'patient_id' => 'required|exists:inpatient_admissions,id',
            'from_room' => 'required|string',
            'to_room' => 'required|string|different:from_room'
        ]);

        try {
            DB::beginTransaction();

            $admission = InpatientAdmission::with(['room', 'patient'])
                ->where('id', $request->patient_id)
                ->whereNull('discharge_date')
                ->first();

            if (!$admission || $admission->room->no_kamar !== $request->from_room) {
                DB::rollBack();
                return $this->jsonResponse(false, 'Patient not found in specified room', [], 404);
            }

            $destinationRoom = Ruangan::where('no_kamar', $request->to_room)->first();
            if (!$destinationRoom) {
                DB::rollBack();
                return $this->jsonResponse(false, 'Destination room not found', [], 404);
            }

            // Check capacity
            $currentOccupancy = InpatientAdmission::where('ruangan_id', $destinationRoom->id)
                ->whereNull('discharge_date')->count();

            if ($currentOccupancy >= ($destinationRoom->capacity ?? 1)) {
                DB::rollBack();
                return $this->jsonResponse(false, 'Destination room is at full capacity', [], 400);
            }

            // Transfer
            $admission->update([
                'ruangan_id' => $destinationRoom->id,
                'transfer_date' => now(),
                'transfer_notes' => 'Transferred by ' . Auth::user()->name
            ]);

            DB::commit();

            return $this->jsonResponse(true, 'Patient successfully transferred', [
                'patient_name' => $admission->patient->name,
                'from_room' => $request->from_room,
                'to_room' => $request->to_room,
                'transferred_by' => Auth::user()->name
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in transferPatient: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error transferring patient: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Discharge patient (fixed method)
     */
    public function dischargePatient($admissionId)
    {
        if (!in_array(Auth::user()->role, [self::ROLE_OWNER, self::ROLE_ADMIN])) {
            return $this->jsonResponse(false, 'Unauthorized', [], 403);
        }

        try {
            $admission = InpatientAdmission::with(['patient', 'room'])->findOrFail($admissionId);

            if ($admission->discharge_date) {
                return $this->jsonResponse(false, 'Patient already discharged on: ' . $admission->discharge_date);
            }

            $admission->update(['discharge_date' => now()]);

            return $this->jsonResponse(true, 'Patient discharged successfully', [
                'patient_name' => optional($admission->patient)->name ?? 'N/A',
                'room_number' => optional($admission->room)->no_kamar ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'discharge_date' => $admission->discharge_date
            ]);
        } catch (\Exception $e) {
            Log::error('Error in dischargePatient: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error discharging patient: ' . $e->getMessage(), [], 500);
        }
    }

    // Helper Methods
    private function calculateAge($patient)
    {
        if (!$patient || !$patient->tgl_lahir) return 'N/A';

        try {
            return Carbon::parse($patient->tgl_lahir)->age;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get vital signs history (public endpoint)
     */
    public function getVitalSignsHistory($admissionId)
    {
        try {
            $vitalSigns = VitalSign::with('recordedBy:id,name')
                ->where('admission_id', $admissionId)
                ->orderBy('measurement_time', 'desc')
                ->get()
                ->map(function ($vs) {
                    return [
                        'id' => $vs->id,
                        'measurement_time' => $vs->measurement_time ? $vs->measurement_time->format('d/m/Y H:i') : 'N/A',
                        'blood_pressure_systolic' => $vs->blood_pressure_systolic,
                        'blood_pressure_diastolic' => $vs->blood_pressure_diastolic,
                        'heart_rate' => $vs->heart_rate,
                        'temperature' => number_format($vs->temperature, 1),
                        'respiratory_rate' => $vs->respiratory_rate,
                        'oxygen_saturation' => $vs->oxygen_saturation,
                        'consciousness_level' => $vs->consciousness_level,
                        'notes' => $vs->notes,
                        'recorded_by' => $vs->recordedBy->name ?? '-'
                    ];
                });

            return $this->jsonResponse(true, 'Vital signs history retrieved', [
                'vital_signs' => $vitalSigns
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getVitalSignsHistory: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error retrieving vital signs history: ' . $e->getMessage(), [], 500);
        }
    }

    private function getNursingNotesHistory($admissionId)
    {
        try {
            return NursingCareRecord::with('nurse:id,name')
                ->where('admission_id', $admissionId)
                ->orderBy('recorded_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'note' => $note->note ?? $note->interventions ?? 'N/A',
                        'note_type' => $note->note_type ?? 'general',
                        'priority' => $note->priority ?? 'normal',
                        'nurse_name' => optional($note->nurse)->name ?? 'N/A',
                        'recorded_at' => $note->recorded_at ? $note->recorded_at->format('Y-m-d H:i:s') : 'N/A',
                    ];
                });
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function formatBloodPressure($systolic, $diastolic)
    {
        if (!$systolic && !$diastolic) return null;
        if ($systolic && $diastolic) return $systolic . '/' . $diastolic . ' mmHg';
        return ($systolic ?: '-') . '/' . ($diastolic ?: '-') . ' mmHg';
    }

    private function mapAdmissionStatus($status)
    {
        $statusMap = [
            'active' => 'active',
            'stable' => 'stable',
            'critical' => 'critical',
            'observation' => 'observation',
            'recovery' => 'recovery',
            'discharged' => 'discharged',
            'improving' => 'stable',
            'monitoring' => 'stable'
        ];

        return $statusMap[$status] ?? 'active';
    }

    private function jsonResponse($success, $message, $data = [], $status = 200)
    {
        $response = ['success' => $success, 'message' => $message];
        if (!empty($data)) $response['data'] = $data;

        return response()->json($response, $status);
    }

    /**
     * Get medication schedule based on prescription orders (for all patients)
     */
    public function getMedicationScheduleAll()
    {
        try {
            $today = Carbon::today();

            // Get active inpatient admissions with their medications
            $admissions = InpatientAdmission::with([
                'encounter.prescriptionOrders' => function ($query) use ($today) {
                    $query->where('status', '!=', 'cancelled')
                        ->whereDate('created_at', '>=', $today);
                },
                'encounter.prescriptionOrders.medications.administrations' => function ($query) use ($today) {
                    $query->whereDate('administered_at', $today);
                },
                'encounter.prescriptionOrders.doctor',
                'ruangan'
            ])
                ->where('status', 'active')
                ->whereNull('discharge_date')
                ->get();

            // Process medication schedule
            $groupedSchedule = $admissions->map(function ($admission) use ($today) {
                $medications = [];

                foreach ($admission->encounter->prescriptionOrders as $order) {
                    foreach ($order->medications as $medication) {
                        $scheduledTimes = $medication->scheduled_times ?? [];

                        foreach ($scheduledTimes as $time) {
                            // Find administration for this time today
                            $administration = $medication->administrations
                                ->where('admission_id', $admission->id)
                                ->whereDate('administered_at', $today)
                                ->where('administered_at', 'like', '%' . $time . '%')
                                ->first();

                            $medications[] = [
                                'prescription_order_id' => $order->id,
                                'prescription_medication_id' => $medication->id,
                                'medication_name' => $medication->medication_name,
                                'dosage' => $medication->dosage,
                                'route' => $medication->route,
                                'scheduled_time' => $time,
                                'pharmacy_status' => $order->pharmacy_status,
                                'doctor_name' => $order->doctor->name ?? 'Unknown',
                                'status' => $administration->status ?? 'Pending',
                                'administered_at' => $administration->administered_at ?? null,
                                'administration_notes' => $administration->notes ?? null
                            ];
                        }
                    }
                }

                return [
                    'admission_id' => $admission->id,
                    'patient_name' => $admission->encounter->name_pasien,
                    'medical_record' => $admission->encounter->rekam_medis,
                    'room_number' => $admission->ruangan->nama_ruangan ?? 'Unknown',
                    'bed_number' => $admission->bed_number,
                    'medications' => $medications
                ];
            })->reject(function ($patient) {
                return empty($patient['medications']);
            })->values();

            return $this->jsonResponse(true, 'Medication schedule loaded successfully', $groupedSchedule);
        } catch (\Exception $e) {
            Log::error('Error in getMedicationSchedule: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to load medication schedule: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get prescription order details
     */
    public function getPrescriptionOrder($prescriptionOrderId)
    {
        try {
            $prescription = DB::table('prescription_orders as po')
                ->join('prescription_medications as pm', 'po.id', '=', 'pm.prescription_order_id')
                ->join('users as doctor', 'po.doctor_id', '=', 'doctor.id')
                ->where('po.id', $prescriptionOrderId)
                ->select([
                    'po.id as prescription_order_id',
                    'pm.medication_name',
                    'pm.dosage',
                    'pm.route',
                    'pm.frequency',
                    'pm.instructions',
                    'po.pharmacy_status',
                    'doctor.name as doctor_name',
                    'po.created_at as prescribed_at'
                ])
                ->first();

            if (!$prescription) {
                return $this->jsonResponse(false, 'Prescription order not found', [], 404);
            }

            return $this->jsonResponse(true, 'Prescription order loaded successfully', $prescription);
        } catch (\Exception $e) {
            Log::error('Error in getPrescriptionOrder: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to load prescription order: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Record medication administration
     */
    public function recordMedicationAdministration(Request $request)
    {
        try {
            $request->validate([
                'prescription_medication_id' => 'required|exists:prescription_medications,id',
                'admission_id' => 'required|exists:inpatient_admissions,id',
                'actual_given_time' => 'required|date',
                'administration_status' => 'required|in:Given,Given Late,Refused,Held,Not Available,Patient NPO,Patient Sleeping',
                'administration_notes' => 'nullable|string|max:1000'
            ]);

            // Get prescription medication details
            $prescriptionMed = PrescriptionMedication::with('prescriptionOrder')->find($request->prescription_medication_id);

            if (!$prescriptionMed) {
                return $this->jsonResponse(false, 'Prescription medication not found', [], 404);
            }

            // Check if already administered for this time
            $existingAdmin = MedicationAdministration::where('prescription_medication_id', $request->prescription_medication_id)
                ->where('admission_id', $request->admission_id)
                ->whereDate('administered_at', Carbon::parse($request->actual_given_time)->toDateString())
                ->whereTime('administered_at', Carbon::parse($request->actual_given_time)->toTimeString())
                ->first();

            if ($existingAdmin) {
                return $this->jsonResponse(false, 'Medication already recorded for this time', [], 400);
            }

            // Record medication administration
            $administration = MedicationAdministration::create([
                'prescription_medication_id' => $request->prescription_medication_id,
                'admission_id' => $request->admission_id,
                'nurse_id' => Auth::id(),
                'administered_at' => $request->actual_given_time,
                'status' => $request->administration_status,
                'notes' => $request->administration_notes
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'medication_administration',
                'description' => "Recorded medication administration: {$prescriptionMed->medication_name} {$prescriptionMed->dosage} - Status: {$request->administration_status}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->jsonResponse(true, 'Medication administration recorded successfully', [
                'administration_id' => $administration->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error in recordMedicationAdministration: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to record medication administration: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get pending medications for specific patient
     */
    public function getPatientPendingMedications($admissionId)
    {
        try {
            $today = Carbon::today();
            $currentTime = Carbon::now()->format('H:i');

            $pendingMedications = DB::table('inpatient_admissions as ia')
                ->join('encounters as e', 'ia.encounter_id', '=', 'e.id')
                ->join('prescription_orders as po', 'e.id', '=', 'po.encounter_id')
                ->join('prescription_medications as pm', 'po.id', '=', 'pm.prescription_order_id')
                ->leftJoin('medication_administrations as ma', function ($join) use ($today) {
                    $join->on('pm.id', '=', 'ma.prescription_medication_id')
                        ->whereDate('ma.administered_at', $today);
                })
                ->leftJoin('users as doctor', 'po.doctor_id', '=', 'doctor.id')
                ->where('ia.admission_id', $admissionId)
                ->where('po.status', '!=', 'cancelled')
                ->where('po.pharmacy_status', 'Verified')
                ->whereNull('ma.id') // Not yet administered today
                ->select([
                    'po.id as prescription_order_id',
                    'pm.medication_name',
                    'pm.dosage',
                    'pm.route',
                    'pm.scheduled_times',
                    'doctor.name as doctor_name'
                ])
                ->get();

            $formattedMedications = [];

            foreach ($pendingMedications as $med) {
                $scheduledTimes = json_decode($med->scheduled_times ?? '[]', true);

                foreach ($scheduledTimes as $time) {
                    // Only show medications that are due or overdue
                    if ($time <= $currentTime) {
                        $formattedMedications[] = [
                            'prescription_order_id' => $med->prescription_order_id,
                            'medication_name' => $med->medication_name,
                            'dosage' => $med->dosage,
                            'route' => $med->route,
                            'scheduled_time' => $time,
                            'doctor_name' => $med->doctor_name
                        ];
                    }
                }
            }

            return $this->jsonResponse(true, 'Pending medications loaded successfully', $formattedMedications);
        } catch (\Exception $e) {
            Log::error('Error in getPatientPendingMedications: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to load pending medications: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Store new prescription order
     */
    public function storePrescriptionOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'encounter_id' => 'required|exists:encounters,id',
                'doctor_id' => 'required|exists:users,id',
                'diagnosis' => 'nullable|string',
                'notes' => 'nullable|string',
                'medications' => 'required|array|min:1',
                'medications.*.medication_name' => 'required|string',
                'medications.*.dosage' => 'required|string',
                'medications.*.route' => 'required|string',
                'medications.*.frequency' => 'required|string',
                'medications.*.scheduled_times' => 'required|array',
                'medications.*.instructions' => 'nullable|string',
                'medications.*.duration_days' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

            // Create prescription order
            $prescriptionOrder = PrescriptionOrder::create([
                'encounter_id' => $validated['encounter_id'],
                'doctor_id' => $validated['doctor_id'],
                'notes' => ($validated['diagnosis'] ?? '') . ($validated['notes'] ? "\n\nCatatan: " . $validated['notes'] : ''),
                'status' => 'active',
                'pharmacy_status' => 'Pending'
            ]);

            // Create medications
            foreach ($validated['medications'] as $medication) {
                PrescriptionMedication::create([
                    'prescription_order_id' => $prescriptionOrder->id,
                    'medication_name' => $medication['medication_name'],
                    'dosage' => $medication['dosage'],
                    'route' => $medication['route'],
                    'frequency' => $medication['frequency'],
                    'scheduled_times' => $medication['scheduled_times'],
                    'instructions' => $medication['instructions'] ?? null,
                    'duration_days' => $medication['duration_days']
                ]);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'subject' => 'Membuat resep obat baru',
                'module' => 'Prescription Order',
                'method' => 'POST',
                'url' => $request->fullUrl(),
                'route_name' => $request->route()->getName(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 200,
                'payload' => [
                    'encounter_id' => $validated['encounter_id'],
                    'doctor_id' => $validated['doctor_id'],
                    'medications_count' => count($validated['medications'])
                ]
            ]);

            DB::commit();

            return $this->jsonResponse(true, 'Prescription order created successfully', $prescriptionOrder->load('medications'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->jsonResponse(false, 'Validation error', $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storePrescriptionOrder: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to create prescription order: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get prescription orders for a patient
     */
    public function getPatientPrescriptions($encounterId)
    {
        try {
            $prescriptions = PrescriptionOrder::with([
                'doctor:id,name',
                'medications.administrations'
            ])
                ->where('encounter_id', $encounterId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($prescription) {
                    return [
                        'id' => $prescription->id,
                        'doctor_name' => $prescription->doctor->name ?? '-',
                        'status' => $prescription->status,
                        'pharmacy_status' => $prescription->pharmacy_status,
                        'notes' => $prescription->notes,
                        'created_at' => Carbon::parse($prescription->created_at)->format('d/m/Y H:i'),
                        'medications' => $prescription->medications->map(function ($med) {
                            $totalAdministrations = $med->administrations->count();
                            $completedAdministrations = $med->administrations->whereIn('status', ['Given', 'Given Late'])->count();

                            // Resolve medication name from UUID
                            $medicationName = $med->medication_name;
                            // Check if it's a UUID format
                            if ($medicationName && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $medicationName)) {
                                try {
                                    $product = ProductApotek::find($medicationName);
                                    if ($product && $product->name) {
                                        $medicationName = $product->name;
                                    } else {
                                        // Product not found - try to find by old data or mark as deleted
                                        $medicationName = '[Obat Tidak Ditemukan - ID: ' . substr($medicationName, 0, 8) . '...]';
                                    }
                                } catch (\Exception $e) {
                                    Log::error('Error resolving medication name: ' . $e->getMessage());
                                    $medicationName = '[Error Loading]';
                                }
                            }

                            return [
                                'id' => $med->id,
                                'medication_name' => $medicationName,
                                'dosage' => $med->dosage,
                                'route' => $med->route,
                                'frequency' => $med->frequency,
                                'scheduled_times' => $med->scheduled_times,
                                'instructions' => $med->instructions,
                                'duration_days' => $med->duration_days,
                                'total_administrations' => $totalAdministrations,
                                'completed_administrations' => $completedAdministrations
                            ];
                        })
                    ];
                });

            return $this->jsonResponse(true, 'Prescriptions retrieved successfully', $prescriptions);
        } catch (\Exception $e) {
            Log::error('Error in getPatientPrescriptions: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to retrieve prescriptions: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Delete prescription order (only if status is Pending)
     */
    public function deletePrescriptionOrder($id)
    {
        try {
            $prescription = PrescriptionOrder::findOrFail($id);

            // Only allow deletion if pharmacy_status is still Pending
            if ($prescription->pharmacy_status !== 'Pending') {
                return $this->jsonResponse(false, 'Resep yang sudah disiapkan tidak dapat dihapus', [], 403);
            }

            $encounterId = $prescription->encounter_id;

            DB::beginTransaction();

            // Delete related medication administrations first
            foreach ($prescription->medications as $medication) {
                $medication->administrations()->delete();
            }

            // Delete medications
            $prescription->medications()->delete();

            // Delete prescription
            $prescription->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'subject' => 'Menghapus resep obat',
                'module' => 'Prescription Order',
                'method' => 'DELETE',
                'url' => request()->fullUrl(),
                'route_name' => request()->route()->getName(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 200,
                'payload' => [
                    'prescription_id' => $id,
                    'encounter_id' => $encounterId
                ]
            ]);

            DB::commit();

            return $this->jsonResponse(true, 'Prescription deleted successfully', [
                'encounter_id' => $encounterId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in deletePrescriptionOrder: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to delete prescription: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get medication schedule for a patient
     */
    public function getMedicationSchedule($encounterId)
    {
        try {
            $today = Carbon::today();
            $schedules = [];

            $prescriptions = PrescriptionOrder::with([
                'doctor:id,name',
                'medications.administrations' => function ($query) use ($today) {
                    $query->whereDate('scheduled_time', $today);
                }
            ])
                ->where('encounter_id', $encounterId)
                ->where('status', 'active')
                ->where('pharmacy_status', 'Dispensed')
                ->get();

            foreach ($prescriptions as $prescription) {
                foreach ($prescription->medications as $medication) {
                    foreach ($medication->scheduled_times as $time) {
                        $administration = $medication->administrations->where('scheduled_time', $today->format('Y-m-d') . ' ' . $time)->first();

                        $schedules[] = [
                            'medication_id' => $medication->id,
                            'medication_name' => $medication->medication_name,
                            'dosage' => $medication->dosage,
                            'route' => $medication->route,
                            'scheduled_time' => $time,
                            'status' => $administration ? $administration->status : 'pending',
                            'administered_by' => $administration ? $administration->administeredBy->name : null,
                            'administered_at' => $administration ? Carbon::parse($administration->administered_at)->format('H:i') : null,
                            'notes' => $administration ? $administration->notes : null,
                            'doctor_name' => $prescription->doctor->name ?? '-'
                        ];
                    }
                }
            }

            // Sort by time
            usort($schedules, function ($a, $b) {
                return strcmp($a['scheduled_time'], $b['scheduled_time']);
            });

            return $this->jsonResponse(true, 'Medication schedule retrieved successfully', $schedules);
        } catch (\Exception $e) {
            Log::error('Error in getMedicationSchedule: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to retrieve medication schedule: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Update prescription order status
     */
    public function updatePrescriptionStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:active,completed,cancelled',
                'reason' => 'nullable|string'
            ]);

            $prescription = PrescriptionOrder::findOrFail($id);
            $prescription->status = $validated['status'];

            if (isset($validated['reason'])) {
                $prescription->notes = $prescription->notes . "\n\n[" . $validated['status'] . "] " . $validated['reason'];
            }

            $prescription->save();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Update status resep',
                'description' => 'Mengubah status resep ' . $id . ' menjadi ' . $validated['status']
            ]);

            return $this->jsonResponse(true, 'Prescription status updated successfully', $prescription);
        } catch (\Exception $e) {
            Log::error('Error in updatePrescriptionStatus: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to update prescription status: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get list of doctors
     */
    public function getDoctorsList()
    {
        try {
            $doctors = User::where('role', self::ROLE_DOCTOR)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return $this->jsonResponse(true, 'Doctors retrieved successfully', $doctors);
        } catch (\Exception $e) {
            Log::error('Error in getDoctorsList: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to retrieve doctors: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get medications list from pharmacy
     */
    public function getMedicationsList(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $today = Carbon::today();

            $medications = DB::table('product_apoteks')
                ->leftJoin('apotek_stoks', 'product_apoteks.id', '=', 'apotek_stoks.product_apotek_id')
                ->select(
                    'product_apoteks.id',
                    'product_apoteks.name',
                    'product_apoteks.satuan as unit',
                    'product_apoteks.stok as stock',
                    'product_apoteks.harga as price',
                    DB::raw('MIN(CASE WHEN apotek_stoks.expired_at >= CURDATE() THEN apotek_stoks.expired_at END) as nearest_valid_expiry'),
                    DB::raw('MIN(apotek_stoks.expired_at) as earliest_expiry'),
                    DB::raw('MAX(CASE WHEN apotek_stoks.expired_at >= CURDATE() THEN 1 ELSE 0 END) as has_valid_stock')
                )
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('product_apoteks.name', 'like', '%' . $search . '%')
                            ->orWhere('product_apoteks.code', 'like', '%' . $search . '%');
                    }
                })
                ->where('product_apoteks.status', 1)
                ->groupBy(
                    'product_apoteks.id',
                    'product_apoteks.name',
                    'product_apoteks.satuan',
                    'product_apoteks.stok',
                    'product_apoteks.harga'
                )
                ->orderByRaw('CASE
                    WHEN product_apoteks.stok > 0 THEN 1
                    ELSE 2
                END')
                ->orderBy('product_apoteks.name')
                ->limit(50)
                ->get()
                ->map(function ($item) use ($today) {
                    // Determine status based on stock and expiry
                    if ($item->stock <= 0) {
                        $item->status = 'out_of_stock';
                        $item->disabled = true;
                    } elseif ($item->has_valid_stock == 1) {
                        // Has stock that's not expired
                        $item->status = 'available';
                        $item->disabled = false;
                        $item->expired_date = $item->nearest_valid_expiry;
                    } elseif ($item->earliest_expiry && Carbon::parse($item->earliest_expiry)->lt($today)) {
                        // Has expiry data and all stock is expired
                        $item->status = 'expired';
                        $item->disabled = true;
                        $item->expired_date = $item->earliest_expiry;
                    } else {
                        // Stock available but no expiry data (probably no batch records yet)
                        $item->status = 'available';
                        $item->disabled = false;
                        $item->expired_date = null;
                    }

                    // Format expired date
                    if ($item->expired_date) {
                        $expiredDate = Carbon::parse($item->expired_date);
                        $item->expired_date_formatted = $expiredDate->format('d/m/Y');

                        // Check if near expiry (within 30 days)
                        $daysUntilExpiry = $today->diffInDays($expiredDate, false);
                        $item->is_near_expiry = $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
                    } else {
                        $item->expired_date_formatted = null;
                        $item->is_near_expiry = false;
                    }

                    // Clean up temporary fields
                    unset($item->nearest_valid_expiry);
                    unset($item->earliest_expiry);
                    unset($item->has_valid_stock);

                    return $item;
                });

            return $this->jsonResponse(true, 'Medications retrieved successfully', $medications);
        } catch (\Exception $e) {
            Log::error('Error in getMedicationsList: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Failed to retrieve medications: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get product name by ID
     */
    public function getProductName($id)
    {
        try {
            $product = DB::table('product_apoteks')
                ->where('id', $id)
                ->first(['name']);

            if ($product) {
                return response()->json([
                    'success' => true,
                    'name' => $product->name
                ]);
            }

            return response()->json([
                'success' => false,
                'name' => 'Unknown Product'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'name' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Record medication administration
     */
    public function recordAdministration(Request $request)
    {
        $validated = $request->validate([
            'medication_id' => 'required|exists:prescription_medications,id',
            'admission_id' => 'required', // Changed: will accept encounter_id
            'administered_at' => 'required|date',
            'status' => 'required|in:Given,Given Late,Refused,Held,Not Available,Patient NPO,Patient Sleeping',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Log untuk debug
            Log::info('Record Administration Input:', [
                'admission_id_input' => $validated['admission_id'],
                'medication_id' => $validated['medication_id']
            ]);

            // Get admission_id from encounter_id (admission_id comes as encounter_id from frontend)
            $admission = DB::table('inpatient_admissions')
                ->where('encounter_id', $validated['admission_id'])
                ->first(['id']);

            Log::info('Admission Query Result:', [
                'found' => $admission ? 'yes' : 'no',
                'admission_id' => $admission ? $admission->id : null
            ]);

            if (!$admission) {
                // Try to find admission_id directly (maybe it's already admission_id, not encounter_id)
                $admissionDirect = DB::table('inpatient_admissions')
                    ->where('id', $validated['admission_id'])
                    ->first(['id']);

                if ($admissionDirect) {
                    $admission = $admissionDirect;
                    Log::info('Found admission by direct ID');
                } else {
                    throw new \Exception('Admission tidak ditemukan untuk encounter ini');
                }
            }

            $administration = MedicationAdministration::create([
                'prescription_medication_id' => $validated['medication_id'],
                'admission_id' => $admission->id,
                'nurse_id' => auth()->id(),
                'administered_at' => $validated['administered_at'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null
            ]);

            // Log activity
            $medication = PrescriptionMedication::find($validated['medication_id']);
            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => "Pemberian obat: {$medication->medication_name} - Status: {$validated['status']}",
                'description' => json_encode([
                    'medication_id' => $validated['medication_id'],
                    'medication_name' => $medication->medication_name,
                    'administered_at' => $validated['administered_at'],
                    'status' => $validated['status']
                ]),
                'category' => 'Ruangan'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pemberian obat berhasil dicatat',
                'data' => $administration
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording administration: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pemberian obat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medication administration history
     */
    public function getMedicationHistory($medicationId)
    {
        try {
            $history = MedicationAdministration::where('prescription_medication_id', $medicationId)
                ->with('nurse:id,name')
                ->orderBy('administered_at', 'desc')
                ->get()
                ->map(function ($admin) {
                    return [
                        'id' => $admin->id,
                        'administered_at' => Carbon::parse($admin->administered_at)->format('d/m/Y H:i'),
                        'status' => $admin->status,
                        'notes' => $admin->notes,
                        'nurse_name' => $admin->nurse->name ?? 'Unknown'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting medication history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat histori: ' . $e->getMessage()
            ], 500);
        }
    }
}
