<?php

namespace App\Http\Controllers;

use App\Models\InpatientAdmission;
use App\Models\NursingCareRecord;
use App\Models\User;
use App\Models\VitalSign;
use App\Models\Ruangan;
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

            // Single query for all admission data
            $admissions = $this->getActiveAdmissions();

            $nurseAssignments = $this->mapNurseAssignments($admissions);
            $roomPatients = $this->groupPatientsByRoom($admissions);
            $urgentTasks = $this->generateUrgentTasks($nurseAssignments);
            $shiftInfo = $this->getCurrentShiftInfo();
            $allNurses = $this->getNurses();

            return view('pages.ruangan.nurse-bed-dashboard', compact(
                'availability',
                'summary',
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
     * Get all active admissions with optimized relationships
     */
    private function getActiveAdmissions()
    {
        return InpatientAdmission::with([
            'room:id,no_kamar,class,capacity,category_id',
            'room.category:id,name',
            'pasien:id,name,rekam_medis,tgl_lahir,jenis_kelamin',
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
     * Map admissions to nurse assignments format
     */
    private function mapNurseAssignments($admissions)
    {
        return $admissions->take(10)->map(function ($admission) {
            return [
                'id' => $admission->id,
                'patient_name' => optional($admission->pasien)->name ?? 'N/A',
                'room' => optional($admission->room)->no_kamar ?? 'N/A',
                'room_category' => optional($admission->room->category)->name ?? 'N/A',
                'room_class' => optional($admission->room)->class ?? 'Umum',
                'condition' => $this->mapAdmissionStatus($admission->status ?? 'active'),
                'doctor_name' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'days_admitted' => $admission->admission_date ? now()->diffInDays($admission->admission_date) : 0,
                'medical_record' => optional($admission->pasien)->rekam_medis ?? 'N/A'
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
                    'patient_name' => optional($admission->pasien)->name ?? 'N/A',
                    'condition' => $this->mapAdmissionStatus($admission->status ?? 'active'),
                    'days_admitted' => $admission->admission_date ? now()->diffInDays($admission->admission_date) : 0,
                    'medical_record' => optional($admission->pasien)->rekam_medis ?? 'N/A'
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
                    'category_name' => optional($admission->room->category)->name ?? 'N/A',
                    'class' => optional($admission->room)->class ?? 'Umum',
                    'patient_name' => optional($admission->pasien)->name ?? 'N/A',
                    'gender' => optional($admission->pasien)->jenis_kelamin ?? 'N/A',
                    'age' => $this->calculateAge($admission->pasien),
                    'medical_record' => optional($admission->pasien)->rekam_medis ?? 'N/A',
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
                'room:id,no_kamar,class,category_id',
                'room.category:id,name',
                'pasien:id,name,rekam_medis,tgl_lahir,jenis_kelamin',
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
                'patient_name' => optional($admission->pasien)->name ?? 'N/A',
                'medical_record' => optional($admission->pasien)->rekam_medis ?? 'N/A',
                'age' => $this->calculateAge($admission->pasien),
                'gender' => optional($admission->pasien)->jenis_kelamin ?? 'N/A',
                'room_number' => optional($admission->room)->no_kamar ?? 'N/A',
                'room_category' => optional($admission->room->category)->name ?? 'N/A',
                'room_class' => optional($admission->room)->class ?? 'Umum',
                'doctor_name' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'days_admitted' => $admission->admission_date ? now()->diffInDays($admission->admission_date) : 0,
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
            'consciousness_level' => 'nullable|string|in:alert,drowsy,confused,unconscious',
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

            $admission = InpatientAdmission::with(['room', 'pasien'])
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
                'patient_name' => $admission->pasien->name,
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
            $admission = InpatientAdmission::with(['pasien', 'room'])->findOrFail($admissionId);

            if ($admission->discharge_date) {
                return $this->jsonResponse(false, 'Patient already discharged on: ' . $admission->discharge_date);
            }

            $admission->update(['discharge_date' => now()]);

            return $this->jsonResponse(true, 'Patient discharged successfully', [
                'patient_name' => optional($admission->pasien)->name ?? 'N/A',
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

    private function getVitalSignsHistory($admissionId)
    {
        try {
            return VitalSign::where('admission_id', $admissionId)
                ->with('recordedBy:id,name')
                ->orderBy('measurement_time', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($vital) {
                    return [
                        'time' => $vital->measurement_time ? $vital->measurement_time->format('d/m/Y H:i') : 'N/A',
                        'summary' => "TD: {$vital->blood_pressure_systolic}/{$vital->blood_pressure_diastolic}, N: {$vital->heart_rate}, S: {$vital->temperature}°C",
                        'recorded_by' => optional($vital->recordedBy)->name ?? 'N/A',
                    ];
                });
        } catch (\Exception $e) {
            return collect();
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
}
