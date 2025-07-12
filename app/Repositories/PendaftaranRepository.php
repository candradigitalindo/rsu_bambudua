<?php

namespace App\Repositories;

use App\Events\AntrianEvent;
use App\Models\Agama;
use App\Models\Antrian;
use App\Models\Encounter;
use App\Models\Loket;
use App\Models\Pasien;
use App\Models\Pekerjaan;
use App\Models\Practitioner;
use App\Models\Profile;
use App\Models\Province;
use App\Models\Ruangan;
use App\Models\Spesialis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PendaftaranRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $loket = Loket::where('user_id', Auth::id())->first();

        if (!$loket) {
            return ['antrian' => "--", 'jumlah' => 0];
        }

        // Query antrian hanya sekali, ambil status 2 (sedang dilayani) dan status 1 (menunggu) sekaligus
        $today = date('Y-m-d');
        $antrianSedang = Antrian::whereDate('created_at', $today)
            ->where('lokasiloket_id', $loket->lokasiloket_id)
            ->where('status', 2)
            ->orderByDesc('updated_at')
            ->first();

        $jumlahMenunggu = Antrian::whereDate('created_at', $today)
            ->where('lokasiloket_id', $loket->lokasiloket_id)
            ->where('status', 1)
            ->count();

        return [
            'antrian' => $antrianSedang ? ($antrianSedang->prefix . " " . $antrianSedang->nomor) : 0,
            'jumlah'  => $jumlahMenunggu
        ];
    }

    public function showRawatJalan()
    {
        // Eager load practitioner untuk menghindari N+1 query
        $encounters = Encounter::with(['practitioner'])
            ->where('type', 1)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $encounters->transform(function ($e) {
            $e['status'] = $e->status == 1 ? "Progress" : "Finish";
            $e['type'] = "Rawat Jalan";

            // Ambil nama dokter dari relasi practitioner (jika ada)
            $e['dokter'] = $e->practitioner->first()->name ?? '-';

            $e['jenis_jaminan'] = $e->jenis_jaminan == 1 ? "Umum" : "Lainnya";

            $e['tujuan_kunjungan'] = match ($e->tujuan_kunjungan) {
                1 => "Kunjungan Sehat (Promotif/Preventif)",
                2 => "Rehabilitatif",
                3 => "Kunjungan Sakit",
                4 => "Darurat",
                5 => "Kontrol / Tindak Lanjut",
                6 => "Treatment",
                7 => "Konsultasi",
                default => "-",
            };

            return $e;
        });

        return $encounters;
    }

    // Show rawatDarurat
    public function showRawatDarurat()
    {
        // Eager load practitioner untuk menghindari N+1 query
        $encounters = Encounter::with(['practitioner'])
            ->where('type', 3)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere(function ($query) {
                        $query->where('status', 2)
                            ->whereDate('updated_at', Carbon::now()->toDateString());
                    });
            })
            ->orderBy('updated_at', 'asc')
            ->get();
        $encounters->transform(function ($e) {
            $e['status'] = $e->status == 1 ? "Progress" : "Finish";
            $e['type'] = "Rawat Darurat";
            // Ambil nama dokter dari relasi practitioner (jika ada)
            $e['dokter'] = $e->practitioner->first()->name ?? '-';
            $e['jenis_jaminan'] = $e->jenis_jaminan == 1 ? "Umum" : "Lainnya";
            $e['tujuan_kunjungan'] = match ($e->tujuan_kunjungan) {
                1 => "Kunjungan Sehat (Promotif/Preventif)",
                2 => "Rehabilitatif",
                3 => "Kunjungan Sakit",
                4 => "Darurat",
                5 => "Kontrol / Tindak Lanjut",
                6 => "Treatment",
                7 => "Konsultasi",
                default => "-",
            };
            return $e;
        });
        return $encounters;
    }
    // show rawatInap
    public function showRawatInap()
    {
        // Eager load practitioner untuk menghindari N+1 query
        $encounters = Encounter::with(['practitioner'])
            ->where('type', 2)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere(function ($query) {
                        $query->where('status', 2)
                            ->whereDate('updated_at', Carbon::now()->toDateString());
                    });
            })
            ->orderBy('updated_at', 'asc')
            ->get();
        $encounters->transform(function ($e) {
            $e['status'] = $e->status == 1 ? "Progress" : "Finish";
            $e['type'] = "Rawat Inap";
            // Ambil nama dokter dari relasi practitioner (jika ada)
            $e['dokter'] = $e->practitioner->first()->name ?? '-';
            $e['jenis_jaminan'] = $e->jenis_jaminan == 1 ? "Umum" : "Lainnya";
            $e['tujuan_kunjungan'] = match ($e->tujuan_kunjungan) {
                1 => "Kunjungan Sehat (Promotif/Preventif)",
                2 => "Rehabilitatif",
                3 => "Kunjungan Sakit",
                4 => "Darurat",
                5 => "Kontrol / Tindak Lanjut",
                6 => "Treatment",
                7 => "Konsultasi",
                default => "-",
            };
            return $e;
        });
        return $encounters;
    }

    public function editEncounterRajal($id)
    {
        $encounter = Encounter::findOrFail($id);

        // Eager load pasien dan dokter sekaligus
        $pasien = Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
        $dokter = Practitioner::where('encounter_id', $encounter->id)
            ->orderByDesc('created_at')
            ->first();

        // Umur pasien
        $encounter['umur'] = $pasien ? Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari') : '-';

        // Status pasien
        $statusList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $encounter['status'] = $pasien ? ($statusList[$pasien->status] ?? "-") : "-";

        // Tanggal encounter
        $encounter['tgl_encounter'] = $encounter->created_at ? date('d M Y H:i', strtotime($encounter->created_at)) : "-";

        // Type encounter
        $typeList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $encounter['type'] = $typeList[$encounter->type] ?? "-";

        // Nama dokter
        $encounter['dokter'] = $dokter ? $dokter->name : "-";

        return $encounter;
    }

    public function update_antrian()
    {
        $loket   = Loket::where('user_id', Auth::user()->id)->first();
        if ($loket) {
            $antrian = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 1)->orderBy('nomor', 'ASC')->first();
            if ($antrian) {
                $antrian->update(['status' => 2]);
            }
            $jumlah  = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 1)->count();
            return ['antrian' => $antrian, 'loket' => $loket, 'jumlah' => $jumlah];
        } else {
            return null;
        }
    }

    public function cariPasien($request)
    {
        $q = $request->q;

        // Query hanya sekali, gunakan orWhere untuk semua kemungkinan
        $pasiens = Pasien::where(function ($query) use ($q) {
            $query->where('name', 'like', '%' . $q . '%')
                ->orWhere('rekam_medis', $q)
                ->orWhere('no_identitas', $q)
                ->orWhere('no_hp', $q)
                ->orWhere('mr_lama', $q);
        })
            ->get();

        // Ambil semua encounter terbaru sekaligus untuk seluruh pasien
        $rekamMedisList = $pasiens->pluck('rekam_medis')->toArray();
        $encounterMap = Encounter::whereIn('rekam_medis', $rekamMedisList)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->unique('rekam_medis')
            ->keyBy('rekam_medis');

        // Mapping hasil
        $pasiens->transform(function ($pasien) use ($encounterMap) {
            // Jenis identitas
            $jenis_identitas = match ($pasien->jenis_identitas) {
                1 => 'KTP',
                2 => 'SIM',
                3 => 'Paspor',
                default => 'Lainnya',
            };
            $pasien['jenis_identitas'] = $jenis_identitas;

            // Status pasien
            $status = match ($pasien->status) {
                1 => 'Rawat Jalan',
                2 => 'Rawat Inap',
                3 => 'IGD',
                default => '-',
            };
            $pasien['status'] = $status;

            // Encounter terbaru
            $encounter = $encounterMap[$pasien->rekam_medis] ?? null;
            if ($encounter) {
                $pasien['no_encounter']  = $encounter->no_encounter;
                $pasien['tgl_encounter'] = date('d M Y H:i', strtotime($encounter->created_at));
                $pasien['type'] = match ($encounter->type) {
                    1 => 'Rawat Jalan',
                    2 => 'Rawat Inap',
                    3 => 'IGD',
                    default => '-',
                };
            } else {
                $pasien['no_encounter']  = '-';
                $pasien['tgl_encounter'] = '-';
                $pasien['type']          = '-';
            }

            return $pasien;
        });

        return $pasiens;
    }

    public function pekerjaan()
    {
        $pekerjaan = Pekerjaan::all();
        return $pekerjaan;
    }

    public function agama()
    {
        $agama = Agama::all();
        return $agama;
    }

    public function provinsi()
    {
        $provinsi = Province::orderBy('code', 'ASC')->get();
        return $provinsi;
    }

    public function store_pasien($request)
    {
        $count = Pasien::whereDate('created_at', date('Y-m-d'))->count();
        $pasien = Pasien::create([
            'rekam_medis'       => date('ymd') . ($count == 0 ? 0 : $count + 1),
            'name'              => strtoupper($request->name_pasien),
            'jenis_identitas'   => $request->jenis_identitas,
            'no_identitas'      => $request->no_identitas,
            'tgl_lahir'         => $request->tgl_lahir,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'golongan_darah'    => $request->golongan_darah,
            'kewarganegaraan'   => $request->kewarganegaraan,
            'pekerjaan'         => $request->pekerjaan,
            'status_menikah'    => $request->status_menikah,
            'agama'             => $request->agama,
            'no_hp'             => $request->no_hp,
            'no_telepon'        => $request->no_telepon,
            'mr_lama'           => $request->mr_lama,
            'alamat'            => $request->alamat,
            'province_code'     => $request->province,
            'city_code'         => $request->city
        ]);

        return $pasien;
    }

    public function editPasien($id)
    {
        $pasien = Pasien::findOrFail($id);
        return $pasien;
    }

    public function updatePasien($request, $id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->update([
            'name'              => strtoupper($request->name_pasien),
            'jenis_identitas'   => $request->jenis_identitas,
            'no_identitas'      => $request->no_identitas,
            'tgl_lahir'         => $request->tgl_lahir,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'golongan_darah'    => $request->golongan_darah,
            'kewarganegaraan'   => $request->kewarganegaraan,
            'pekerjaan'         => $request->pekerjaan,
            'status_menikah'    => $request->status_menikah,
            'agama'             => $request->agama,
            'no_hp'             => $request->no_hp,
            'no_telepon'        => $request->no_telepon,
            'mr_lama'           => $request->mr_lama,
            'alamat'            => $request->alamat,
            'province_code'     => $request->province,
            'city_code'         => $request->city
        ]);

        return $pasien;
    }

    public function showPasien($id)
    {
        $pasien = Pasien::findOrFail($id);

        // Hitung umur
        $pasien['umur'] = Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari');

        // Status pasien
        $statusList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $pasien['status'] = $statusList[$pasien->status] ?? "-";

        // Encounter terbaru
        $encounter = Encounter::where('rekam_medis', $pasien->rekam_medis)
            ->orderByDesc('created_at')
            ->first();

        if ($encounter) {
            $pasien['no_encounter']  = $encounter->no_encounter;
            $pasien['tgl_encounter'] = date('d M Y H:i', strtotime($encounter->created_at));
            $typeList = [
                0 => "-",
                1 => "Rawat Jalan",
                2 => "Rawat Inap",
                3 => "IGD"
            ];
            $pasien['type'] = $typeList[$encounter->type] ?? "-";
        } else {
            $pasien['no_encounter']  = "-";
            $pasien['tgl_encounter'] = "-";
            $pasien['type']          = "-";
        }

        return $pasien;
    }

    public function showDokter()
    {
        // Ambil kode spesialis yang mengandung kata 'Dokter' langsung dalam satu query
        $kodeSpesialis = Spesialis::where('name', 'like', '%Dokter%')->pluck('kode');

        // Ambil profile dokter berdasarkan kode spesialis
        return Profile::whereIn('spesialis', $kodeSpesialis)->get();
    }
    // Ambil data ruangan
    public function ruangan()
    {
        return Ruangan::all();
    }

    public function postRawatJalan($request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        // Hitung encounter hari ini, nomor urut selalu dua digit
        $count = Encounter::whereDate('created_at', now()->toDateString())->count();
        $noEncounter = 'E-' . now()->format('ymd') . str_pad($count + 1, 2, '0', STR_PAD_LEFT);

        // Buat encounter baru
        $encounter = Encounter::create([
            'no_encounter'        => $noEncounter,
            'rekam_medis'         => $pasien->rekam_medis,
            'name_pasien'         => $pasien->name,
            'pasien_satusehat_id' => $pasien->satusehat_id,
            'type'                => 1,
            'jenis_jaminan'       => $request->jenis_jaminan,
            'tujuan_kunjungan'    => $request->tujuan_kunjungan
        ]);

        // Jika ada dokter, buat practitioner
        if ($request->filled('dokter')) {
            $dokter = User::where('name', $request->dokter)->first();
            if ($dokter) {
                Practitioner::create([
                    'encounter_id' => $encounter->id,
                    'name'         => $dokter->name,
                    'id_petugas'   => $dokter->id_petugas,
                    'satusehat_id' => $dokter->satusehat_id
                ]);
            }
        }

        // Update status pasien menjadi Rawat Jalan (1)
        $pasien->update(['status' => 1]);

        return $encounter;
    }
    public function updateRawatJalan($request, $id)
    {
        $encounter = Encounter::findOrFail($id);

        // Update encounter hanya jika ada perubahan
        $encounter->update([
            'jenis_jaminan'    => $request->jenis_jaminan,
            'tujuan_kunjungan' => $request->tujuan_kunjungan
        ]);

        // Update practitioner hanya jika dokter valid
        if ($request->filled('dokter')) {
            $dokter = User::where('name', $request->dokter)->first();
            if ($dokter) {
                Practitioner::updateOrCreate(
                    ['encounter_id' => $encounter->id],
                    [
                        'name'         => $dokter->name,
                        'id_petugas'   => $dokter->id_petugas,
                        'satusehat_id' => $dokter->satusehat_id
                    ]
                );
            }
        }

        return $encounter;
    }
    public function destroyEncounterRajal($id)
    {
        $encounter = Encounter::findOrFail($id);

        // Ubah status pasien menjadi 0 (jika ada pasien terkait)
        Pasien::where('rekam_medis', $encounter->rekam_medis)->update(['status' => 0]);

        $encounter->delete();
        return $encounter;
    }
    // postRawatDarurat
    public function postRawatDarurat($request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        // Hitung encounter hari ini, nomor urut selalu dua digit
        $count = Encounter::whereDate('created_at', now()->toDateString())->count();
        $noEncounter = 'E-' . now()->format('ymd') . str_pad($count + 1, 2, '0', STR_PAD_LEFT);

        // Buat encounter baru
        $encounter = Encounter::create([
            'no_encounter'        => $noEncounter,
            'rekam_medis'         => $pasien->rekam_medis,
            'name_pasien'         => $pasien->name,
            'pasien_satusehat_id' => $pasien->satusehat_id,
            'type'                => 3,
            'jenis_jaminan'       => $request->jenis_jaminan,
            'tujuan_kunjungan'    => $request->tujuan_kunjungan
        ]);

        // Jika ada dokter, buat practitioner
        if ($request->filled('dokter')) {
            $dokter = User::where('name', $request->dokter)->first();
            if ($dokter) {
                Practitioner::create([
                    'encounter_id' => $encounter->id,
                    'name'         => $dokter->name,
                    'id_petugas'   => $dokter->id_petugas,
                    'satusehat_id' => $dokter->satusehat_id
                ]);
            }
        }

        // Update status pasien menjadi Rawat Darurat (3)
        $pasien->update(['status' => 3]);

        return $encounter;
    }
    // editEncounterRdarurat
    public function editEncounterRdarurat($id)
    {
        $encounter = Encounter::findOrFail($id);

        // Eager load pasien dan dokter sekaligus
        $pasien = Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
        $dokter = Practitioner::where('encounter_id', $encounter->id)
            ->orderByDesc('created_at')
            ->first();

        // Umur pasien
        $encounter['umur'] = $pasien ? Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari') : '-';

        // Status pasien
        $statusList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $encounter['status'] = $pasien ? ($statusList[$pasien->status] ?? "-") : "-";

        // Tanggal encounter
        $encounter['tgl_encounter'] = $encounter->created_at ? date('d M Y H:i', strtotime($encounter->created_at)) : "-";

        // Type encounter
        $typeList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $encounter['type'] = $typeList[$encounter->type] ?? "-";

        // Nama dokter
        $encounter['dokter'] = $dokter ? $dokter->name : "-";

        return $encounter;
    }

    public function updateRawatDarurat($request, $id)
    {
        $encounter = Encounter::findOrFail($id);

        // Update encounter hanya jika ada perubahan
        $encounter->update([
            'jenis_jaminan'    => $request->jenis_jaminan,
            'tujuan_kunjungan' => $request->tujuan_kunjungan
        ]);

        // Update practitioner hanya jika dokter valid
        if ($request->filled('dokter')) {
            $dokter = User::where('name', $request->dokter)->first();
            if ($dokter) {
                Practitioner::updateOrCreate(
                    ['encounter_id' => $encounter->id],
                    [
                        'name'         => $dokter->name,
                        'id_petugas'   => $dokter->id_petugas,
                        'satusehat_id' => $dokter->satusehat_id
                    ]
                );
            }
        }

        return $encounter;
    }
    public function destroyEncounterRdarurat($id)
    {
        $encounter = Encounter::findOrFail($id);

        // Ubah status pasien menjadi 0 (jika ada pasien terkait)
        Pasien::where('rekam_medis', $encounter->rekam_medis)->update(['status' => 0]);

        $encounter->delete();
        return $encounter;
    }
    // postRawatInap
    public function postRawatInap($request, $id)
    {
        $pasien = Pasien::findOrFail($id);
        // Hitung encounter hari ini, nomor urut selalu dua digit
        $count = Encounter::whereDate('created_at', now()->toDateString())->count();
        $noEncounter = 'E-' . now()->format('ymd') . str_pad($count + 1, 2, '0', STR_PAD_LEFT);

        $encounter = Encounter::create([
            'no_encounter'        => $noEncounter,
            'rekam_medis'         => $pasien->rekam_medis,
            'name_pasien'         => $pasien->name,
            'pasien_satusehat_id' => $pasien->satusehat_id,
            'type'                => 2,
            'jenis_jaminan'       => $request->jenis_jaminan,
            'tujuan_kunjungan'    => $request->tujuan_kunjungan
        ]);

        // InpatientAdmission
        $admission = InpatientAdmission::create([
            'encounter_id'      => $encounter->id,
            'pasien_id'         => $pasien->id,
        ]);
        // Update status pasien menjadi Rawat Inap (2)
        $pasien->update(['status' => 2]);

        return $encounter;
    }
}
