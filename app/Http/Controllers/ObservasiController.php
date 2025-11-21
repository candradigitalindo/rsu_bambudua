<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsActivity;

use App\Models\JenisPemeriksaanPenunjang;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\ObservasiRepository;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ObservasiController extends Controller
{
    use LogsActivity;
    public $observasiRepository;
    public function __construct(ObservasiRepository $observasiRepository)
    {
        $this->observasiRepository = $observasiRepository;
    }

    public function index($id)
    {
        // Cek apakah encounter_id valid
        $encounter = $this->observasiRepository->getEncounterById($id);
        $observasi = $id;
        // Ambil data dokter yang menangani
        $dokters = $this->observasiRepository->getDokters($id);
        $jenisPemeriksaan = JenisPemeriksaanPenunjang::all();
        // Ambil data perawat yang menangani
        $perawats = $this->observasiRepository->getPerawats($id);
        // [LAB] Ambil semua permintaan lab dan hasilnya untuk encounter ini
        $labRequests = \App\Models\LabRequest::with('items.jenisPemeriksaan.templateFields.fieldItems')->where('encounter_id', $id)->orderByDesc('created_at')->get();
        return view('pages.observasi.index', compact('observasi', 'encounter', 'dokters', 'perawats', 'jenisPemeriksaan', 'labRequests'));
    }
    public function riwayatPenyakit($id)
    {
        $riwayatPenyakit = $this->observasiRepository->riwayatPenyakit($id);
        if ($riwayatPenyakit) {
            return response()->json($riwayatPenyakit);
        } else {
            return response()->json(['message' => 'Riwayat penyakit tidak ditemukan'], 404);
        }
    }
    public function postAnemnesis(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'dokter_ids'   => 'required|array',
            'dokter_ids.*' => 'exists:users,id', // Memastikan setiap ID dokter valid
            'keluhan_utama' => 'required|string',
            'riwayat_penyakit' => 'nullable|string',
            'riwayat_penyakit_keluarga' => 'nullable|string',
        ]);
        $result = $this->observasiRepository->postAnemnesis($request, $id);
        $this->activity('Mengisi Anamnesis', ['encounter_id' => $id], 'kunjungan');
        return response()->json([
            'status' => 200,
            'message' => 'Data Anamnesis berhasil disimpan.',
            'data' => $result
        ]);
    }
    // ambil tanda vital berdasarkan encounter_id
    public function tandaVital($id)
    {
        $tandaVital = $this->observasiRepository->tandaVital($id);
        return response()->json($tandaVital); // Selalu kembalikan 200, frontend akan handle jika null
    }
    public function postTandaVital(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nadi' => 'nullable|string|max:255',
            'pernapasan' => 'nullable|string',
            'sistolik' => 'nullable|string',
            'diastolik' => 'nullable|string',
            'suhu' => 'nullable|string',
            'berat_badan' => 'nullable|string',
            'tinggi_badan' => 'nullable|string',
            'kesadaran' => 'nullable|string|max:255',
        ]);
        $result = $this->observasiRepository->postTandaVital($request, $id);
        $this->activity('Mengisi Tanda Vital', ['encounter_id' => $id], 'kunjungan');
        return response()->json([
            'status' => 200,
            'message' => 'Data Tanda Vital berhasil disimpan.',
            'data' => $result
        ]);
    }
    public function pemeriksaanPenunjang($id)
    {
        // Gunakan repository agar termasuk Lab dan Radiologi serta memiliki field 'type'
        $items = $this->observasiRepository->pemeriksaanPenunjang($id);
        return response()->json($items);
    }
    public function postPemeriksaanPenunjang(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_pemeriksaan_id' => 'required|exists:jenis_pemeriksaan_penunjangs,id',
        ]);

        $jp = JenisPemeriksaanPenunjang::findOrFail($validated['jenis_pemeriksaan_id']);
        $message = '';

        DB::transaction(function () use ($id, $jp, $request) {
            $encounter = \App\Models\Encounter::findOrFail($id);
            $dokter = Auth::user();

            // Cek tipe pemeriksaan dari kolom 'type' ('lab' atau 'radiologi')
            if (strtolower($jp->type) === 'radiologi') {
                \App\Models\RadiologyRequest::create([
                    'encounter_id' => $id,
                    'pasien_id' => $encounter->pasien->id,
                    'jenis_pemeriksaan_id' => $jp->id,
                    'dokter_id' => $dokter->id,
                    'status' => 'processing', // Langsung processing, tidak perlu dijadwalkan
                    'price' => (float) $jp->harga,
                    'created_by' => $dokter->id,
                ]);
                $message = 'Permintaan Radiologi berhasil dibuat.';
            } else { // Default ke Lab
                $req = LabRequest::create([
                    'encounter_id' => $id,
                    'requested_by' => $dokter->id,
                    'status' => 'requested',
                    'requested_at' => now(),
                    'notes' => null,
                    'total_charge' => (int)$jp->harga,
                    'charged' => false,
                ]);
                LabRequestItem::create([
                    'lab_request_id' => $req->id,
                    'test_id' => $jp->id,
                    'test_name' => $jp->name,
                    'price' => (int)$jp->harga,
                ]);
                $message = 'Permintaan Laboratorium berhasil dibuat.';
            }

            // [CHANGED] Fee penunjang akan dibuat saat pembayaran kasir, bukan saat request
            // Logika dipindahkan ke KasirController
        });

        // Recalculate encounter totals to include LabRequest/RadiologyRequest items
        $this->observasiRepository->updateEncounterTotalTindakan($id);

        $this->activity('Membuat Permintaan Laboratorium', ['encounter_id' => $id, 'pemeriksaan' => $jp->name], 'kunjungan');
        return response()->json([
            'status' => 200,
            'message' => $message,
        ]);
    }
    public function deletePemeriksaanPenunjang($id)
    {
        $result = $this->observasiRepository->deletePemeriksaanPenunjang($id);
        $this->activity('Menghapus Permintaan Penunjang', ['item_id' => $id], 'kunjungan');
        if (is_array($result) && array_key_exists('success', $result)) {
            return response()->json([
                'status' => $result['success'],
                'message' => $result['message'] ?? ($result['success'] ? 'Berhasil dihapus.' : 'Gagal menghapus.'),
            ]);
        }
        return response()->json([
            'status' => (bool)$result,
            'message' => $result ? 'Berhasil dihapus.' : 'Gagal menghapus.'
        ]);
    }
    public function printPemeriksaanPenunjang($id)
    {
        $pemeriksaan = \App\Models\PemeriksaanPenunjang::findOrFail($id);
        $encounter = \App\Models\Encounter::with('practitioner', 'diagnosis', 'nurses')->findOrFail($pemeriksaan->encounter_id);
        $pasien = \App\Models\Pasien::where('rekam_medis', $encounter->rekam_medis)->first();

        // Cek jenis pemeriksaan untuk menggunakan template yang sesuai
        $template = 'pages.observasi.pemeriksaan_penunjang_print';
        if (
            stripos($pemeriksaan->jenis_pemeriksaan, 'ECHOCARDIOGRAPHY') !== false ||
            stripos($pemeriksaan->jenis_pemeriksaan, 'ECHO') !== false
        ) {
            $template = 'pages.observasi.pemeriksaan_echo_print';
        }

        return view($template, compact('pemeriksaan', 'encounter', 'pasien'));
    }

    public function downloadPemeriksaanPenunjang($id)
    {
        $pemeriksaan = \App\Models\PemeriksaanPenunjang::findOrFail($id);
        $encounter = \App\Models\Encounter::with('practitioner', 'diagnosis', 'nurses')->findOrFail($pemeriksaan->encounter_id);
        $pasien = \App\Models\Pasien::where('rekam_medis', $encounter->rekam_medis)->first();

        // Cek jenis pemeriksaan untuk menggunakan template yang sesuai
        $template = 'pages.observasi.pemeriksaan_penunjang_print';
        if (
            stripos($pemeriksaan->jenis_pemeriksaan, 'ECHOCARDIOGRAPHY') !== false ||
            stripos($pemeriksaan->jenis_pemeriksaan, 'ECHO') !== false
        ) {
            $template = 'pages.observasi.pemeriksaan_echo_print';
        }

        $pdf = Pdf::loadView($template, compact('pemeriksaan', 'encounter', 'pasien') + ['pdf' => true]);
        return $pdf->download('hasil-pemeriksaan-' . $encounter->rekam_medis . '.pdf');
    }

    // AJAX: Ambil semua LabRequest beserta items untuk encounter
    public function labRequests($id)
    {
        $rows = \App\Models\LabRequest::with('items.jenisPemeriksaan.templateFields.fieldItems')
            ->where('encounter_id', $id)
            ->orderByDesc('created_at')
            ->get();
        return response()->json($rows->map(function ($req) {
            return [
                'id' => $req->id,
                'status' => $req->status,
                'created_at' => optional($req->created_at)->format('d M Y H:i'),
                'items' => $req->items->map(function ($it) {
                    // Build template metadata for grouped fields
                    $templateMeta = [];
                    if ($it->jenisPemeriksaan && $it->jenisPemeriksaan->templateFields) {
                        foreach ($it->jenisPemeriksaan->templateFields as $field) {
                            if ($field->field_type === 'group' && $field->fieldItems) {
                                $groupMeta = [];
                                foreach ($field->fieldItems as $item) {
                                    $groupMeta[$item->item_name] = [
                                        'label' => $item->examination_name ?? $item->item_label,
                                        'unit' => $item->unit,
                                        'normal_range' => $item->normal_range,
                                    ];
                                }
                                $templateMeta[$field->field_name] = $groupMeta;
                            }
                        }
                    }

                    return [
                        'test_name' => $it->test_name,
                        'price' => (int) $it->price,
                        'result_payload' => $it->result_payload,
                        'result_value' => $it->result_value,
                        'result_unit' => $it->result_unit,
                        'result_reference' => $it->result_reference,
                        'result_notes' => $it->result_notes,
                        'template_meta' => $templateMeta, // Add template metadata
                    ];
                })->toArray(),
            ];
        })->toArray());
    }

    // Print hasil lab dari halaman observasi
    public function printLabRequest($id)
    {
        $req = \App\Models\LabRequest::with(['encounter.user', 'items.jenisPemeriksaan.templateFields.fieldItems', 'requester'])->findOrFail($id);
        if ($req->status !== 'completed') {
            return redirect()->back()->with('error', 'Hasil hanya dapat dicetak jika status Completed.');
        }
        return view('pages.lab.requests.print-medical', compact('req'));
    }

    // AJAX: Ambil semua RadiologyRequest beserta ringkas hasil terakhir untuk encounter
    public function radiologyRequests($id)
    {
        $rows = \App\Models\RadiologyRequest::with(['jenis', 'results' => function ($q) {
            $q->with('radiologist')->orderByDesc('created_at');
        }])
            ->where('encounter_id', $id)
            ->orderByDesc('created_at')
            ->get();
        return response()->json($rows->map(function ($req) {
            $latest = $req->results->first();
            return [
                'id' => $req->id,
                'status' => $req->status,
                'created_at' => optional($req->created_at)->format('d M Y H:i'),
                'jenis_name' => optional($req->jenis)->name,
                'price' => (int)($req->price ?? 0),
                'latest' => $latest ? [
                    'findings' => $latest->findings,
                    'impression' => $latest->impression,
                    'payload' => $latest->payload, // Include custom fields data
                    'radiologist_name' => optional($latest->radiologist)->name,
                ] : null,
            ];
        })->toArray());
    }

    // Batalkan permintaan radiologi dari halaman Observasi (requested -> canceled)
    public function cancelRadiologyRequest($id)
    {
        try {
            $req = \App\Models\RadiologyRequest::findOrFail($id);
            $from = $req->status ?? 'requested';
            if (!in_array($from, ['requested', 'processing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tidak bisa dibatalkan pada status ' . ucfirst($from) . '.',
                ], 422);
            }
            $req->status = 'canceled';
            $req->save();
            return response()->json([
                'success' => true,
                'message' => 'Permintaan radiologi dibatalkan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan permintaan radiologi.',
            ], 500);
        }
    }

    // Hapus permintaan radiologi (hapus total seperti Lab)
    public function destroyRadiologyRequest($id)
    {
        try {
            $req = \App\Models\RadiologyRequest::find($id);
            if (!$req) {
                return response()->json(['success' => false, 'message' => 'Permintaan radiologi tidak ditemukan.'], 404);
            }
            // Boleh hapus jika masih requested/canceled (belum selesai)
            if (!in_array($req->status, ['requested', 'canceled'])) {
                return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus permintaan pada status ' . ucfirst($req->status) . '.'], 422);
            }
            $encounterId = $req->encounter_id;
            // Hapus relasi
            \App\Models\RadiologyResult::where('radiology_request_id', $req->id)->delete();
            \App\Models\RadiologySchedule::where('radiology_request_id', $req->id)->delete();
            // Hapus request
            $req->delete();
            // Update total encounter
            $this->observasiRepository->updateEncounterTotalTindakan($encounterId);
            return response()->json(['success' => true, 'message' => 'Permintaan radiologi berhasil dihapus.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus permintaan radiologi.'], 500);
        }
    }

    public function getTemplateFields($id)
    {
        $jenisPemeriksaan = JenisPemeriksaanPenunjang::with('templateFields.fieldItems')->find($id);
        $fields = $jenisPemeriksaan ? $jenisPemeriksaan->templateFields : [];
        return response()->json($fields);
    }

    // ambil data tindakan
    public function getTindakan($id)
    {
        $tindakan = $this->observasiRepository->getTindakan($id);
        if ($tindakan) {
            return response()->json($tindakan);
        } else {
            return response()->json(['message' => 'Tindakan tidak ditemukan'], 404);
        }
    }
    // ambil data tindakan berdasarkan encounter_id
    public function getTindakanEncounter($id)
    {
        $tindakan = $this->observasiRepository->getTindakanEncounter($id);
        // Always return array, even if empty
        return response()->json($tindakan ?? []);
    }
    // post tindakan encounter
    public function postTindakanEncounter(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'jenis_tindakan' => 'required|string|max:255',
            'qty' => 'required|integer|max:255',
        ]);
        $result = $this->observasiRepository->postTindakanEncounter($request, $id);
        $this->activity('Menambahkan Tindakan Medis', ['encounter_id' => $id, 'jenis_tindakan' => $request->input('jenis_tindakan'), 'qty' => (int)$request->input('qty')], 'kunjungan');
        return response()->json([
            'status' => 200,
            'message' => 'Data Tindakan berhasil disimpan.',
            'data' => $result
        ]);
    }
    // hapus tindakan
    public function deleteTindakanEncounter($id)
    {
        $result = $this->observasiRepository->deleteTindakanEncounter($id);
        $this->activity('Menghapus Tindakan Medis', ['tindakan_encounter_id' => $id], 'kunjungan');
        return response()->json([
            'status' => $result['success'],
            'message' => $result['message']
        ]);
    }
    // Ambil data icd10
    public function getIcd10($id)
    {
        $query = \App\Models\Icd10::query();

        $search = request('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Untuk kebutuhan Select2 AJAX, biasanya tidak perlu paginate
        return $query->limit(20)->get(['id', 'code', 'description']);
    }
    // Ambil semua data diagosis sesuai encounter_id
    public function getDiagnosis($id)
    {
        $diagnosis = $this->observasiRepository->getDiagnosis($id);
        return response()->json($diagnosis);
    }
    // post diagnosis
    public function postDiagnosis(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'icd10_id' => 'required|string|max:255',
            'diagnosis_type' => 'required|string|max:255',
        ]);
        $result = $this->observasiRepository->postDiagnosis($request, $id);
        $this->activity('Menambahkan Diagnosis', ['encounter_id' => $id, 'icd10_id' => $request->input('icd10_id')], 'kunjungan');
        return response()->json([
            'status' => 200,
            'message' => 'Data Diagnosis berhasil disimpan.',
            'data' => $result
        ]);
    }
    // hapus diagnosis
    public function deleteDiagnosis($id)
    {
        $result = $this->observasiRepository->deleteDiagnosis($id);
        $this->activity('Menghapus Diagnosis', ['diagnosis_id' => $id], 'kunjungan');
        return response()->json([
            'status' => $result['success'],
            'message' => $result['message']
        ]);
    }
    // ambil data resep
    public function getResep($id)
    {
        $resep = $this->observasiRepository->getResep($id);
        if ($resep) {
            return response()->json($resep);
        } else {
            return response()->json($resep);
        }
    }
    // post resep
    public function postResep(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'masa_pemakaian_hari' => 'required|string|max:255',
        ], [
            'masa_pemakaian_hari.required' => 'Masa pemakaian hari harus diisi.',
        ]);
        $result = $this->observasiRepository->postResep($request, $id);
        $this->activity('Membuat Resep', ['encounter_id' => $id, 'masa_pemakaian_hari' => $request->input('masa_pemakaian_hari')], 'kunjungan');
        return response()->json([
            'status' => 200,
            'message' => 'Data Resep berhasil disimpan.',
            'data' => $result
        ]);
    }
    // getProduk apotek
    public function getProdukApotek($id)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Data Produk Apotek berhasil diambil.',
            'data' => $this->observasiRepository->getProdukApotek($id)
        ]);
    }
    // postResepDetail
    public function postResepDetail(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'product_apotek_id' => 'required|string|max:255',
            'qty_obat' => 'required|integer|max:255',
            'aturan_pakai' => 'required|string|max:255',
        ]);
        $result = $this->observasiRepository->postResepDetail($request, $id);

        if ($result['success'] == false) {
            return response()->json([
                'status' => 400,
                'message' => $result['message']
            ], 400);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Data Resep Detail berhasil disimpan.',
                'data' => $result
            ]);
            $this->activity('Menambahkan Obat ke Resep', [
                'encounter_id' => $id,
                'product_apotek_id' => $request->input('product_apotek_id'),
                'qty' => (int)$request->input('qty_obat'),
            ], 'kunjungan');
        }
    }
    // hapus resep detail
    public function deleteResepDetail($id)
    {
        $result = $this->observasiRepository->deleteResepDetail($id);
        $this->activity('Menghapus Obat dari Resep', ['resep_detail_id' => $id], 'kunjungan');
        return response()->json([
            'status' => $result['success'],
            'message' => $result['message']
        ]);
    }
    // ambil data encounter
    public function getEncounterById($id)
    {
        $encounter = $this->observasiRepository->getEncounterById($id);
        if ($encounter) {
            return response()->json($encounter);
        } else {
            return response()->json($encounter);
        }
    }

    // Ringkasan encounter terakhir pasien
    public function lastEncounterSummary($id)
    {
        $summary = $this->observasiRepository->getLastEncounterSummary($id);
        return response()->json($summary ?? []);
    }

    // Data lengkap encounter terakhir untuk copy
    public function lastEncounterFull($id)
    {
        try {
            $current = \App\Models\Encounter::find($id);
            if (!$current) {
                return response()->json(['error' => 'Encounter tidak ditemukan'], 404);
            }

            $prev = \App\Models\Encounter::where('rekam_medis', $current->rekam_medis)
                ->where('id', '!=', $id)
                ->orderByDesc('created_at')
                ->first();

            if (!$prev) {
                return response()->json(['error' => 'Tidak ada kunjungan sebelumnya'], 404);
            }

            // Anamnesis lengkap
            $anamnesis = \App\Models\Anamnesis::where('encounter_id', $prev->id)->first();

            // Riwayat Penyakit (uses pasien_id, not encounter_id)
            $riwayatPenyakit = null;
            $pasien = \App\Models\Pasien::where('rekam_medis', $prev->rekam_medis)->first();
            if ($pasien) {
                $riwayatPenyakit = \App\Models\RiwayatPenyakit::where('pasien_id', $pasien->id)->first();
            }

            // Diagnosis
            $diagnosis = \App\Models\Diagnosis::where('encounter_id', $prev->id)->get()->map(function ($d) {
                return [
                    'diagnosis_code' => $d->diagnosis_code,
                    'diagnosis_description' => $d->diagnosis_description,
                    'diagnosis_type' => $d->diagnosis_type,
                ];
            })->toArray();

            // Tindakan
            $tindakan = \App\Models\TindakanEncounter::where('encounter_id', $prev->id)->get()->map(function ($t) {
                return [
                    'tindakan_id' => $t->tindakan_id,
                    'nama_tindakan' => $t->tindakan->nama_tindakan ?? null,
                    'qty' => $t->qty,
                    'harga' => $t->harga,
                ];
            })->toArray();

            // Resep
            $resep = \App\Models\Resep::where('encounter_id', $prev->id)->latest()->first();
            $resepItems = [];
            if ($resep) {
                $resepDetails = \App\Models\ResepDetail::where('resep_id', $resep->id)->get();
                foreach ($resepDetails as $d) {
                    $resepItems[] = [
                        'product_apotek_id' => $d->product_apotek_id,
                        'nama_obat' => $d->nama_obat,
                        'qty' => $d->qty,
                        'harga' => $d->harga,
                        'total_harga' => $d->total_harga,
                        'aturan_pakai' => $d->aturan_pakai,
                        'status' => $d->status,
                    ];
                }
            }

            return response()->json([
                'encounter_id' => $prev->id,
                'anamnesis' => $anamnesis ? [
                    'keluhan_utama' => $anamnesis->keluhan_utama,
                    'riwayat_penyakit' => $riwayatPenyakit->riwayat_penyakit ?? null,
                    'riwayat_penyakit_keluarga' => $riwayatPenyakit->riwayat_penyakit_keluarga ?? null,
                ] : null,
                'ttv' => \App\Models\TandaVital::where('encounter_id', $prev->id)->first(),
                'diagnosis' => $diagnosis,
                'tindakan' => $tindakan,
                'resep' => $resepItems,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Copy data encounter terakhir ke encounter saat ini
    public function copyLastEncounter(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $current = \App\Models\Encounter::find($id);
            if (!$current) {
                return response()->json(['success' => false, 'message' => 'Encounter tidak ditemukan'], 404);
            }

            $prev = \App\Models\Encounter::where('rekam_medis', $current->rekam_medis)
                ->where('id', '!=', $id)
                ->orderByDesc('created_at')
                ->first();

            if (!$prev) {
                return response()->json(['success' => false, 'message' => 'Tidak ada kunjungan sebelumnya'], 404);
            }

            $copiedItems = [];

            // 1. Copy Anamnesis
            $prevAnamnesis = \App\Models\Anamnesis::where('encounter_id', $prev->id)->first();
            if ($prevAnamnesis) {
                \App\Models\Anamnesis::updateOrCreate(
                    ['encounter_id' => $id],
                    ['keluhan_utama' => $prevAnamnesis->keluhan_utama]
                );
                $copiedItems[] = 'Anamnesis';
            }

            // 2. Copy Riwayat Penyakit (uses pasien_id, not encounter_id)
            $pasien = \App\Models\Pasien::where('rekam_medis', $current->rekam_medis)->first();
            if ($pasien) {
                $prevRiwayat = \App\Models\RiwayatPenyakit::where('pasien_id', $pasien->id)->first();
                if ($prevRiwayat) {
                    // Riwayat Penyakit is patient-level, so it's already shared
                    // Just mark as copied if exists
                    $copiedItems[] = 'Riwayat Penyakit';
                }
            }

            // 3. Copy TTV
            $prevTTV = \App\Models\TandaVital::where('encounter_id', $prev->id)->first();
            if ($prevTTV) {
                \App\Models\TandaVital::updateOrCreate(
                    ['encounter_id' => $id],
                    [
                        'nadi' => $prevTTV->nadi,
                        'pernapasan' => $prevTTV->pernapasan,
                        'sistolik' => $prevTTV->sistolik,
                        'diastolik' => $prevTTV->diastolik,
                        'suhu' => $prevTTV->suhu,
                        'kesadaran' => $prevTTV->kesadaran,
                        'tinggi_badan' => $prevTTV->tinggi_badan,
                        'berat_badan' => $prevTTV->berat_badan,
                    ]
                );
                $copiedItems[] = 'Tanda Vital';
            }

            // 4. Copy Diagnosis
            $prevDiagnosis = \App\Models\Diagnosis::where('encounter_id', $prev->id)->get();
            $diagnosisCount = 0;
            foreach ($prevDiagnosis as $diag) {
                // Check if not already exists
                $exists = \App\Models\Diagnosis::where('encounter_id', $id)
                    ->where('diagnosis_code', $diag->diagnosis_code)
                    ->exists();

                if (!$exists) {
                    \App\Models\Diagnosis::create([
                        'encounter_id' => $id,
                        'diagnosis_code' => $diag->diagnosis_code,
                        'diagnosis_description' => $diag->diagnosis_description,
                        'diagnosis_type' => $diag->diagnosis_type,
                        'id_petugas' => Auth::user()->id_petugas,
                        'petugas_name' => Auth::user()->name,
                    ]);
                    $diagnosisCount++;
                }
            }
            if ($diagnosisCount > 0) {
                $copiedItems[] = "$diagnosisCount Diagnosis";
            }

            // 5. Copy Tindakan
            $prevTindakan = \App\Models\TindakanEncounter::where('encounter_id', $prev->id)->get();
            $tindakanCount = 0;
            foreach ($prevTindakan as $tind) {
                // Check if not already exists
                $exists = \App\Models\TindakanEncounter::where('encounter_id', $id)
                    ->where('tindakan_id', $tind->tindakan_id)
                    ->exists();

                if (!$exists) {
                    \App\Models\TindakanEncounter::create([
                        'encounter_id' => $id,
                        'tindakan_id' => $tind->tindakan_id,
                        'tindakan_name' => $tind->tindakan_name,
                        'tindakan_description' => $tind->tindakan_description,
                        'tindakan_harga' => $tind->tindakan_harga,
                        'qty' => $tind->qty,
                        'total_harga' => $tind->total_harga,
                        'id_petugas' => Auth::user()->id_petugas,
                        'petugas_name' => Auth::user()->name,
                    ]);
                    $tindakanCount++;
                }
            }
            if ($tindakanCount > 0) {
                $copiedItems[] = "$tindakanCount Tindakan";
            }

            // 6. Copy Resep
            $prevResep = \App\Models\Resep::where('encounter_id', $prev->id)->latest()->first();
            if ($prevResep) {
                // Check if resep already exists for current encounter
                $currentResep = \App\Models\Resep::where('encounter_id', $id)->first();

                if (!$currentResep) {
                    // Generate kode_resep
                    $lastKodeResep = \App\Models\Resep::max('kode_resep');
                    if ($lastKodeResep) {
                        $lastNumber = (int) substr($lastKodeResep, 3);
                        $kodeResep = 'RSP' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
                    } else {
                        $kodeResep = 'RSP00001';
                    }

                    // Create new resep
                    $currentResep = \App\Models\Resep::create([
                        'encounter_id' => $id,
                        'kode_resep' => $kodeResep,
                        'masa_pemakaian_hari' => $prevResep->masa_pemakaian_hari,
                        'dokter' => Auth::user()->name,
                    ]);
                }

                $prevResepDetails = \App\Models\ResepDetail::where('resep_id', $prevResep->id)->get();
                $resepCount = 0;
                foreach ($prevResepDetails as $detail) {
                    // Check if not already exists (using product_apotek_id)
                    $exists = \App\Models\ResepDetail::where('resep_id', $currentResep->id)
                        ->where('product_apotek_id', $detail->product_apotek_id)
                        ->exists();

                    if (!$exists) {
                        \App\Models\ResepDetail::create([
                            'resep_id' => $currentResep->id,
                            'product_apotek_id' => $detail->product_apotek_id,
                            'nama_obat' => $detail->nama_obat,
                            'satuan' => $detail->satuan,
                            'qty' => $detail->qty,
                            'harga' => $detail->harga,
                            'total_harga' => $detail->total_harga,
                            'aturan_pakai' => $detail->aturan_pakai,
                            'status' => 'Diajukan',
                        ]);
                        $resepCount++;
                    }
                }
                if ($resepCount > 0) {
                    $copiedItems[] = "$resepCount Resep Obat";
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di-copy: ' . implode(', ', $copiedItems),
                'copied_items' => $copiedItems
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal meng-copy data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Buat diskon tindakan
    public function postDiskonTindakan(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'diskon_tindakan' => 'required|numeric|min:0',
        ], [
            'diskon_tindakan.required' => 'Diskon tindakan harus diisi.',
            'diskon_tindakan.numeric' => 'Diskon tindakan harus berupa angka.',
            'diskon_tindakan.min' => 'Diskon tindakan minimal 0.',
            'diskon_tindakan.max' => 'Diskon tindakan maksimal 100.',
        ]);
        $result = $this->observasiRepository->postDiskonTindakan($request, $id);
        return response()->json($result);
    }
    // Buat diskon resep
    public function postDiskonResep(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'diskon_resep' => 'required|numeric|min:0',
        ], [
            'diskon_resep.required' => 'Diskon resep harus diisi.',
            'diskon_resep.numeric' => 'Diskon resep harus berupa angka.',
            'diskon_resep.min' => 'Diskon resep minimal 0.',
            'diskon_resep.max' => 'Diskon resep maksimal 100.',
        ]);
        $result = $this->observasiRepository->postDiskonResep($request, $id);
        return response()->json($result);
    }
    // post catatan encounter
    public function postCatatanEncounter(Request $request, $id)
    {
        // Ambil tipe encounter untuk validasi kondisional
        $encounter = \App\Models\Encounter::find($id);
        if (!$encounter) {
            return response()->json(['message' => 'Encounter tidak ditemukan.'], 404);
        }

        $request->validate([
            'catatan' => 'nullable|string|max:255',
            'status_pulang' => 'required|numeric',
            // Validasi perawat_ids, wajib jika tipe encounter adalah 1 (RJ) atau 3 (IGD)
            'perawat_ids' => 'required_if:encounter.type,1,3|array',
            'perawat_ids.*' => 'exists:users,id',
        ], [
            'perawat_ids.required_if' => 'Perawat harus dipilih untuk Rawat Jalan atau Rawat Darurat.',
            'perawat_ids.array' => 'Format data perawat tidak valid.'
        ]);
        $result = $this->observasiRepository->postCatatanEncounter($request, $id);
        return response()->json($result);
    }

    public function getInpatientAdmission($id)
    {
        $getInpatientAdmission = $this->observasiRepository->getInpatientAdmission($id);
        if (!$getInpatientAdmission) {
            abort(404, 'Rawat inap tidak ditemukan');
        }
        // Samakan variabel dengan Observasi index
        $encounter = \App\Models\Encounter::findOrFail($getInpatientAdmission->encounter_id);
        $observasi = $encounter->id;
        $dokters = $this->observasiRepository->getDokters($observasi);
        $perawats = $this->observasiRepository->getPerawats($observasi);
        $jenisPemeriksaan = \App\Models\JenisPemeriksaanPenunjang::all();
        $labRequests = \App\Models\LabRequest::with('items.jenisPemeriksaan.templateFields.fieldItems')->where('encounter_id', $observasi)->orderByDesc('created_at')->get();
        return view('pages.observasi.rinap', compact('encounter', 'observasi', 'dokters', 'perawats', 'jenisPemeriksaan', 'labRequests'));
    }
    public function postInpatientTreatment(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'type' => 'required|string|max:255',
            'treatment_date' => 'required|date',
            'tindakan_id' => 'required|string|max:255',
            'result' => 'nullable|string|max:255',
        ]);

        $result = $this->observasiRepository->postInpatientTreatment($request, $id);
        if ($result['success'] == false) {
            return response()->json([
                'status' => 404,
                'message' => $result['message']
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Tindakan berhasil disimpan.',
                'data' => $result
            ]);
        }
    }
    //getInpatientTreatment
    public function getInpatientTreatment($id)
    {
        $inpatientTreatment = $this->observasiRepository->getInpatientTreatment($id);
        return response()->json($inpatientTreatment ?? []);
    }
    public function deleteInpatientTreatment($id)
    {
        $result = $this->observasiRepository->destroyInpatientTreatment($id);
        return response()->json([
            'status' => $result['success'],
            'message' => $result['message']
        ]);
    }
    //getInpatientDailyMedications
    public function getInpatientDailyMedications($id)
    {
        $inpatientDailyMedications = $this->observasiRepository->getInpatientDailyMedications($id);
        if ($inpatientDailyMedications) {
            return response()->json($inpatientDailyMedications);
        } else {
            return response()->json(['message' => 'Obat tidak ditemukan'], 404);
        }
    }
    public function updateInpatientDailyMedicationStatus($id)
    // Update status obat harian
    {
        $result = $this->observasiRepository->updateInpatientDailyMedicationStatus($id);
        if ($result['success'] == false) {
            return response()->json([
                'status' => 404,
                'message' => $result['message']
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Status obat berhasil diperbarui.',
                'data' => $result
            ]);
        }
    }
    //postInpatientDailyMedication
    public function postInpatientDailyMedication(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'product_apotek_id' => 'required|string|max:255',
            'jumlah' => 'required|integer|max:255',
            'dosage_instructions' => 'required|string|max:255',
            'frequensi' => 'required|string|max:255',
            'route' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
            'medicine_date' => 'required|date',
        ]);

        $result = $this->observasiRepository->postInpatientDailyMedication($request, $id);
        if ($result['success'] == false) {
            return response()->json([
                'status' => 404,
                'message' => $result['message']
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Obat berhasil disimpan.',
                'data' => $result
            ]);
        }
    }
    //deleteInpatientDailyMedication
    public function deleteInpatientDailyMedication($id)
    {
        $result = $this->observasiRepository->deleteInpatientDailyMedication($id);
        if ($result['success'] == false) {
            return response()->json([
                'status' => 404,
                'message' => $result['message']
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Obat berhasil dihapus.'
            ]);
        }
    }

    /**
     * Get lab request results for modal display
     */
    public function getLabResults($id)
    {
        try {
            $labRequest = LabRequest::with([
                'encounter.pasien',
                'items.jenisPemeriksaan',
                'requester'
            ])->findOrFail($id);

            if ($labRequest->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil lab belum tersedia. Status: ' . ucfirst($labRequest->status)
                ], 404);
            }

            $pasien = $labRequest->encounter->pasien;

            $data = [
                'klinik_nama' => config('app.name', 'Klinik Bambu Dua'),
                'pasien_no_rm' => $pasien->no_rm ?? '-',
                'pasien_nama' => $pasien->nama ?? '-',
                'pasien_tgl_lahir' => $pasien->tgl_lahir ? \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d M Y') : '-',
                'nomor_permintaan' => $labRequest->id,
                'tanggal_permintaan' => $labRequest->requested_at ? $labRequest->requested_at->format('d M Y H:i') : '-',
                'dokter_nama' => $labRequest->requester->name ?? '-',
                'catatan' => $labRequest->notes,
                'petugas_lab' => $labRequest->completed_by ? \App\Models\User::find($labRequest->completed_by)?->name : null,
                'items' => []
            ];

            foreach ($labRequest->items as $item) {
                // Get data from result_payload JSON first, fallback to regular columns
                $payload = $item->result_payload ?? [];

                $namaPemeriksaan = $item->test_name ?? ($item->jenisPemeriksaan->name ?? '-');

                // Check if payload has nested structure (categorized results)
                $hasNestedPayload = false;
                if (is_array($payload) && !empty($payload)) {
                    // Check if all values are arrays (nested structure)
                    $hasNestedPayload = !empty(array_filter($payload, fn($val) => is_array($val)));
                }

                if ($hasNestedPayload) {
                    // Nested structure - add category-based results
                    foreach ($payload as $category => $tests) {
                        if (is_array($tests)) {
                            foreach ($tests as $testName => $testValue) {
                                $data['items'][] = [
                                    'nama_pemeriksaan' => ucwords(str_replace('_', ' ', $category)) . ' - ' . ucwords(str_replace('_', ' ', $testName)),
                                    'hasil' => $testValue ?? '-',
                                    'nilai_normal' => '-',
                                    'satuan' => '',
                                    'catatan' => '',
                                    'is_abnormal' => false,
                                    'is_category_item' => true
                                ];
                            }
                        }
                    }
                } else {
                    // Simple structure or fallback to regular columns
                    $hasil = $payload['result_value'] ?? $item->result_value ?? '-';
                    $nilaiNormal = $payload['result_reference'] ?? $item->result_reference ?? '-';
                    $satuan = $payload['result_unit'] ?? $item->result_unit ?? '';
                    $catatan = $payload['result_notes'] ?? $item->result_notes ?? '';

                    // Check if result is abnormal (basic check)
                    $isAbnormal = false;
                    if (is_numeric($hasil) && $nilaiNormal) {
                        // Try to parse reference range (e.g., "10-20" or "< 10")
                        if (preg_match('/(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)/', $nilaiNormal, $matches)) {
                            $min = (float)$matches[1];
                            $max = (float)$matches[2];
                            $value = (float)$hasil;
                            $isAbnormal = ($value < $min || $value > $max);
                        }
                    }

                    $data['items'][] = [
                        'nama_pemeriksaan' => $namaPemeriksaan,
                        'hasil' => $hasil,
                        'nilai_normal' => $nilaiNormal,
                        'satuan' => $satuan,
                        'catatan' => $catatan,
                        'is_abnormal' => $isAbnormal,
                        'is_category_item' => false
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat hasil lab: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get radiology request results for modal display
     */
    public function getRadiologyResults($id)
    {
        try {
            $radioRequest = \App\Models\RadiologyRequest::with([
                'encounter.pasien',
                'jenis',
                'dokter',
                'results.radiologist'
            ])->findOrFail($id);

            if ($radioRequest->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil radiologi belum tersedia. Status: ' . ucfirst($radioRequest->status)
                ], 404);
            }

            $pasien = $radioRequest->encounter->pasien;
            $latestResult = $radioRequest->results->first(); // Get the latest result

            // Get data from payload JSON first, fallback to regular columns
            $payload = $latestResult->payload ?? [];

            // Extract findings, impression from payload or regular columns
            $findings = $latestResult->findings ?? '-';
            $impression = $latestResult->impression ?? '-';
            $technique = null;
            $clinicalInfo = null;
            $conclusion = null;

            // Extract component results from payload (for Echo, USG, etc.)
            $componentResults = [];
            $hasComponentData = false;

            if (is_array($payload) && !empty($payload)) {
                // Check if payload contains standard fields or component data
                if (isset($payload['findings']) || isset($payload['impression']) || isset($payload['technique']) || isset($payload['clinical_info'])) {
                    // Standard format in payload
                    $findings = $payload['findings'] ?? $findings;
                    $impression = $payload['impression'] ?? $impression;
                    $technique = $payload['technique'] ?? null;
                    $clinicalInfo = $payload['clinical_info'] ?? null;
                    $conclusion = $payload['conclusion'] ?? null;
                } else {
                    // Component-based data (Echo, USG, Rontgen, etc.)
                    // This means payload contains detailed measurements/findings
                    $hasComponentData = true;
                    foreach ($payload as $key => $value) {
                        if (!empty($value) && $value !== '-') {
                            $componentResults[] = [
                                'nama' => $key,
                                'nilai' => $value
                            ];
                        }
                    }

                    // If we have component data, clear findings/impression to avoid redundancy
                    // unless they contain meaningful narrative text
                    if (!empty($componentResults)) {
                        // Only keep findings/impression if they're not just placeholder values
                        if (in_array(strtolower(trim($findings)), ['', '-', 'null', '11'])) {
                            $findings = null;
                        }
                        if (in_array(strtolower(trim($impression)), ['', '-', 'null', '11'])) {
                            $impression = null;
                        }
                    }
                }
            }

            $data = [
                'klinik_nama' => config('app.name', 'Klinik Bambu Dua'),
                'pasien_no_rm' => $pasien->no_rm ?? '-',
                'pasien_nama' => $pasien->nama ?? '-',
                'pasien_tgl_lahir' => $pasien->tgl_lahir ? \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d M Y') : '-',
                'nomor_permintaan' => $radioRequest->id,
                'tanggal_permintaan' => $radioRequest->created_at ? $radioRequest->created_at->format('d M Y H:i') : '-',
                'dokter_nama' => $radioRequest->dokter->name ?? '-',
                'items' => [
                    [
                        'nama_pemeriksaan' => $radioRequest->jenis->name ?? '-'
                    ]
                ],
                'component_results' => $componentResults,
                'findings' => $findings,
                'impression' => $impression,
                'technique' => $technique,
                'clinical_info' => $clinicalInfo,
                'conclusion' => $conclusion,
                'radiolog' => $latestResult->radiologist->name ?? null,
                'images' => []
            ];            // Parse files for images if available
            if ($latestResult && $latestResult->files) {
                $files = is_string($latestResult->files) ? json_decode($latestResult->files, true) : $latestResult->files;
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $data['images'][] = [
                            'url' => asset('storage/' . $file)
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat hasil radiologi: ' . $e->getMessage()
            ], 500);
        }
    }
}
