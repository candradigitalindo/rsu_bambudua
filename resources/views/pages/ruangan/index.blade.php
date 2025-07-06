@extends('layouts.app')
@section('title')
    Data Ruangan
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
                    <h5 class="card-title">Data Ruangan</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <a href="{{ route('category.index') }}" class="btn btn-outline-primary btn-sm" id="createCategory">
                            <i class="ri-calendar-todo-fill"></i>
                            <span class="btn-text" id="textCreateCategory">Kategori</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinerCreateCategory"></span>
                        </a>
                        <a href="{{ route('ruangan.create') }}" class="btn btn-outline-primary btn-sm" id="createRoom">
                            <i class="ri-hotel-bed-fill"></i>
                            <span class="btn-text" id="textCreateRoom">Tambah Ruangan</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinerCreateRoom"></span>
                        </a>
                    </div>
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>No Ruangan</th>
                                        <th>Kelas</th>
                                        <th>Jumlah Tempat Tidur</th>
                                        <th>Harga</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ruangans as $ruangan)
                                        <tr>
                                            <td>{{ $ruangan->category->name }}</td>
                                            <td>{{ $ruangan->no_kamar }}</td>
                                            <td>{{ $ruangan->class }}</td>
                                            <td>{{ $ruangan->capacity }}</td>
                                            <td>{{ $ruangan->getHargaFormattedAttribute() }}</td>
                                            <td>{{ $ruangan->description }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('ruangan.edit', $ruangan->id) }}"
                                                    class="btn btn-primary btn-sm" id="edit-{{ $ruangan->id }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    <span class="btn-text" id="text-{{ $ruangan->id }}">Edit </span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-{{ $ruangan->id }}"></span>
                                                </a>
                                                <script src="{{ asset('js/jquery.min.js') }}"></script>
                                                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                                <script>
                                                    $(document).ready(function() {
                                                        $("#edit-{{ $ruangan->id }}").click(function() {
                                                            $("#spiner-{{ $ruangan->id }}").removeClass("d-none");
                                                            $("#edit-{{ $ruangan->id }}").addClass("disabled", true);
                                                            $("#text-{{ $ruangan->id }}").text("Mohon Tunggu ...");
                                                        });
                                                    });
                                                </script>
                                                <form action="{{ route('ruangan.destroy', $ruangan->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah anda yakin ingin menghapus ruangan {{ $ruangan->no_kamar }} ?')">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                        Hapus
                                                    </button>
                                                </form>


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
                    <div class="text-xs-center mt-2">{{ $ruangans->links('pagination::bootstrap-4') }}</div>
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
            $("#createCategory").click(function() {
                $("#spinerCreateCategory").removeClass("d-none");
                $("#createCategory").addClass("disabled", true);
                $("#textCreateCategory").text("Mohon Tunggu ...");
            });
            $("#createRoom").click(function() {
                $("#spinerCreateRoom").removeClass("d-none");
                $("#createRoom").addClass("disabled", true);
                $("#textCreateRoom").text("Mohon Tunggu ...");
            });
        });
    </script>
@endpush
