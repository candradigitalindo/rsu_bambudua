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
        $total_row = $data->count();

        if ($total_row > 0) {
            $output = [];
            foreach ($data as $d) {
                $output[] = view('components.pendaftaran.rawat_jalan_row', compact('d'))->render();
            }
            $output = implode('', $output);
        } else {
            $output = '<tr>
                        <td colspan="6" class="text-center">Data tidak ada</td>
                    </tr>';
        }

        return response()->json($output);
    }
    // showRawatDarurat
    public function showRawatDarurat()
    {
        $data = $this->pendaftaranRepository->showRawatDarurat();
        $total_row = $data->count();
        if ($total_row > 0) {
            $output = [];
            foreach ($data as $d) {
                $output[] = view('components.pendaftaran.rawat_darurat_row', compact('d'))->render();
            }
            $output = implode('', $output);
        } else {
            $output = '<tr>
                        <td colspan="6" class="text-center">Data tidak ada</td>
                    </tr>';
        }
        return response()->json($output);
    }
    // showRawatDarurat
    public function showRawatInap()
    {
        $data = $this->pendaftaranRepository->showRawatInap();
        $total_row = $data->count();

        if ($total_row > 0) {
            $output = [];
            foreach ($data as $d) {
                $output[] = view('components.pendaftaran.rawat_inap_row', compact('d'))->render();
            }
            $output = implode('', $output);
        } else {
            $output = '<tr>
                        <td colspan="6" class="text-center">Data tidak ada</td>
                    </tr>';
        }

        return response()->json($output);
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
    public function postRawatDarurat(Request $request, $id)
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
            $encounter = $this->pendaftaranRepository->postRawatDarurat($request, $id);
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
            'dokter'            => 'required|string',
            'tujuan_kunjungan'  => 'required|string',

        ], [
            'jenis_jaminan.required'    => 'Kolom Jenis Jaminan masih kosong',
            'dokter.required'           => 'Kolom Dokter masih kosong',
            'tujuan_kunjungan.required' => 'Kolom Tujuan Kunjungan masih kosong',
        ]);

        if ($validator->passes()) {
            $encounter = $this->pendaftaranRepository->updateRawatDarurat($request, $id);
            return response()->json(['status' => true, 'text' => 'Pendaftaran Rawat Darurat Pasien ' . $encounter->name_pasien . ' berhasil diubah'], 200);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }
    public function destroyEncounterRdarurat($id)
    {
        $result = $this->pendaftaranRepository->destroyEncounterRdarurat($id);
        return response()->json(['status' => true, 'text' => 'Encounter berhasil dihapus', 'data' => $result]);
    }
}
