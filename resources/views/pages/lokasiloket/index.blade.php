@extends('layouts.app')
@section('title')
    Lokasi Loket Antrian
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
                    <h5 class="card-title">Informasi Lokasi Loket</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <a href="{{ route('lokasiloket.create') }}" class="btn btn-outline-primary btn-sm" id="create">
                            <span class="btn-text" id="text-create">Tambah Lokasi Loket</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spiner-create"></span>
                            <i class="ri-download-cloud-line text-primary ms-1"></i>
                        </a>
                    </div>
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Nama Lokasi</th>
                                        <th class="text-center">Prefix Antrian</th>
                                        <th class="text-center">Jumlah Loket</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lokasis as $l)
                                        <tr>
                                            <td>
                                                {{ $l->lokasi_loket }}
                                            </td>
                                            <td class="text-center">
                                                {{ $l->prefix_antrian }}
                                            </td>
                                            <td class="text-center">
                                                {{ formatPrice($l->loket->count()) }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('lokasiloket.edit', $l->id) }}"
                                                    class="btn btn-primary btn-sm" id="edit-{{ $l->id }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    <span class="btn-text" id="text-{{ $l->id }}">Edit </span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-{{ $l->id }}"></span>
                                                </a>
                                                <script src="{{ asset('js/jquery.min.js') }}"></script>
                                                <script>
                                                    $(document).ready(function() {
                                                        $("#edit-{{ $l->id }}").click(function() {
                                                            $("#spiner-{{ $l->id }}").removeClass("d-none");
                                                            $("#edit-{{ $l->id }}").addClass("disabled", true);
                                                            $("#text-{{ $l->id }}").text("Mohon Tunggu ...");
                                                        });
                                                    });
                                                </script>
                                                <a href="{{ route('lokasiloket.destroy', $l->id) }}"
                                                    class="btn btn-danger btn-sm" data-confirm-delete="true">Hapus</a>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Data tidak ada</td>
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

            $("#create").click(function() {
                $("#spiner-create").removeClass("d-none");
                $("#create").addClass("disabled", true);
                $("#text-create").text("Mohon Tunggu ...");
            });

            // $("#kecamatan").click(function() {
            //     $("#spiner-kecamatan").removeClass("d-none");
            //     $("#provinsi").addClass("disabled", true);
            //     $("#kota").addClass("disabled", true);
            //     $("#kecamatan").addClass("disabled", true);
            //     $("#desa").addClass("disabled", true);
            //     $("#text-kecamatan").text("Mohon Tunggu ...");
            // });

            // $("#desa").click(function() {
            //     $("#spiner-desa").removeClass("d-none");
            //     $("#provinsi").addClass("disabled", true);
            //     $("#kota").addClass("disabled", true);
            //     $("#kecamatan").addClass("disabled", true);
            //     $("#desa").addClass("disabled", true);
            //     $("#text-desa").text("Mohon Tunggu ...");
            // });
        });
    </script>
@endpush
