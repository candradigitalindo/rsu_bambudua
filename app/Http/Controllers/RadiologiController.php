<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use App\Models\JenisPemeriksaanPenunjang;
use App\Models\Pasien;
use App\Models\User;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use App\Models\RadiologySupply;
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

        // Supply statistics
        $supplyStats = [
            'habis' => RadiologySupply::where('stock', '<=', 0)->count(),
            'kadaluarsa' => RadiologySupply::whereHas('batches', function ($q) {
                $q->where('expiry_date', '<=', now())
                    ->where('remaining_quantity', '>', 0);
            })->count(),
        ];

        $recent = \App\Models\RadiologyRequest::with(['pasien', 'jenis', 'dokter'])
            ->orderByDesc('created_at')->limit(10)->get();
        return view('pages.radiologi.dashboard', compact('stats', 'recent', 'supplyStats'));
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
        $req = RadiologyRequest::with(['pasien', 'jenis.templateFields.fieldItems', 'dokter'])->findOrFail($id);

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
        $req = RadiologyRequest::with('jenis.templateFields.fieldItems')->findOrFail($id);

        if ($req->status !== 'processing') {
            return redirect()->route('radiologi.requests.show', $id)
                ->with('error', 'Hasil hanya bisa diisi saat status processing. Status saat ini: ' . ucfirst($req->status));
        }

        // Check if it's ECHOCARDIOGRAPHY
        $isEcho = $req->jenis && (stripos($req->jenis->name, 'ECHOCARDIOGRAPHY') !== false || stripos($req->jenis->name, 'ECHO') !== false);

        $rules = [
            'radiologist_id' => 'required|exists:users,id',
            'reporter_id' => 'required|exists:users,id',
            'findings'   => $isEcho ? 'nullable|string' : 'required|string',
            'impression' => $isEcho ? 'required|string' : 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240',
        ];

        // Build validation rules for custom fields
        if ($req->jenis && $req->jenis->templateFields->isNotEmpty()) {
            foreach ($req->jenis->templateFields as $field) {
                if ($field->field_type === 'group') {
                    // For group fields, validate each sub-field
                    foreach ($field->fieldItems as $item) {
                        $rules['payload.' . $field->field_name . '.' . $item->item_name] = 'nullable';
                    }
                } else {
                    $rules['payload.' . $field->field_name] = 'nullable';
                }
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
            // Filter out empty values to keep payload clean
            $payload = array_filter($payload, function ($value) {
                return $value !== null && $value !== '';
            });
        }

        $result = new RadiologyResult();
        $result->radiology_request_id = $req->id;
        $result->radiologist_id = $data['radiologist_id'];
        $result->reported_by = $data['reporter_id']; // Perawat yang melakukan input

        // For ECHO: findings is optional, impression is required
        // For others: findings is required and also used as impression
        if ($isEcho) {
            $result->findings = $data['findings'] ?? '-'; // Default '-' if empty to avoid NULL
            $result->impression = $data['impression'];
        } else {
            $result->findings = $data['findings'];
            $result->impression = $data['findings']; // Use findings as impression for backward compatibility
        }

        $result->payload = !empty($payload) ? $payload : null;
        $result->files = $files ?: null;
        $result->reported_at = now();
        $result->save();

        // Mark request as completed
        $req->status = 'completed';
        $req->save();

        // Process incentives untuk pemeriksaan radiologi
        $observasiRepo = new \App\Repositories\ObservasiRepository();
        $observasiRepo->processPenunjangIncentives($req->id, 'radiologi');

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

        // Process incentives jika status menjadi completed
        if ($to === 'completed') {
            $observasiRepo = new \App\Repositories\ObservasiRepository();
            $observasiRepo->processPenunjangIncentives($req->id, 'radiologi');
        }

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
        // Validasi: encounter_id atau pasien_id harus ada
        $request->validate([
            'encounter_id' => 'nullable|uuid|exists:encounters,id',
            'pasien_id' => 'nullable|uuid|exists:pasiens,id',
            'catatan' => 'nullable|string',
        ]);

        // Jika ada encounter_id, gunakan itu
        if ($request->has('encounter_id') && $request->input('encounter_id')) {
            $encounter = \App\Models\Encounter::findOrFail($request->input('encounter_id'));
        }
        // Jika tidak ada encounter_id, cek pasien_id dan buat encounter baru
        elseif ($request->has('pasien_id') && $request->input('pasien_id')) {
            $pasien = \App\Models\Pasien::findOrFail($request->input('pasien_id'));

            // Cek apakah ada encounter aktif untuk pasien ini (encounter hari ini)
            $encounter = \App\Models\Encounter::where('rekam_medis', $pasien->rekam_medis)
                ->whereDate('created_at', now()->toDateString())
                ->latest()
                ->first();

            // Jika tidak ada encounter aktif, buat baru
            if (!$encounter) {
                $encounter = new \App\Models\Encounter();
                $encounter->no_encounter = 'ENC-' . strtoupper(uniqid());
                $encounter->rekam_medis = $pasien->rekam_medis;
                $encounter->name_pasien = $pasien->name;
                $encounter->type = 1; // 1 = Rawat Jalan
                $encounter->tujuan_kunjungan = 3; // 3 = Kunjungan Sakit
                $encounter->created_by = Auth::id();
                $encounter->save();
            }
        } else {
            $msg = 'Pasien atau encounter wajib dipilih.';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['status' => false, 'message' => $msg], 422)
                : back()->withErrors(['pasien_id' => $msg]);
        }

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
            $req->pasien_id = $encounter->pasien->id; // Ambil dari relasi pasien
            $req->jenis_pemeriksaan_id = $jenisPemeriksaan->id;
            $req->dokter_id = $dokterId;
            $req->notes = $request->input('catatan');
            $req->status = 'requested';
            $req->price = (float) $jenisPemeriksaan->harga;
            $req->created_by = Auth::id();
            $req->save();

            // [CHANGED] Fee penunjang radiologi akan dibuat saat pembayaran kasir, bukan saat request
            // Logika dipindahkan ke KasirController

            // Update total tagihan di encounter
            $observasiRepo = new \App\Repositories\ObservasiRepository();
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

        // Cek jenis pemeriksaan untuk menggunakan template yang sesuai
        $template = 'pages.radiologi.permintaan.print';
        $jenisNama = optional($req->jenis)->name ?? '';
        if (
            stripos($jenisNama, 'ECHOCARDIOGRAPHY') !== false ||
            stripos($jenisNama, 'ECHO') !== false
        ) {
            $template = 'pages.radiologi.permintaan.print_echo';
        }

        return view($template, [
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
