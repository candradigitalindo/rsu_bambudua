@extends('layouts.app')
@section('title', 'Riwayat Medical Records')

@push('style')
    <style>
        .patient-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .patient-row:hover {
            background-color: #f8f9fa;
        }

        .patient-detail {
            background-color: #f8f9fa;
        }

        .loading-detail {
            text-align: center;
            padding: 20px;
        }

        .encounter-card {
            border-left: 4px solid #0d6efd;
        }

        /* Button extra small */
        .btn-xs {
            padding: 0.15rem 0.4rem;
            font-size: 0.75rem;
            line-height: 1.2;
        }

    </style>
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush

@section('content')
    <div class="row gx-3">
        <div class="col-12">
            <!-- Search Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Riwayat Medical Records</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('medical-records.riwayat') }}" method="GET" class="row g-2">
                        <div class="col-md-8">
                            <input type="text" name="q" class="form-control"
                                placeholder="Cari Nama, No. RM, RM Lama, atau No. HP..." value="{{ request('q') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-search-line"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Patient List -->
            <div class="card">
                <div class="card-body">
                    @if ($patients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>JK</th>
                                        <th>Tgl Lahir</th>
                                        <th>No. HP</th>
                                        <th class="text-center">Jumlah Kunjungan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($patients as $patient)
                                        <tr class="patient-row" data-bs-toggle="collapse"
                                            data-bs-target="#patient-{{ $patient->rekam_medis }}"
                                            data-rekam-medis="{{ $patient->rekam_medis }}">
                                            <td>
                                                <i class="ri-arrow-right-s-line toggle-icon"></i>
                                            </td>
                                            <td><strong>{{ $patient->rekam_medis }}</strong></td>
                                            <td>{{ $patient->name }}</td>
                                            <td>{{ $patient->jenis_kelamin == 1 ? 'L' : 'P' }}</td>
                                            <td>{{ $patient->tgl_lahir ? \Carbon\Carbon::parse($patient->tgl_lahir)->format('d M Y') : '-' }}
                                            </td>
                                            <td>{{ $patient->no_hp ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $patient->encounters_count }}</span>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="patient-{{ $patient->rekam_medis }}">
                                            <td colspan="7" class="patient-detail p-0">
                                                <div class="detail-container"
                                                    data-rekam-medis="{{ $patient->rekam_medis }}">
                                                    <div class="loading-detail">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        <p class="text-muted mt-2">Memuat riwayat kunjungan...</p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $patients->links() }}
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="ri-file-search-line" style="font-size: 48px;"></i>
                            <p class="mt-2">
                                @if (request('q'))
                                    Tidak ada data yang sesuai dengan pencarian "{{ request('q') }}"
                                @else
                                    Gunakan form pencarian untuk mencari pasien
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        let loadedPatients = {};
        let currentPages = {};

        // Toggle arrow icon
        $(document).on('click', '.patient-row', function() {
            const icon = $(this).find('.toggle-icon');
            icon.toggleClass('ri-arrow-right-s-line ri-arrow-down-s-line');
        });

        // Load patient detail when collapse is shown
        $(document).on('shown.bs.collapse', function(e) {
            const collapseId = e.target.id;
            const rekamMedis = collapseId.replace('patient-', '');

            // Only load if not already loaded
            if (!loadedPatients[rekamMedis]) {
                loadPatientDetail(rekamMedis, 1);
                loadedPatients[rekamMedis] = true;
                currentPages[rekamMedis] = 1;
            }
        });

        function loadPatientDetail(rekamMedis, page = 1) {
            const container = $(`.detail-container[data-rekam-medis="${rekamMedis}"]`);

            container.html(`
                <div class="loading-detail">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Memuat riwayat kunjungan...</p>
                </div>
            `);

            $.ajax({
                url: `/medical-records/riwayat/pasien/${rekamMedis}`,
                data: {
                    page: page
                },
                method: 'GET',
                success: function(response) {
                    if (response.status) {
                        displayPatientDetail(container, response);
                        currentPages[rekamMedis] = page;
                    }
                },
                error: function(xhr) {
                    container.html(`
                        <div class="p-4 text-center text-danger">
                            <i class="ri-error-warning-line" style="font-size: 48px;"></i>
                            <p class="mt-2">Gagal memuat data</p>
                        </div>
                    `);
                }
            });
        }

        function displayPatientDetail(container, response) {
            const {
                pasien,
                encounters,
                pagination
            } = response;

            let html = `
                <div class="p-4">
                    <!-- Patient Info -->
                    <div class="card mb-3 bg-white">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <small class="text-muted">No. RM</small>
                                    <div><strong>${pasien.rekam_medis}</strong></div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Nama</small>
                                    <div>${pasien.name}</div>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">JK / Umur</small>
                                    <div>${pasien.jenis_kelamin} ${pasien.age ? `(${pasien.age} thn)` : ''}</div>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Tgl Lahir</small>
                                    <div>${pasien.tgl_lahir || '-'}</div>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">No. HP</small>
                                    <div>${pasien.no_hp || '-'}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Encounters -->
            `;

            if (encounters.length === 0) {
                html += '<div class="alert alert-info">Belum ada riwayat kunjungan</div>';
            } else {
                encounters.forEach(function(enc) {
                    html += `
                        <div class="card mb-3 encounter-card">
                            <div class="card-header bg-white">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong>${enc.no_encounter}</strong>
                                        <span class="badge bg-primary ms-2">${enc.type_label}</span>
                                        <small class="text-muted ms-2">${enc.date}</small>
                                    </div>
                                    <div class="col-auto">
                                        <a href="${enc.cetak_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ri-printer-line"></i> Cetak
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Info -->
                                    <div class="col-md-12">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-light text-dark border">Poliklinik: ${enc.clinic || '-'}</span>
                                            <span class="badge bg-light text-dark border">Tujuan: ${enc.purpose}</span>
                                            <span class="badge bg-light text-dark border">Jaminan: ${enc.insurance}</span>
                                        </div>
                                    </div>

                                    <!-- Tim Medis -->
                                    <div class="col-md-12">
                                        <strong>Tim Medis:</strong>
                                        ${enc.doctors.map(d => `<span class="badge bg-info ms-1">${d}</span>`).join('')}
                                        ${enc.nurses.length > 0 ? enc.nurses.map(n => `<span class="badge bg-secondary ms-1">${n}</span>`).join('') : ''}
                                    </div>

                                    <!-- Diagnosis -->
                                    ${enc.diagnoses.length > 0 ? `
                                                            <div class="col-md-12">
                                                                <strong>Diagnosis:</strong>
                                                                ${enc.diagnoses.map(d => `<span class="badge bg-danger-subtle text-danger border ms-1">${d}</span>`).join('')}
                                                            </div>
                                                            ` : ''}

                                    <!-- TTV -->
                                    ${enc.ttv ? `
                                                            <div class="col-md-12">
                                                                <strong>Tanda Vital:</strong>
                                                                <div class="d-flex flex-wrap gap-2 mt-1">
                                                                    <span class="badge bg-light text-dark border">TD: ${enc.ttv.sistolik || '-'}/${enc.ttv.diastolik || '-'}</span>
                                                                    <span class="badge bg-light text-dark border">Nadi: ${enc.ttv.nadi || '-'}</span>
                                                                    <span class="badge bg-light text-dark border">Napas: ${enc.ttv.pernapasan || '-'}</span>
                                                                    <span class="badge bg-light text-dark border">Suhu: ${enc.ttv.suhu || '-'}°C</span>
                                                                    <span class="badge bg-light text-dark border">Kesadaran: ${enc.ttv.kesadaran || '-'}</span>
                                                                    ${enc.ttv.berat_badan ? `<span class="badge bg-light text-dark border">BB: ${enc.ttv.berat_badan} kg</span>` : ''}
                                                                    ${enc.ttv.tinggi_badan ? `<span class="badge bg-light text-dark border">TB: ${enc.ttv.tinggi_badan} cm</span>` : ''}
                                                                </div>
                                                            </div>
                                                            ` : ''}

                                    <!-- Tindakan -->
                                    ${enc.tindakan.length > 0 ? `
                                                            <div class="col-md-6">
                                                                <strong>Tindakan:</strong>
                                                                <table class="table table-sm table-bordered mt-2">
                                                                    <thead><tr><th>Nama</th><th width="80">Qty</th></tr></thead>
                                                                    <tbody>
                                                                        ${enc.tindakan.map(t => `
                                                    <tr>
                                                        <td>${t.tindakan_name}</td>
                                                        <td class="text-center">${t.qty}</td>
                                                    </tr>
                                                `).join('')}
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            ` : ''}

                                    <!-- Lab -->
                                    ${enc.lab.length > 0 ? `
                                                <div class="col-md-6">
                                                    <strong>Laboratorium:</strong>
                                                    <table class="table table-sm table-bordered mt-2">
                                                        <thead>
                                                            <tr>
                                                                <th>Pemeriksaan</th>
                                                                <th width="100">Status</th>
                                                                <th width="80" class="text-center">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            ${enc.lab.map((l, idx) => `
                                                    <tr>
                                                        <td>
                                                            <strong>${l.test_name}</strong>
                                                            ${l.result_value ? `
                                                                            <br><small class="text-muted">Hasil: ${l.result_value} ${l.result_unit || ''}</small>
                                                                        ` : ''}
                                                        </td>
                                                        <td>
                                                            <span class="badge ${l.status === 'completed' ? 'bg-success' : 'bg-warning'}">${l.status}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            ${l.status === 'completed' ? `
                                                                    <a href="/laboratorium/requests/${l.lab_request_id}/print" target="_blank" class="btn btn-xs btn-outline-primary">
                                                                        <i class="ri-printer-line"></i> Cetak
                                                                    </a>
                                                                ` : '<span class="text-muted">-</span>'}
                                                        </td>
                                                    </tr>
                                                `).join('')}
                                                        </tbody>
                                                    </table>
                                                </div>
                                                ` : ''}                                    <!-- Radiologi -->
                                    ${enc.radiologi.length > 0 ? `
                                            <div class="col-md-6">
                                                <strong>Radiologi:</strong>
                                                <table class="table table-sm table-bordered mt-2">
                                                    <thead>
                                                        <tr>
                                                            <th>Pemeriksaan</th>
                                                            <th width="100">Status</th>
                                                            <th width="80" class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${enc.radiologi.map((r, idx) => `
                                                    <tr>
                                                        <td>
                                                            <strong>${r.name}</strong>
                                                            ${r.results && r.results.length > 0 && r.results[0].conclusion ? `
                                                                        <br><small class="text-muted">Kesimpulan: ${r.results[0].conclusion}</small>
                                                                    ` : ''}
                                                        </td>
                                                        <td>
                                                            <span class="badge ${r.status === 'completed' ? 'bg-success' : 'bg-warning'}">${r.status}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            ${r.status === 'completed' ? `
                                                                    <a href="/radiologi/permintaan/${r.radiology_request_id}/print" target="_blank" class="btn btn-xs btn-outline-primary">
                                                                        <i class="ri-printer-line"></i> Cetak
                                                                    </a>
                                                                ` : '<span class="text-muted">-</span>'}
                                                        </td>
                                                    </tr>
                                                `).join('')}
                                                    </tbody>
                                                </table>
                                            </div>
                                            ` : ''}                                    <!-- Resep -->
                                    ${enc.resep.length > 0 ? `
                                                            <div class="col-md-12">
                                                                <strong>Resep Obat:</strong>
                                                                <table class="table table-sm table-bordered mt-2">
                                                                    <thead><tr><th>Obat</th><th width="80">Qty</th><th width="250">Aturan Pakai</th></tr></thead>
                                                                    <tbody>
                                                                        ${enc.resep.map(r => `
                                                    <tr>
                                                        <td>${r.nama_obat}</td>
                                                        <td class="text-center">${r.qty}</td>
                                                        <td>${r.aturan_pakai}</td>
                                                    </tr>
                                                `).join('')}
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Pagination
                if (pagination.last_page > 1) {
                    html += `
                        <nav class="mt-3">
                            <ul class="pagination pagination-sm justify-content-center">
                                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadPatientDetail('${pasien.rekam_medis}', ${pagination.current_page - 1}); return false;">Previous</a>
                                </li>
                    `;

                    for (let i = 1; i <= pagination.last_page; i++) {
                        html += `
                            <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="loadPatientDetail('${pasien.rekam_medis}', ${i}); return false;">${i}</a>
                            </li>
                        `;
                    }

                    html += `
                                <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                                    <a class="page-link" href="#" onclick="loadPatientDetail('${pasien.rekam_medis}', ${pagination.current_page + 1}); return false;">Next</a>
                                </li>
                            </ul>
                        </nav>
                    `;
                }
            }

            html += '</div>';
            container.html(html);
        }
    </script>
@endpush
