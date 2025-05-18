@extends('layouts.app')
@section('title', 'Data Produk Apotek')
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
                    <h5 class="mb-0">Data Produk Apotek</h5>
                    <div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary" id="btnTambahProduk">
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerTambahProduk" role="status"
                                aria-hidden="true"></span>
                            <span id="textTambahProduk">Tambah Produk</span>
                        </a>
                        <a href="{{ route('product.getAllHistori') }}" class="btn btn-outline-primary ms-2" id="btnHistoriProduk">
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerHistoriProduk" role="status"
                                aria-hidden="true"></span>
                            <span id="textHistoriProduk">Histori Produk</span>
                        </a>

                    </div>
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
                            <input type="text" name="search" class="form-control" placeholder="Cari produk..."
                                value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Cari</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Produk</th>
                                    <th>Kategori</th>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Warning Stok</th>
                                    <th>Warning Expired</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>

                                        <td>{{ $product->code }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ number_format($product->price, 0, ',', '.') }}</td>
                                        {{-- Format harga dengan titik sebagai pemisah ribuan --}}
                                        <td>{{ $product->stok }}</td>
                                        <td>
                                            @if ($product->stok == 0)
                                                <span class="badge bg-danger">Stok Habis</span>
                                            @elseif ($product->stok < $product->warning_stok)
                                                <span class="badge bg-warning text-dark">Stok Sedikit</span>
                                            @else
                                                <span class="badge bg-success">Stok Tersedia</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product->expired_count > 0)
                                                <span class="badge bg-warning text-dark">
                                                    Akan Expired: {{ $product->expired_count }}
                                                </span>
                                            @endif
                                            @if ($product->expired_past_count > 0)
                                                <span class="badge bg-danger ms-1">
                                                    Expired: {{ $product->expired_past_count }}
                                                </span>
                                            @endif
                                            @if ($product->expired_count == 0 && $product->expired_past_count == 0)
                                                <span class="badge bg-success">Aman</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Edit --}}
                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="btn btn-warning btn-sm" id="btnEditProduk{{ $product->id }}">
                                                <span class="spinner-border spinner-border-sm d-none"
                                                    id="spinnerEditProduk{{ $product->id }}" role="status"
                                                    aria-hidden="true"></span>
                                                Edit
                                            </a>

                                            {{-- Tambah Stok --}}
                                            <a href="{{ route('product.addStock', $product->id) }}"
                                                class="btn btn-success btn-sm" id="btnTambahStok{{ $product->id }}">
                                                <span class="spinner-border spinner-border-sm d-none"
                                                    id="spinnerTambahStok{{ $product->id }}" role="status"
                                                    aria-hidden="true"></span>
                                                <span id="textTambahStok{{ $product->id }}">Tambah & Kurang Stok</span>
                                            </a>

                                            {{-- Hapus --}}
                                            @if ($product->stock > 0)
                                                <a href="#" class="btn btn-danger btn-sm disabled"
                                                    id="btnHapusProduk{{ $product->id }}">
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spinnerHapusProduk{{ $product->id }}" role="status"
                                                        aria-hidden="true"></span>
                                                    Hapus
                                                </a>
                                            @else
                                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data produk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $products->links() }}
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
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loading untuk tombol Tambah
            const btnTambah = document.getElementById('btnTambahProduk');
            const spinnerTambah = document.getElementById('spinnerTambahProduk');
            const textTambah = document.getElementById('textTambahProduk');
            if (btnTambah) {
                btnTambah.addEventListener('click', function() {
                    spinnerTambah.classList.remove('d-none');
                    textTambah.textContent = 'Loading...';
                });
            }
            // Loading untuk tombol Edit
            document.querySelectorAll('[id^="btnEditProduk"]').forEach(function(editBtn) {
                editBtn.addEventListener('click', function() {
                    const id = this.id.replace('btnEditProduk', '');
                    const spinnerEdit = document.getElementById('spinnerEditProduk' + id);
                    if (spinnerEdit) spinnerEdit.classList.remove('d-none');
                    this.querySelector('span:not(.spinner-border)').textContent = 'Loading...';
                });
            });
            // Loading untuk tombol Tambah Stok
            document.querySelectorAll('[id^="btnTambahStok"]').forEach(function(tambahStokBtn) {
                tambahStokBtn.addEventListener('click', function() {
                    const id = this.id.replace('btnTambahStok', '');
                    const spinnerTambahStok = document.getElementById('spinnerTambahStok' + id);
                    const textTambahStok = document.getElementById('textTambahStok' + id);
                    if (spinnerTambahStok && textTambahStok) {
                        spinnerTambahStok.classList.remove('d-none');
                        textTambahStok.textContent = 'Loading...';
                    }
                });
            });
            // Loading untuk tombol Hapus
            document.querySelectorAll('[id^="btnHapusProduk"]').forEach(function(hapusBtn) {
                hapusBtn.addEventListener('click', function() {
                    const id = this.id.replace('btnHapusProduk', '');
                    const spinnerHapus = document.getElementById('spinnerHapusProduk' + id);
                    const textHapus = document.getElementById('textHapusProduk' + id);
                    if (spinnerHapus && textHapus) {
                        spinnerHapus.classList.remove('d-none');
                        textHapus.textContent = 'Loading...';
                    }
                });
            });
            // Spinner untuk tombol Histori Produk
            const btnHistori = document.getElementById('btnHistoriProduk');
            const spinnerHistori = document.getElementById('spinnerHistoriProduk');
            const textHistori = document.getElementById('textHistoriProduk');
            if (btnHistori) {
                btnHistori.addEventListener('click', function() {
                    spinnerHistori.classList.remove('d-none');
                    textHistori.textContent = 'Loading...';
                });
            }
        });
    </script>
@endpush
