@extends('layouts.app')
@section('title')
    Data Histori Bahan
@endsection
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <!-- Custom CSS -->
    <!-- Date Range CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">

    <!-- Date Range CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
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
                    <h5 class="card-title">Data Histori</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">
                                <div class="ms-2">
                                    <a href="{{ route('bahans.index') }}" class="btn btn-outline-primary" id="kembali">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textKembali">Kembali</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="spinerKembali"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th class="text-center">Nama Bahan</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Tanggal Expired</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bahan as $bahan)
                                        <tr>
                                            <td>{{ $bahan->created_at }}</td>
                                            <td class="text-center">{{ $bahan->bahan->name }}</td>
                                            <td class="text-center">{{ $bahan->quantity }}</td>
                                            <td class="text-center">
                                                @if ($bahan->status == 'masuk')
                                                    <span class="badge bg-success">Masuk</span>
                                                @else
                                                    <span class="badge bg-danger">Keluar</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $bahan->expired_at }}</td>
                                            <td>{{ $bahan->description }}</td>

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

    <!-- Date Range JS -->
    <script src="{{ asset('vendor/daterange/daterange.js') }}"></script>
    <script src="{{ asset('vendor/daterange/custom-daterange.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#kembali").click(function() {
                $("#spinerKembali").removeClass("d-none");
                $("#kembali").addClass("disabled", true);
                $("#textKembali").text("Mohon Tunggu ...");
            });

        });
    </script>
@endpush
