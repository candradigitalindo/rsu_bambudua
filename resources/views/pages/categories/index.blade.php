@extends('layouts.app')
@section('title', 'Daftar Kategori')
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
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Kategori</h5>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm" id="btnTambahKategori">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerTambahKategori" role="status"
                            aria-hidden="true"></span>
                        <span id="textTambahKategori">Tambah Kategori</span>
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari kategori..."
                                value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Cari</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th>Nama Kategori</th>
                                    <th style="width: 20%;">Produk Apotek</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                                        </td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->products_count }}</td>
                                        <td>
                                            <a href="{{ route('categories.edit', $category->id) }}"
                                                class="btn btn-warning btn-sm btn-edit-kategori" id="btnEditKategori{{ $category->id }}">
                                                <span class="spinner-border spinner-border-sm d-none" id="spinnerEditKategori{{ $category->id }}" role="status" aria-hidden="true"></span>
                                                <span id="textEditKategori{{ $category->id }}">Edit</span>
                                            </a>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Yakin hapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data kategori</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $categories->links() }}
                    </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Tombol Tambah Kategori
            const btn = document.getElementById('btnTambahKategori');
            const spinner = document.getElementById('spinnerTambahKategori');
            const text = document.getElementById('textTambahKategori');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    spinner.classList.remove('d-none');
                    text.textContent = 'Loading...';
                });
            }

            // Tombol Edit Kategori
            document.querySelectorAll('.btn-edit-kategori').forEach(function(editBtn) {
                editBtn.addEventListener('click', function() {
                    const id = this.id.replace('btnEditKategori', '');
                    const spinnerEdit = document.getElementById('spinnerEditKategori' + id);
                    const textEdit = document.getElementById('textEditKategori' + id);
                    if (spinnerEdit && textEdit) {
                        spinnerEdit.classList.remove('d-none');
                        textEdit.textContent = 'Loading...';
                    }
                });
            });
        });
    </script>
@endpush
