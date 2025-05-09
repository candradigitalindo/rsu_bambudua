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
        // query if auth user is doctor in practitioner table
        if (auth()->user()->role == 2) {
            $encounters = Encounter::where('name_pasien', 'like', '%' . request('name') . '%')->where('type', 1)->where('status', 1)->whereHas('practitioner', function ($query) {
                $query->where('id_petugas', auth()->user()->id_petugas);
            })->orderBy('updated_at', 'ASC')->get();
        }else {
            $encounters = Encounter::where('name_pasien', 'like', '%' . request('name') . '%')->where('type', 1)->where('status', 1)->orderBy('updated_at', 'ASC')->get();
        }
        $encounters->map(function ($encounter) {
            $encounter['status'] = $encounter->status == 1 ? "Progress" : "Finish";
            $encounter['jenis_jaminan'] = $encounter->jenis_jaminan == 1 ? "Umum" : "Lainnya";
            switch ($encounter->tujuan_kunjungan) {
                case '1':
                    $kujungan = "Kunjungan Sehat (Promotif/Preventif)";
                    break;
                case '2':
                    $kujungan = "Rehabilitatif";
                    break;
                case '3':
                    $kujungan = "Kunjungan Sakit";
                    break;
                case '4':
                    $kujungan = "Darurat";
                    break;
                case '5':
                    $kujungan = "Kontrol / Tindak Lanjut";
                    break;
                case '6':
                    $kujungan = "Treatment";
                    break;
                case '7':
                    $kujungan = "Konsultasi";
                    break;
                default:
                    $kujungan = "-";
                    break;
            }
            $encounter['tujuan_kunjungan'] = $kujungan;
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
        }else {
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
        }else {
            $encounters = Encounter::where('name_pasien', 'like', '%' . request('name') . '%')->where('type', 3)->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        }
        return $encounters;
    }
}
