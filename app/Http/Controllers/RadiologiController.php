<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use App\Models\JenisPemeriksaanPenunjang;
use App\Models\Pasien;
use App\Models\User;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use Illuminate\Support\Facades\Auth;

class RadiologiController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'today' => \App\Models\RadiologyRequest::whereDate('created_at', now()->toDateString())->count(),
            'processing' => \App\Models\RadiologyRequest::where('status', 'processing')->count(),
            'completed'  => \App\Models\RadiologyRequest::where('status', 'completed')->count(),
            'requested'  => \App\Models\RadiologyRequest::where('status', 'requested')->count(),
        ];
        $recent = \App\Models\RadiologyRequest::with(['pasien', 'jenis', 'dokter'])
            ->orderByDesc('created_at')->limit(10)->get();
        return view('pages.radiologi.dashboard', compact('stats', 'recent'));
    }

    public function requestsIndex()
    {
        $requests = RadiologyRequest::with(['pasien', 'jenis', 'dokter'])->orderByDesc('created_at')->paginate(15);
        return view('pages.radiologi.permintaan.index', compact('requests'));
    }

    public function resultsIndex()
    {
        $results = RadiologyResult::with(['request.pasien', 'request.jenis', 'radiologist', 'reporter'])
            ->whereHas('request', function ($query) {
                $query->where('status', 'completed');
            })
            ->orderByDesc('reported_at')->paginate(15);
        return view('pages.radiologi.hasil.index', compact('results'));
    }

    public function requestsShow($id)
    {
        $req = RadiologyRequest::with(['pasien', 'jenis', 'dokter', 'results' => function ($q) {
            $q->with(['radiologist', 'reporter'])->orderByDesc('created_at');
        }])->findOrFail($id);
        $latestResult = $req->results->first();
        return view('pages.radiologi.permintaan.show', compact('req', 'latestResult'));
    }

    public function resultsEdit($id)
    {
        $req = RadiologyRequest::with(['pasien', 'jenis.templateFields', 'dokter'])->findOrFail($id);

        // Auto-update status to processing if currently requested
        if ($req->status === 'requested') {
            $req->update(['status' => 'processing']);
        }

        if ($req->status !== 'processing') {
            return redirect()->route('radiologi.requests.show', $id)
                ->with('error', 'Hasil hanya bisa diisi saat status processing. Status saat ini: ' . ucfirst($req->status));
        }

        return view('pages.radiologi.permintaan.results', compact('req'));
    }

    public function resultsStore(Request $request, $id)
    {
        $req = RadiologyRequest::with('jenis.templateFields')->findOrFail($id);

        if ($req->status !== 'processing') {
            return redirect()->route('radiologi.requests.show', $id)
                ->with('error', 'Hasil hanya bisa diisi saat status processing. Status saat ini: ' . ucfirst($req->status));
        }

        $rules = [
            'radiologist_id' => 'required|exists:users,id',
            'findings'   => 'required|string',
            'impression' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ];

        // Build validation rules for custom fields
        if ($req->jenis && $req->jenis->templateFields->isNotEmpty()) {
            foreach ($req->jenis->templateFields as $field) {
                $rules['payload.' . $field->field_name] = 'nullable';
            }
        }

        $data = $request->validate($rules);

        $files = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $f) {
                $path = $f->store('radiology/results', 'public');
                $files[] = $path;
            }
        }

        // Collect custom field values from payload
        $payload = [];
        if ($request->has('payload')) {
            $payload = $request->input('payload', []);
        }

        $result = new RadiologyResult();
        $result->radiology_request_id = $req->id;
        $result->radiologist_id = $data['radiologist_id'];
        $result->findings = $data['findings'];
        $result->impression = $data['impression'];
        $result->payload = !empty($payload) ? $payload : null;
        $result->files = $files ?: null;
        $result->reported_by = Auth::id();
        $result->reported_at = now();
        $result->save();

        // Mark request as completed
        $req->status = 'completed';
        $req->save();

        return redirect()->route('radiologi.requests.show', $req->id)
            ->with('success', 'Hasil radiologi tersimpan dan status diselesaikan.');
    }

    public function requestsUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:requested,processing,completed,canceled',
        ]);

        $req = RadiologyRequest::findOrFail($id);
        $from = $req->status ?? 'requested';
        $to = $request->input('status');

        $allowed = [
            'requested' => ['processing', 'canceled'],
            'processing' => ['completed', 'canceled'],
            'completed' => [],
            'canceled' => [],
        ];

        if (!isset($allowed[$from]) || !in_array($to, $allowed[$from], true)) {
            return back()->with('error', "Perubahan status dari '$from' ke '$to' tidak diizinkan.");
        }

        $req->status = $to;
        $req->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status permintaan radiologi diperbarui menjadi ' . ucfirst($to) . '.',
            ]);
        }
        return back()->with('success', 'Status permintaan radiologi diperbarui menjadi ' . ucfirst($to) . '.');
    }

    public function requestsCreate()
    {
        // Untuk saat ini, tampilkan semua jenis pemeriksaan penunjang.
        // Jika nanti ada kolom tipe (lab/radiologi), filter di sini.
        $jenis = JenisPemeriksaanPenunjang::orderBy('name')->get();
        return view('pages.radiologi.permintaan.create', [
            'jenisPemeriksaan' => $jenis,
        ]);
    }

    public function requestsStore(Request $request)
    {
        // Validasi dasar: encounter wajib, lainnya fleksibel (agar bisa dipanggil dari Observasi)
        $request->validate([
            'encounter_id' => 'required|uuid|exists:encounters,id',
            'catatan' => 'nullable|string',
        ]);

        $encounter = \App\Models\Encounter::findOrFail($request->input('encounter_id'));
        $jenisId = $request->input('pemeriksaan') ?: $request->input('jenis_pemeriksaan_id');
        if (!$jenisId) {
            $msg = 'Jenis pemeriksaan radiologi wajib dipilih.';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['status' => false, 'message' => $msg], 422)
                : back()->withErrors(['pemeriksaan' => $msg]);
        }
        $jenisPemeriksaan = \App\Models\JenisPemeriksaanPenunjang::findOrFail($jenisId);

        // Dokter: gunakan input jika ada, atau default dokter yang login
        $dokterId = $request->input('dokter_id') ?: Auth::id();
        $dokterPerujuk = \App\Models\User::findOrFail($dokterId);

        // Simpan permintaan radiologi
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $jenisPemeriksaan, $encounter, $dokterPerujuk, $dokterId) {
            $req = new \App\Models\RadiologyRequest();
            $req->encounter_id = $encounter->id;
            $req->pasien_id = $encounter->pasien->id; // Ambil dari encounter
            $req->jenis_pemeriksaan_id = $jenisPemeriksaan->id;
            $req->dokter_id = $dokterId;
            $req->notes = $request->input('catatan');
            $req->status = 'requested';
            $req->price = (float) $jenisPemeriksaan->harga;
            $req->created_by = Auth::id();
            $req->save();

            // Buat insentif untuk dokter yang merujuk
            $observasiRepo = new \App\Repositories\ObservasiRepository();
            $observasiRepo->createPemeriksaanPenunjangIncentive($encounter, $dokterPerujuk, $jenisPemeriksaan->name, (float)$jenisPemeriksaan->harga, 'radiologi');

            // Update total tagihan di encounter
            $observasiRepo->updateEncounterTotalTindakan($encounter->id);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 200,
                'message' => 'Permintaan Radiologi berhasil dibuat.'
            ]);
        }
        return redirect()->route('radiologi.requests.index')->with('success', 'Permintaan radiologi berhasil dibuat dan insentif telah dicatat.');
    }

    public function print($id)
    {
        $req = \App\Models\RadiologyRequest::with(['pasien', 'jenis', 'dokter', 'results' => function ($q) {
            $q->with(['radiologist', 'reporter'])->orderByDesc('created_at');
        }])->findOrFail($id);
        $latest = $req->results->first();
        return view('pages.radiologi.permintaan.print', [
            'req' => $req,
            'latest' => $latest,
        ]);
    }

    public function searchPatients(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json(['results' => []]);
        }
        $items = Pasien::query()
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('rekam_medis', 'like', "%$q%")
                    ->orWhere('no_identitas', 'like', "%$q%")
                    ->orWhere('no_hp', 'like', "%$q%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'rekam_medis', 'name']);

        $results = $items->map(function ($p) {
            return [
                'id' => $p->id,
                'text' => $p->rekam_medis . ' - ' . $p->name,
                'rekam_medis' => $p->rekam_medis,
                'name' => $p->name,
            ];
        });
        return response()->json(['results' => $results]);
    }

    public function searchDoctors(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json(['results' => []]);
        }
        $items = User::query()
            ->where('role', 2) // 2 = dokter
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('username', 'like', "%$q%")
                    ->orWhere('id_petugas', 'like', "%$q%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'username']);

        $results = $items->map(function ($u) {
            $label = $u->name . ($u->username ? ' (' . $u->username . ')' : '');
            return [
                'id' => $u->id,
                'text' => $label,
                'name' => $u->name,
            ];
        });
        return response()->json(['results' => $results]);
    }
}
