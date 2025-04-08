@extends('layouts.app')
@section('title')
    Data Kategori
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
                    <h5 class="card-title">Data Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <a href="{{ route('ruangan.index') }}" class="btn btn-outline-primary btn-sm" id="createRoom">
                            <i class="ri-hotel-bed-fill"></i>
                            <span class="btn-text" id="textCreateRoom">Data Ruangan</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinerCreateRoom"></span>
                        </a>
                        <a href="{{ route('category.create') }}" class="btn btn-outline-primary btn-sm" id="createCategory">
                            <i class="ri-calendar-todo-fill"></i>
                            <span class="btn-text" id="textCreateCategory">Tambah Kategori</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinerCreateCategory"></span>
                        </a>

                    </div>

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->description }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('category.edit', $category->id) }}"
                                                    class="btn btn-primary btn-sm" id="edit-{{ $category->id }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    <span class="btn-text" id="text-{{ $category->id }}">Edit </span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-{{ $category->id }}"></span>
                                                </a>
                                                <form action="{{ route('category.destroy', $category->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah anda yakin ingin menghapus kategori {{ $category->name }} ?')">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                                <script src="{{ asset('js/jquery.min.js') }}"></script>

                                                <script>
                                                    $(document).ready(function() {
                                                        $("#edit-{{ $category->id }}").click(function() {
                                                            $("#spiner-{{ $category->id }}").removeClass("d-none");
                                                            $("#edit-{{ $category->id }}").addClass("disabled", true);
                                                            $("#text-{{ $category->id }}").text("Mohon Tunggu ...");
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
