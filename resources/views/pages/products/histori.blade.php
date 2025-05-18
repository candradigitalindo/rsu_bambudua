@extends('layouts.app')
@section('title', 'Histori Produk Apotek')
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
                    <h5 class="mb-0">Histori Produk Apotek</h5>
                    <div>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary" id="btnKembaliProduk">
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerKembaliProduk" role="status"
                                aria-hidden="true"></span>
                            <span id="textKembaliProduk">Kembali</span>
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
                    <form method="GET" action="{{ route('product.getAllHistori') }}" class="mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama/kode produk..." value="{{ request('search') }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Expired</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historis as $history)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $history->productApotek->code ?? '-' }}</td>
                                        <td>{{ $history->productApotek->name ?? '-' }}</td>
                                        <td>
                                            @if ($history->type == 0)
                                                <span class="badge bg-success">Masuk</span>
                                            @else
                                                <span class="badge bg-danger">Keluar</span>
                                            @endif
                                        </td>
                                        <td>{{ $history->jumlah }}</td>
                                        <td>
                                            {{ $history->expired_at ? \Carbon\Carbon::parse($history->expired_at)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>{{ $history->keterangan ?? '-' }}</td>
                                        <td>{{ $history->created_at ? $history->created_at->format('d-m-Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada histori produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <!-- Overlay Scroll JS -->
        <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btnKembali = document.getElementById('btnKembaliProduk');
                const spinnerKembali = document.getElementById('spinnerKembaliProduk');
                const textKembali = document.getElementById('textKembaliProduk');
                if (btnKembali) {
                    btnKembali.addEventListener('click', function() {
                        spinnerKembali.classList.remove('d-none');
                        textKembali.textContent = 'Loading...';
                    });
                }
            });
        </script>
    @endpush
@endsection
