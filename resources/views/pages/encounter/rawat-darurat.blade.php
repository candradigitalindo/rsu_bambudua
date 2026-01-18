@extends('layouts.app')
@section('title')
    Data Rawat Darurat
@endsection
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Data Rawat Darurat</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">

                                <!-- Search Patient Starts -->
                                <div class="search-container d-xl-block d-none">
                                    <form method="GET" action="{{ route('kunjungan.rawatDarurat') }}">
                                        <input type="text" class="form-control" name="name" id="searchPatient"
                                            placeholder="Search">
                                        <i class="ri-search-line"></i>
                                    </form>
                                </div>
                                <!-- Search Patient Ends -->

                            </div>
                        </div>

                    </div>

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>No. Kunjungan</th>
                                        <th>Nama Pasien</th>
                                        <th>Status Kerabat</th>
                                        <th>Jenis Jaminan</th>
                                        <th>Tujuan Kunjungan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($encounters as $encounter)
                                        <tr>
                                            <td>{{ $encounter->no_encounter }}</td>
                                            <td>{{ $encounter->name_pasien }}</td>
                                            <td>
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
                                                    <span class="badge bg-secondary text-white">
                                                        <i class="ri-user-line"></i> Reguler
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $encounter->jenis_jaminan }}</td>
                                            <td>{{ $encounter->tujuan_kunjungan }}</td>
                                            <td class="text-center">
                                                @if ($encounter->status == 'Progress')
                                                    <span class="badge bg-warning">Progress</span>
                                                @else
                                                    <span class="badge bg-success">Finish</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($encounter->status == 'Progress')
                                                    <a href="{{ route('observasi.index', $encounter->id) }}"
                                                        class="btn btn-outline-primary btn-sm periksa-btn"
                                                        data-id="{{ $encounter->id }}">
                                                        <i class="ri-stethoscope-line"></i>
                                                        <span class="btn-text"
                                                            id="textPeriksa-{{ $encounter->id }}">Pemeriksaan</span>
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            id="spinerPeriksa-{{ $encounter->id }}"></span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('observasi.cetakEncounter', $encounter->id) }}"
                                                        target="_blank" class="btn btn-outline-info btn-sm cetak-btn"
                                                        data-id="{{ $encounter->id }}">
                                                        <i class="ri-profile-line"></i>
                                                        <span class="btn-text" id="textCetak-{{ $encounter->id }}"> Cetak
                                                            Hasil </span>
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            id="spinerCetak-{{ $encounter->id }}"></span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Data tidak ada</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Row ends -->
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#createTindakan").click(function() {
                $("#spinerCreateTindakan").removeClass("d-none");
                $("#createTindakan").addClass("disabled", true);
                $("#textCreateTindakan").text("Mohon Tunggu ...");
            });

            // Event delegation for periksa buttons
            $('.table-responsive').on('click', '.periksa-btn', function() {
                let encounterId = $(this).data('id');
                $("#spinerPeriksa-" + encounterId).removeClass("d-none");
                $(this).addClass("disabled");
                $("#textPeriksa-" + encounterId).text("Mohon Tunggu ...");
            });

        });
    </script>
@endpush
