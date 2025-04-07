<?php

namespace App\Http\Controllers;

use App\Repositories\PendaftaranRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PendaftaranController extends Controller
{
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
        $provinsi   = $this->pendaftaranRepository->provinsi();
        $dokter     = $this->pendaftaranRepository->showDokter();
        return view('pages.pendaftaran.index', compact('antrian', 'pekerjaan', 'agama', 'provinsi', 'dokter'));
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

    public function showRawatJalan()
    {
        $data = $this->pendaftaranRepository->showRawatJalan();
        $total_row  = $data->count();
        if ($total_row > 0) {
            foreach ($data as $d) {
                $output[] = '

                     <tr>
                        <td>

                            <table>
                                <tr>
                                    <td>No. Kunjungan</td>
                                    <td>:</td>
                                    <td>' . $d->no_encounter . '</td>
                                </tr>
                                <tr>
                                    <td>No. RM</td>
                                    <td>:</td>
                                    <td>' . $d->rekam_medis . '</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary-subtle rounded-pill text-primary">
                                        <i class="ri-circle-fill me-1"></i>Status : ' . $d->status . '</span>
                                    </td>

                                </tr>
                            </table>
                        </td>
                        <td>

                            <table>
                                <tr>
                                    <td>Pasien</td>
                                    <td>:</td>
                                    <td>' . $d->name_pasien . '</td>
                                </tr>
                                <tr>
                                    <td>Dokter</td>
                                    <td>:</td>
                                    <td>' . $d->dokter . '</td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>' . $d->jenis_jaminan . '</td>
                                </tr>
                                <tr>
                                    <td>' . $d->tujuan_kunjungan . '</td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <button type="button" class="btn editrawatJalan btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-rawatJalan" id=' . $d->id . '>
                                <i class="ri-edit-2-fill"></i>
                                Edit
                            </button>
                            <button type="button" class="btn destoryRawatJalan btn-outline-danger btn-sm" id=' . $d->id . '>
                                <i class="ri-delete-bin-5-fill"></i>
                                Hapus
                            </button>
                        </td>
                    </tr>
                ';
            }
        } else {
            $output = '<tr>
                        <td colspan="6" class="text-center">Data tidak ada</td>
                    </tr>';
        }

        return json_encode($output);
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

        if ($total_row > 0) {
            foreach ($data as $d) {
                $output[] = '
                    <div class="card border mt-3">
                        <div class="card-body">
                            <span class="badge bg-primary-subtle rounded-pill text-primary">
                                <i class="ri-circle-fill me-1"></i>Status : ' . $d->status . '</span>
                            <hr>

                                <div class="row justify-content-between">
                                    <div class="col-4">
                                        <div class="d-flex flex-column mw-100">
                                            <div class="text-primary fw-semibold">
                                                    Identitas Pasien
                                            </div>
                                            <div>
                                                <table>
                                                    <tr>
                                                        <td>No.RM</td>
                                                        <td>:</td>
                                                        <td class="fw-semibold">' . $d->rekam_medis . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nama</td>
                                                        <td>:</td>
                                                        <td class="fw-semibold">' . $d->name . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td>' . $d->jenis_identitas . '</td>
                                                        <td>:</td>
                                                        <td class="fw-semibold">' . $d->no_identitas . '</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column mw-100">
                                            <div class="text-primary fw-semibold">
                                                Kontak Pasien
                                            </div>
                                            <div>
                                                <table>
                                                    <tr>
                                                        <td>No Hp</td>
                                                        <td>:</td>
                                                        <td class="fw-semibold">' . $d->no_hp . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alamat</td>
                                                        <td>:</td>
                                                        <td class="fw-semibold">' . $d->alamat . '
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column mw-100">
                                            <div class="text-primary fw-semibold">
                                                Kunjungan Terakhir
                                            </div>
                                            <div>
                                                <table>
                                                    <tr>
                                                        <td>No. Kunjungan</td>
                                                        <td>:</td>
                                                        <td class="fw-semibold">' . $d->no_encounter . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal</td>
                                                        <td>:</td>
                                                        <td class="fw-semibold">' . $d->tgl_encounter . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jenis</td>
                                                        <td>:</td>
                                                        <td class="fw-semibold">' . $d->type . '</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn rawatJalan btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modal-rawatJalan" id=' . $d->id . '>
                                <i class="ri-stethoscope-line"></i>
                                Daftar Rawat Jalan
                            </button>
                            <button type="button" class="btn rawatInap btn-outline-primary btn-sm" id=' . $d->id . '>
                                <i class="ri-hotel-bed-fill"></i>
                                Daftar Rawat Inap
                            </button>
                            <button type="button" class="btn igd btn-outline-primary btn-sm" id=' . $d->id . '>
                                <i class="ri-dossier-fill"></i>
                                Daftar IGD
                            </button>
                            <button type="button" class="btn edit btn-outline-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#form-edit-pasien" id=' . $d->id . '>
                                <i class="ri-edit-2-fill"></i>
                                Edit Data Pasien
                            </button>
                        </div>
                    </div>
                ';
            }
        } else {
            $output = '
                <div class="card border mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center text-center">
                        <strong>Data Pasien tidak ditemukan...</strong>
                    </div>
                    </div>
                </div>
            ';
        }

        return json_encode($output);
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
            'dokter'            => 'required|string',
            'tujuan_kunjungan'  => 'required|string',

        ], [
            'jenis_jaminan.required'    => 'Kolom Jenis Jaminan masih kosong',
            'dokter.required'           => 'Kolom Dokter masih kosong',
            'tujuan_kunjungan.required' => 'Kolom Tujuan Kunjungan masih kosong',
        ]);

        if ($validator->passes()) {
            $encounter = $this->pendaftaranRepository->postRawatJalan($request, $id);
            return response()->json(['status' => true, 'text' => 'Pendaftaran Rawat Jalan Pasien ' . $encounter->name_pasien . ' berhasil'], 200);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }
    public function updateRawatJalan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jenis_jaminan'     => 'required|string',
            'dokter'            => 'required|string',
            'tujuan_kunjungan'  => 'required|string',

        ], [
            'jenis_jaminan.required'    => 'Kolom Jenis Jaminan masih kosong',
            'dokter.required'           => 'Kolom Dokter masih kosong',
            'tujuan_kunjungan.required' => 'Kolom Tujuan Kunjungan masih kosong',
        ]);

        if ($validator->passes()) {
            $encounter = $this->pendaftaranRepository->updateRawatJalan($request, $id);
            return response()->json(['status' => true, 'text' => 'Pendaftaran Rawat Jalan Pasien ' . $encounter->name_pasien . ' berhasil diubah'], 200);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }
    public function destroyEncounterRajal($id)
    {
        $result = $this->pendaftaranRepository->destroyEncounterRajal($id);
        return response()->json(['status' => true, 'text' => 'Encounter berhasil dihapus', 'data' => $result]);
    }
}
