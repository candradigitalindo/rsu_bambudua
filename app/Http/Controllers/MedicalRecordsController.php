<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Pasien;
use App\Models\Encounter;
use App\Models\Diagnosis;
use App\Models\TindakanEncounter;
use App\Models\LabRequest;
use App\Models\Resep;
use App\Models\MedicalRecordFile;
use Illuminate\Support\Facades\Auth;

class MedicalRecordsController extends Controller
{
    public function dashboard()
    {
        $totalPasien = Pasien::count();
        $totalEncounter = Encounter::count();
        $bulanIniEncounter = Encounter::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $rawatJalan = Encounter::where('type', 1)->count();
        $rawatInap = Encounter::where('type', 2)->count();
        $igd = Encounter::where('type', 3)->count();

        // Encounters per month (current year)
        $year = now()->year;
        $raw = Encounter::selectRaw('MONTH(created_at) m, COUNT(*) c')
            ->whereYear('created_at', $year)
            ->groupBy('m')
            ->pluck('c', 'm');
        $categories = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $seriesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $seriesData[] = (int)($raw[$i] ?? 0);
        }
        $grafikData = [
            'categories' => $categories,
            'series' => [['name' => 'Kunjungan', 'data' => $seriesData]],
        ];

        // Top 5 diagnosis (code + description)
        $topDiagnosis = Diagnosis::select('diagnosis_code', 'diagnosis_description', DB::raw('COUNT(*) as total'))
            ->whereNotNull('diagnosis_code')
            ->groupBy('diagnosis_code', 'diagnosis_description')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        $topDiag = [
            'labels' => $topDiagnosis->map(fn($d) => trim(($d->diagnosis_code ?: '') . ($d->diagnosis_code && $d->diagnosis_description ? ' - ' : '') . ($d->diagnosis_description ?: '')))->toArray(),
            'data' => $topDiagnosis->pluck('total')->map(fn($v) => (int)$v)->toArray(),
        ];

