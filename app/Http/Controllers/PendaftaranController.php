<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsActivity;

use App\Models\User;
use App\Models\Clinic;
use App\Models\Pasien;
use App\Repositories\PendaftaranRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PendaftaranController extends Controller
{
    use LogsActivity;
    public $pendaftaranRepository;
    public function __construct(PendaftaranRepository $pendaftaranRepository)
    {
        $this->pendaftaranRepository = $pendaftaranRepository;
    }
    public function index()
    {
        $antrian    = $this->pendaftaranRepository->index();
        $pekerjaan  = $this->pendaftaranRepository->pekerjaan();
        $agama      = $this->pendaftaranRepository->agama();
        $provinsi  = $this->pendaftaranRepository->provinsi();
        $clinics     = $this->pendaftaranRepository->showClinic();
        $ruangan   = $this->pendaftaranRepository->ruangan();
        $jenisjaminan = $this->pendaftaranRepository->jenisjaminan();
        // Load doctors robustly (role = 2). Fallback: doctors attached to any clinic
        $doctors = User::where('role', 2)->orderBy('name')->get(['id', 'name']);
        if ($doctors->isEmpty()) {
            $doctors = Clinic::with(['users' => function ($q) {
                $q->where('role', 2);
            }])
                ->get()->pluck('users')->flatten()->unique('id')->sortBy('name')->values();
        }
        return view('pages.pendaftaran.index', compact('antrian', 'pekerjaan', 'agama', 'provinsi', 'clinics', 'ruangan', 'jenisjaminan', 'doctors'));
    }

    public function update_antrian()
    {
        $antrian = $this->pendaftaranRepository->update_antrian();
        if ($antrian) {
            return response()->json(['status' => true, 'antrian' => $antrian['antrian'], 'loket' => $antrian['loket'], 'jumlah' => $antrian['jumlah']], 200);
        } else {
            return response()->json(['status' => false], 404);
        }
    }

    public function showRawatJalan(Request $request)
    {
        $data = $this->pendaftaranRepository->showRawatJalan($request);
        $total_row = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->total() : (is_countable($data) ? count($data) : 0);

        if ($total_row > 0) {
            $rows = [];
            foreach ($data as $d) {
                $rows[] = view('components.pendaftaran.rawat_jalan_row', compact('d'))->render();
            }
            $rows = implode('', $rows);
        } else {
            $rows = '<tr>
                        <td colspan="6" class="text-center">Data tidak ada</td>
                    </tr>';
        }

        $pagination = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->links()->toHtml() : '';
        return response()->json(['rows' => $rows, 'pagination' => $pagination]);
    }
    // showRawatDarurat - Handle both AJAX and direct URL requests
    public function showRawatDarurat(Request $request)
    {
        // If this is an AJAX request, return JSON data for table
        if ($request->ajax() || $request->expectsJson()) {
            $data = $this->pendaftaranRepository->showRawatDarurat($request);
            $total_row = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->total() : (is_countable($data) ? count($data) : 0);
            if ($total_row > 0) {
                $rows = [];
                foreach ($data as $d) {
                    $rows[] = view('components.pendaftaran.rawat_darurat_row', compact('d'))->render();
                }
                $rows = implode('', $rows);
            } else {
                $rows = '<tr>
                            <td colspan="6" class="text-center">Data tidak ada</td>
                        </tr>';
            }
            $pagination = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->links()->toHtml() : '';
            return response()->json(['rows' => $rows, 'pagination' => $pagination]);
        }

        // If this is a direct URL request, show main page with rawat darurat focus
        $transferToRoom = $request->input('transfer_to_room');

        // Get necessary data for the main pendaftaran page
        $antrian    = $this->pendaftaranRepository->index();
        $pekerjaan  = $this->pendaftaranRepository->pekerjaan();
        $agama      = $this->pendaftaranRepository->agama();
        $provinsi  = $this->pendaftaranRepository->provinsi();
        $clinics     = $this->pendaftaranRepository->showClinic();
        $ruangan   = $this->pendaftaranRepository->ruangan();
        $jenisjaminan = $this->pendaftaranRepository->jenisjaminan();
        $doctors = User::where('role', 2)->orderBy('name')->get(['id', 'name']);
        if ($doctors->isEmpty()) {
            $doctors = Clinic::with(['users' => function ($q) {
                $q->where('role', 2);
            }])
                ->get()->pluck('users')->flatten()->unique('id')->sortBy('name')->values();
        }

        return view('pages.pendaftaran.index', compact('antrian', 'pekerjaan', 'agama', 'provinsi', 'clinics', 'ruangan', 'jenisjaminan', 'doctors', 'transferToRoom'));
    }
    // showRawatInap - Handle both AJAX and direct URL requests
    public function showRawatInap(Request $request)
    {
        // If this is an AJAX request, return JSON data for table
        if ($request->ajax() || $request->expectsJson()) {
            $data = $this->pendaftaranRepository->showRawatInap($request);
            $total_row = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->total() : (is_countable($data) ? count($data) : 0);

            if ($total_row > 0) {
                $rows = [];
                foreach ($data as $d) {
                    $rows[] = view('components.pendaftaran.rawat_inap_row', compact('d'))->render();
                }
                $rows = implode('', $rows);
            } else {
                $rows = '<tr>
                            <td colspan="6" class="text-center">Data tidak ada</td>
                        </tr>';
            }

            $pagination = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->links()->toHtml() : '';
            return response()->json(['rows' => $rows, 'pagination' => $pagination]);
        }

        // If this is a direct URL request (e.g., from nurse dashboard), redirect to main page
        $selectedRoom = $request->input('room');

        // Get necessary data for the main pendaftaran page
        $antrian    = $this->pendaftaranRepository->index();
        $pekerjaan  = $this->pendaftaranRepository->pekerjaan();
        $agama      = $this->pendaftaranRepository->agama();
        $provinsi  = $this->pendaftaranRepository->provinsi();
        $clinics     = $this->pendaftaranRepository->showClinic();
        $ruangan   = $this->pendaftaranRepository->ruangan();
        $jenisjaminan = $this->pendaftaranRepository->jenisjaminan();
        $doctors = User::where('role', 2)->orderBy('name')->get(['id', 'name']);
        if ($doctors->isEmpty()) {
            $doctors = Clinic::with(['users' => function ($q) {
                $q->where('role', 2);
            }])
                ->get()->pluck('users')->flatten()->unique('id')->sortBy('name')->values();
        }

        return view('pages.pendaftaran.index', compact('antrian', 'pekerjaan', 'agama', 'provinsi', 'clinics', 'ruangan', 'jenisjaminan', 'doctors', 'selectedRoom'));
    }

    public function editEncounterRajal($id)
    {
        $encounter = $this->pendaftaranRepository->editEncounterRajal($id);
        return response()->json(['status' => true, 'data' => $encounter], 200);
    }

    public function cariPasien(Request $request)
    {
        $data       = $this->pendaftaranRepository->cariPasien($request);
        $total_row  = $data->count();

        $output = [];

        if ($total_row > 0) {
            foreach ($data as $d) {
                $output[] = view('components.pendaftaran.cari_pasien_card', compact('d'))->render();
            }
            $output = implode('', $output);
        } else {
            $output = view('components.pendaftaran.cari_pasien_notfound')->render();
        }

        return response()->json($output);
    }

    public function cariPasienJson(Request $request)
    {
        $term = $request->input('term', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $pasiens = Pasien::where(function ($query) use ($term) {
            $query->where('name', 'like', '%' . $term . '%')
                ->orWhere('rekam_medis', $term)
                ->orWhere('no_identitas', $term)
                ->orWhere('no_hp', $term);
        })
            ->select('rekam_medis', 'name')
            ->limit(20)
            ->get();

        return response()->json($pasiens);
    }

    public function store_pasien(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_identitas'   => 'nullable|string',
            'no_identitas'      => 'nullable|string',
            'name_pasien'       => 'required|string',
            'jenis_kelamin'     => 'required|string',
            'tgl_lahir'         => 'required|string',
            'golongan_darah'    => 'nullable|string',
            'kewarganegaraan'   => 'nullable|string',
            'pekerjaan'         => 'nullable|string',
            'status_menikah'    => 'nullable|string',
            'agama'             => 'nullable|string',
            'no_hp'             => 'required|string',
            'no_telepon'        => 'nullable|string',
            'mr_lama'           => 'nullable|string',
            'alamat'            => 'required|string',
            'province'          => 'nullable|string',
            'city'              => 'nullable|string'

        ], [
            'name_pasien.required'   => 'Kolom Nama Pasien masih kosong',
            'jenis_kelamin.required' => 'Kolom Jenis Kelamin masih kosong',
            'tgl_lahir.required'     => 'Kolom Tanggal Lahir masih kosong',
            'no_hp.required'         => 'Kolom No Handphone masih kosong',
            'alamat.required'        => 'Kolom Alamat masih kosong'
        ]);

        if ($validator->passes()) {
            $pasien = $this->pendaftaranRepository->store_pasien($request);
            $this->activity('Menambahkan Pasien Baru', [
                'pasien_id' => $pasien->id ?? null,
                'nama' => $pasien->name ?? $request->input('name_pasien'),
            ], 'pendaftaran');
            return response()->json(['status' => true, 'text' => 'Data Pasien Baru ' . $pasien->name . ' berhasil ditambahkan']);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }

    public function editPasien($id)
    {
        $pasien = $this->pendaftaranRepository->editPasien($id);
        return response()->json(['status' => true, 'data' => $pasien], 200);
    }

    public function updatePasien(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_identitas'   => 'nullable|string',
            'no_identitas'      => 'nullable|string',
            'name_pasien'       => 'required|string',
            'jenis_kelamin'     => 'required|string',
            'tgl_lahir'         => 'required|string',
            'golongan_darah'    => 'nullable|string',
            'kewarganegaraan'   => 'nullable|string',
            'pekerjaan'         => 'nullable|string',
            'status_menikah'    => 'nullable|string',
            'agama'             => 'nullable|string',
            'no_hp'             => 'required|string',
            'no_telepon'        => 'nullable|string',
            'mr_lama'           => 'nullable|string',
            'alamat'            => 'required|string',
            'province'          => 'nullable|string',
            'city'              => 'nullable|string'

        ], [
            'name_pasien.required'   => 'Kolom Nama Pasien masih kosong',
            'jenis_kelamin.required' => 'Kolom Jenis Kelamin masih kosong',
            'tgl_lahir.required'     => 'Kolom Tanggal Lahir masih kosong',
            'no_hp.required'         => 'Kolom No Handphone masih kosong',
            'alamat.required'        => 'Kolom Alamat masih kosong'
        ]);

        if ($validator->passes()) {
            $pasien = $this->pendaftaranRepository->updatePasien($request, $id);
            $subject = $request->has('tgl_lahir') ? 'Merubah Tanggal Lahir Pasien' : 'Mengubah Data Pasien';
            $this->activity($subject, [
                'pasien_id' => $id,
                'fields' => array_keys($request->except(['_token']))
            ], 'pendaftaran');
            return response()->json(['status' => true, 'text' => 'Data Pasien ' . $pasien->name . ' berhasil diubah.']);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }

    public function showPasien($id)
    {
        $pasien = $this->pendaftaranRepository->showPasien($id);
        return response()->json(['status' => true, 'data' => $pasien]);
    }

    public function postRawatJalan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_jaminan'     => 'required|string',
            'dokter'            => 'required|array',
            'tujuan_kunjungan'  => 'required|string',

        ], [
            'jenis_jaminan.required'    => 'Kolom Jenis Jaminan masih kosong',
            'dokter.required'           => 'Kolom Dokter harus dipilih',
            'tujuan_kunjungan.required' => 'Kolom Tujuan Kunjungan masih kosong',
        ]);

        if ($validator->passes()) {
            $encounter = $this->pendaftaranRepository->postRawatJalan($request, $id);
            $this->activity('Mendaftarkan Rawat Jalan', [
                'encounter_id' => $encounter->id ?? null,
                'pasien' => $encounter->name_pasien ?? null,
                'dokter' => $request->input('dokter')
            ], 'pendaftaran');
            return response()->json(['status' => true, 'text' => 'Pendaftaran Rawat Jalan Pasien ' . $encounter->name_pasien . ' berhasil'], 200);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }
    public function updateRawatJalan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_jaminan'     => 'required|string',
            'dokter'            => 'required|array',
            'tujuan_kunjungan'  => 'required|string',

        ], [
            'jenis_jaminan.required'    => 'Kolom Jenis Jaminan masih kosong',
            'dokter.required'           => 'Kolom Dokter harus dipilih',
            'tujuan_kunjungan.required' => 'Kolom Tujuan Kunjungan masih kosong',
        ]);

        if ($validator->passes()) {
            $encounter = $this->pendaftaranRepository->updateRawatJalan($request, $id);
            $this->activity('Mengubah Pendaftaran Rawat Jalan', [
                'encounter_id' => $encounter->id ?? null,
                'pasien' => $encounter->name_pasien ?? null,
                'dokter' => $request->input('dokter')
            ], 'pendaftaran');
            return response()->json(['status' => true, 'text' => 'Pendaftaran Rawat Jalan Pasien ' . $encounter->name_pasien . ' berhasil diubah'], 200);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }
    public function destroyEncounterRajal($id)
    {
        // Check if encounter has been paid
        $encounter = \App\Models\Encounter::findOrFail($id);
        if ($encounter->status_bayar_tindakan || $encounter->status_bayar_resep) {
            return response()->json([
                'status' => false,
                'text' => 'Tidak dapat menghapus encounter yang sudah dibayar'
            ], 403);
        }

        $result = $this->pendaftaranRepository->destroyEncounterRajal($id);
        return response()->json(['status' => true, 'text' => 'Encounter berhasil dihapus', 'data' => $result]);
    }
    public function postRawatDarurat(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_jaminan'     => 'required|string',
            'dokter'            => 'nullable|array',
            'tujuan_kunjungan'  => 'required|string',

        ], [
            'jenis_jaminan.required'    => 'Kolom Jenis Jaminan masih kosong',
            'tujuan_kunjungan.required' => 'Kolom Tujuan Kunjungan masih kosong',
        ]);

        if ($validator->passes()) {
            $encounter = $this->pendaftaranRepository->postRawatDarurat($request, $id);
            $this->activity('Mendaftarkan Rawat Darurat (IGD)', [
                'encounter_id' => $encounter->id ?? null,
                'pasien' => $encounter->name_pasien ?? null,
                'dokter' => $request->input('dokter'),
                'tingkat_kegawatan' => $request->input('tingkat_kegawatan'),
                'keluhan_utama' => $request->input('keluhan_utama')
            ], 'pendaftaran');
            return response()->json(['status' => true, 'text' => 'Pendaftaran Rawat Darurat Pasien ' . $encounter->name_pasien . ' berhasil'], 200);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }
    // editEncounterRdarurat
    public function editEncounterRdarurat($id)
    {
        $encounter = $this->pendaftaranRepository->editEncounterRdarurat($id);
        return response()->json(['status' => true, 'data' => $encounter], 200);
    }
    public function updateRawatDarurat(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_jaminan'     => 'required|string',
            'dokter'            => 'required|array',
            'tujuan_kunjungan'  => 'required|string',

        ], [
            'jenis_jaminan.required'    => 'Kolom Jenis Jaminan masih kosong',
            'dokter.required'           => 'Kolom Dokter harus dipilih',
            'tujuan_kunjungan.required' => 'Kolom Tujuan Kunjungan masih kosong',
        ]);

        if ($validator->passes()) {
            $encounter = $this->pendaftaranRepository->updateRawatDarurat($request, $id);
            $this->activity('Mengubah Pendaftaran Rawat Darurat (IGD)', [
                'encounter_id' => $encounter->id ?? null,
                'pasien' => $encounter->name_pasien ?? null,
                'dokter' => $request->input('dokter'),
                'tingkat_kegawatan' => $request->input('tingkat_kegawatan'),
                'keluhan_utama' => $request->input('keluhan_utama')
            ], 'pendaftaran');
            return response()->json(['status' => true, 'text' => 'Pendaftaran Rawat Darurat Pasien ' . $encounter->name_pasien . ' berhasil diubah'], 200);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }
    public function destroyEncounterRdarurat($id)
    {
        // Check if encounter has been paid
        $encounter = \App\Models\Encounter::findOrFail($id);
        if ($encounter->status_bayar_tindakan || $encounter->status_bayar_resep) {
            return response()->json([
                'status' => false,
                'text' => 'Tidak dapat menghapus encounter yang sudah dibayar'
            ], 403);
        }

        $result = $this->pendaftaranRepository->destroyEncounterRdarurat($id);
        return response()->json(['status' => true, 'text' => 'Encounter berhasil dihapus', 'data' => $result]);
    }
    // editRawatInap
    public function editEncounterRinap($id)
    {
        $encounter = $this->pendaftaranRepository->editEncounterRinap($id);
        return response()->json(['status' => true, 'data' => $encounter], 200);
    }
    public function postRawatInap(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_jaminan'     => 'required|string',
            'dokter'            => 'required|array',
            'name_companion'   => 'required|string',
            'nik_companion'     => 'nullable|string',
            'phone_companion'   => 'required|string',
            'relation_companion' => 'required|string',
            'ruangan'           => 'required|string'

        ], [
            'jenis_jaminan.required'    => 'Kolom Jenis Jaminan masih kosong',
            'dokter.required'           => 'Kolom Dokter harus dipilih',
            'phone_companion.required'  => 'Kolom Telepon Pendamping masih kosong',
            'relation_companion.required' => 'Kolom Hubungan Pendamping masih kosong',
            'name_companion.required'   => 'Kolom Nama Pendamping masih kosong',
            'ruangan.required'          => 'Kolom Ruangan masih kosong'
        ]);

        if ($validator->passes()) {
            $encounter = $this->pendaftaranRepository->postRawatInap($request, $id);
            return response()->json(['status' => true, 'text' => 'Pendaftaran Rawat Inap Pasien ' . $encounter->name_pasien . ' berhasil'], 200);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }

    public function getDokterByClinic($clinicId)
    {
        $clinic = \App\Models\Clinic::with(['users' => function ($q) {
            $q->where('role', 2); // 2 = dokter
        }])->findOrFail($clinicId);

        $dokters = $clinic->users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        });

        return response()->json($dokters);
    }

    // Fallback endpoint to fetch all doctors for IGD modal
    public function getAllDoctors()
    {
        $doctors = User::where('role', 2)->orderBy('name')->get(['id', 'name']);
        if ($doctors->isEmpty()) {
            $doctors = Clinic::with(['users' => function ($q) {
                $q->where('role', 2);
            }])
                ->get()->pluck('users')->flatten()->unique('id')->sortBy('name')->values();
        }
        return response()->json($doctors->map(function ($u) {
            return ['id' => $u->id, 'name' => $u->name];
        }));
    }

    // destroyEncounterRinap
    public function destroyRawatInap($id)
    {
        // Check if encounter has been paid
        $encounter = \App\Models\Encounter::findOrFail($id);
        if ($encounter->status_bayar_tindakan || $encounter->status_bayar_resep) {
            return response()->json([
                'status' => false,
                'text' => 'Tidak dapat menghapus encounter yang sudah dibayar'
            ], 403);
        }

        $result = $this->pendaftaranRepository->destroyEncounterRinap($id);
        return response()->json(['status' => true, 'text' => 'Encounter berhasil dihapus', 'data' => $result]);
    }

    // EXPORTS CSV
    public function exportRawatJalan(Request $request)
    {
        $q = $request->q;
        $orderBy = in_array($request->order_by ?? '', ['created_at', 'no_encounter', 'name_pasien']) ? $request->order_by : 'created_at';
        $orderDir = in_array(strtolower($request->order_dir ?? ''), ['asc', 'desc']) ? strtolower($request->order_dir) : 'desc';

        $rows = \App\Models\Encounter::with(['practitioner', 'clinic'])
            ->where('type', 1)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere(function ($query) {
                        $query->where('status', 2)->whereDate('updated_at', now()->toDateString());
                    });
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('no_encounter', 'like', "%{$q}%")
                        ->orWhere('rekam_medis', 'like', "%{$q}%")
                        ->orWhere('name_pasien', 'like', "%{$q}%");
                });
            })
            ->orderBy($orderBy, $orderDir)
            ->get();

        $filename = 'rawat_jalan_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No Kunjungan', 'No RM', 'Nama', 'Dokter', 'Klinik', 'Jaminan', 'Tujuan', 'Waktu', 'Status']);
            foreach ($rows as $e) {
                $dokter = optional($e->practitioner->first())->name ?: '-';
                $klinik = optional($e->clinic)->nama ?: '-';
                $jaminan = $e->jenis_jaminan == 1 ? 'Umum' : 'Lainnya';
                $tujuan = match ($e->tujuan_kunjungan) {
                    1 => 'Kunjungan Sehat',
                    2 => 'Rehabilitatif',
                    3 => 'Kunjungan Sakit',
                    4 => 'Darurat',
                    5 => 'Kontrol',
                    6 => 'Treatment',
                    7 => 'Konsultasi',
                    default => '-',
                };
                $status = $e->status == 1 ? 'Aktif' : 'Non-Aktif';
                fputcsv($out, [
                    $e->no_encounter,
                    $e->rekam_medis,
                    $e->name_pasien,
                    $dokter,
                    $klinik,
                    $jaminan,
                    $tujuan,
                    optional($e->created_at)->format('d M Y H:i'),
                    $status
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public function exportRawatDarurat(Request $request)
    {
        $q = $request->q;
        $orderBy = in_array($request->order_by ?? '', ['created_at', 'no_encounter', 'name_pasien']) ? $request->order_by : 'updated_at';
        $orderDir = in_array(strtolower($request->order_dir ?? ''), ['asc', 'desc']) ? strtolower($request->order_dir) : 'asc';

        $rows = \App\Models\Encounter::with(['practitioner'])
            ->where('type', 3)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere(function ($query) {
                        $query->where('status', 2)->whereDate('updated_at', now()->toDateString());
                    });
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('no_encounter', 'like', "%{$q}%")
                        ->orWhere('rekam_medis', 'like', "%{$q}%")
                        ->orWhere('name_pasien', 'like', "%{$q}%");
                });
            })
            ->orderBy($orderBy, $orderDir)
            ->get();

        $filename = 'igd_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No Kunjungan', 'No RM', 'Nama', 'Dokter', 'Jaminan', 'Tujuan', 'Waktu', 'Status']);
            foreach ($rows as $e) {
                $dokter = optional($e->practitioner->first())->name ?: '-';
                $jaminan = $e->jenis_jaminan == 1 ? 'Umum' : 'Lainnya';
                $tujuan = match ($e->tujuan_kunjungan) {
                    1 => 'Kunjungan Sehat',
                    2 => 'Rehabilitatif',
                    3 => 'Kunjungan Sakit',
                    4 => 'Darurat',
                    5 => 'Kontrol',
                    6 => 'Treatment',
                    7 => 'Konsultasi',
                    default => '-'
                };
                $status = $e->status == 1 ? 'Aktif' : 'Non-Aktif';
                fputcsv($out, [
                    $e->no_encounter,
                    $e->rekam_medis,
                    $e->name_pasien,
                    $dokter,
                    $jaminan,
                    $tujuan,
                    optional($e->created_at)->format('d M Y H:i'),
                    $status
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public function exportRawatInap(Request $request)
    {
        $q = $request->q;
        $orderBy = in_array($request->order_by ?? '', ['created_at', 'no_encounter', 'name_pasien']) ? $request->order_by : 'updated_at';
        $orderDir = in_array(strtolower($request->order_dir ?? ''), ['asc', 'desc']) ? strtolower($request->order_dir) : 'desc';

        $rows = \App\Models\Encounter::with(['admission'])
            ->where('type', 2)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere(function ($query) {
                        $query->where('status', 2)->whereDate('updated_at', now()->toDateString());
                    });
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('no_encounter', 'like', "%{$q}%")
                        ->orWhere('rekam_medis', 'like', "%{$q}%")
                        ->orWhere('name_pasien', 'like', "%{$q}%");
                });
            })
            ->orderBy($orderBy, $orderDir)
            ->get();

        $filename = 'rawat_inap_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No Kunjungan', 'No RM', 'Nama', 'Dokter', 'No Kamar', 'Jaminan', 'Tujuan', 'Waktu', 'Status']);
            foreach ($rows as $e) {
                $dokter = $e->admission->nama_dokter ?? '-';
                $noKamar = optional(optional($e->admission)->room)->no_kamar ?? '-';
                $jaminan = $e->jenis_jaminan == 1 ? 'Umum' : 'Lainnya';
                $tujuan = match ($e->tujuan_kunjungan) {
                    1 => 'Kunjungan Sehat',
                    2 => 'Rehabilitatif',
                    3 => 'Kunjungan Sakit',
                    4 => 'Darurat',
                    5 => 'Kontrol',
                    6 => 'Treatment',
                    7 => 'Konsultasi',
                    default => '-'
                };
                $status = $e->status == 1 ? 'Aktif' : 'Non-Aktif';
                fputcsv($out, [
                    $e->no_encounter,
                    $e->rekam_medis,
                    $e->name_pasien,
                    $dokter,
                    $noKamar,
                    $jaminan,
                    $tujuan,
                    optional($e->created_at)->format('d M Y H:i'),
                    $status
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
