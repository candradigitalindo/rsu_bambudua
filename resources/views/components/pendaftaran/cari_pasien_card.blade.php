<div class="card border mt-3">
    <div class="card-body">
        @if (!empty($d->status) && $d->status !== '-')
            <span class="badge bg-primary-subtle rounded-pill text-primary">
                <i class="ri-circle-fill me-1"></i>Status : {{ $d->status }}
            </span>
            <hr>
        @endif
        <div class="row justify-content-between">
            <div class="col-4">
                <div class="d-flex flex-column mw-100">
                    <div class="text-primary fw-semibold">Identitas Pasien</div>
                    <div>
                        <table>
                            <tr>
                                <td>No.RM</td>
                                <td>:</td>
                                <td class="fw-semibold">{{ $d->rekam_medis }}</td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td class="fw-semibold">{{ $d->name }}</td>
                            </tr>
                            <tr>
                                <td>{{ $d->jenis_identitas }}</td>
                                <td>:</td>
                                <td class="fw-semibold">{{ $d->no_identitas }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="d-flex flex-column mw-100">
                    <div class="text-primary fw-semibold">Kontak Pasien</div>
                    <div>
                        <table>
                            <tr>
                                <td>No Hp</td>
                                <td>:</td>
                                <td class="fw-semibold">{{ $d->no_hp }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>:</td>
                                <td class="fw-semibold">{{ $d->alamat }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="d-flex flex-column mw-100">
                    <div class="text-primary fw-semibold">Kunjungan Terakhir</div>
                    <div>
                        <table>
                            <tr>
                                <td>No. Kunjungan</td>
                                <td>:</td>
                                <td class="fw-semibold">{{ $d->no_encounter }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td>:</td>
                                <td class="fw-semibold">{{ $d->tgl_encounter }}</td>
                            </tr>
                            <tr>
                                <td>Jenis</td>
                                <td>:</td>
                                <td class="fw-semibold">{{ $d->type }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn rawatJalan btn-outline-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#modal-rawatJalan" id="{{ $d->id }}">
            <i class="ri-stethoscope-line"></i>
            Daftar Rawat Jalan
        </button>
        <button type="button" class="btn rawatInap btn-outline-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#modal-rawatInap" id="{{ $d->id }}">
            <i class="ri-hotel-bed-fill"></i>
            Daftar Rawat Inap
        </button>
        <button type="button" class="btn igd btn-outline-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#modal-rawatDarurat" id="{{ $d->id }}">
            <i class="ri-dossier-fill"></i>
            Daftar IGD
        </button>
        <button type="button" class="btn edit btn-outline-warning btn-sm" data-bs-toggle="modal"
            data-bs-target="#form-edit-pasien" id="{{ $d->id }}">
            <i class="ri-edit-2-fill"></i>
            Edit Data Pasien
        </button>
    </div>
</div>
