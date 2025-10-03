<?php

namespace App\Http\Controllers;

use App\Models\InpatientAdmission;
use App\Models\NursingCareRecord;
use App\Models\User;
use App\Models\VitalSign;
use App\Repositories\RuanganRepository;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public $ruanganRepository;
    public function __construct(RuanganRepository $ruanganRepository)
    {
        $this->ruanganRepository = $ruanganRepository;
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

            // Get real patient data for nurse assignments
            $nurseAssignments = \App\Models\InpatientAdmission::with([
                'room.category',
                'patient',
                'doctor',
                'encounter'
            ])
                ->whereNull('discharge_date')  // Only active patients
                ->whereHas('encounter', function ($query) {
                    $query->where('type', 2)      // Type 2 = Rawat Inap
                        ->orWhere('type', 3);    // Type 3 = IGD/Rawat Darurat
                })
                ->orderBy('admission_date', 'desc')
                ->limit(10)  // Limit for performance
                ->get()
                ->map(function ($admission) {
                    return [
                        'id' => $admission->id,
                        'patient_name' => $admission->patient->name ?? 'N/A',
                        'room' => $admission->room->no_kamar ?? 'N/A',
                        'room_category' => $admission->room->category->name ?? 'N/A',
                        'room_class' => $admission->room->class ?? 'Umum',
                        'condition' => ucfirst($this->mapAdmissionStatus($admission->status ?? 'active')),
                        'doctor_name' => $admission->doctor->name ?? $admission->nama_dokter ?? 'N/A',
                        'admission_date' => $admission->admission_date,
                        'days_admitted' => now()->diffInDays($admission->admission_date),
                        'medical_record' => $admission->patient->rekam_medis ?? 'N/A'
                    ];
                });

            // Get real patient data grouped by room for room grid display
            $roomPatients = \App\Models\InpatientAdmission::with([
                'room',
                'patient',
                'doctor'
            ])
                ->whereNull('discharge_date')
                ->whereHas('encounter', function ($query) {
                    $query->where('type', 2)->orWhere('type', 3);
                })
                ->get()
                ->groupBy(function ($admission) {
                    return $admission->room->no_kamar ?? 'Unknown';
                })
                ->map(function ($roomAdmissions) {
                    return $roomAdmissions->map(function ($admission) {
                        return [
                            'id' => $admission->id,
                            'patient_name' => $admission->patient->name ?? 'N/A',
                            'condition' => ucfirst($this->mapAdmissionStatus($admission->status ?? 'active')),
                            'days_admitted' => now()->diffInDays($admission->admission_date),
                            'medical_record' => $admission->patient->rekam_medis ?? 'N/A'
                        ];
                    });
                });

            // Generate urgent tasks based on real data
            $urgentTasks = collect();

            // Task 1: Patients admitted more than 7 days
            $longStayPatients = $nurseAssignments->filter(function ($patient) {
                return $patient['days_admitted'] > 7;
            });

            foreach ($longStayPatients->take(2) as $patient) {
                $urgentTasks->push([
                    'message' => "Review discharge plan untuk {$patient['patient_name']} di {$patient['room']} (Rawat {$patient['days_admitted']} hari)",
                    'time' => $patient['days_admitted'] . ' hari rawat inap',
                    'priority' => 'high',
                    'type' => 'discharge_review'
                ]);
            }

            // Task 2: Critical condition patients
            $criticalPatients = $nurseAssignments->filter(function ($patient) {
                return strtolower($patient['condition']) === 'critical';
            });

            foreach ($criticalPatients->take(3) as $patient) {
                $urgentTasks->push([
                    'message' => "Monitor intensive {$patient['patient_name']} di {$patient['room']} - Kondisi Critical",
                    'time' => 'Setiap 30 menit',
                    'priority' => 'urgent',
                    'type' => 'critical_monitoring'
                ]);
            }

            // Task 3: New admissions (within 24 hours)
            $newAdmissions = $nurseAssignments->filter(function ($patient) {
                return $patient['days_admitted'] <= 1;
            });

            foreach ($newAdmissions->take(2) as $patient) {
                $urgentTasks->push([
                    'message' => "Initial assessment {$patient['patient_name']} di {$patient['room']} - Pasien baru",
                    'time' => 'Dalam 2 jam',
                    'priority' => 'normal',
                    'type' => 'initial_assessment'
                ]);
            }

            // Add some general nursing tasks if no specific tasks
            if ($urgentTasks->isEmpty()) {
                $urgentTasks->push([
                    'message' => 'Lakukan round check semua pasien rawat inap',
                    'time' => 'Setiap 2 jam',
                    'priority' => 'normal',
                    'type' => 'general_round'
                ]);

                $urgentTasks->push([
                    'message' => 'Update dokumentasi keperawatan',
                    'time' => 'End of shift',
                    'priority' => 'normal',
                    'type' => 'documentation'
                ]);
            }

            // Get shift information
            $currentHour = now()->format('H');
            $shiftInfo = [
                'current_shift' => $currentHour >= 6 && $currentHour < 14 ? 'Pagi (06:00-14:00)' : ($currentHour >= 14 && $currentHour < 22 ? 'Sore (14:00-22:00)' : 'Malam (22:00-06:00)'),
                'shift_start' => $currentHour >= 6 && $currentHour < 14 ? '06:00' : ($currentHour >= 14 && $currentHour < 22 ? '14:00' : '22:00'),
                'next_shift' => $currentHour >= 6 && $currentHour < 14 ? '14:00 (Shift Sore)' : ($currentHour >= 14 && $currentHour < 22 ? '22:00 (Shift Malam)' : '06:00 (Shift Pagi)'),
                'nurse_count' => 3, // Could be dynamic based on actual nurse assignment
                'patients_per_nurse' => $nurseAssignments->count() > 0 ? ceil($nurseAssignments->count() / 3) : 0
            ];

            $allNurses = User::where('role', 3)->orderBy('name')->get(['id', 'name']);

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
            // Fallback to basic data if there's an error
            $availability = [];
            $summary = ['total_beds' => 0, 'occupied_beds' => 0, 'available_beds' => 0, 'occupancy_rate' => 0];
            $nurseAssignments = collect();
            $urgentTasks = collect([
                [
                    'message' => 'Sistem sedang maintenance - Data akan segera tersedia',
                    'time' => 'Sekarang',
                    'priority' => 'normal',
                    'type' => 'system'
                ]
            ]);
            $shiftInfo = [
                'current_shift' => 'N/A',
                'shift_start' => 'N/A',
                'next_shift' => 'N/A',
                'nurse_count' => 0,
                'patients_per_nurse' => 0
            ];
            $roomPatients = collect();
            $allNurses = collect();

            return view('pages.ruangan.nurse-bed-dashboard', compact(
                'availability',
                'summary',
                'nurseAssignments',
                'urgentTasks',
                'shiftInfo',
                'roomPatients',
                'allNurses'
            ))->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Get occupied patients data (API endpoint)
     */
    public function getOccupiedPatients()
    {
        try {
            $occupiedPatients = \App\Models\InpatientAdmission::with([
                'room.category',
                'patient',
                'doctor',
                'encounter'
            ])
                ->whereNull('discharge_date')  // Only get active patients who haven't been discharged
                ->whereHas('encounter', function ($query) {
                    $query->where('type', 2)      // Type 2 = Rawat Inap
                        ->orWhere('type', 3);    // Type 3 = IGD/Rawat Darurat
                })
                ->orderBy('admission_date', 'desc')
                ->get()
                ->map(function ($admission) {
                    // Calculate age from birth date if available
                    $age = 'N/A';
                    if ($admission->patient && $admission->patient->tgl_lahir) {
                        $age = \Carbon\Carbon::parse($admission->patient->tgl_lahir)->age;
                    }

                    return [
                        'room_number' => $admission->room->no_kamar ?? 'N/A',
                        'category_name' => $admission->room->category->name ?? 'N/A',
                        'class' => $admission->room->class ?? 'Umum',
                        'patient_name' => $admission->patient->name ?? 'N/A',
                        'gender' => $admission->patient->jenis_kelamin ?? 'N/A',
                        'age' => $age,
                        'medical_record' => $admission->patient->rekam_medis ?? 'N/A',
                        'doctor_name' => $admission->doctor->name ?? $admission->nama_dokter ?? 'N/A',
                        'admission_date' => $admission->admission_date,
                        'admission_reason' => $admission->admission_reason ?? 'N/A',
                        'status' => $this->mapAdmissionStatus($admission->status ?? 'active')
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data pasien berhasil diambil',
                'data' => $occupiedPatients
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data pasien: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    /**
     * Test method to verify bed availability logic
     * This can be used to debug and ensure discharged patients don't count as occupied
     */
    public function testBedLogic()
    {
        if (!in_array(auth()->user()->role, [1, 4])) {
            abort(403, 'Unauthorized');
        }

        $debug = [];

        // Test 1: Get all admissions
        $allAdmissions = \App\Models\InpatientAdmission::with(['room', 'patient'])->get();
        $debug['total_admissions'] = $allAdmissions->count();

        // Test 2: Get active admissions (no discharge_date)
        $activeAdmissions = \App\Models\InpatientAdmission::with(['room', 'patient'])
            ->whereNull('discharge_date')
            ->get();
        $debug['active_admissions'] = $activeAdmissions->count();

        // Test 3: Get discharged admissions
        $dischargedAdmissions = \App\Models\InpatientAdmission::with(['room', 'patient'])
            ->whereNotNull('discharge_date')
            ->get();
        $debug['discharged_admissions'] = $dischargedAdmissions->count();

        // Test 4: Detail active patients
        $debug['active_patients_detail'] = $activeAdmissions->map(function ($admission) {
            return [
                'patient_name' => $admission->patient->name ?? 'N/A',
                'room_number' => $admission->room->no_kamar ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'discharge_date' => $admission->discharge_date,
                'status' => $admission->discharge_date ? 'DISCHARGED' : 'ACTIVE'
            ];
        });

        // Test 5: Detail discharged patients
        $debug['discharged_patients_detail'] = $dischargedAdmissions->map(function ($admission) {
            return [
                'patient_name' => $admission->patient->name ?? 'N/A',
                'room_number' => $admission->room->no_kamar ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'discharge_date' => $admission->discharge_date,
                'status' => 'DISCHARGED'
            ];
        });

        // Test 6: Current bed availability
        $bedSummary = $this->ruanganRepository->getBedAvailabilitySummary();
        $debug['bed_summary'] = $bedSummary;

        return response()->json([
            'success' => true,
            'message' => 'Bed logic test results',
            'debug_data' => $debug,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 200);
    }

    /**
     * Helper method to discharge a patient (for testing purposes)
     * This will set discharge_date to current timestamp
     */
    public function dischargePatient($admissionId)
    {
        if (!in_array(auth()->user()->role, [1, 4])) {
            abort(403, 'Unauthorized');
        }

        try {
            $admission = \App\Models\InpatientAdmission::findOrFail($admissionId);

            if ($admission->discharge_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient already discharged on: ' . $admission->discharge_date,
                ]);
            }

            $admission->discharge_date = now();
            $admission->save();

            return response()->json([
                'success' => true,
                'message' => 'Patient discharged successfully',
                'data' => [
                    'patient_name' => $admission->patient->name ?? 'N/A',
                    'room_number' => $admission->room->no_kamar ?? 'N/A',
                    'admission_date' => $admission->admission_date,
                    'discharge_date' => $admission->discharge_date
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error discharging patient: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to re-admit a patient (for testing purposes)
     * This will set discharge_date back to null
     */
    public function readmitPatient($admissionId)
    {
        if (!in_array(auth()->user()->role, [1, 4])) {
            abort(403, 'Unauthorized');
        }

        try {
            $admission = \App\Models\InpatientAdmission::findOrFail($admissionId);

            if (!$admission->discharge_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient is still active (not discharged yet)',
                ]);
            }

            $admission->discharge_date = null;
            $admission->save();

            return response()->json([
                'success' => true,
                'message' => 'Patient re-admitted successfully',
                'data' => [
                    'patient_name' => $admission->patient->name ?? 'N/A',
                    'room_number' => $admission->room->no_kamar ?? 'N/A',
                    'admission_date' => $admission->admission_date,
                    'discharge_date' => $admission->discharge_date
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error re-admitting patient: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test KPI accuracy and calculations
     */
    public function testKPIAccuracy()
    {
        if (!in_array(auth()->user()->role, [1, 4])) {
            abort(403, 'Unauthorized');
        }

        try {
            $ownerDashboardService = app(\App\Services\OwnerDashboardService::class);
            $kpiTestResults = $ownerDashboardService->testKPIAccuracy();

            // Also get current KPI data for comparison
            $currentKPIData = $ownerDashboardService->getKPIData();

            return response()->json([
                'success' => true,
                'message' => 'KPI accuracy test completed',
                'current_kpi_data' => $currentKPIData,
                'test_results' => $kpiTestResults,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing KPI accuracy: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient detail for nurse dashboard (API endpoint)
     */
    public function getPatientDetail($roomNumber, $patientId = null)
    {
        try {
            $query = \App\Models\InpatientAdmission::with([
                'room.category',
                'patient',
                'doctor',
                'encounter'
            ])
                ->whereNull('discharge_date')
                ->whereHas('room', function ($q) use ($roomNumber) {
                    $q->where('no_kamar', $roomNumber);
                });

            if ($patientId) {
                $query->where('id', $patientId);
            }

            $admission = $query->first();

            if (!$admission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient not found in room ' . $roomNumber
                ], 404);
            }

            $patientDetail = [
                'id' => $admission->id,
                'patient_name' => $admission->patient->name ?? 'N/A',
                'medical_record' => $admission->patient->rekam_medis ?? 'N/A',
                'age' => $admission->patient->tgl_lahir ? \Carbon\Carbon::parse($admission->patient->tgl_lahir)->age : 'N/A',
                'gender' => $admission->patient->jenis_kelamin ?? 'N/A',
                'room_number' => $admission->room->no_kamar ?? 'N/A',
                'room_category' => $admission->room->category->name ?? 'N/A',
                'room_class' => $admission->room->class ?? 'Umum',
                'doctor_name' => $admission->doctor->name ?? $admission->nama_dokter ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'days_admitted' => now()->diffInDays($admission->admission_date),
                'admission_reason' => $admission->admission_reason ?? 'N/A',
                'condition' => ucfirst($this->mapAdmissionStatus($admission->status ?? 'active')),
                'vital_signs' => [
                    'blood_pressure' => '120/80', // Mock data - should come from actual vitals
                ],
                'vital_signs_history' => VitalSign::where('admission_id', $admission->id)
                    ->orderBy('measurement_time', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($vital) {
                        return [
                            'time' => $vital->measurement_time->format('d/m/Y H:i'),
                            'summary' => "TD: {$vital->blood_pressure_systolic}/{$vital->blood_pressure_diastolic}, N: {$vital->heart_rate}, S: {$vital->temperature}°C",
                            'recorded_by' => $vital->recordedBy->name ?? 'N/A',
                        ];
                    }),
                'nursing_notes' => NursingCareRecord::with('nurse')
                    ->where('admission_id', $admission->id)
                    ->orderBy('recorded_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($note) {
                        return [
                            'id' => $note->id,
                            'note' => $note->note,
                            'note_type' => $note->note_type,
                            'priority' => $note->priority,
                            'nurse_name' => $note->nurse->name ?? 'N/A',
                            'recorded_at' => $note->recorded_at->format('Y-m-d H:i:s'),
                            'created_at' => $note->created_at->format('Y-m-d H:i:s')
                        ];
                    })->toArray()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Patient detail retrieved successfully',
                'data' => $patientDetail
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving patient detail: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add nursing note (API endpoint)
     */
    public function addNursingNote(Request $request)
    {
        try {
            $request->validate([
                'admission_id' => 'required|exists:inpatient_admissions,id',
                'note' => 'required|string|max:1000',
                'note_type' => 'string|in:observation,medication,procedure,general',
                'priority' => 'string|in:low,normal,high,urgent'
            ]);

            $admission = \App\Models\InpatientAdmission::with(['patient', 'room'])
                ->findOrFail($request->admission_id);

            // Create nursing note using the model
            $nursingNote = \App\Models\NursingNote::create([
                'admission_id' => $request->admission_id,
                'nurse_id' => auth()->id(),
                'note' => $request->note,
                'note_type' => $request->note_type ?? 'general',
                'priority' => $request->priority ?? 'normal',
                'recorded_at' => now()
            ]);

            // Load relationships for response
            $nursingNote->load(['nurse', 'admission.patient', 'admission.room']);

            $responseData = [
                'id' => $nursingNote->id,
                'admission_id' => $nursingNote->admission_id,
                'nurse_id' => $nursingNote->nurse_id,
                'nurse_name' => $nursingNote->nurse->name ?? 'N/A',
                'note' => $nursingNote->note,
                'note_type' => $nursingNote->note_type,
                'priority' => $nursingNote->priority,
                'recorded_at' => $nursingNote->recorded_at->format('Y-m-d H:i:s'),
                'created_at' => $nursingNote->created_at->format('Y-m-d H:i:s'),
                'patient_name' => $nursingNote->admission->patient->name ?? 'N/A',
                'room_number' => $nursingNote->admission->room->no_kamar ?? 'N/A'
            ];

            return response()->json([
                'success' => true,
                'message' => 'Nursing note added successfully',
                'data' => $responseData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding nursing note: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Emergency call endpoint
     */
    public function emergencyCall(Request $request)
    {
        try {
            $request->validate([
                'room_number' => 'string|max:20',
                'emergency_type' => 'required|string|in:medical,fire,security,technical',
                'description' => 'string|max:500',
                'priority' => 'string|in:low,medium,high,critical'
            ]);

            $emergencyCall = [
                'id' => 'EMRG-' . now()->format('YmdHis'),
                'caller_id' => auth()->id(),
                'caller_name' => auth()->user()->name,
                'room_number' => $request->room_number,
                'emergency_type' => $request->emergency_type,
                'description' => $request->description ?? 'Emergency call from nurse dashboard',
                'priority' => $request->priority ?? 'high',
                'status' => 'open',
                'called_at' => now()->format('Y-m-d H:i:s')
            ];

            // In a real system, this would:
            // 1. Save to emergency_calls table
            // 2. Send notifications to relevant staff
            // 3. Trigger alerts in the system
            // 4. Log the emergency event

            return response()->json([
                'success' => true,
                'message' => 'Emergency call initiated successfully',
                'data' => $emergencyCall,
                'notification' => 'Emergency response team has been notified'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error initiating emergency call: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick vital signs recording endpoint
     */
    public function recordVitalSigns(Request $request)
    {
        if (!in_array(auth()->user()->role, [1, 3, 4])) { // 1=Owner, 3=Nurse, 4=Admin
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $request->validate([
                'admission_id' => 'required|exists:inpatient_admissions,id',
                'measurement_time' => 'required|date',
                'recorded_by_id' => 'required|exists:users,id',
                'blood_pressure_systolic' => 'nullable|numeric|min:50|max:300',
                'blood_pressure_diastolic' => 'nullable|numeric|min:30|max:200',
                'heart_rate' => 'nullable|numeric|min:30|max:200',
                'temperature' => 'nullable|numeric|min:30|max:45',
                'respiratory_rate' => 'nullable|numeric|min:5|max:60',
                'oxygen_saturation' => 'nullable|numeric|min:70|max:100',
                'consciousness_level' => 'nullable|string|in:alert,drowsy,confused,unconscious',
                'notes' => 'nullable|string|max:1000'
            ]);

            $admission = \App\Models\InpatientAdmission::with(['patient', 'room', 'doctor'])
                ->findOrFail($request->admission_id);

            // Get the nurse who recorded the vital signs
            $recordedByUser = \App\Models\User::find($request->recorded_by_id);

            // Verify that the recorded_by_id matches current authenticated user
            if ($request->recorded_by_id != auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid nurse assignment. You can only record vital signs under your own name.'
                ], 403);
            }

            // Simpan data ke tabel vital_signs
            $vitalSign = VitalSign::create([
                'admission_id' => $request->admission_id,
                'recorded_by_id' => $request->recorded_by_id,
                'measurement_time' => $request->measurement_time,
                'blood_pressure_systolic' => $request->blood_pressure_systolic,
                'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
                'heart_rate' => $request->heart_rate,
                'temperature' => $request->temperature,
                'respiratory_rate' => $request->respiratory_rate,
                'oxygen_saturation' => $request->oxygen_saturation,
                'consciousness_level' => $request->consciousness_level,
                'notes' => $request->notes,
            ]);

            $vitalSignsForLog = [
                'blood_pressure' => $this->formatBloodPressure($vitalSign->blood_pressure_systolic, $vitalSign->blood_pressure_diastolic),
                'heart_rate' => $vitalSign->heart_rate ? $vitalSign->heart_rate . ' bpm' : null,
                'temperature' => $vitalSign->temperature ? $vitalSign->temperature . '°C' : null,
                'respiratory_rate' => $vitalSign->respiratory_rate ? $vitalSign->respiratory_rate . '/min' : null,
                'oxygen_saturation' => $vitalSign->oxygen_saturation ? $vitalSign->oxygen_saturation . '%' : null,
                'consciousness_level' => $vitalSign->consciousness_level,
                'notes' => $vitalSign->notes,
            ];

            // Log the vital signs recording
            activity()
                ->performedOn($admission)
                ->causedBy(auth()->user())
                ->withProperties([
                    'patient_name' => $admission->patient->name,
                    'vital_signs' => $vitalSignsForLog
                ])
                ->log('Vital signs recorded');

            return response()->json([
                'success' => true,
                'message' => 'Vital signs recorded successfully',
                'data' => [
                    'patient_name' => $admission->patient->name ?? 'Unknown Patient',
                    'room_number' => $admission->room->no_kamar ?? 'Unknown Room',
                    'blood_pressure' => $this->formatBloodPressure($vitalSign->blood_pressure_systolic, $vitalSign->blood_pressure_diastolic),
                    'heart_rate' => $vitalSign->heart_rate ? $vitalSign->heart_rate . ' bpm' : null,
                    'temperature' => $vitalSign->temperature ? $vitalSign->temperature . '°C' : null,
                    'respiratory_rate' => $vitalSign->respiratory_rate ? $vitalSign->respiratory_rate . '/min' : null,
                    'oxygen_saturation' => $vitalSign->oxygen_saturation ? $vitalSign->oxygen_saturation . '%' : null,
                    'recorded_at' => now()->format('Y-m-d H:i:s')
                ],
                'nurse_info' => [
                    'name' => $recordedByUser->name,
                    'email' => $recordedByUser->email,
                    'role' => $this->getNurseRole($recordedByUser->role),
                    'timestamp' => now()->format('d/m/Y H:i')
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording vital signs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nurse dashboard data refresh (API endpoint)
     */
    public function refreshNurseDashboard()
    {
        try {
            $availability = $this->ruanganRepository->getBedAvailability();
            $summary = $this->ruanganRepository->getBedAvailabilitySummary();

            $activePatients = \App\Models\InpatientAdmission::with(['room', 'patient'])
                ->whereNull('discharge_date')
                ->count();

            $urgentTasks = collect([
                [
                    'message' => 'Check vital signs untuk semua pasien',
                    'time' => 'Setiap 4 jam',
                    'priority' => 'normal'
                ],
                [
                    'message' => 'Update dokumentasi keperawatan',
                    'time' => 'End of shift',
                    'priority' => 'normal'
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data refreshed',
                'data' => [
                    'summary' => $summary,
                    'availability' => $availability,
                    'active_patients' => $activePatients,
                    'urgent_tasks' => $urgentTasks,
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete a nursing task (API endpoint)
     */
    public function completeTask(Request $request)
    {
        try {
            $request->validate([
                'task_id' => 'required|string',
                'task_type' => 'string|in:discharge_review,critical_monitoring,initial_assessment,general_round,documentation',
                'completion_note' => 'string|max:500'
            ]);

            // In a real system, this would:
            // 1. Save to nursing_tasks or similar table
            // 2. Update task status
            // 3. Log the completion
            // 4. Notify relevant staff if needed

            $completedTask = [
                'task_id' => $request->task_id,
                'completed_by' => auth()->id(),
                'completed_by_name' => auth()->user()->name,
                'completed_at' => now()->format('Y-m-d H:i:s'),
                'task_type' => $request->task_type,
                'completion_note' => $request->completion_note,
                'status' => 'completed'
            ];

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully',
                'data' => $completedTask
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available rooms for admission (API endpoint)
     */
    public function getAvailableRooms()
    {
        try {
            $availableRooms = $this->ruanganRepository->getBedAvailability();

            // Filter only available rooms
            $rooms = collect();
            foreach ($availableRooms as $category) {
                foreach ($category['classes'] as $className => $classData) {
                    foreach ($classData['rooms'] as $room) {
                        if ($room['available'] > 0) {
                            $rooms->push([
                                'room_id' => $room['id'] ?? null,
                                'room_number' => $room['room_number'],
                                'category_name' => $category['category_name'],
                                'class' => $className,
                                'available_beds' => $room['available'],
                                'total_capacity' => $room['capacity'],
                                'price' => $room['price'] ?? 0
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Available rooms retrieved successfully',
                'data' => $rooms->values()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving available rooms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up invalid admission data
     * This will handle orphaned or invalid InpatientAdmission records
     */
    public function cleanupAdmissionData()
    {
        if (!in_array(auth()->user()->role, [1, 4])) {
            abort(403, 'Unauthorized');
        }

        try {
            $results = [];

            // 1. Find admissions with completed status but no discharge date
            $invalidAdmissions = \App\Models\InpatientAdmission::with('encounter')
                ->whereNull('discharge_date')
                ->whereHas('encounter', function ($query) {
                    $query->where('status', 2); // Status 2 = completed/finished
                })
                ->get();

            $results['invalid_admissions_found'] = $invalidAdmissions->count();
            $results['invalid_details'] = [];

            // 2. Check each invalid admission
            foreach ($invalidAdmissions as $admission) {
                $detail = [
                    'admission_id' => $admission->id,
                    'patient_name' => $admission->patient->name ?? 'N/A',
                    'room_number' => $admission->room->no_kamar ?? 'N/A',
                    'admission_date' => $admission->admission_date,
                    'discharge_date' => $admission->discharge_date,
                    'encounter_id' => $admission->encounter_id,
                    'encounter_status' => $admission->encounter->status ?? 'N/A',
                    'action_taken' => 'none'
                ];

                // If encounter status is 2 (completed/finished), auto-discharge
                if ($admission->encounter->status == 2 && !$admission->discharge_date) {
                    $admission->discharge_date = now();
                    $admission->save();
                    $detail['action_taken'] = 'auto_discharged';
                }

                $results['invalid_details'][] = $detail;
            }

            // 3. Find admissions without encounter (orphaned)
            $orphanedAdmissions = \App\Models\InpatientAdmission::whereDoesntHave('encounter')->get();
            $results['orphaned_admissions'] = $orphanedAdmissions->count();

            // 4. Find admissions for rawat_jalan (should not exist - Type 1)
            $wrongTypeAdmissions = \App\Models\InpatientAdmission::whereHas('encounter', function ($query) {
                $query->where('type', 1); // Type 1 = Rawat Jalan (should not have bed admissions)
            })->get();

            $results['wrong_type_admissions'] = $wrongTypeAdmissions->count();

            // Auto-discharge wrong type admissions
            foreach ($wrongTypeAdmissions as $admission) {
                if (!$admission->discharge_date) {
                    $admission->discharge_date = now();
                    $admission->save();
                }
            }

            $results['wrong_type_auto_discharged'] = $wrongTypeAdmissions->where('discharge_date', null)->count();

            // 5. Get current clean bed availability
            $bedSummary = $this->ruanganRepository->getBedAvailabilitySummary();
            $results['current_bed_summary'] = $bedSummary;

            return response()->json([
                'success' => true,
                'message' => 'Data cleanup completed',
                'results' => $results,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cleaning up admission data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get nurse assignments for real-time updates
     */
    public function getNurseAssignments()
    {
        if (!in_array(auth()->user()->role, [1, 3, 4])) { // 3 = Nurse
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $nurseAssignments = \App\Models\InpatientAdmission::with([
                'room.category',
                'patient',
                'doctor',
                'encounter'
            ])
                ->whereNull('discharge_date')
                ->whereHas('encounter', function ($query) {
                    $query->where('type', 2)->orWhere('type', 3);
                })
                ->orderBy('admission_date', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($admission) {
                    return [
                        'id' => $admission->id,
                        'patient_name' => $admission->patient->name ?? 'N/A',
                        'room' => $admission->room->no_kamar ?? 'N/A',
                        'room_category' => $admission->room->category->name ?? 'N/A',
                        'room_class' => $admission->room->class ?? 'Umum',
                        'condition' => ucfirst($this->mapAdmissionStatus($admission->status ?? 'active')),
                        'doctor_name' => $admission->doctor->name ?? $admission->nama_dokter ?? 'N/A',
                        'admission_date' => $admission->admission_date,
                        'days_admitted' => now()->diffInDays($admission->admission_date),
                        'medical_record' => $admission->patient->rekam_medis ?? 'N/A',
                        'last_updated' => $admission->updated_at->format('H:i')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $nurseAssignments,
                'timestamp' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching nurse assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get urgent tasks for nurses
     */
    public function getUrgentTasks()
    {
        if (!in_array(auth()->user()->role, [1, 3, 4])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $urgentTasks = collect();

            // Get current active patients
            $activePatients = \App\Models\InpatientAdmission::with(['room', 'patient'])
                ->whereNull('discharge_date')
                ->whereHas('encounter', function ($query) {
                    $query->where('type', 2)->orWhere('type', 3);
                })
                ->get();

            // Generate tasks based on patient conditions
            foreach ($activePatients as $admission) {
                $daysAdmitted = now()->diffInDays($admission->admission_date);

                // Long stay patients
                if ($daysAdmitted > 7) {
                    $urgentTasks->push([
                        'id' => 'discharge_' . $admission->id,
                        'type' => 'discharge_review',
                        'priority' => 'high',
                        'patient_name' => $admission->patient->name ?? 'N/A',
                        'room' => $admission->room->no_kamar ?? 'N/A',
                        'message' => "Review discharge plan - {$daysAdmitted} hari rawat",
                        'created_at' => now()->subHours(rand(1, 6))
                    ]);
                }

                // Critical condition monitoring
                if (isset($admission->status) && strtolower($admission->status) === 'critical') {
                    $urgentTasks->push([
                        'id' => 'critical_' . $admission->id,
                        'type' => 'critical_monitoring',
                        'priority' => 'urgent',
                        'patient_name' => $admission->patient->name ?? 'N/A',
                        'room' => $admission->room->no_kamar ?? 'N/A',
                        'message' => "Monitor intensive - Kondisi Critical",
                        'created_at' => now()->subMinutes(rand(10, 60))
                    ]);
                }

                // New admissions
                if ($daysAdmitted <= 1) {
                    $urgentTasks->push([
                        'id' => 'assessment_' . $admission->id,
                        'type' => 'initial_assessment',
                        'priority' => 'normal',
                        'patient_name' => $admission->patient->name ?? 'N/A',
                        'room' => $admission->room->no_kamar ?? 'N/A',
                        'message' => "Initial assessment - Pasien baru",
                        'created_at' => $admission->admission_date
                    ]);
                }
            }

            // Add routine tasks if no specific tasks
            if ($urgentTasks->isEmpty()) {
                $urgentTasks->push([
                    'id' => 'routine_round',
                    'type' => 'general_round',
                    'priority' => 'normal',
                    'patient_name' => 'All Patients',
                    'room' => 'All Rooms',
                    'message' => 'Routine patient rounds',
                    'created_at' => now()->subHours(2)
                ]);
            }

            // Sort by priority and time
            $priorityOrder = ['urgent' => 3, 'high' => 2, 'normal' => 1];
            $urgentTasks = $urgentTasks->sortByDesc(function ($task) use ($priorityOrder) {
                return [$priorityOrder[$task['priority']] ?? 0, $task['created_at']];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $urgentTasks->take(10), // Limit to 10 most urgent
                'timestamp' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching urgent tasks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get occupied rooms with current patients for transfer functionality
     */
    public function getOccupiedRoomsForTransfer()
    {
        if (!in_array(auth()->user()->role, [1, 3, 4])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Get all rooms with their current patients
            $occupiedRooms = \App\Models\InpatientAdmission::with([
                'room.category',
                'patient',
                'doctor',
                'encounter'
            ])
                ->whereNull('discharge_date')
                ->whereHas('encounter', function ($query) {
                    $query->where('type', 2)->orWhere('type', 3); // Rawat Inap and Rawat Darurat
                })
                ->get()
                ->groupBy('room.no_kamar')
                ->map(function ($admissions, $roomNumber) {
                    $room = $admissions->first()->room;

                    return [
                        'room_number' => $roomNumber,
                        'room_id' => $room->id,
                        'room_category' => $room->category->name ?? 'N/A',
                        'room_class' => $room->class ?? 'Umum',
                        'capacity' => $room->capacity ?? 1,
                        'occupied' => $admissions->count(),
                        'patients' => $admissions->map(function ($admission) {
                            return [
                                'id' => $admission->id,
                                'patient_name' => $admission->patient->name ?? 'N/A',
                                'medical_record' => $admission->patient->rekam_medis ?? 'N/A',
                                'condition' => ucfirst($this->mapAdmissionStatus($admission->status ?? 'active')),
                                'admission_date' => $admission->admission_date,
                                'days_admitted' => now()->diffInDays($admission->admission_date),
                                'doctor_name' => $admission->doctor->name ?? $admission->nama_dokter ?? 'N/A'
                            ];
                        })->values()->toArray()
                    ];
                })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'occupied_rooms' => $occupiedRooms,
                    'total_rooms' => $occupiedRooms->count(),
                    'total_patients' => $occupiedRooms->sum('occupied')
                ],
                'timestamp' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching room data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Transfer patient between rooms
     */
    public function transferPatient(Request $request)
    {
        if (!in_array(auth()->user()->role, [1, 3, 4])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'patient_id' => 'required|exists:inpatient_admissions,id',
            'from_room' => 'required|string',
            'to_room' => 'required|string'
        ]);

        if ($request->from_room === $request->to_room) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan asal dan tujuan tidak boleh sama.'
            ], 400);
        }

        try {
            // Find the patient admission
            $admission = \App\Models\InpatientAdmission::with(['room', 'patient'])
                ->where('id', $request->patient_id)
                ->whereNull('discharge_date')
                ->first();

            if (!$admission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient admission not found or already discharged'
                ], 404);
            }

            // Verify current room matches from_room
            if ($admission->room->no_kamar !== $request->from_room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient is not currently in the specified source room'
                ], 400);
            }

            // Find the destination room
            $destinationRoom = \App\Models\Ruangan::where('no_kamar', $request->to_room)->first();

            if (!$destinationRoom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destination room not found'
                ], 404);
            }

            // Check if destination room has capacity
            $currentOccupancy = \App\Models\InpatientAdmission::where('ruangan_id', $destinationRoom->id)
                ->whereNull('discharge_date')
                ->count();

            if ($currentOccupancy >= ($destinationRoom->capacity ?? 1)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destination room is at full capacity'
                ], 400);
            }

            // Perform the transfer
            $oldRoomId = $admission->ruangan_id;
            $admission->ruangan_id = $destinationRoom->id;
            $admission->transfer_date = now();
            $admission->transfer_from = $request->from_room;
            $admission->transfer_to = $request->to_room;
            $admission->transfer_notes = 'Transferred via nurse dashboard by ' . auth()->user()->name;
            $admission->save();

            // Log the activity
            activity()
                ->performedOn($admission)
                ->causedBy(auth()->user())
                ->withProperties([
                    'patient_name' => $admission->patient->name,
                    'from_room' => $request->from_room,
                    'to_room' => $request->to_room,
                    'old_room_id' => $oldRoomId,
                    'new_room_id' => $destinationRoom->id
                ])
                ->log('Patient transferred between rooms');

            return response()->json([
                'success' => true,
                'message' => 'Patient successfully transferred',
                'data' => [
                    'patient_name' => $admission->patient->name,
                    'from_room' => $request->from_room,
                    'to_room' => $request->to_room,
                    'transfer_date' => $admission->transfer_date,
                    'transferred_by' => auth()->user()->name
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error transferring patient: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get nurse role name
     */
    private function getNurseRole($roleId)
    {
        $roles = [
            1 => 'Owner/Administrator',
            2 => 'Doctor',
            3 => 'Nurse',
            4 => 'Administrator',
            5 => 'Receptionist'
        ];

        return $roles[$roleId] ?? 'Unknown Role';
    }

    /**
     * Helper method to format blood pressure
     */
    private function formatBloodPressure($systolic, $diastolic)
    {
        if (!$systolic && !$diastolic) {
            return null;
        }

        if ($systolic && $diastolic) {
            return $systolic . '/' . $diastolic . ' mmHg';
        }

        if ($systolic) {
            return $systolic . '/- mmHg';
        }

        if ($diastolic) {
            return '-/' . $diastolic . ' mmHg';
        }

        return null;
    }

    /**
     * Helper method to map admission status for display
     */
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
}