        return view('pages.medical-records.dashboard', compact(
            'totalPasien',
            'totalEncounter',
            'bulanIniEncounter',
            'rawatJalan',
            'rawatInap',
            'igd',
            'grafikData',
            'topDiag'
        ));
    }

    public function riwayat(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $patients = Pasien::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('rekam_medis', 'like', "%{$q}%")
                        ->orWhere('mr_lama', 'like', "%{$q}%")
                        ->orWhere('no_hp', 'like', "%{$q}%");
                });
            })
            ->withCount('encounters')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('pages.medical-records.riwayat', compact('patients'));
    }

    public function riwayatPasien($rekam_medis)
    {
        $pasien = Pasien::where('rekam_medis', $rekam_medis)->first();

        if (!$pasien) {
            return response()->json(['status' => false, 'message' => 'Pasien tidak ditemukan'], 404);
        }

        // Ambil encounters dengan detail lengkap
        $encounters = Encounter::with([
            'practitioner',
            'tandaVital',
            'resep.details',
            'diagnosis',
            'clinic',
            'nurses',
            'tindakan',
            'labRequests.items',
            'radiologyRequests.jenis',
            'radiologyRequests.results'
        ])
            ->where('rekam_medis', $pasien->rekam_medis)
            ->orderByDesc('created_at')
            ->paginate(10);

        $data = $encounters->map(function ($e) {
            // Diagnosis
            $diagnoses = ($e->diagnosis ?? collect())->map(function ($d) {
                $code = $d->diagnosis_code ?: '';
                $desc = $d->diagnosis_description ?: '';
                $type = $d->diagnosis_type ?: '';
                return trim($code . ($code && $desc ? ' - ' : '') . $desc . ($type ? " ({$type})" : ''));
            })->filter()->values();

            // Dokter/practitioner & Perawat
            $doctors = ($e->practitioner ?? collect())->pluck('name')->filter()->values();
            $nurses = ($e->nurses ?? collect())->pluck('name')->filter()->values();

            // Tujuan Kunjungan & Jaminan
            $purposeMap = [
                1 => 'Kunjungan Sehat (Promotif/Preventif)',
                2 => 'Rehabilitatif',
                3 => 'Kunjungan Sakit',
                4 => 'Darurat',
                5 => 'Kontrol / Tindak Lanjut',
                6 => 'Treatment',
                7 => 'Konsultasi',
            ];
            $purpose = $purposeMap[$e->tujuan_kunjungan] ?? '-';
            $insurance = $e->jenis_jaminan == 1 ? 'Umum' : 'Lainnya';

            // Poliklinik
            $clinicName = optional($e->clinic)->name;

            // TTV
            $ttvModel = $e->tandaVital;
            $ttv = $ttvModel ? [
                'nadi' => $ttvModel->nadi,
                'pernapasan' => $ttvModel->pernapasan,
                'sistolik' => $ttvModel->sistolik,
                'diastolik' => $ttvModel->diastolik,
                'suhu' => $ttvModel->suhu,
                'kesadaran' => $ttvModel->kesadaran,
                'tinggi_badan' => $ttvModel->tinggi_badan,
                'berat_badan' => $ttvModel->berat_badan,
            ] : null;

            // Tindakan
            $tindakan = $e->tindakan->map(function ($row) {
                return [
                    'tindakan_name' => $row->tindakan_name ?? '',
                    'qty' => (int)($row->qty ?? 0),
                    'harga' => $row->tindakan_harga ?? 0,
                ];
            })->values();

            // Lab
            $labItems = [];
            foreach ($e->labRequests as $req) {
                foreach ($req->items as $it) {
                    $labItems[] = [
                        'lab_request_id' => $req->id,
                        'test_name' => $it->test_name,
                        'status' => $req->status,
                        'result_value' => $it->result_value,
                        'result_unit' => $it->result_unit,
                        'result_reference' => $it->result_reference,
                        'result_notes' => $it->result_notes,
                    ];
                }
            }

            // Radiologi
            $radiologiItems = $e->radiologyRequests->map(function ($rad) {
                $results = $rad->results->map(function ($res) {
                    return [
                        'description' => $res->description ?? '',
                        'conclusion' => $res->conclusion ?? '',
                        'notes' => $res->notes ?? '',
                    ];
                })->toArray();

                return [
                    'radiology_request_id' => $rad->id,
                    'name' => $rad->jenis->name ?? 'Radiologi',
                    'status' => $rad->status,
                    'results' => $results,
                ];
            })->values();

            // Resep
            $resepItems = [];
            if ($e->resep && $e->resep->details) {
                $resepItems = $e->resep->details->map(function ($d) {
                    return [
                        'nama_obat' => $d->nama_obat,
                        'qty' => (int)($d->qty ?? 0),
                        'aturan_pakai' => $d->aturan_pakai,
                        'harga' => $d->harga ?? 0,
                    ];
                })->values();
            }

            return [
                'encounter_id' => $e->id,
                'no_encounter' => $e->no_encounter,
                'date' => optional($e->created_at)->format('d M Y H:i'),
                'type' => $e->type, // 1=RJ,2=RI,3=IGD
                'type_label' => $e->type == 1 ? 'Rawat Jalan' : ($e->type == 2 ? 'Rawat Inap' : 'IGD'),
                'purpose' => $purpose,
                'insurance' => $insurance,
                'clinic' => $clinicName,
                'diagnoses' => $diagnoses,
                'doctors' => $doctors,
                'nurses' => $nurses,
                'ttv' => $ttv,
                'tindakan' => $tindakan,
                'lab' => $labItems,
                'radiologi' => $radiologiItems,
                'resep' => $resepItems,
                'cetak_url' => route('observasi.cetakEncounter', $e->id),
            ];
        })->values();

        // Ambil file dokumen pasien
        $files = MedicalRecordFile::where('rekam_medis', $pasien->rekam_medis)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'file_type' => $file->file_type,
                    'file_size' => $file->file_size,
                    'description' => $file->description,
                    'url' => Storage::url($file->file_path),
                    'uploaded_at' => optional($file->created_at)->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'status' => true,
            'pasien' => [
                'rekam_medis' => $pasien->rekam_medis,
                'name' => $pasien->name,
                'jenis_kelamin' => $pasien->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan',
                'tgl_lahir' => optional($pasien->tgl_lahir)->format('d M Y'),
                'age' => $pasien->tgl_lahir ? \Carbon\Carbon::parse($pasien->tgl_lahir)->age : null,
                'no_hp' => $pasien->no_hp,
                'alamat' => $pasien->alamat,
            ],
            'encounters' => $data,
            'files' => $files,
            'pagination' => [
                'current_page' => $encounters->currentPage(),
                'last_page' => $encounters->lastPage(),
                'per_page' => $encounters->perPage(),
                'total' => $encounters->total(),
            ]
        ]);
    }

    public function riwayatData(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $patients = Pasien::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('rekam_medis', 'like', "%{$q}%")
                        ->orWhere('mr_lama', 'like', "%{$q}%")
                        ->orWhere('no_hp', 'like', "%{$q}%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get();

        $rows = $patients->map(function ($p) {
            // Ambil 5 kunjungan terakhir + diagnosisnya
            $encounters = Encounter::with(['practitioner', 'tandaVital', 'resep.details', 'diagnosis', 'clinic', 'nurses'])
                ->where('rekam_medis', $p->rekam_medis)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(function ($e) {
                    // Diagnosis
                    $diagnoses = ($e->diagnosis ?? collect())->map(function ($d) {
                        $code = $d->diagnosis_code ?: '';
                        $desc = $d->diagnosis_description ?: '';
                        $type = $d->diagnosis_type ?: '';
                        return trim($code . ($code && $desc ? ' - ' : '') . $desc . ($type ? " ({$type})" : ''));
                    })->filter()->values();

                    // Dokter/practitioner & Perawat
                    $doctors = ($e->practitioner ?? collect())->pluck('name')->filter()->values();
                    $nurses = ($e->nurses ?? collect())->pluck('name')->filter()->values();

                    // Tujuan Kunjungan & Jaminan
                    $purposeMap = [
                        1 => 'Kunjungan Sehat (Promotif/Preventif)',
                        2 => 'Rehabilitatif',
                        3 => 'Kunjungan Sakit',
                        4 => 'Darurat',
                        5 => 'Kontrol / Tindak Lanjut',
                        6 => 'Treatment',
                        7 => 'Konsultasi',
                    ];
                    $purpose = $purposeMap[$e->tujuan_kunjungan] ?? '-';
                    $insurance = $e->jenis_jaminan == 1 ? 'Umum' : 'Lainnya';

                    // Poliklinik
                    $clinicName = optional($e->clinic)->name;

                    // TTV
                    $ttvModel = $e->tandaVital;
                    $ttv = $ttvModel ? [
                        'nadi' => $ttvModel->nadi,
                        'pernapasan' => $ttvModel->pernapasan,
                        'sistolik' => $ttvModel->sistolik,
                        'diastolik' => $ttvModel->diastolik,
                        'suhu' => $ttvModel->suhu,
                        'kesadaran' => $ttvModel->kesadaran,
                        'tinggi_badan' => $ttvModel->tinggi_badan,
                        'berat_badan' => $ttvModel->berat_badan,
                    ] : null;

                    // Tindakan
                    $tindakanRows = TindakanEncounter::where('encounter_id', $e->id)->get();
                    $tindakan = [
                        'items' => $tindakanRows->map(function ($row) {
                            return [
                                'tindakan_name' => $row->tindakan_name ?? ($row->jenis_tindakan ?? ''),
                                'qty' => (int)($row->qty ?? 0),
                            ];
                        })->values(),
                    ];

                    // Lab (permintaan & status)
                    $labReqs = LabRequest::with('items')->where('encounter_id', $e->id)->orderByDesc('created_at')->get();
                    $labItems = [];
                    foreach ($labReqs as $req) {
                        foreach ($req->items as $it) {
                            $labItems[] = [
                                'test_name' => $it->test_name,
                                'status' => $req->status,
                            ];
                        }
                    }

                    // Resep
                    $resepModel = $e->resep; // with details
                    $resepItems = [];
                    if ($resepModel && $resepModel->details) {
                        foreach ($resepModel->details as $d) {
                            $resepItems[] = [
                                'nama_obat' => $d->nama_obat,
                                'qty' => (int)($d->qty ?? 0),
                                'aturan_pakai' => $d->aturan_pakai,
                            ];
                        }
                    }

                    return [
                        'encounter_id' => $e->id,
                        'date' => optional($e->created_at)->format('d M Y H:i'),
                        'type' => $e->type, // 1=RJ,2=RI,3=IGD
                        'purpose' => $purpose,
                        'insurance' => $insurance,
                        'clinic' => $clinicName,
                        'diagnoses' => $diagnoses,
                        'doctors' => $doctors,
                        'nurses' => $nurses,
                        'ttv' => $ttv,
                        'tindakan' => $tindakan,
                        'lab' => ['items' => $labItems],
                        'resep' => ['items' => $resepItems],
                        'observasi_url' => route('observasi.index', $e->id),
                    ];
                })->values();

            $last = $encounters->first();
            return [
                'rekam_medis' => $p->rekam_medis,
                'name' => $p->name,
                'jenis_kelamin' => $p->jenis_kelamin,
                'tgl_lahir' => optional($p->tgl_lahir)->format('d M Y'),
                'no_hp' => $p->no_hp,
                'last_visit' => $last['date'] ?? null,
                'last_visit_type' => $last['type'] ?? null,
                'encounters' => $encounters,
            ];
        })->values();

        return response()->json(['data' => $rows]);
    }

    public function statistik()
    {
        $year = now()->year;
        // Monthly encounters
        $raw = Encounter::selectRaw('MONTH(created_at) m, COUNT(*) c')
            ->whereYear('created_at', $year)
            ->groupBy('m')
            ->pluck('c', 'm');
        $categories = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $seriesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $seriesData[] = (int)($raw[$i] ?? 0);
        }

        // Top 5 diagnosis codes
        $topDiagnosis = Diagnosis::select('diagnosis_code', DB::raw('COUNT(*) as total'))
            ->whereNotNull('diagnosis_code')
            ->groupBy('diagnosis_code')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $stat = [
            'monthly' => ['categories' => $categories, 'series' => [['name' => 'Kunjungan', 'data' => $seriesData]]],
            'topDiagnosis' => [
                'labels' => $topDiagnosis->pluck('diagnosis_code')->toArray(),
                'data' => $topDiagnosis->pluck('total')->map(fn($v) => (int)$v)->toArray(),
            ],
        ];

        return view('pages.medical-records.statistik', compact('stat'));
    }

    public function arsip()
    {
        return view('pages.medical-records.arsip');
    }

    public function arsipUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'rekam_medis' => 'required|string|exists:pasiens,rekam_medis',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = $request->rekam_medis . '_' . time() . '_' . $originalName;
        $path = $file->storeAs('medical-records-archive', $fileName, 'public');

        $medicalFile = MedicalRecordFile::create([
            'rekam_medis' => $request->rekam_medis,
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => $extension,
            'file_size' => $file->getSize(),
            'description' => $request->description,
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'File berhasil diupload',
            'data' => $medicalFile
        ]);
    }

    public function arsipList(Request $request)
    {
        $rekamMedis = $request->get('rekam_medis');

        $query = MedicalRecordFile::with(['pasien', 'uploader']);

        if ($rekamMedis) {
            $query->where('rekam_medis', $rekamMedis);
        }

        $files = $query->orderByDesc('created_at')->get()->map(function ($file) {
            return [
                'id' => $file->id,
                'rekam_medis' => $file->rekam_medis,
                'pasien_name' => optional($file->pasien)->name,
                'file_name' => $file->file_name,
                'file_type' => $file->file_type,
                'file_size' => $file->file_size,
                'description' => $file->description,
                'url' => Storage::url($file->file_path),
                'uploaded_by' => optional($file->uploader)->name,
                'uploaded_at' => optional($file->created_at)->format('d M Y H:i'),
            ];
        });

        return response()->json(['data' => $files]);
    }

    public function arsipDelete($id)
    {
        $file = MedicalRecordFile::find($id);

        if (!$file) {
            return response()->json(['status' => false, 'message' => 'File tidak ditemukan'], 404);
        }

        // Delete physical file
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Delete database record
        $file->delete();

        return response()->json(['status' => true, 'message' => 'File berhasil dihapus']);
    }
}
