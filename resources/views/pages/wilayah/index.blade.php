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
                <div class="col-sm-4 col-12">
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

                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
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
                <div class="col-sm-4 col-12">
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
            </div>
            <!-- Row ends -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Informasi Data Wilayah</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <a href="{{ route('wilayah.saveProvince') }}" class="btn btn-outline-primary btn-sm btn-sync"
                            data-type="provinsi">
                            <span class="btn-text">Tambah Data Provinsi</span>
                            <span class="spinner-border spinner-border-sm d-none ms-1"></span>
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
                                                    class="btn btn-outline-primary btn-sm btn-sync" data-type="kota">
                                                    <span class="btn-text">Update Data Kota</span>
                                                    <span class="spinner-border spinner-border-sm d-none ms-1"></span>
                                                    <i class="ri-download-cloud-line text-primary ms-1"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                {{ formatPrice($provinsi->kecamatan) }}
                                            </td>
                                            <td class="ms-2 text-center">
                                                <a href="{{ route('wilayah.saveDistrict', $provinsi->code) }}"
                                                    class="btn btn-outline-primary btn-sm btn-sync" data-type="kecamatan">
                                                    <span class="btn-text">Update Data Kecamatan</span>
                                                    <span class="spinner-border spinner-border-sm d-none ms-1"></span>
                                                    <i class="ri-download-cloud-line text-primary ms-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Data tidak ada</td>
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
            // Handle click pada semua button sync
            $('.btn-sync').click(function(e) {
                const $btn = $(this);
                const type = $btn.data('type');
                const $spinner = $btn.find('.spinner-border');
                const $text = $btn.find('.btn-text');
                const $icon = $btn.find('i');
                const originalText = $text.text();

                // Tampilkan loading
                $spinner.removeClass('d-none');
                $icon.addClass('d-none');
                $text.text('Mohon Tunggu...');

                // Disable semua button sync
                $('.btn-sync').each(function() {
                    $(this).addClass('disabled');
                    $(this).css('pointer-events', 'none');
                    $(this).css('opacity', '0.6');
                });

                // Tampilkan notifikasi
                let message = '';
                switch (type) {
                    case 'provinsi':
                        message = 'Sedang mengambil data provinsi dari Satu Sehat...';
                        break;
                    case 'kota':
                        message = 'Sedang mengambil data kota/kabupaten dari Satu Sehat...';
                        break;
                    case 'kecamatan':
                        message = 'Sedang mengambil data kecamatan dari Satu Sehat...';
                        break;
                }

                // Tampilkan toast notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Proses Sinkronisasi',
                        html: message +
                            '<br><small class="text-muted">Proses ini memerlukan waktu beberapa saat, mohon jangan tutup halaman ini.</small>',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            });
        });
    </script>
@endpush
