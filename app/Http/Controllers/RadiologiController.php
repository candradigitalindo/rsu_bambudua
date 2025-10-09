<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use App\Models\JenisPemeriksaanPenunjang;
use App\Models\Pasien;
use App\Models\User;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use App\Models\RadiologySchedule;
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
        $recent = \App\Models\RadiologyRequest::with(['pasien','jenis','dokter'])
            ->orderByDesc('created_at')->limit(10)->get();
        return view('pages.radiologi.dashboard', compact('stats','recent'));
    }

    public function requestsIndex()
    {
        $requests = RadiologyRequest::with(['pasien', 'jenis', 'dokter'])->orderByDesc('created_at')->paginate(15);
        return view('pages.radiologi.permintaan.index', compact('requests'));
    }

    public function resultsIndex()
    {
        $results = RadiologyResult::with(['request.pasien', 'request.jenis', 'reporter'])
            ->whereHas('request', function ($query) {
                $query->where('status', 'completed');
            })
            ->orderByDesc('reported_at')->paginate(15);
        return view('pages.radiologi.hasil.index', compact('results'));
    }

    public function scheduleIndex()
    {
        $date = request('date');
        $query = RadiologySchedule::with(['request.pasien', 'request.jenis', 'radiographer'])
            ->orderBy('scheduled_start');
        if ($date) {
            $query->whereDate('scheduled_start', $date);
        } else {
            $query->whereDate('scheduled_start', now()->toDateString());
        }
        $schedules = $query->paginate(20);
        return view('pages.radiologi.jadwal.index', compact('schedules', 'date'));
    }

    public function requestsShow($id)
    {
        $req = RadiologyRequest::with(['pasien', 'jenis', 'dokter', 'results' => function ($q) {
            $q->orderByDesc('created_at');
        }])->findOrFail($id);
        $latestResult = $req->results->first();
        return view('pages.radiologi.permintaan.show', compact('req', 'latestResult'));
    }

    public function resultsEdit($id)
    {
        $req = RadiologyRequest::with(['pasien', 'jenis', 'dokter'])->findOrFail($id);
        abort_unless($req->status === 'processing', 403, 'Hasil hanya bisa diisi saat status processing.');
        return view('pages.radiologi.permintaan.results', compact('req'));
    }

    public function resultsStore(Request $request, $id)
    {
        $req = RadiologyRequest::findOrFail($id);
        abort_unless($req->status === 'processing', 403, 'Hasil hanya bisa diisi saat status processing.');

        $data = $request->validate([
            'findings'   => 'required|string',
            'impression' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $files = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $f) {
                $path = $f->store('radiology/results', 'public');
                $files[] = $path;
            }
        }

        $result = new RadiologyResult();
        $result->radiology_request_id = $req->id;
        $result->findings = $data['findings'];
        $result->impression = $data['impression'];
        $result->payload = null; // future: dynamic fields
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
            $q->orderByDesc('created_at');
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

    public function scheduleCreate($id)
    {
        $req = RadiologyRequest::with(['pasien', 'jenis', 'dokter', 'schedule'])->findOrFail($id);
        abort_if($req->status !== 'requested', 403, 'Jadwal hanya bisa dibuat saat status requested.');
        $radiographers = User::where('role', 3)->orderBy('name')->get(['id', 'name']); // asumsikan role 3 = perawat/radiografer
        return view('pages.radiologi.permintaan.schedule', compact('req', 'radiographers'));
    }

    public function scheduleStore(Request $request, $id)
    {
        $req = RadiologyRequest::with('schedule')->findOrFail($id);
        abort_if($req->status !== 'requested', 403, 'Jadwal hanya bisa dibuat saat status requested.');

        $data = $request->validate([
            'scheduled_start' => 'required|date',
            'scheduled_end'   => 'nullable|date|after:scheduled_start',
            'modality'        => 'nullable|string|max:100',
            'room'            => 'nullable|string|max:100',
            'radiographer_id' => 'nullable|exists:users,id',
            'preparation'     => 'nullable|string',
            'priority'        => 'required|string|in:routine,urgent,stat',
            'notes'           => 'nullable|string',
        ]);

        // Cegah double schedule pada request
        if ($req->schedule) {
            return back()->with('error', 'Permintaan ini sudah memiliki jadwal.');
        }

        $sched = new RadiologySchedule();
        $sched->radiology_request_id = $req->id;
        $sched->scheduled_start = $data['scheduled_start'];
        $sched->scheduled_end = $data['scheduled_end'] ?? null;
        $sched->modality = $data['modality'] ?? null;
        $sched->room = $data['room'] ?? null;
        $sched->radiographer_id = $data['radiographer_id'] ?? null;
        $sched->preparation = $data['preparation'] ?? null;
        $sched->priority = $data['priority'];
        $sched->status = 'scheduled';
        $sched->notes = $data['notes'] ?? null;
        $sched->created_by = Auth::id();
        $sched->save();

        return redirect()->route('radiologi.requests.show', $req->id)->with('success', 'Jadwal radiologi berhasil dibuat.');
    }

    public function scheduleStart($scheduleId)
    {
        $sched = RadiologySchedule::with('request')->findOrFail($scheduleId);
        if ($sched->status !== 'scheduled') {
            return back()->with('error', 'Hanya jadwal berstatus scheduled yang bisa dimulai.');
        }
        $sched->status = 'in_progress';
        $sched->save();
        // Update request ke processing
        $req = $sched->request;
        if ($req && $req->status === 'requested') {
            $req->status = 'processing';
            $req->save();
        }
        return back()->with('success', 'Pemeriksaan radiologi dimulai.');
    }

    public function scheduleCancel($scheduleId)
    {
        $sched = RadiologySchedule::findOrFail($scheduleId);
        if (!in_array($sched->status, ['scheduled', 'in_progress'])) {
            return back()->with('error', 'Jadwal tidak dapat dibatalkan pada status saat ini.');
        }
        $sched->status = 'canceled';
        $sched->save();
        return back()->with('success', 'Jadwal radiologi dibatalkan.');
    }

    public function scheduleNoShow($scheduleId)
    {
        $sched = RadiologySchedule::findOrFail($scheduleId);
        if ($sched->status !== 'scheduled') {
            return back()->with('error', 'No-show hanya untuk jadwal berstatus scheduled.');
        }
        $sched->status = 'no_show';
        $sched->save();
        return back()->with('success', 'Jadwal ditandai sebagai no-show.');
    }
}
