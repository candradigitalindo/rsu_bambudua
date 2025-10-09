<?php

namespace App\Repositories;

use App\Models\Agama;
use App\Models\Antrian;
use App\Models\Clinic;
use App\Models\Encounter;
use App\Models\InpatientAdmission;
use App\Models\Loket;
use App\Models\NursingCareRecord;
use App\Models\Pasien;
use App\Models\PatientCompanion;
use App\Models\Pekerjaan;
use App\Models\Practitioner;
use App\Models\Province;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Jenisjaminan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\VitalSign;

class PendaftaranRepository
{
    // Role constants (consistent with RuanganController)
    const ROLE_OWNER = 1;
    const ROLE_DOCTOR = 2;
    const ROLE_NURSE = 3;
    const ROLE_ADMIN = 4;
    const ROLE_RECEPTIONIST = 5;

    // Roles allowed to perform certain inpatient actions
    const AUTHORIZED_ROLES = [self::ROLE_OWNER, self::ROLE_NURSE, self::ROLE_ADMIN];

    // Encounter type constants
    const ENCOUNTER_RAWAT_JALAN = 1;
    const ENCOUNTER_RAWAT_INAP = 2;
    const ENCOUNTER_IGD = 3;
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

    public function showRawatJalan($request = null)
    {
        $q = $request->q ?? null;
        $perPage = (int)($request->per_page ?? 10);
        $orderBy = in_array($request->order_by ?? '', ['created_at', 'no_encounter', 'name_pasien']) ? $request->order_by : null;
        $orderDir = in_array(strtolower($request->order_dir ?? ''), ['asc', 'desc']) ? strtolower($request->order_dir) : 'desc';

        $encounters = Encounter::with(['practitioner', 'clinic'])
            ->where('type', 1)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere(function ($query) {
                        $query->where('status', 2)
                            ->whereDate('updated_at', Carbon::now()->toDateString());
                    });
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('no_encounter', 'like', "%{$q}%")
                        ->orWhere('rekam_medis', 'like', "%{$q}%")
                        ->orWhere('name_pasien', 'like', "%{$q}%");
                });
            })
            ->when($orderBy, function ($query) use ($orderBy, $orderDir) {
                $query->orderBy($orderBy, $orderDir);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($perPage);

        // Map jaminan IDs to names once per request
        $jaminanMap = Jenisjaminan::pluck('name', 'id')->toArray();

        foreach ($encounters as $e) {
            $e['status_label'] = $e->status == 1 ? "Aktif" : "Non-Aktif";
            $e['type'] = "Rawat Jalan";
            $e['dokter'] = optional($e->practitioner->first())->name ?: '-';
            $e['jenis_jaminan'] = $jaminanMap[$e->jenis_jaminan] ?? '-';
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
            $e['poliklinik'] = optional($e->clinic)->nama ?: '-';
            $e['created_at_fmt'] = $e->created_at ? $e->created_at->format('d M Y H:i') : '-';
        }

        return $encounters;
    }

    public function showRawatDarurat($request = null)
    {
        $q = $request->q ?? null;
        $perPage = (int)($request->per_page ?? 10);
        $orderBy = in_array($request->order_by ?? '', ['created_at', 'no_encounter', 'name_pasien']) ? $request->order_by : null;
        $orderDir = in_array(strtolower($request->order_dir ?? ''), ['asc', 'desc']) ? strtolower($request->order_dir) : 'asc';

        $encounters = Encounter::with(['practitioner'])
            ->where('type', 3)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere(function ($query) {
                        $query->where('status', 2)
                            ->whereDate('updated_at', Carbon::now()->toDateString());
                    });
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('no_encounter', 'like', "%{$q}%")
                        ->orWhere('rekam_medis', 'like', "%{$q}%")
                        ->orWhere('name_pasien', 'like', "%{$q}%");
                });
            })
            ->when($orderBy, function ($query) use ($orderBy, $orderDir) {
                $query->orderBy($orderBy, $orderDir);
            }, function ($query) {
                $query->orderBy('updated_at', 'asc');
            })
            ->paginate($perPage);

        // Map jaminan IDs to names once per request
        $jaminanMap = Jenisjaminan::pluck('name', 'id')->toArray();

        foreach ($encounters as $e) {
            $e['status_label'] = $e->status == 1 ? "Aktif" : "Non-Aktif";
            $e['type'] = "Rawat Darurat";
            $e['dokter'] = optional($e->practitioner->first())->name ?: '-';
            $e['jenis_jaminan'] = $jaminanMap[$e->jenis_jaminan] ?? '-';
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
            $e['created_at_fmt'] = $e->created_at ? $e->created_at->format('d M Y H:i') : '-';
        }
        return $encounters;
    }

    public function showRawatInap($request = null)
    {
        $q = $request->q ?? null;
        $perPage = (int)($request->per_page ?? 10);
        $orderBy = in_array($request->order_by ?? '', ['created_at', 'no_encounter', 'name_pasien']) ? $request->order_by : null;
        $orderDir = in_array(strtolower($request->order_dir ?? ''), ['asc', 'desc']) ? strtolower($request->order_dir) : 'desc';

        $encounters = Encounter::with(['admission'])
            ->where('type', 2)
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere(function ($query) {
                        $query->where('status', 2)
                            ->whereDate('updated_at', Carbon::now()->toDateString());
                    });
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('no_encounter', 'like', "%{$q}%")
                        ->orWhere('rekam_medis', 'like', "%{$q}%")
                        ->orWhere('name_pasien', 'like', "%{$q}%");
                });
            })
            ->when($orderBy, function ($query) use ($orderBy, $orderDir) {
                $query->orderBy($orderBy, $orderDir);
            }, function ($query) {
                $query->orderBy('updated_at', 'DESC');
            })
            ->paginate($perPage);

        // Map jaminan IDs to names once per request
        $jaminanMap = Jenisjaminan::pluck('name', 'id')->toArray();

        foreach ($encounters as $e) {
            $e['status_label'] = $e->status == 1 ? "Aktif" : "Non-Aktif";
            $e['type'] = "Rawat Inap";
            $e['dokter'] = optional($e->admission)->nama_dokter ?: '-';
            $e['jenis_jaminan'] = $jaminanMap[$e->jenis_jaminan] ?? '-';
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
            $e['created_at_fmt'] = $e->created_at ? $e->created_at->format('d M Y H:i') : '-';
        }
        return $encounters;
    }

    public function editEncounterRajal($id)
    {
        $encounter = Encounter::with(['clinic', 'practitioner.user'])->findOrFail($id);
        $pasien = Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
        $encounter['umur'] = $pasien ? Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari') : '-';
        $encounter['tgl_encounter'] = $encounter->created_at ? date('d M Y H:i', strtotime($encounter->created_at)) : "-";
        $typeList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $encounter['type'] = $typeList[$encounter->type] ?? "-";
        $encounter['dokter_ids'] = $encounter->practitioner->pluck('user.id')->filter()->toArray();
        $encounter['poliklinik'] = $encounter->clinic ? $encounter->clinic->nama : "-";
        $encounter['clinic_id'] = $encounter->clinic ? $encounter->clinic->id : null;
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

        $pasiens = Pasien::where(function ($query) use ($q) {
            $query->where('name', 'like', '%' . $q . '%')
                ->orWhere('rekam_medis', $q)
                ->orWhere('no_identitas', $q)
                ->orWhere('no_hp', $q)
                ->orWhere('mr_lama', $q);
        })
            ->get();

        $rekamMedisList = $pasiens->pluck('rekam_medis')->toArray();
        $encounterMap = Encounter::whereIn('rekam_medis', $rekamMedisList)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->unique('rekam_medis')
            ->keyBy('rekam_medis');

        $pasiens->transform(function ($pasien) use ($encounterMap) {
            $jenis_identitas = match ($pasien->jenis_identitas) {
                1 => 'KTP',
                2 => 'SIM',
                3 => 'Paspor',
                default => 'Lainnya',
            };
            $pasien['jenis_identitas'] = $jenis_identitas;

            $pasien['status'] = null;

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
        return Pekerjaan::all();
    }

    public function agama()
    {
        return Agama::all();
    }

    public function provinsi()
    {
        return Province::orderBy('code', 'ASC')->get();
    }

    public function jenisjaminan()
    {
        return \App\Models\Jenisjaminan::where('status', 1)->orderBy('name', 'ASC')->get();
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
        $pasienData = $pasien->toArray();
        if ($pasien->tgl_lahir) {
            $pasienData['tgl_lahir'] = $pasien->tgl_lahir->format('Y-m-d');
        }
        return (object) $pasienData;
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

        $pasien['umur'] = Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari');

        $statusList = [
            0 => "Pasien Baru",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $statusValue = $pasien->status;
        if (is_bool($statusValue)) {
            $statusValue = $statusValue ? 1 : 0;
        }
        $pasien['status'] = $statusList[$statusValue] ?? "Pasien Baru";

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

    public function showClinic()
    {
        return Clinic::all();
    }

    public function ruangan()
    {
        return Ruangan::orderBy('no_kamar', 'ASC')->get();
    }

    public function postRawatJalan($request, $id)
    {
        $pasien = Pasien::findOrFail($id);
        $count = Encounter::whereDate('created_at', now()->toDateString())->count();
        $noEncounter = 'E-' . now()->format('ymd') . str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        $encounter = Encounter::create([
            'no_encounter'        => $noEncounter,
            'rekam_medis'         => $pasien->rekam_medis,
            'name_pasien'         => $pasien->name,
            'pasien_satusehat_id' => $pasien->satusehat_id,
            'type'                => 1,
            'jenis_jaminan'       => $request->jenis_jaminan,
            'tujuan_kunjungan'    => $request->tujuan_kunjungan,
            'clinic_id'           => $request->clinic_id ? $request->clinic_id : null,
            'created_by'         => Auth::id()
        ]);

        if ($request->filled('dokter') && is_array($request->dokter)) {
            $dokters = User::whereIn('id', $request->dokter)->get();
            foreach ($dokters as $dokter) {
                Practitioner::create([
                    'encounter_id' => $encounter->id,
                    'name'         => $dokter->name,
                    'id_petugas'   => $dokter->id,
                    'satusehat_id' => $dokter->satusehat_id
                ]);
            }
        }

        $pasien->update(['status' => 1]);
        return $encounter;
    }

    public function updateRawatJalan($request, $id)
    {
        $encounter = Encounter::findOrFail($id);
        $encounter->update([
            'jenis_jaminan'    => $request->jenis_jaminan,
            'tujuan_kunjungan' => $request->tujuan_kunjungan,
            'clinic_id'        => $request->clinic_id ? $request->clinic_id : null,
        ]);

        if ($request->filled('dokter') && is_array($request->dokter)) {
            Practitioner::where('encounter_id', $encounter->id)->delete();
            $dokters = User::whereIn('id', $request->dokter)->get();
            foreach ($dokters as $dokter) {
                Practitioner::create([
                    'encounter_id' => $encounter->id,
                    'name'         => $dokter->name,
                    'id_petugas'   => $dokter->id,
                    'satusehat_id' => $dokter->satusehat_id
                ]);
            }
        }
        return $encounter;
    }

    public function destroyEncounterRajal($id)
    {
        $encounter = Encounter::findOrFail($id);
        Pasien::where('rekam_medis', $encounter->rekam_medis)->update(['status' => 0]);
        $encounter->delete();
        return $encounter;
    }

    public function postRawatDarurat($request, $id)
    {
        $pasien = Pasien::findOrFail($id);
        $count = Encounter::whereDate('created_at', now()->toDateString())->count();
        $noEncounter = 'E-' . now()->format('ymd') . str_pad($count + 1, 2, '0', STR_PAD_LEFT);
        $encounter = Encounter::create([
            'no_encounter'        => $noEncounter,
            'rekam_medis'         => $pasien->rekam_medis,
            'name_pasien'         => $pasien->name,
            'pasien_satusehat_id' => $pasien->satusehat_id,
            'type'                => 3,
            'jenis_jaminan'       => $request->jenis_jaminan,
            'tujuan_kunjungan'    => $request->tujuan_kunjungan,
            'created_by'         => Auth::id()
        ]);

        if ($request->filled('dokter') && is_array($request->dokter)) {
            $dokters = User::whereIn('id', $request->dokter)->get();
            foreach ($dokters as $dokter) {
                Practitioner::create([
                    'encounter_id' => $encounter->id,
                    'name'         => $dokter->name,
                    'id_petugas'   => $dokter->id,
                    'satusehat_id' => $dokter->satusehat_id
                ]);
            }
        }

        $pasien->update(['status' => 3]);
        return $encounter;
    }

    public function editEncounterRdarurat($id)
    {
        $encounter = Encounter::with(['practitioner.user'])->findOrFail($id);
        $pasien = Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
        $encounter['umur'] = $pasien ? Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari') : '-';
        $encounter['tgl_encounter'] = $encounter->created_at ? date('d M Y H:i', strtotime($encounter->created_at)) : "-";
        $typeList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $encounter['type'] = $typeList[$encounter->type] ?? "-";
        $encounter['dokter_ids'] = $encounter->practitioner->pluck('user.id')->filter()->toArray();
        return $encounter;
    }

    public function updateRawatDarurat($request, $id)
    {
        $encounter = Encounter::findOrFail($id);
        $encounter->update([
            'jenis_jaminan'    => $request->jenis_jaminan,
            'tujuan_kunjungan' => $request->tujuan_kunjungan
        ]);
        if ($request->filled('dokter') && is_array($request->dokter)) {
            Practitioner::where('encounter_id', $encounter->id)->delete();
            $dokters = User::whereIn('id', $request->dokter)->get();
            foreach ($dokters as $dokter) {
                Practitioner::create([
                    'encounter_id' => $encounter->id,
                    'name'         => $dokter->name,
                    'id_petugas'   => $dokter->id,
                    'satusehat_id' => $dokter->satusehat_id
                ]);
            }
        }
        return $encounter;
    }

    public function destroyEncounterRdarurat($id)
    {
        $encounter = Encounter::findOrFail($id);
        Pasien::where('rekam_medis', $encounter->rekam_medis)->update(['status' => 0]);
        $encounter->delete();
        return $encounter;
    }

    public function postRawatInap($request, $id)
    {
        $encounter = Encounter::findOrFail($id);
        Practitioner::where('encounter_id', $encounter->id)->delete();
        if ($request->filled('dokter') && is_array($request->dokter)) {
            $dokters = User::whereIn('id', $request->dokter)->get();
            foreach ($dokters as $dokter) {
                Practitioner::create([
                    'encounter_id' => $encounter->id,
                    'name'         => $dokter->name,
                    'id_petugas'   => $dokter->id,
                    'satusehat_id' => $dokter->satusehat_id
                ]);
            }
        }

        $encounter->admission->update([
            'ruang_id' => $request->ruang_id,
            'ruangan_id' => $request->ruangan,
            'nama_dokter' => isset($dokters) ? $dokters->pluck('name')->implode(', ') : ($encounter->admission->nama_dokter ?? null),
        ]);
        $patient_companions = PatientCompanion::where('admission_id', $encounter->admission->id)->first();
        if (!$patient_companions) {
            PatientCompanion::create([
                'admission_id' => $encounter->admission->id,
                'name'         => $request->name_companion,
                'nik'         => $request->nik_companion,
                'phone'         => $request->phone_companion,
                'relation'     => $request->relation_companion
            ]);
        } else {
            $patient_companions->update([
                'name'         => $request->name_companion,
                'nik'         => $request->nik_companion,
                'phone'         => $request->phone_companion,
                'relation'     => $request->relation_companion
            ]);
        }
        return $encounter;
    }

    public function editEncounterRinap($id)
    {
        $encounter = Encounter::with(['admission', 'admission.companions', 'practitioner.user'])->findOrFail($id);

        $pasien = Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
        $dokters = $encounter->practitioner;

        $encounter['umur'] = $pasien ? Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari') : '-';

        $statusList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $encounter['status'] = $pasien ? ($statusList[$pasien->status] ?? "-") : "-";

        $encounter['tgl_encounter'] = $encounter->created_at ? date('d M Y H:i', strtotime($encounter->created_at)) : "-";

        $typeList = [
            0 => "-",
            1 => "Rawat Jalan",
            2 => "Rawat Inap",
            3 => "IGD"
        ];
        $encounter['type'] = $typeList[$encounter->type] ?? "-";

        $encounter['dokter_ids'] = $dokters->pluck('user.id')->filter()->toArray();

        return $encounter;
    }

    public function destroyEncounterRinap($id)
    {
        $encounter = Encounter::findOrFail($id);

        Pasien::where('rekam_medis', $encounter->rekam_medis)->update(['status' => 0]);

        if ($encounter->admission) {
            PatientCompanion::where('admission_id', $encounter->admission->id)->delete();
            $encounter->admission->delete();
        }

        $encounter->delete();
        return $encounter;
    }

    /**
     * Generate urgent tasks based on patient data
     */
    private function generateUrgentTasks($nurseAssignments)
    {
        $urgentTasks = collect();

        // Long stay patients (>7 days)
        $longStayPatients = $nurseAssignments->filter(fn($patient) => ($patient['days_admitted'] ?? 0) > 7);
        foreach ($longStayPatients->take(2) as $patient) {
            $urgentTasks->push([
                'message' => "Review discharge plan untuk {$patient['patient_name']} di {$patient['room']} ({$patient['days_admitted']} hari)",
                'time' => $patient['days_admitted'] . ' hari rawat inap',
                'priority' => 'high',
                'type' => 'discharge_review'
            ]);
        }

        // Critical patients
        $criticalPatients = $nurseAssignments->filter(fn($patient) => strtolower($patient['condition']) === 'critical');
        foreach ($criticalPatients->take(3) as $patient) {
            $urgentTasks->push([
                'message' => "Monitor intensive {$patient['patient_name']} di {$patient['room']} - Kondisi Critical",
                'time' => 'Setiap 30 menit',
                'priority' => 'urgent',
                'type' => 'critical_monitoring'
            ]);
        }

        // New admissions
        $newAdmissions = $nurseAssignments->filter(fn($patient) => ($patient['days_admitted'] ?? 0) <= 1);
        foreach ($newAdmissions->take(2) as $patient) {
            $urgentTasks->push([
                'message' => "Initial assessment {$patient['patient_name']} di {$patient['room']} - Pasien baru",
                'time' => 'Dalam 2 jam',
                'priority' => 'normal',
                'type' => 'initial_assessment'
            ]);
        }

        // Default tasks if empty
        if ($urgentTasks->isEmpty()) {
            $urgentTasks->push([
                'message' => 'Lakukan round check semua pasien rawat inap',
                'time' => 'Setiap 2 jam',
                'priority' => 'normal',
                'type' => 'general_round'
            ]);
        }

        return $urgentTasks;
    }

    /**
     * Get current shift information
     */
    private function getCurrentShiftInfo()
    {
        $currentHour = now()->format('H');

        if ($currentHour >= 6 && $currentHour < 14) {
            $shift = ['current' => 'Pagi (06:00-14:00)', 'start' => '06:00', 'next' => '14:00 (Shift Sore)'];
        } elseif ($currentHour >= 14 && $currentHour < 22) {
            $shift = ['current' => 'Sore (14:00-22:00)', 'start' => '14:00', 'next' => '22:00 (Shift Malam)'];
        } else {
            $shift = ['current' => 'Malam (22:00-06:00)', 'start' => '22:00', 'next' => '06:00 (Shift Pagi)'];
        }

        return [
            'current_shift' => $shift['current'],
            'shift_start' => $shift['start'],
            'next_shift' => $shift['next'],
            'nurse_count' => 3,
            'patients_per_nurse' => 0
        ];
    }

    /**
     * Get nurses list optimized
     */
    private function getNurses()
    {
        return User::where('role', self::ROLE_NURSE)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Return fallback dashboard data
     */
    private function returnFallbackDashboard($errorMessage)
    {
        return view('pages.ruangan.nurse-bed-dashboard', [
            'availability' => [],
            'summary' => ['total_beds' => 0, 'occupied_beds' => 0, 'available_beds' => 0, 'occupancy_rate' => 0],
            'nurseAssignments' => collect(),
            'urgentTasks' => collect([
                ['message' => 'Sistem sedang maintenance - Data akan segera tersedia', 'time' => 'Sekarang', 'priority' => 'normal', 'type' => 'system']
            ]),
            'shiftInfo' => ['current_shift' => 'N/A', 'shift_start' => 'N/A', 'next_shift' => 'N/A', 'nurse_count' => 0, 'patients_per_nurse' => 0],
            'roomPatients' => collect(),
            'allNurses' => collect()
        ])->with('error', 'Terjadi kesalahan saat memuat data: ' . $errorMessage);
    }

    /**
     * Optimized get occupied patients with single query
     */
    public function getOccupiedPatients()
    {
        try {
            $occupiedPatients = $this->getActiveAdmissions()->map(function ($admission) {
                return [
                    'room_number' => optional($admission->room)->no_kamar ?? 'N/A',
                    'category_name' => optional($admission->room->category)->name ?? 'N/A',
                    'class' => optional($admission->room)->class ?? 'Umum',
                    'patient_name' => optional($admission->pasien)->name ?? 'N/A',
                    'gender' => optional($admission->pasien)->jenis_kelamin ?? 'N/A',
                    'age' => $this->calculateAge($admission->pasien),
                    'medical_record' => optional($admission->pasien)->rekam_medis ?? 'N/A',
                    'doctor_name' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                    'admission_date' => $admission->admission_date,
                    'admission_reason' => $admission->admission_reason ?? 'N/A',
                    'status' => $this->mapAdmissionStatus($admission->status ?? 'active'),
                ];
            });

            return $this->jsonResponse(true, 'Data pasien berhasil diambil', $occupiedPatients);
        } catch (\Exception $e) {
            Log::error('Error in getOccupiedPatients: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error mengambil data pasien: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get all active admissions with optimized relationships (shared helper)
     */
    private function getActiveAdmissions()
    {
        return InpatientAdmission::with([
            'room:id,no_kamar,class,capacity,category_id',
            'room.category:id,name',
            'pasien:id,name,rekam_medis,tgl_lahir,jenis_kelamin',
            'doctor:id,name',
            'encounter:id,type,status'
        ])
            ->whereNull('discharge_date')
            ->whereHas('encounter', function ($query) {
                $query->whereIn('type', [self::ENCOUNTER_RAWAT_INAP, self::ENCOUNTER_IGD]);
            })
            ->orderBy('admission_date', 'desc')
            ->get();
    }

    /**
     * Optimized patient detail with eager loading
     */
    public function getPatientDetail($roomNumber, $patientId = null)
    {
        try {
            $query = InpatientAdmission::with([
                'room:id,no_kamar,class,category_id',
                'room.category:id,name',
                'pasien:id,name,rekam_medis,tgl_lahir,jenis_kelamin',
                'doctor:id,name',
                'encounter:id,type,status'
            ])
                ->whereNull('discharge_date')
                ->whereHas('room', fn($q) => $q->where('no_kamar', $roomNumber));

            if ($patientId) {
                $query->where('id', $patientId);
            }

            $admission = $query->first();

            if (!$admission) {
                return $this->jsonResponse(false, 'Patient not found in room ' . $roomNumber, [], 404);
            }

            $patientDetail = [
                'id' => $admission->id,
                'patient_name' => optional($admission->pasien)->name ?? 'N/A',
                'medical_record' => optional($admission->pasien)->rekam_medis ?? 'N/A',
                'age' => $this->calculateAge($admission->pasien),
                'gender' => optional($admission->pasien)->jenis_kelamin ?? 'N/A',
                'room_number' => optional($admission->room)->no_kamar ?? 'N/A',
                'room_category' => optional($admission->room->category)->name ?? 'N/A',
                'room_class' => optional($admission->room)->class ?? 'Umum',
                'doctor_name' => optional($admission->doctor)->name ?? $admission->nama_dokter ?? 'N/A',
                'admission_date' => $admission->admission_date,
                'days_admitted' => $admission->admission_date ? now()->diffInDays($admission->admission_date) : 0,
                'admission_reason' => $admission->admission_reason ?? 'N/A',
                'condition' => $this->mapAdmissionStatus($admission->status ?? 'active'),
                'vital_signs_history' => $this->getVitalSignsHistory($admission->id),
                'nursing_notes' => $this->getNursingNotesHistory($admission->id)
            ];

            return $this->jsonResponse(true, 'Patient detail retrieved successfully', $patientDetail);
        } catch (\Exception $e) {
            Log::error('Error in getPatientDetail: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error retrieving patient detail: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Optimized nursing note creation
     */
    public function addNursingNote(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|exists:inpatient_admissions,id',
            'note' => 'required|string|max:1000',
            'note_type' => 'nullable|string|in:observation,medication,procedure,general',
            'priority' => 'nullable|string|in:low,normal,high,urgent'
        ]);

        try {
            $nursingNote = NursingCareRecord::create([
                'admission_id' => $request->admission_id,
                'nurse_id' => Auth::id(),
                'note' => $request->note,
                'note_type' => $request->note_type ?? 'general',
                'priority' => $request->priority ?? 'normal',
                'recorded_at' => now()
            ]);

            return $this->jsonResponse(true, 'Nursing note added successfully', [
                'id' => $nursingNote->id,
                'nurse_name' => Auth::user()->name,
                'note' => $nursingNote->note,
                'recorded_at' => $nursingNote->recorded_at->format('Y-m-d H:i:s')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in addNursingNote: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error adding nursing note: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Optimized vital signs recording
     */
    public function recordVitalSigns(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|exists:inpatient_admissions,id',
            'measurement_time' => 'required|date',
            'blood_pressure_systolic' => 'nullable|numeric|min:50|max:300',
            'blood_pressure_diastolic' => 'nullable|numeric|min:30|max:200',
            'heart_rate' => 'nullable|numeric|min:30|max:200',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'respiratory_rate' => 'nullable|numeric|min:5|max:60',
            'oxygen_saturation' => 'nullable|numeric|min:70|max:100',
            'consciousness_level' => 'nullable|string|in:alert,drowsy,confused,unconscious',
            'notes' => 'nullable|string|max:1000'
        ]);

        if (!in_array(Auth::user()->role, self::AUTHORIZED_ROLES)) {
            return $this->jsonResponse(false, 'Unauthorized role.', [], 403);
        }

        try {
            $vitalSign = VitalSign::create(array_merge(
                $request->only([
                    'admission_id',
                    'measurement_time',
                    'blood_pressure_systolic',
                    'blood_pressure_diastolic',
                    'heart_rate',
                    'temperature',
                    'respiratory_rate',
                    'oxygen_saturation',
                    'consciousness_level',
                    'notes'
                ]),
                ['recorded_by_id' => Auth::id()]
            ));

            return $this->jsonResponse(true, 'Vital signs recorded successfully', [
                'blood_pressure' => $this->formatBloodPressure($vitalSign->blood_pressure_systolic, $vitalSign->blood_pressure_diastolic),
                'heart_rate' => $vitalSign->heart_rate ? $vitalSign->heart_rate . ' bpm' : null,
                'temperature' => $vitalSign->temperature ? $vitalSign->temperature . '°C' : null,
                'recorded_at' => now()->format('Y-m-d H:i:s')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in recordVitalSigns: ' . $e->getMessage());
            return $this->jsonResponse(false, 'Error recording vital signs: ' . $e->getMessage(), [], 500);
        }
    }

    // ===== Helper methods to avoid undefined method errors =====
    private function calculateAge($patient)
    {
        if (!$patient || !$patient->tgl_lahir) return 'N/A';
        try {
            return \Carbon\Carbon::parse($patient->tgl_lahir)->age;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function mapAdmissionStatus($status)
    {
        $statusMap = [
            'active' => 'active',
            'stable' => 'stable',
            'critical' => 'critical',
            'observation' => 'observation',
            'recovery' => 'recovery',
            'discharged' => 'discharged',
            'improving' => 'stable',
            'monitoring' => 'stable'
        ];
        return $statusMap[$status] ?? 'active';
    }

    private function getVitalSignsHistory($admissionId)
    {
        try {
            return VitalSign::where('admission_id', $admissionId)
                ->with('recordedBy:id,name')
                ->orderBy('measurement_time', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($vital) {
                    return [
                        'time' => $vital->measurement_time ? $vital->measurement_time->format('d/m/Y H:i') : 'N/A',
                        'summary' => $this->formatBloodPressure($vital->blood_pressure_systolic, $vital->blood_pressure_diastolic),
                        'recorded_by' => optional($vital->recordedBy)->name ?? 'N/A',
                    ];
                });
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getNursingNotesHistory($admissionId)
    {
        try {
            // If NursingCareRecord doesn't support these columns in this project, return empty collection
            if (!class_exists(\App\Models\NursingCareRecord::class)) {
                return collect();
            }
            return NursingCareRecord::with('nurse:id,name')
                ->where('admission_id', $admissionId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'note' => $note->note ?? $note->interventions ?? $note->evaluation_notes ?? 'N/A',
                        'nurse_name' => optional($note->nurse)->name ?? 'N/A',
                        'recorded_at' => optional($note->created_at)->format('Y-m-d H:i:s') ?? 'N/A',
                    ];
                });
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function formatBloodPressure($systolic, $diastolic)
    {
        if (!$systolic && !$diastolic) return null;
        if ($systolic && $diastolic) return $systolic . '/' . $diastolic . ' mmHg';
        return ($systolic ?: '-') . '/' . ($diastolic ?: '-') . ' mmHg';
    }

    private function jsonResponse($success, $message, $data = [], $code = 200)
    {
        return response()->json([
            'success' => (bool) $success,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
