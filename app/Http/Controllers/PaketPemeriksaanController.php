<?php

namespace App\Http\Controllers;

use App\Models\PaketPemeriksaan;
use App\Models\PaketPasien;
use App\Models\PaketPasienUsage;
use App\Models\Tindakan;
use App\Models\JenisPemeriksaanPenunjang;
use App\Models\ProductApotek;
use Illuminate\Http\Request;

class PaketPemeriksaanController extends Controller
{
    // ==================== MASTER PAKET ====================

    public function index(Request $request)
    {
        $pakets = PaketPemeriksaan::withCount('paketPasiens')
            ->when($request->name, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            })->orderBy('name')->paginate(15)->appends($request->query());

        $stats = [
            'total' => PaketPemeriksaan::count(),
            'aktif' => PaketPemeriksaan::where('status', 1)->count(),
            'nonaktif' => PaketPemeriksaan::where('status', 0)->count(),
            'gratis' => PaketPemeriksaan::where('is_gratis', 1)->count(),
        ];

        return view('pages.paket-pemeriksaan.index', compact('pakets', 'stats'));
    }

    public function create()
    {
        $tindakans = Tindakan::where('status', 1)->orderBy('name')->get();
        $labs = JenisPemeriksaanPenunjang::where('type', 'lab')->orderBy('name')->get();
        $radiologis = JenisPemeriksaanPenunjang::where('type', 'radiologi')->orderBy('name')->get();
        $obats = ProductApotek::where('status', 1)->orderBy('name')->get();
        return view('pages.paket-pemeriksaan.create', compact('tindakans', 'labs', 'radiologis', 'obats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tindakan_items' => 'nullable|array',
            'tindakan_items.*' => 'integer|min:1',
            'lab_items' => 'nullable|array',
            'lab_items.*' => 'integer|min:1',
            'radiologi_items' => 'nullable|array',
            'radiologi_items.*' => 'integer|min:1',
            'obat_items' => 'nullable|array',
            'obat_items.*' => 'integer|min:1',
            'jumlah_sesi' => 'required|integer|min:1',
            'harga' => 'required|string',
            'is_gratis' => 'nullable|boolean',
            'masa_berlaku_hari' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Nama paket tidak boleh kosong.',
            'jumlah_sesi.required' => 'Jumlah sesi harus diisi.',
            'jumlah_sesi.min' => 'Jumlah sesi minimal 1.',
            'harga.required' => 'Harga harus diisi.',
            'masa_berlaku_hari.required' => 'Masa berlaku harus diisi.',
            'masa_berlaku_hari.min' => 'Masa berlaku minimal 1 hari.',
        ]);

        $isGratis = $request->boolean('is_gratis');

        $buildItems = function ($items) {
            if (empty($items)) return null;
            return collect($items)->map(fn($qty, $id) => ['id' => $id, 'qty' => max(1, (int) $qty)])->values()->toArray();
        };

        PaketPemeriksaan::create([
            'name' => $request->name,
            'description' => $request->description,
            'tindakan_ids' => $buildItems($request->tindakan_items),
            'lab_ids' => $buildItems($request->lab_items),
            'radiologi_ids' => $buildItems($request->radiologi_items),
            'obat_ids' => $buildItems($request->obat_items),
            'jumlah_sesi' => $request->jumlah_sesi,
            'harga' => $isGratis ? 0 : (int) str_replace('.', '', $request->harga),
            'is_gratis' => $isGratis,
            'masa_berlaku_hari' => $request->masa_berlaku_hari,
            'status' => $request->status,
        ]);

        return redirect()->route('paket-pemeriksaan.index')
            ->with('success', 'Paket pemeriksaan berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $paket = PaketPemeriksaan::findOrFail($id);
        $tindakans = Tindakan::where('status', 1)->orderBy('name')->get();
        $labs = JenisPemeriksaanPenunjang::where('type', 'lab')->orderBy('name')->get();
        $radiologis = JenisPemeriksaanPenunjang::where('type', 'radiologi')->orderBy('name')->get();
        $obats = ProductApotek::where('status', 1)->orderBy('name')->get();
        return view('pages.paket-pemeriksaan.edit', compact('paket', 'tindakans', 'labs', 'radiologis', 'obats'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tindakan_items' => 'nullable|array',
            'tindakan_items.*' => 'integer|min:1',
            'lab_items' => 'nullable|array',
            'lab_items.*' => 'integer|min:1',
            'radiologi_items' => 'nullable|array',
            'radiologi_items.*' => 'integer|min:1',
            'obat_items' => 'nullable|array',
            'obat_items.*' => 'integer|min:1',
            'jumlah_sesi' => 'required|integer|min:1',
            'harga' => 'required|string',
            'is_gratis' => 'nullable|boolean',
            'masa_berlaku_hari' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Nama paket tidak boleh kosong.',
            'jumlah_sesi.required' => 'Jumlah sesi harus diisi.',
            'harga.required' => 'Harga harus diisi.',
            'masa_berlaku_hari.required' => 'Masa berlaku harus diisi.',
        ]);

        $paket = PaketPemeriksaan::findOrFail($id);
        $isGratis = $request->boolean('is_gratis');

        $buildItems = function ($items) {
            if (empty($items)) return null;
            return collect($items)->map(fn($qty, $id) => ['id' => $id, 'qty' => max(1, (int) $qty)])->values()->toArray();
        };

        $paket->update([
            'name' => $request->name,
            'description' => $request->description,
            'tindakan_ids' => $buildItems($request->tindakan_items),
            'lab_ids' => $buildItems($request->lab_items),
            'radiologi_ids' => $buildItems($request->radiologi_items),
            'obat_ids' => $buildItems($request->obat_items),
            'jumlah_sesi' => $request->jumlah_sesi,
            'harga' => $isGratis ? 0 : (int) str_replace('.', '', $request->harga),
            'is_gratis' => $isGratis,
            'masa_berlaku_hari' => $request->masa_berlaku_hari,
            'status' => $request->status,
        ]);

        return redirect()->route('paket-pemeriksaan.index')
            ->with('success', 'Paket pemeriksaan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $paket = PaketPemeriksaan::findOrFail($id);
        $name = $paket->name;
        $paket->delete();

        return redirect()->route('paket-pemeriksaan.index')
            ->with('success', 'Paket ' . $name . ' berhasil dihapus.');
    }

    // ==================== PAKET PASIEN ====================

    public function pasienIndex(Request $request)
    {
        $query = PaketPasien::with(['paketPemeriksaan', 'pasien', 'createdBy']);

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('rekam_medis', 'like', '%' . $search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $paketPasiens = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        $stats = [
            'total' => PaketPasien::count(),
            'aktif' => PaketPasien::where('status', 'aktif')->count(),
            'selesai' => PaketPasien::where('status', 'selesai')->count(),
            'expired' => PaketPasien::where('status', 'expired')->count(),
            'batal' => PaketPasien::where('status', 'batal')->count(),
        ];

        return view('pages.paket-pemeriksaan.pasien-index', compact('paketPasiens', 'stats'));
    }

    public function pasienShow(string $id)
    {
        $paketPasien = PaketPasien::with([
            'paketPemeriksaan', 'pasien', 'usages.usedBy', 'usages.encounter', 'createdBy'
        ])->findOrFail($id);

        return view('pages.paket-pemeriksaan.pasien-show', compact('paketPasien'));
    }

    public function pasienUseSesi(Request $request, string $id)
    {
        $paketPasien = PaketPasien::findOrFail($id);

        if ($paketPasien->status !== 'aktif') {
            return back()->with('error', 'Paket sudah tidak aktif.');
        }

        if ($paketPasien->isExpired()) {
            $paketPasien->update(['status' => 'expired']);
            return back()->with('error', 'Paket sudah expired.');
        }

        if ($paketPasien->sesi_terpakai >= $paketPasien->total_sesi) {
            $paketPasien->update(['status' => 'selesai']);
            return back()->with('error', 'Semua sesi sudah terpakai.');
        }

        $sesiKe = $paketPasien->sesi_terpakai + 1;

        PaketPasienUsage::create([
            'paket_pasien_id' => $paketPasien->id,
            'encounter_id' => $request->encounter_id,
            'sesi_ke' => $sesiKe,
            'used_by' => auth()->id(),
            'catatan' => $request->catatan,
        ]);

        $paketPasien->increment('sesi_terpakai');

        if ($paketPasien->sesi_terpakai >= $paketPasien->total_sesi) {
            $paketPasien->update(['status' => 'selesai']);
        }

        return back()->with('success', 'Sesi ke-' . $sesiKe . ' berhasil digunakan.');
    }

    public function pasienCancel(string $id)
    {
        $paketPasien = PaketPasien::findOrFail($id);
        $paketPasien->update(['status' => 'batal']);

        return back()->with('success', 'Paket pasien berhasil dibatalkan.');
    }
}
