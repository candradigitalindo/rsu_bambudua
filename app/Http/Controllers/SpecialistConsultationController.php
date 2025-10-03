<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\SpecialistConsultation;
use App\Models\Spesialis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpecialistConsultationController extends Controller
{
    // Jelaskan fungsi fitur
    // Fitur ini mencatat dan mengelola permintaan konsultasi ke dokter spesialis untuk pasien pada Encounter tertentu:
    // - Mencatat alasan/indikasi, spesialis yang diminta, penjadwalan, dan hasil konsultasi.
    // - Membantu tracking status (requested, scheduled, completed, cancelled).

    public function index(Request $request)
    {
        $q = $request->input('q');
        $status = $request->input('status');

        $consultations = SpecialistConsultation::with(['encounter', 'specialist', 'assignedDoctor'])
            ->when($q, function ($query) use ($q) {
                $query->whereHas('encounter', function ($q2) use ($q) {
                    $q2->where('rekam_medis', 'like', "%{$q}%")
                       ->orWhere('name_pasien', 'like', "%{$q}%");
                });
            })
            ->when($status, fn($qr) => $qr->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('pages.konsultasi.index', compact('consultations', 'q', 'status'));
    }

    public function create()
    {
        $spesialis = Spesialis::orderBy('name')->get();
        $doctors = User::where('role', 2)->where('is_active', true)->orderBy('name')->get();
        return view('pages.konsultasi.create', compact('spesialis', 'doctors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'encounter_id' => 'required|string|exists:encounters,id',
            'specialist_id' => 'required|string',
            'reason' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'assigned_doctor_id' => 'nullable|string',
        ]);

        SpecialistConsultation::create([
            'encounter_id' => $data['encounter_id'],
            'requested_by' => Auth::id(),
            'specialist_id' => $data['specialist_id'],
            'assigned_doctor_id' => $data['assigned_doctor_id'] ?? null,
            'reason' => $data['reason'],
            'status' => $request->input('scheduled_at') ? 'scheduled' : 'requested',
            'scheduled_at' => $data['scheduled_at'] ?? null,
        ]);

        return redirect()->route('konsultasi.index')->with('success', 'Permintaan konsultasi tersimpan.');
    }

    public function edit(string $id)
    {
        $consultation = SpecialistConsultation::with(['encounter', 'specialist', 'assignedDoctor'])->findOrFail($id);
        $spesialis = Spesialis::orderBy('name')->get();
        $doctors = User::where('role', 2)->where('is_active', true)->orderBy('name')->get();
        return view('pages.konsultasi.edit', compact('consultation', 'spesialis', 'doctors'));
    }

    public function update(Request $request, string $id)
    {
        $consultation = SpecialistConsultation::findOrFail($id);
        $data = $request->validate([
            'specialist_id' => 'required|string',
            'reason' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'assigned_doctor_id' => 'nullable|string',
            'status' => 'required|in:requested,scheduled,completed,cancelled',
            'result_notes' => 'nullable|string',
        ]);

        $consultation->update($data);
        return redirect()->route('konsultasi.index')->with('success', 'Konsultasi diperbarui.');
    }

    public function show(string $id)
    {
        $consultation = SpecialistConsultation::with(['encounter', 'specialist', 'assignedDoctor', 'requester'])->findOrFail($id);
        return view('pages.konsultasi.show', compact('consultation'));
    }

    public function print(string $id)
    {
        $consultation = SpecialistConsultation::with(['encounter', 'specialist', 'assignedDoctor', 'requester'])->findOrFail($id);
        return view('pages.konsultasi.print', compact('consultation'));
    }

    public function searchEncounters(Request $request)
    {
        // Support Select2 'id' fetch for preselected value
        if ($request->filled('id')) {
            $e = Encounter::find($request->input('id'));
            if (!$e) {
                return response()->json(['results' => []]);
            }
            return response()->json([
                'results' => [[
                    'id' => $e->id,
                    'text' => sprintf('%s — %s (%s)', $e->rekam_medis, $e->name_pasien, optional($e->created_at)->format('d M Y H:i'))
                ]]
            ]);
        }

        $q = $request->input('q');
        $query = Encounter::query();
        if ($q) {
            $query->where(function($x) use ($q){
                $x->where('rekam_medis', 'like', "%{$q}%")
                  ->orWhere('name_pasien', 'like', "%{$q}%");
            });
        }
        $encounters = $query->orderByDesc('created_at')->limit(20)->get();
        $results = $encounters->map(function($e){
            return [
                'id' => $e->id,
                'text' => sprintf('%s — %s (%s)', $e->rekam_medis, $e->name_pasien, optional($e->created_at)->format('d M Y H:i'))
            ];
        });
        return response()->json(['results' => $results]);
    }
}