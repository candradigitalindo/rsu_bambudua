<?php

namespace App\Repositories;

use App\Models\Encounter;

class EncounterRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getAllRawatJalan()
    {
        $query = Encounter::query()
            ->whereIn('type', [1, 2])
            ->where(function ($q) {
                $q->where('status', 1)
                    ->orWhere(function ($q2) {
                        $q2->where('status', 2)
                            ->whereDate('updated_at', now()->toDateString());
                    });
            });

        if (request('name')) {
            $query->where('name_pasien', 'like', '%' . request('name') . '%');
        }

        // Jika user dokter, filter by practitioner
        if (auth()->user()->role == 2) {
            $query->whereHas('practitioner', function ($q) {
                $q->where('id_petugas', auth()->user()->id_petugas);
            });
        }

        $encounters = $query->orderBy('updated_at', 'asc')->get();

        // Mapping data
        $encounters->transform(function ($encounter) {
            $encounter->status = $encounter->status == 1 ? "Progress" : "Finish";
            $encounter->jenis_jaminan = $encounter->jenis_jaminan == 1 ? "Umum" : "Lainnya";
            $encounter->tujuan_kunjungan = match ($encounter->tujuan_kunjungan) {
                1 => "Kunjungan Sehat (Promotif/Preventif)",
                2 => "Rehabilitatif",
                3 => "Kunjungan Sakit",
                4 => "Darurat",
                5 => "Kontrol / Tindak Lanjut",
                6 => "Treatment",
                7 => "Konsultasi",
                default => "-",
            };
            return $encounter;
        });

        return $encounters;
    }
    public function getAllRawatInap()
    {
        // query if auth user is doctor in practitioner table
        if (auth()->user()->role == 2) {
            $encounters = Encounter::where('name_pasien', 'like', '%' . request('name') . '%')->where('type', 2)->where('status', 1)->whereHas('practitioner', function ($query) {
                $query->where('id_petugas', auth()->user()->id_petugas);
            })->orderBy('updated_at', 'DESC')->get();
        } else {
            $encounters = Encounter::where('name_pasien', 'like', '%' . request('name') . '%')->where('type', 2)->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        }
        return $encounters;
    }
    public function getAllRawatDarurat()
    {
        // query if auth user is doctor in practitioner table
        if (auth()->user()->role == 2) {
            $encounters = Encounter::where('name_pasien', 'like', '%' . request('name') . '%')->where('type', 3)->where('status', 1)->whereHas('practitioner', function ($query) {
                $query->where('id_petugas', auth()->user()->id_petugas);
            })->orderBy('updated_at', 'DESC')->get();
        } else {
            $encounters = Encounter::where('name_pasien', 'like', '%' . request('name') . '%')->where('type', 3)->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        }
        return $encounters;
    }
    // Cetak Encounter
    public function getEncounterById($id)
    {
        $encounter = Encounter::findOrFail($id);
        $encounter->status = $encounter->status == 1 ? "Progress" : "Finish";
        $encounter->jenis_jaminan = $encounter->jenis_jaminan == 1 ? "Umum" : "Lainnya";
        $encounter->tujuan_kunjungan = match ($encounter->tujuan_kunjungan) {
            1 => "Kunjungan Sehat (Promotif/Preventif)",
            2 => "Rehabilitatif",
            3 => "Kunjungan Sakit",
            4 => "Darurat",
            5 => "Kontrol / Tindak Lanjut",
            6 => "Treatment",
            7 => "Konsultasi",
            default => "-",
        };
        return $encounter;
    }
}
