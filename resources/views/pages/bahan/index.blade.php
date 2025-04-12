@extends('layouts.app')
@section('title')
    Data Bahan
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
                    <h5 class="card-title">Data Bahan</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">

                                <!-- Search Patient Starts -->
                                <div class="search-container d-xl-block d-none">
                                    <form method="GET" action="{{ route('bahans.index') }}">
                                        <input type="text" class="form-control" name="name" placeholder="Search">
                                        <i class="ri-search-line"></i>
                                    </form>
                                </div>
                                <!-- Search Patient Ends -->

                                <!-- Button Group Starts -->
                                <div class="ms-2">
                                    <a href="{{ route('bahans.create') }}" class="btn btn-outline-primary"
                                        id="createBahan">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textCreateBahan">Tambah Stok Perlengkapan</span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                            id="spinerCreateBahan"></span>
                                    </a>
                                </div>
                                <!-- Button Group Ends -->

                            </div>
                        </div>

                    </div>

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th class="text-center">Memiliki Expired</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bahans as $bahan)
                                        <tr>
                                            <td>{{ ucwords($bahan->name) }}</td>
                                            <td class="text-center">{{ $bahan->is_expired == 1 ? 'YA' : 'TIDAK' }}</td>
                                            <td>{{ $bahan->description }}</td>
                                            <td class="text-center">
                                                @if ($bahan->is_active == 1)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('bahans.edit', $bahan->id) }}"
                                                    class="btn btn-primary btn-sm" id="edit-{{ $bahan->id }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    <span class="btn-text" id="text-{{ $bahan->id }}">Edit </span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-{{ $bahan->id }}"></span>
                                                </a>
                                                <form action="{{ route('bahans.destroy', $bahan->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah anda yakin ingin menghapus Item {{ $bahan->name }} ?')">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                                <script src="{{ asset('js/jquery.min.js') }}"></script>

                                                <script>
                                                    $(document).ready(function() {
                                                        $("#edit-{{ $bahan->id }}").click(function() {
                                                            $("#spiner-{{ $bahan->id }}").removeClass("d-none");
                                                            $("#edit-{{ $bahan->id }}").addClass("disabled", true);
                                                            $("#text-{{ $bahan->id }}").text("Mohon Tunggu ...");
                                                        });
                                                    });
                                                </script>

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
            $("#createBahan").click(function() {
                $("#spinerCreateBahan").removeClass("d-none");
                $("#createBahan").addClass("disabled", true);
                $("#textCreateBahan").text("Mohon Tunggu ...");
            });

        });
    </script>
@endpush
