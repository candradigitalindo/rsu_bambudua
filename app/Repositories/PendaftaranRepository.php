<?php

namespace App\Repositories;

use App\Events\AntrianEvent;
use App\Models\Agama;
use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Pasien;
use App\Models\Pekerjaan;
use App\Models\Province;
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
        $loket   = Loket::where('user_id', Auth::user()->id)->first();
        if ($loket) {
            $antrian = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 2)->orderBy('updated_at', 'DESC')->first();
            $jumlah  = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 1)->count();
            return ['antrian' => $antrian == null ? 0 : $antrian->prefix . " " . $antrian->nomor, 'jumlah' => $jumlah];
        } else {
            return ['antrian' => "--", 'jumlah' => 0];
        }
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
        $pasiens = Pasien::where('name', 'like', '%' . $request->q . '%')->orWhere('rekam_medis', $request->q)->orWhere('no_identitas', $request->q)->orWhere('no_hp', $request->q)->orWhere('mr_lama', $request->q)->get();
        $pasiens->map(function ($pasien) {
            switch ($pasien->jenis_identitas) {
                case '1':
                    $jenis_identitas = "KTP";
                    break;
                case '2':
                    $jenis_identitas = "SIM";
                    break;
                case '3':
                    $jenis_identitas = "Paspor";
                    break;
                default:
                    $jenis_identitas = "Lainnya";
                    break;
            }
            $pasien['jenis_identitas'] = $jenis_identitas;

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
}
