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
        $loket   = Loket::where('user_id', Auth::user()->id)->first();
        if ($loket) {
            $antrian = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 2)->orderBy('updated_at', 'DESC')->first();
            $jumlah  = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 1)->count();
            return ['antrian' => $antrian == null ? 0 : $antrian->prefix . " " . $antrian->nomor, 'jumlah' => $jumlah];
        } else {
            return ['antrian' => "--", 'jumlah' => 0];
        }
    }

    public function showRawatJalan()
    {
        $encounter = Encounter::where('type', 1)->where('status', 1)->orderBy('created_at', 'desc')->get();
        $encounter->map(function ($e) {
            $e['status'] = $e->status == 1 ? "Progress" : "Finish";
            switch ($e->type) {
                case '0':
                    $type = "-";
                    break;
                case '1':
                    $type = "Rawat Jalan";
                    break;
                case '2':
                    $type = "Rawat Inap";
                    break;
                case '3':
                    $type = "IGD";
                    break;
                default:
                    $type = "-";
                    break;
            }
            $e['type'] = $type;
            $dokter = Practitioner::where('encounter_id', $e->id)->first();
            $e['dokter'] = $dokter->name;
            $e['jenis_jaminan'] = $e->jenis_jaminan == 1 ? "Umum" : "Lainnya";
            switch ($e->tujuan_kunjungan) {
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
            $e['tujuan_kunjungan'] = $kujungan;
        });
        return $encounter;
    }

    public function editEncounterRajal($id)
    {
        $encounter  = Encounter::findOrFail($id);
        $pasien     = Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
        $encounter['umur'] = Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari');
        switch ($pasien->status) {
            case '0':
                $status = "-";
                break;
            case '1':
                $status = "Rawat Jalan";
                break;
            case '2':
                $status = "Rawat Inap";
                break;
            case '3':
                $status = "IGD";
                break;
            default:
                $status = "-";
                break;
        }

        $encounter['status'] = $status;
        $encounter['tgl_encounter']    = date('d M Y H:i', strtotime($encounter->created_at));
        switch ($encounter->type) {
            case '0':
                $type = "-";
                break;
            case '1':
                $type = "Rawat Jalan";
                break;
            case '2':
                $type = "Rawat Inap";
                break;
            case '3':
                $type = "IGD";
                break;
            default:
                $type = "-";
                break;
        }
        $encounter['type']     = $type;
        $dokter       = Practitioner::where('encounter_id', $encounter->id)->orderBy('created_at', 'DESC')->first();
        $encounter['dokter']     = $dokter->name;
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
            switch ($pasien->status) {
                case '0':
                    $status = "-";
                    break;
                case '1':
                    $status = "Rawat Jalan";
                    break;
                case '2':
                    $status = "Rawat Inap";
                    break;
                case '3':
                    $status = "IGD";
                    break;
                default:
                    $status = "-";
                    break;
            }

            $pasien['status'] = $status;
            $encounter = Encounter::where('rekam_medis', $pasien->rekam_medis)->orderBy('created_at', 'DESC')->first();
            if ($encounter) {
                $pasien['no_encounter']     = $encounter->no_encounter;
                $pasien['tgl_encounter']    = date('d M Y H:i', strtotime($encounter->created_at));
                switch ($encounter->type) {
                    case '0':
                        $type = "-";
                        break;
                    case '1':
                        $type = "Rawat Jalan";
                        break;
                    case '2':
                        $type = "Rawat Inap";
                        break;
                    case '3':
                        $type = "IGD";
                        break;
                    default:
                        $type = "-";
                        break;
                }
                $pasien['type']     = $type;
            } else {
                $pasien['no_encounter']     = "-";
                $pasien['tgl_encounter']    = "-";
                $pasien['type']             = "-";
            }
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
        $pasien['umur'] = Carbon::parse($pasien->tgl_lahir)->diff(Carbon::now())->format('%y tahun, %m bulan, %d hari');
        switch ($pasien->status) {
            case '0':
                $status = "-";
                break;
            case '1':
                $status = "Rawat Jalan";
                break;
            case '2':
                $status = "Rawat Inap";
                break;
            case '3':
                $status = "IGD";
                break;
            default:
                $status = "-";
                break;
        }
        $pasien['status'] = $status;
        $encounter = Encounter::where('rekam_medis', $pasien->rekam_medis)->orderBy('created_at', 'DESC')->first();
        if ($encounter) {
            $pasien['no_encounter']     = $encounter->no_encounter;
            $pasien['tgl_encounter']    = date('d M Y H:i', strtotime($encounter->created_at));
            switch ($encounter->type) {
                case '0':
                    $type = "-";
                    break;
                case '1':
                    $type = "Rawat Jalan";
                    break;
                case '2':
                    $type = "Rawat Inap";
                    break;
                case '3':
                    $type = "IGD";
                    break;
                default:
                    $type = "-";
                    break;
            }
            $pasien['type']     = $type;
        } else {
            $pasien['no_encounter']     = "-";
            $pasien['tgl_encounter']    = "-";
            $pasien['type']             = "-";
        }
        return $pasien;
    }

    public function showDokter()
    {

        $spesialis = Spesialis::where('name', 'like', '%Dokter%')->get();
        $hasil = [];
        foreach ($spesialis as $key) {
            $hasil[] = $key->kode;
        }
        $profile = Profile::whereIn('spesialis', $hasil)->get();
        return $profile;
    }

    public function postRawatJalan($request, $id)
    {
        $pasien     = Pasien::findOrFail($id);
        $count      = Encounter::whereDate('created_at', date('Y-m-d'))->count();
        $encounter  = Encounter::create([
            'no_encounter'      => 'E-' . date('ymd') . ($count == 0 ? 0 : $count + 1),
            'rekam_medis'       => $pasien->rekam_medis,
            'name_pasien'       => $pasien->name,
            'pasien_satusehat_id' => $pasien->satusehat_id,
            'type'              => 1,
            'jenis_jaminan'     => $request->jenis_jaminan,
            'tujuan_kunjungan'  => $request->tujuan_kunjungan
        ]);

        $dokter = User::where('name', $request->dokter)->first();

        if ($dokter) {
            Practitioner::create([
                'encounter_id'  => $encounter->id,
                'name'          => $dokter->name,
                'satusehat_id'  => $dokter->satusehat_id
            ]);
        }

        $pasien->update(['status' => 1]);

        return $encounter;
    }
}
