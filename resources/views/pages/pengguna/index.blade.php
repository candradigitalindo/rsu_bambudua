@extends('layouts.app')
@section('title')
    Data Pengguna
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
                    <h5 class="card-title">Data Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <a href="{{ route('pengguna.create') }}" class="btn btn-outline-primary btn-sm" id="create">
                            <i class="ri-user-add-line text-primary "></i>
                            <span class="btn-text" id="text-create">Tambah Pengguna</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spiner-create"></span>
                        </a>
                    </div>
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>ID Pengenal</th>
                                        <th>Hak Akses</th>
                                        <th>Spesialis</th>
                                        <th>Poliklinik</th>
                                        <th>SIP</th>
                                        <th>STR</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $u)
                                        <tr>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->username }}</td>
                                            <td>{{ $u->id_petugas }}</td>

                                            <td>
                                                <span
                                                    class="badge border border-primary text-primary">{{ \App\Enums\UserRole::fromValue($u->role)?->label() ?? 'Tidak Diketahui' }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge border border-primary text-primary">{{ $u->spesialis }}</span>
                                            </td>
                                            <td>
                                                @foreach ($u->clinics as $clinic)
                                                    <span
                                                        class="badge border border-primary text-primary">{{ $clinic->nama }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if (!empty($u->sip_number))
                                                    <div><strong>{{ $u->sip_number }}</strong></div>
                                                    <div class="text-muted small">Exp: {{ $u->sip_expiry }}</div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($u->str_number))
                                                    <div><strong>{{ $u->str_number }}</strong></div>
                                                    <div class="text-muted small">Exp: {{ $u->str_expiry }}</div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('pengguna.edit', $u->id) }}"
                                                    class="btn btn-primary btn-sm" id="edit-{{ $u->id }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    <span class="btn-text" id="text-{{ $u->id }}">Edit </span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-{{ $u->id }}"></span>
                                                </a>
                                                <a href="{{ route('pengguna.gaji.atur', $u->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="ri-money-dollar-circle-line"></i> Atur Gaji
                                                </a>
                                                <script src="{{ asset('js/jquery.min.js') }}"></script>
                                                <script>
                                                    $(document).ready(function() {
                                                        $("#edit-{{ $u->id }}").click(function() {
                                                            $("#spiner-{{ $u->id }}").removeClass("d-none");
                                                            $("#edit-{{ $u->id }}").addClass("disabled", true);
                                                            $("#text-{{ $u->id }}").text("Mohon Tunggu ...");
                                                        });
                                                    });
                                                </script>
                                                <form action="{{ route('pengguna.destroy', $u->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        data-confirm-delete="true"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?')">Hapus</button>
                                                </form>

                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-xs-center mt-2">{{ $users->links('pagination::bootstrap-4') }}</div>
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
        });
    </script>
@endpush
