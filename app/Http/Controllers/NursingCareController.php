<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\NursingCareRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NursingCareController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $records = NursingCareRecord::with(['encounter','nurse'])
            ->when($q, function($query) use ($q){
                $query->whereHas('encounter', function($x) use ($q){
                    $x->where('rekam_medis','like',"%{$q}%")
                      ->orWhere('name_pasien','like',"%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();
        return view('pages.keperawatan.index', compact('records','q'));
    }

    public function create()
    {
        // Perawat aktif (opsional untuk assign manual)
        $nurses = User::where('role', 3)->where('is_active', true)->orderBy('name')->get();
        return view('pages.keperawatan.create', compact('nurses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'encounter_id' => 'required|string|exists:encounters,id',
            'nurse_id' => 'nullable|string|exists:users,id',
            'shift' => 'nullable|string|max:10',
            'systolic' => 'nullable|integer',
            'diastolic' => 'nullable|integer',
            'heart_rate' => 'nullable|integer',
            'resp_rate' => 'nullable|integer',
            'temperature' => 'nullable|numeric',
            'spo2' => 'nullable|integer',
            'pain_scale' => 'nullable|integer|min:0|max:10',
            'nursing_diagnosis' => 'nullable|string',
            'interventions' => 'nullable|string',
            'evaluation_notes' => 'nullable|string',
        ]);

        $data['nurse_id'] = $data['nurse_id'] ?? Auth::id();
        NursingCareRecord::create($data);
        return redirect()->route('keperawatan.index')->with('success','Asuhan keperawatan dicatat.');
    }

    public function show(string $id)
    {
        $record = NursingCareRecord::with(['encounter','nurse'])->findOrFail($id);
        return view('pages.keperawatan.show', compact('record'));
    }

    public function edit(string $id)
    {
        $record = NursingCareRecord::with(['encounter','nurse'])->findOrFail($id);
        $nurses = User::where('role', 3)->where('is_active', true)->orderBy('name')->get();
        return view('pages.keperawatan.edit', compact('record','nurses'));
    }

    public function update(Request $request, string $id)
    {
        $record = NursingCareRecord::findOrFail($id);
        $data = $request->validate([
            'nurse_id' => 'nullable|string|exists:users,id',
            'shift' => 'nullable|string|max:10',
            'systolic' => 'nullable|integer',
            'diastolic' => 'nullable|integer',
            'heart_rate' => 'nullable|integer',
            'resp_rate' => 'nullable|integer',
            'temperature' => 'nullable|numeric',
            'spo2' => 'nullable|integer',
            'pain_scale' => 'nullable|integer|min:0|max:10',
            'nursing_diagnosis' => 'nullable|string',
            'interventions' => 'nullable|string',
            'evaluation_notes' => 'nullable|string',
        ]);
        $record->update($data);
        return redirect()->route('keperawatan.index')->with('success','Asuhan keperawatan diperbarui.');
    }

    public function print(string $id)
    {
        $record = NursingCareRecord::with(['encounter','nurse'])->findOrFail($id);
        return view('pages.keperawatan.print', compact('record'));
    }
}