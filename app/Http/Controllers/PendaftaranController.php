<?php

namespace App\Http\Controllers;

use App\Repositories\PendaftaranRepository;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public $pendaftaranRepository;
    public function __construct(PendaftaranRepository $pendaftaranRepository)
    {
        $this->pendaftaranRepository = $pendaftaranRepository;
    }
    public function index()
    {
        return view('pages.pendaftaran.rawatJalan');
    }

    public function cariPasien(Request $request)
    {
        $data       = $this->pendaftaranRepository->cariPasien($request);
        $total_row  = $data->count();
        if ($total_row > 0) {
            foreach ($data as $d) {
                $output = '
                    <a href="">
                        <div class="d-flex flex-wrap gap-3 border-bottom mb-2">
                            <div class="d-flex flex-column">
                                <div class="text-primary mb-1">
                                    ' . $d->rekam_medis . '
                                </div>
                                <div class="fw-semibold mb-1">' . $d->name . '</div>
                                <ul class="list-unstyled d-flex">
                                    <li class="pe-2 border-end">KTP: ' . $d->no_identitas . '</li>
                                    <li class="px-2 border-end">Tanggal Lahir : ' . $d->tgl_lahir . '</li>
                                    <li class="px-2 border-end">' . $d->jenis_kelamin == 1 ? "Pria" : "Wanita" . '</li>
                                    <li class="px-2">' . $d->golongan_darah . '</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <h6 class="text-primary">Alamat</h6>
                            <p class="text-truncate mb-2">
                            </p>
                            <p class="text-truncate m-0"><span class="text-primary">No HP :</span> ' . $d->no_hp . '</p>
                            <p class="text-truncate m-0"><span class="text-primary">Kunjungan Terakhir :</span> 18/02/2024 at
                                6:30PM</p>
                        </div>
                    </a>
                ';
            }
        } else {
            $output = '
                <div class="card mb-3">
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
}
