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
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">

                                <!-- Search Patient Starts -->
                                <div class="search-container d-xl-block d-none">
                                    <form method="GET" action="{{ route('bahans.index') }}">
                                        <input type="text" class="form-control" name="name" value="{{ request('name') }}" placeholder="Cari nama bahan...">
                                        <i class="ri-search-line"></i>
                                    </form>
                                </div>
                                <!-- Search Patient Ends -->
                                <div class="ms-2">
                                    <a href="{{ route('bahan.getRequestBahan') }}" class="btn btn-outline-primary" id="permintaanTindakan">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textPermintaanTindakan">Permintaan Tindakan</span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                            id="spinerPermintaanTindakan"></span>
                                    </a>
                                </div>
                                <div class="ms-2">
                                    <a href="{{ route('bahan.getAllHistori') }}" class="btn btn-outline-primary"
                                        id="histori">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textHistori">Histori</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="spinerHistori"></span>
                                    </a>
                                </div>
                                <!-- Button Group Starts -->
                                <div class="ms-2">
                                    <a href="{{ route('bahans.create') }}" class="btn btn-outline-primary" id="createBahan">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textCreateBahan">Tambah Data Baru</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="spinerCreateBahan"></span>
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
                                        <th class="text-center">Jumlah Stok</th>
                                        <th class="text-center">Warning Expired</th>
                                        <th class="text-center">Jumlah Expired</th>
                                        <th class="text-center">Dipakai Tindakan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bahans as $bahan)
                                        <tr>
                                            <td>{{ ucwords($bahan->name) }}</td>
                                            <td class="text-center">{{ $bahan->is_expired == 1 ? 'YA' : 'TIDAK' }}</td>
                                            <td class="text-center"><span class="text-primary fw-bold">{{ number_format($bahan->available_count ?? 0, 0, ',', '.') }}</span></td>
                                            <td class="text-center">

                                                @if ($bahan->is_expired == 1)
                                                    @if (($bahan->warning_count ?? 0) > 0)
                                                        <span class="text-info fw-bold">{{ number_format($bahan->warning_count ?? 0, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-success">Tidak ada</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($bahan->is_expired == 1)
                                                    @if (($bahan->expired_count ?? 0) > 0)
                                                        <span class="text-danger fw-bold">{{ number_format($bahan->expired_count ?? 0, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-success">Tidak ada</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                <button type="button" class="btn btn-outline-primary btn-sm btn-detail-tindakan"
                                                    id="histori-{{ $bahan->id }}"
                                                    data-url="{{ route('bahans.tindakan.json', $bahan->id) }}">
                                                    <i class="ri-archive-line"></i>
                                                    <span class="btn-text" id="textHistori-{{ $bahan->id }}">Detail Tindakan</span>
                                                    <span class="spinner-border spinner-border-sm d-none" id="spinerHistori-{{ $bahan->id }}"></span>
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                @if ($bahan->is_active == 1)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                <a href="{{ route('bahan.getBahan', $bahan->id) }}"
                                                    class="btn btn-outline-primary btn-sm"
                                                    id="stokMasuk-{{ $bahan->id }}">
                                                    <i class="ri-calendar-todo-fill"></i>
                                                    <span class="btn-text" id="textStokMasuk-{{ $bahan->id }}">Stok
                                                        Masuk</span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spinerStokMasuk-{{ $bahan->id }}"></span>
                                                </a>
                                                <a href="{{ route('bahan.getBahanKeluar', $bahan->id) }}"
                                                    class="btn btn-outline-danger btn-sm"
                                                    id="stokKeluar-{{ $bahan->id }}">
                                                    <i class="ri-calendar-todo-fill"></i>
                                                    <span class="btn-text" id="textStokKeluar-{{ $bahan->id }}">Stok
                                                        Keluar</span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spinerStokKeluar-{{ $bahan->id }}"></span>
                                                </a>
                                                <a href="{{ route('bahans.edit', $bahan->id) }}"
                                                    class="btn btn-primary btn-sm" id="edit-{{ $bahan->id }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    <span class="btn-text" id="text-{{ $bahan->id }}">Edit </span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-{{ $bahan->id }}"></span>
                                                </a>
                                                <form action="{{ route('bahans.destroy', $bahan->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah anda yakin ingin menghapus Item {{ $bahan->name }} ?')">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                        Hapus
                                                    </button>
                                                </form>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Data tidak ada</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{ $bahans->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Row ends -->

    <!-- Global Modal: Detail Tindakan -->
    <div class="modal fade" id="modalDetailTindakan" tabindex="-1" aria-labelledby="modalDetailTindakanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailTindakanLabel">Detail Tindakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead>
                                <tr>
                                    <th>Nama Tindakan</th>
                                    <th class="text-end">Harga</th>
                                </tr>
                            </thead>
                            <tbody id="modalTindakanBody">
                                <tr><td colspan="2" class="text-center text-muted">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Top-level buttons
            $(document).on('click', '#createBahan', function() {
                $('#spinerCreateBahan').removeClass('d-none');
                $('#createBahan').addClass('disabled');
                $('#textCreateBahan').text('Mohon Tunggu ...');
            });
            $(document).on('click', '#histori', function() {
                $('#spinerHistori').removeClass('d-none');
                $('#histori').addClass('disabled');
                $('#textHistori').text('Mohon Tunggu ...');
            });

            // Row-level buttons (delegated)
            function handleRowAction(prefix, spinnerPrefix, textPrefix, newText) {
                $(document).on('click', `a[id^="${prefix}-"], button[id^="${prefix}-"]`, function() {
                    const id = this.id.split('-').pop();
                    $(`#${spinnerPrefix}-${id}`).removeClass('d-none');
                    $(`#${prefix}-${id}`).addClass('disabled');
                    if (textPrefix) {
                        $(`#${textPrefix}-${id}`).text(newText);
                    }
                });
            }

            handleRowAction('edit', 'spiner', 'text', 'Mohon Tunggu ...');
            handleRowAction('histori', 'spinerHistori', 'textHistori', 'Mohon Tunggu ...');

            // Load tindakan via AJAX ke modal global
            $(document).on('click', '.btn-detail-tindakan', function(e) {
                const url = $(this).data('url');
                const $tbody = $('#modalTindakanBody');
                $tbody.html('<tr><td colspan="2" class="text-center text-muted">Memuat...</td></tr>');
                $.getJSON(url)
                    .done(function(resp) {
                        const rows = resp.tindakan || [];
                        if (!rows.length) {
                            $tbody.html('<tr><td colspan="2" class="text-center text-muted">Tidak ada tindakan terkait.</td></tr>');
                            $('#modalDetailTindakanLabel').text('Detail Tindakan');
                            return;
                        }
                        let html = '';
                        rows.forEach(function(r){
                            html += '<tr>' +
                                    '<td>' + $('<div>').text(r.name).html() + '</td>' +
                                    '<td class="text-end">' + $('<div>').text(r.harga_formatted).html() + '</td>' +
                                    '</tr>';
                        });
                        $tbody.html(html);
                        $('#modalDetailTindakanLabel').text('Tindakan yang menggunakan ' + resp.bahan.name);
                    })
                    .fail(function(){
                        $tbody.html('<tr><td colspan="2" class="text-center text-danger">Gagal memuat data.</td></tr>');
                    })
                    .always(function(){
                        const modalEl = document.getElementById('modalDetailTindakan');
                        if (modalEl) {
                            const modal = new bootstrap.Modal(modalEl);
                            modal.show();
                        }
                    });
            });
            handleRowAction('stokKeluar', 'spinerStokKeluar', 'textStokKeluar', 'Mohon Tunggu ...');
            handleRowAction('stokMasuk', 'spinerStokMasuk', 'textStokMasuk', 'Mohon Tunggu ...');
        });
    </script>
@endpush
