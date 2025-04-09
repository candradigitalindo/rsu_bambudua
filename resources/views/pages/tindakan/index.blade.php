@extends('layouts.app')
@section('title')
    Data Tindakan
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
                    <h5 class="card-title">Data Tindakan</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">

                                <!-- Search Patient Starts -->
                                <div class="search-container d-xl-block d-none">
                                    <form method="GET" action="{{ route('tindakan.index') }}">
                                        <input type="text" class="form-control" name="name" id="searchPatient" placeholder="Search">
                                        <i class="ri-search-line"></i>
                                    </form>
                                </div>
                                <!-- Search Patient Ends -->

                                <!-- Button Group Starts -->
                                <div class="ms-2">
                                    <a href="{{ route('tindakan.create') }}" class="btn btn-outline-primary"
                                        id="createTindakan">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textCreateTindakan">Tambah Tindakan</span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                            id="spinerCreateTindakan"></span>
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
                                        <th>Harga</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tindakans as $tindakan)
                                        <tr>
                                            <td>{{ $tindakan->name }}</td>
                                            <td>{{ $tindakan->getHargaFormattedAttribute() }}</td>
                                            <td>{{ $tindakan->description }}</td>
                                            <td class="text-center">
                                                @if ($tindakan->status == 1)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('tindakan.edit', $tindakan->id) }}"
                                                    class="btn btn-primary btn-sm" id="edit-{{ $tindakan->id }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    <span class="btn-text" id="text-{{ $tindakan->id }}">Edit </span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-{{ $tindakan->id }}"></span>
                                                </a>
                                                <form action="{{ route('tindakan.destroy', $tindakan->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah anda yakin ingin menghapus tindakan {{ $tindakan->name }} ?')">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                                <script src="{{ asset('js/jquery.min.js') }}"></script>

                                                <script>
                                                    $(document).ready(function() {
                                                        $("#edit-{{ $tindakan->id }}").click(function() {
                                                            $("#spiner-{{ $tindakan->id }}").removeClass("d-none");
                                                            $("#edit-{{ $tindakan->id }}").addClass("disabled", true);
                                                            $("#text-{{ $tindakan->id }}").text("Mohon Tunggu ...");
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
            $("#createTindakan").click(function() {
                $("#spinerCreateTindakan").removeClass("d-none");
                $("#createTindakan").addClass("disabled", true);
                $("#textCreateTindakan").text("Mohon Tunggu ...");
            });

        });
    </script>
@endpush
