@extends('layouts.app')
@section('title')
    Pemeriksaan Observasi
@endsection
@push('style')
    <!-- Scrollbar CSS -->
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{ asset('vendor/quill/quill.core.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }

        /* Modal without backdrop - ensure it's above everything */
        #modalLabResults,
        #modalRadioResults {
            z-index: 10600 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            pointer-events: none !important;
            /* Allow clicking through modal container */
        }

        #modalLabResults .modal-dialog,
        #modalRadioResults .modal-dialog {
            z-index: 10700 !important;
            position: relative !important;
            margin: 1.75rem auto !important;
            pointer-events: auto !important;
            /* Re-enable clicks on modal content */
        }

        #modalLabResults .modal-content,
        #modalRadioResults .modal-content {
            position: relative !important;
            z-index: 10800 !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.5) !important;
            /* Add shadow for depth */
        }

        /* Override any parent container z-index */
        .card,
        .tab-content,
        .tab-pane {
            z-index: auto !important;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        .select2-container {
            width: 100% !important;
        }

        .input-group .select2-container--default .select2-selection--single {
            height: 100%;
            line-height: 2.4rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
        }
    </style>
    <style>
        /* Membuat diagnosis_description wrap dan tidak overflow */
        .table td,
        .table th {
            white-space: normal !important;
            word-break: break-word;
            vertical-align: top;
        }
    </style>
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">

            {{-- Banner Paket Pemeriksaan Aktif --}}
            @if (isset($paketAktifs) && $paketAktifs->count() > 0)
                <div class="card mb-3 border-0" style="background: linear-gradient(135deg, #238781 0%, #1a6b66 100%); border-radius: 10px;">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2 text-white">
                                <i class="ri-gift-2-line fs-4"></i>
                                <div>
                                    <div class="fw-bold" style="font-size: 0.95rem;">Paket Pemeriksaan Aktif</div>
                                    <small style="opacity: 0.85;">Pasien memiliki {{ $paketAktifs->count() }} paket aktif yang bisa diterapkan</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($paketAktifs as $pp)
                                    <button type="button" class="btn btn-sm btn-light btn-apply-paket"
                                        data-paket-pasien-id="{{ $pp->id }}"
                                        data-paket-name="{{ $pp->paketPemeriksaan->name }}"
                                        data-sesi-terpakai="{{ $pp->sesi_terpakai }}"
                                        data-total-sesi="{{ $pp->total_sesi }}"
                                        data-tanggal-expired="{{ \Carbon\Carbon::parse($pp->tanggal_expired)->translatedFormat('d F Y') }}">
                                        <i class="ri-play-circle-line me-1"></i>
                                        {{ $pp->paketPemeriksaan->name }}
                                        <span class="badge bg-success ms-1">{{ $pp->sesi_terpakai }}/{{ $pp->total_sesi }} sesi</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Form Pemeriksaan Pasien : {{ $encounter->name_pasien }}</h5>
                        <div>
                            @if ($encounter->pasien)
                                @if ($encounter->pasien->is_kerabat_dokter)
                                    <span class="badge bg-primary text-white">
                                        <i class="ri-user-heart-line"></i> Kerabat Dokter
                                    </span>
                                @endif
                                @if ($encounter->pasien->is_kerabat_karyawan)
                                    <span class="badge bg-success text-white">
                                        <i class="ri-user-smile-line"></i> Kerabat Karyawan
                                    </span>
                                @endif
                                @if ($encounter->pasien->is_kerabat_owner)
                                    <span class="badge bg-warning text-dark">
                                        <i class="ri-vip-crown-line"></i> Kerabat Owner
                                    </span>
                                @endif
                                @if (
                                    !$encounter->pasien->is_kerabat_dokter &&
                                        !$encounter->pasien->is_kerabat_karyawan &&
                                        !$encounter->pasien->is_kerabat_owner)
                                    <span class="badge bg-primary text-white">
                                        <i class="ri-user-line"></i> Reguler
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="ri-user-line"></i> Reguler
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="custom-tabs-container">
                        <ul class="nav nav-tabs" id="customTab3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-anamnesis" data-bs-toggle="tab" href="#anamnesis"
                                    role="tab" aria-controls="anamnesis" aria-selected="true"><i
                                        class="ri-dossier-line"></i>Anamnesis / TTV</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-pemeriksaan-penunjang" data-bs-toggle="tab"
                                    href="#pemeriksaan-penunjang" role="tab" aria-controls="pemeriksaan-penunjang"
                                    aria-selected="false"><i class="ri-user-heart-line"></i>Pemeriksaan Penunjang</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-tindakan-medis" data-bs-toggle="tab" href="#tindakan-medis"
                                    role="tab" aria-controls="tindakan-medis" aria-selected="false"><i
                                        class="ri-stethoscope-line"></i>Tindakan/Prosedur</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if (auth()->user()->role == 3) disabled @endif" id="tab-diagnosis"
                                    data-bs-toggle="tab" href="#diagnosis" role="tab" aria-controls="diagnosis"
                                    aria-selected="false"><i class="ri-health-book-line"></i>Diagnosis</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if (auth()->user()->role == 3) disabled @endif" id="tab-tatalaksana"
                                    data-bs-toggle="tab" href="#tatalaksana" role="tab" aria-controls="tatalaksana"
                                    aria-selected="false"><i class="ri-capsule-fill"></i>Tatalaksana</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                @php
                                    $redirectRoute = match ($encounter->type) {
                                        1 => route('kunjungan.rawatJalan'),
                                        2 => route('kunjungan.rawatInap'),
                                        3 => route('kunjungan.rawatDarurat'),
                                        default => '#',
                                    };
                                @endphp
                                <a class="nav-link" id="tab-catatan" data-bs-toggle="tab" href="#catatan" role="tab"
                                    aria-controls="catatan" aria-selected="false"><i class="ri-draft-line"></i>Catatan</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="customTabContent3">
                            <div class="tab-pane fade show active" id="anamnesis" role="tabpanel">
                                @include('pages.observasi.partials._anamnesis_ttv', [
                                    'observasi' => $observasi,
                                    'dokters' => $dokters,
                                ])
                            </div>
                            <div class="tab-pane fade" id="pemeriksaan-penunjang" role="tabpanel">
                                @include('pages.observasi.partials._penunjang_request', [
                                    'observasi' => $observasi,
                                    'jenisPemeriksaan' => $jenisPemeriksaan,
                                    'labRequests' => $labRequests,
                                ])
                            </div>
                            <div class="tab-pane fade" id="tindakan-medis" role="tabpanel">
                                @include('pages.observasi.partials._tindakan', ['observasi' => $observasi])
                            </div>
                            <div class="tab-pane fade" id="diagnosis" role="tabpanel">
                                @include('pages.observasi.partials._diagnosis', [
                                    'observasi' => $observasi,
                                ])
                            </div>
                            <div class="tab-pane fade" id="tatalaksana" role="tabpanel">
                                @include('pages.observasi.partials._tatalaksana', [
                                    'observasi' => $observasi,
                                ])
                            </div>
                            <div class="tab-pane fade" id="catatan" role="tabpanel">
                                @include('pages.observasi.partials._catatan', [
                                    'observasi' => $observasi,
                                    'perawats' => $perawats,
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- [NEW] Modal untuk Cetak Hasil Pemeriksaan Penunjang -->
    <div class="modal fade" id="modalCetakPenunjang" tabindex="-1" aria-labelledby="modalCetakPenunjangLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCetakPenunjangLabel">Hasil Pemeriksaan Penunjang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="print-content-container">
                        <!-- Konten dari halaman cetak akan dimuat di sini -->
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" class="btn btn-success" id="btn-download-pdf-modal" download><i
                            class="ri-download-2-line"></i> Download PDF</a>
                    <button type="button" class="btn btn-primary" id="btn-print-modal"><i class="ri-printer-line"></i>
                        Cetak</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hasil Lab -->
    <div class="modal fade" id="modalLabResults" tabindex="-1" aria-labelledby="modalLabResultsLabel"
        aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title" id="modalLabResultsLabel">
                        <i class="ri-flask-line"></i> Hasil Pemeriksaan Laboratorium
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="labResultsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <p class="mt-2">Memuat hasil laboratorium...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="printLabResults()">
                        <i class="ri-printer-line"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hasil Radiologi -->
    <div class="modal fade" id="modalRadioResults" tabindex="-1" aria-labelledby="modalRadioResultsLabel"
        aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info-subtle">
                    <h5 class="modal-title" id="modalRadioResultsLabel">
                        <i class="ri-scan-line"></i> Hasil Pemeriksaan Radiologi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="radioResultsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <p class="mt-2">Memuat hasil radiologi...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="printRadioResults()">
                        <i class="ri-printer-line"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@prepend('scripts')
    <!-- Overlay Scroll JS -->
    <!-- Sweet Alert JS -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- Quill Editor JS -->
    <script src="{{ asset('vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('vendor/quill/custom.js') }}"></script>
    <!-- Custom JS files -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dokter_id').select2({
                placeholder: "Pilih satu atau lebih dokter",
                allowClear: true
            });

            // Ensure modal is visible when shown (no backdrop)
            $('#modalLabResults, #modalRadioResults').on('show.bs.modal', function(e) {
                const modal = $(this);
                modal.css('z-index', 10600);
                modal.find('.modal-dialog').css('z-index', 10700);
            });

            // Terapkan Paket Pemeriksaan
            $('.btn-apply-paket').on('click', function() {
                var btn = $(this);
                var paketPasienId = btn.data('paket-pasien-id');
                var paketName = btn.data('paket-name');
                var sesiTerpakai = btn.data('sesi-terpakai');
                var totalSesi = btn.data('total-sesi');
                var tanggalExpired = btn.data('tanggal-expired');
                var sesiKe = parseInt(sesiTerpakai) + 1;

                swal({
                    title: 'Terapkan Paket?',
                    text: 'Terapkan "' + paketName + '" sesi ke-' + sesiKe + ' dari ' + totalSesi + '?\nExp: ' + tanggalExpired + '\n\nItem tindakan, lab, radiologi, dan obat akan otomatis ditambahkan.',
                    icon: 'info',
                    buttons: {
                        cancel: { text: 'Batal', visible: true },
                        confirm: { text: 'Ya, Terapkan', className: 'btn-success' }
                    }
                }).then(function(confirmed) {
                    if (!confirmed) return;

                    btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i> Memproses...');

                    $.ajax({
                        url: '{{ route("observasi.applyPaket", $observasi) }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            paket_pasien_id: paketPasienId
                        },
                        dataType: 'json'
                    }).done(function(res) {
                        if (res.success) {
                            swal({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                timer: 2000,
                                buttons: false
                            });
                            // Reload halaman setelah 1.5 detik agar semua tab ter-update
                            setTimeout(function() { location.reload(); }, 1500);
                        } else {
                            swal({ title: 'Gagal', text: res.message, icon: 'error' });
                            btn.prop('disabled', false).html('<i class="ri-play-circle-line me-1"></i> ' + paketName + ' <span class="badge bg-success ms-1">' + sesiTerpakai + '/' + totalSesi + ' sesi</span>');
                        }
                    }).fail(function(xhr) {
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan pada server';
                        swal({ title: 'Error', text: msg, icon: 'error' });
                        btn.prop('disabled', false).html('<i class="ri-play-circle-line me-1"></i> ' + paketName + ' <span class="badge bg-success ms-1">' + sesiTerpakai + '/' + totalSesi + ' sesi</span>');
                    });
                });
            });
        });
    </script>
@endprepend
