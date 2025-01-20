@extends('layouts.app')
@section('title')
    Master Data Wilayah
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

            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-map-pin-2-fill fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">{{ formatPrice($wilayah['provinsi']) }}</h1>
                                    <p class="m-0">Provinsi</p>
                                </div>
                            </div>
                            {{-- <div class="d-flex align-items-end justify-content-between mt-4">
                                <a href="{{ route('wilayah.saveProvince') }}" class="btn btn-outline-primary btn-sm"
                                    id="provinsi">
                                    <span class="btn-text" id="text-provinsi">Tambah Data Provinsi</span>
                                    <span class="spinner-border spinner-border-sm d-none" id="spiner-provinsi"></span>
                                    <i class="ri-download-cloud-line text-primary ms-1"></i>
                                </a>

                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-map-pin-2-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">{{ formatPrice($wilayah['kota']) }}</h1>
                                    <p class="m-0">Kota / Kabupaten</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-map-pin-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">{{ formatPrice($wilayah['kecamatan']) }}</h1>
                                    <p class="m-0">Kecamatan</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-map-pin-range-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">{{ formatPrice($wilayah['desa']) }}</h1>
                                    <p class="m-0">Desa / Kelurahan</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- Row ends -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Informasi Data Wilayah</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <a href="{{ route('wilayah.saveProvince') }}" class="btn btn-outline-primary btn-sm" id="provinsi">
                            <span class="btn-text" id="text-provinsi">Tambah Data Provinsi</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spiner-provinsi"></span>
                            <i class="ri-download-cloud-line text-primary ms-1"></i>
                        </a>
                    </div>
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Nama Provinsi</th>
                                        <th colspan="2">Kabupaten / Kota</th>
                                        <th colspan="2">Kecamatan</th>
                                        <th colspan="2">Desa / Kelurahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($wilayah['dataProvinces'] as $provinsi)
                                        <tr>
                                            <td>
                                                {{ $provinsi->name }}
                                            </td>
                                            <td class="text-center">
                                                {{ formatPrice($provinsi->kota) }}
                                            </td>
                                            <td class="ms-2 text-center">
                                                <a href="{{ route('wilayah.saveCity', $provinsi->code) }}"
                                                    class="btn btn-outline-primary btn-sm" id="kota">
                                                    <span class="btn-text" id="text-kota">Update Data Kota</span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-kota"></span>
                                                    <i class="ri-download-cloud-line text-primary ms-1"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                {{ formatPrice($provinsi->kecamatan) }}
                                            </td>
                                            <td class="ms-2 text-center">
                                                <a href="{{ route('wilayah.saveDistrict', $provinsi->code) }}"
                                                    class="btn btn-outline-primary btn-sm" id="kota">
                                                    <span class="btn-text" id="text-kota">Update Data Kecamatan</span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-kota"></span>
                                                    <i class="ri-download-cloud-line text-primary ms-1"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                {{ formatPrice($provinsi->desa) }}
                                            </td>
                                            <td class="ms-2 text-center">
                                                <a href="{{ route('wilayah.saveDesa', $provinsi->code) }}"
                                                    class="btn btn-outline-primary btn-sm" id="kota">
                                                    <span class="btn-text" id="text-kota">Update Data Desa</span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-kota"></span>
                                                    <i class="ri-download-cloud-line text-primary ms-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Data tidak ada</td>
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
            $("#provinsi").click(function() {
                $("#spiner-provinsi").removeClass("d-none");
                $("#provinsi").addClass("disabled", true);
                $("#kota").addClass("disabled", true);
                $("#kecamatan").addClass("disabled", true);
                $("#desa").addClass("disabled", true);
                $("#text-provinsi").text("Mohon Tunggu ...");
            });

            $("#kota").click(function() {
                $("#spiner-kota").removeClass("d-none");
                $("#provinsi").addClass("disabled", true);
                $("#kota").addClass("disabled", true);
                $("#kecamatan").addClass("disabled", true);
                $("#desa").addClass("disabled", true);
                $("#text-kota").text("Mohon Tunggu ...");
            });

            $("#kecamatan").click(function() {
                $("#spiner-kecamatan").removeClass("d-none");
                $("#provinsi").addClass("disabled", true);
                $("#kota").addClass("disabled", true);
                $("#kecamatan").addClass("disabled", true);
                $("#desa").addClass("disabled", true);
                $("#text-kecamatan").text("Mohon Tunggu ...");
            });

            $("#desa").click(function() {
                $("#spiner-desa").removeClass("d-none");
                $("#provinsi").addClass("disabled", true);
                $("#kota").addClass("disabled", true);
                $("#kecamatan").addClass("disabled", true);
                $("#desa").addClass("disabled", true);
                $("#text-desa").text("Mohon Tunggu ...");
            });
        });
    </script>
@endpush
