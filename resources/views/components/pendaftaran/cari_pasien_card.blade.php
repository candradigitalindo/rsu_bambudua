<div class="card border-0 shadow-sm mt-3" style="border-left: 4px solid #17a2b8 !important;">
    <div class="card-body py-3">
        @if (!empty($d->status) && $d->status !== '-')
            <span class="badge bg-primary-subtle rounded-pill text-primary">
                <i class="ri-circle-fill me-1"></i>Status : 
                @php
                    $statusText = match(intval($d->status)) {
                        1 => 'Rawat Jalan',
                        2 => 'Rawat Inap', 
                        3 => 'IGD',
                        default => is_string($d->status) ? $d->status : 'Belum terdaftar'
                    };
                @endphp
                {{ $statusText }}
            </span>
            <hr>
        @endif
        <div class="row justify-content-between">
            <div class="col-4">
                <div class="d-flex flex-column mw-100">
                    <div class="text-primary fw-semibold mb-2">Identitas Pasien</div>
                    <div>
                        <table class="table-sm">
                            <tr>
                                <td class="pe-2" style="min-width: 60px;">No.RM</td>
                                <td class="pe-2">:</td>
                                <td class="fw-semibold text-dark">{{ $d->rekam_medis }}</td>
                            </tr>
                            <tr>
                                <td class="pe-2">Nama</td>
                                <td class="pe-2">:</td>
                                <td class="fw-semibold text-dark">{{ $d->name }}</td>
                            </tr>
                            <tr>
                                <td class="pe-2">{{ $d->jenis_identitas }}</td>
                                <td class="pe-2">:</td>
                                <td class="fw-semibold text-dark">{{ $d->no_identitas ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="d-flex flex-column mw-100">
                    <div class="text-primary fw-semibold mb-2">Kontak Pasien</div>
                    <div>
                        <table class="table-sm">
                            <tr>
                                <td class="pe-2" style="min-width: 60px;">No Hp</td>
                                <td class="pe-2">:</td>
                                <td class="fw-semibold text-dark">{{ $d->no_hp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="pe-2">Alamat</td>
                                <td class="pe-2">:</td>
                                <td class="fw-semibold text-dark" style="max-width: 150px; word-wrap: break-word;">{{ $d->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="d-flex flex-column mw-100">
                    <div class="text-primary fw-semibold mb-2">Kunjungan Terakhir</div>
                    <div>
                        <table class="table-sm">
                            <tr>
                                <td class="pe-2" style="min-width: 80px;">No. Kunjungan</td>
                                <td class="pe-2">:</td>
                                <td class="fw-semibold text-dark">{{ $d->no_encounter ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="pe-2">Tanggal</td>
                                <td class="pe-2">:</td>
                                <td class="fw-semibold text-dark">{{ $d->tgl_encounter ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="pe-2">Jenis</td>
                                <td class="pe-2">:</td>
                                <td class="fw-semibold text-dark">{{ $d->type ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer bg-light border-0 py-2">
        <div class="d-flex gap-2 flex-wrap justify-content-end">
            <button type="button" class="btn rawatJalan btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#modal-rawatJalan" id="{{ $d->id }}">
                <i class="ri-stethoscope-line me-1"></i>
                Rawat Jalan
            </button>
            {{-- <button type="button" class="btn rawatInap btn-info btn-sm" data-bs-toggle="modal"
                data-bs-target="#modal-rawatInap" id="{{ $d->id }}">
                <i class="ri-hotel-bed-fill me-1"></i>
                Rawat Inap
            </button> --}}
            <button type="button" class="btn igd btn-danger btn-sm" data-bs-toggle="modal"
                data-bs-target="#modal-rawatDarurat" id="{{ $d->id }}">
                <i class="ri-dossier-fill me-1"></i>
                IGD
            </button>
            <button type="button" class="btn edit btn-outline-secondary btn-sm" data-bs-toggle="modal"
                data-bs-target="#form-edit-pasien" id="{{ $d->id }}">
                <i class="ri-edit-2-fill me-1"></i>
                Edit
            </button>
        </div>
    </div>
</div>
