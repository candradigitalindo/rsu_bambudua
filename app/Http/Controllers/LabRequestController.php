<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabRequestController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $status = $request->input('status');
        $requests = LabRequest::with(['encounter'])
            ->when($q, function($qr) use ($q){
                $qr->whereHas('encounter', function($x) use ($q){
                    $x->where('rekam_medis','like',"%{$q}%")
                      ->orWhere('name_pasien','like',"%{$q}%");
                });
            })
            ->when($status, fn($qr)=>$qr->where('status',$status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();
        return view('pages.lab.requests.index', compact('requests','q','status'));
    }

    public function create()
    {
        return view('pages.lab.requests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'encounter_id' => 'required|string|exists:encounters,id',
            'notes' => 'nullable|string',
'tests' => 'required|array|min:1',
            'tests.*.id' => 'required|string|exists:jenis_pemeriksaan_penunjangs,id',
            'tests.*.name' => 'nullable|string',
        ]);

        $lr = LabRequest::create([
            'encounter_id' => $data['encounter_id'],
            'requested_by' => Auth::id(),
            'status' => 'requested',
            'requested_at' => now(),
            'notes' => $data['notes'] ?? null,
            'total_charge' => 0,
            'charged' => false,
        ]);

        $total = 0;
        foreach ($data['tests'] as $t) {
            $testId = $t['id'];
            $row = DB::table('jenis_pemeriksaan_penunjangs')->where('id', $testId)->first();
            // Karena divalidasi exists, baris seharusnya ada
            $testName = $row->name;
            $price = (int) $row->harga;
            LabRequestItem::create([
                'lab_request_id' => $lr->id,
                'test_id' => $testId,
                'test_name' => $testName,
                'price' => $price,
            ]);
            $total += $price;
        }
        $lr->update(['total_charge' => $total]);

        return redirect()->route('lab.requests.index')->with('success','Permintaan lab berhasil dibuat.');
    }

    public function show(string $id)
    {
        $req = LabRequest::with(['encounter','items'])->findOrFail($id);
        return view('pages.lab.requests.show', compact('req'));
    }

    public function print(string $id)
    {
        $req = LabRequest::with(['encounter','items'])->findOrFail($id);
        // Optional: only allow print when completed
        if ($req->status !== 'completed') {
            return redirect()->route('lab.requests.show', $id)->with('error','Hasil hanya dapat dicetak jika status Completed.');
        }
        return view('pages.lab.requests.print', compact('req'));
    }

    public function edit(string $id)
    {
        $req = LabRequest::with(['encounter','items'])->findOrFail($id);
        return view('pages.lab.requests.edit', compact('req'));
    }

    public function update(Request $request, string $id)
    {
        $req = LabRequest::with('items','encounter')->findOrFail($id);
        $data = $request->validate([
            'status' => 'required|in:requested,collected,processing,completed,cancelled',
            'items' => 'nullable|array',
            'items.*.id' => 'required|string|exists:lab_request_items,id',
            'items.*.result_value' => 'nullable|string',
            'items.*.result_unit' => 'nullable|string',
            'items.*.result_reference' => 'nullable|string',
            'items.*.result_notes' => 'nullable|string',
            'items.*.payload' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        // Update result items if provided
        if (!empty($data['items'])) {
            foreach ($data['items'] as $it) {
                LabRequestItem::where('id', $it['id'])->update([
                    'result_value' => $it['result_value'] ?? null,
                    'result_unit' => $it['result_unit'] ?? null,
                    'result_reference' => $it['result_reference'] ?? null,
                    'result_notes' => $it['result_notes'] ?? null,
                    'result_payload' => $it['payload'] ?? null,
                ]);
            }
        }

        $req->notes = $data['notes'] ?? $req->notes;
        $req->status = $data['status'];
        if ($data['status'] === 'collected' && !$req->collected_at) { $req->collected_at = now(); }
        if ($data['status'] === 'completed' && !$req->completed_at) { $req->completed_at = now(); }
        $req->save();

        // Integrasi Kasir: saat completed dan belum charged, tambahkan total_charge ke Encounter.total_bayar_tindakan
        if ($req->status === 'completed' && !$req->charged) {
            $enc = $req->encounter;
            if ($enc) {
                $enc->total_bayar_tindakan = (int)$enc->total_bayar_tindakan + (int)$req->total_charge;
                $enc->save();
                $req->charged = true;
                $req->save();
            }
        }

        return redirect()->route('lab.requests.show', $req->id)->with('success','Permintaan lab diperbarui.');
    }

    // (Opsional) Autosuggest test dari master Jenis Pemeriksaan Penunjang
    public function searchTests(Request $request)
    {
        $q = $request->input('q');
        $rows = DB::table('jenis_pemeriksaan_penunjangs')
            ->when($q, function($qr) use ($q){
                $qr->where('name','like',"%{$q}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id','name','harga']);
        $results = $rows->map(function($r){
            return [
                'id' => $r->id,
                'text' => $r->name,
                'price' => (int) $r->harga,
            ];
        });
        return response()->json(['results'=>$results]);
    }

    // Pencarian encounter sederhana (by RM/nama) untuk Select2
    public function searchEncounters(Request $request)
    {
        $q = $request->input('q');
        $id = $request->input('id');
        $rows = DB::table('encounters')
            ->when($id, fn($qr)=>$qr->where('id',$id))
            ->when($q, function($qr) use ($q){
                $qr->where(function($x) use ($q){
                    $x->where('rekam_medis','like',"%{$q}%")
                      ->orWhere('name_pasien','like',"%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['id','rekam_medis','name_pasien']);
        $results = $rows->map(fn($r)=>['id'=>$r->id,'text'=>($r->rekam_medis.' - '.$r->name_pasien)]);
        return response()->json(['results'=>$results]);
    }
}
