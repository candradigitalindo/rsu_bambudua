@extends('layouts.app')
@section('title', 'Data Produk Apotek')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            color: gray;
            pointer-events: none;
        }

        .page-header-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            color: #111827;
            margin-bottom: 1.5rem;
            border-left: 5px solid #10b981;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .btn-header-primary {
            background: #10b981;
            color: white;
            border: 2px solid #10b981;
            font-weight: 600;
        }

        .btn-header-primary:hover {
            background: #059669;
            color: white;
            border-color: #059669;
        }

        .btn-header-warning {
            background: #d97706;
            color: white;
            border: 2px solid #d97706;
            font-weight: 600;
        }

        .btn-header-warning:hover {
            background: #b45309;
            color: white;
            border-color: #b45309;
        }

        .page-header-card .card-body {
            padding: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .page-title i {
            color: #10b981;
        }

        .filter-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .modern-card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (min-width: 1200px) {
            .table-responsive {
                overflow-x: visible;
            }
        }

        .modern-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .table-modern {
            margin-bottom: 0;
        }

        .table-modern thead {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .table-modern thead th {
            border: none;
            font-weight: 600;
            padding: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        .table-modern tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f0f0f0;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: #f0fdf4;
        }

        .badge-modern {
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        .product-code {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #10b981;
            background: #d1fae5;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            display: inline-block;
        }

        .product-name {
            font-weight: 600;
            color: #1f2937;
        }

        .price-tag {
            font-weight: 700;
            color: #059669;
            font-size: 0.95rem;
        }

        .stock-display {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
        }

        .btn-action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .btn-action {
            font-size: 0.8rem;
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .alert-modern {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .alert-modern i {
            font-size: 1.5rem;
        }

        .stats-summary {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .stats-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
        }

        .stats-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stats-text {
            flex: 1;
        }

        .stats-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        .stats-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }
    </style>
@endpush
@section('content')
    <!-- Header Card -->
    <div class="card page-header-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <div class="page-title">
                        <i class="ri-medicine-bottle-line"></i>
                        Data Produk Apotek
                    </div>
                    <p class="page-subtitle mb-0">
                        Kelola dan monitor stok obat, kategori produk, dan informasi expired
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.create') }}" class="btn btn-header-primary" id="btnTambahProduk">
                        <i class="ri-add-line"></i>
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerTambahProduk" role="status"
                            aria-hidden="true"></span>
                        <span id="textTambahProduk">Tambah Produk</span>
                    </a>
                    <a href="{{ route('product.getAllHistori') }}" class="btn btn-header-warning" id="btnHistoriProduk">
                        <i class="ri-history-line"></i>
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerHistoriProduk" role="status"
                            aria-hidden="true"></span>
                        <span id="textHistoriProduk">Histori</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card modern-card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                            <i class="ri-checkbox-circle-line"></i>
                            <div>
                                <strong>Berhasil!</strong> {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (request('filter') === 'habis')
                        <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
                            <i class="ri-alert-line"></i>
                            <div class="flex-grow-1">
                                <strong>Filter Aktif:</strong> Menampilkan produk yang stoknya habis (stok ≤ 0)
                            </div>
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-danger">
                                <i class="ri-close-line"></i> Reset
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (request('filter') === 'kadaluarsa')
                        <div class="alert alert-warning alert-modern alert-dismissible fade show" role="alert">
                            <i class="ri-time-line"></i>
                            <div class="flex-grow-1">
                                <strong>Filter Aktif:</strong> Menampilkan produk yang memiliki stok kadaluarsa
                            </div>
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-warning">
                                <i class="ri-close-line"></i> Reset
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filter Card -->
                    <div class="filter-card">
                        <form method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="filter-label">
                                        <i class="ri-search-line"></i> Pencarian
                                    </label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari nama produk..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="filter-label">
                                        <i class="ri-price-tag-3-line"></i> Kategori
                                    </label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="filter-label">
                                        <i class="ri-filter-3-line"></i> Status Stok
                                    </label>
                                    <select name="filter" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="habis" {{ request('filter') === 'habis' ? 'selected' : '' }}>
                                            🔴 Obat Habis
                                        </option>
                                        <option value="kadaluarsa"
                                            {{ request('filter') === 'kadaluarsa' ? 'selected' : '' }}>
                                            ⚠️ Obat Expired
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="filter-label">&nbsp;</label>
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i class="ri-search-line"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-modern table-hover align-middle">
                            <thead>
                                <tr>
                                    <th><i class="ri-barcode-line"></i> Kode</th>
                                    <th><i class="ri-price-tag-3-line"></i> Kategori</th>
                                    <th><i class="ri-medicine-bottle-line"></i> Nama Produk</th>
                                    <th><i class="ri-scales-3-line"></i> Satuan</th>
                                    <th><i class="ri-money-dollar-circle-line"></i> Harga</th>
                                    <th><i class="ri-stack-line"></i> Stok</th>
                                    <th><i class="ri-information-line"></i> Status</th>
                                    <th><i class="ri-time-line"></i> Expired</th>
                                    <th style="width: 15%;"><i class="ri-settings-3-line"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <span class="product-code">{{ $product->code }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-modern bg-info">{{ $product->category->name }}</span>
                                        </td>
                                        <td>
                                            <div class="product-name">{{ $product->name }}</div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-modern bg-success">{{ $product->satuan ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <div class="price-tag">Rp {{ number_format($product->harga, 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="stock-display">{{ $product->stok }}</span>
                                            <small class="text-muted d-block">{{ $product->satuan ?? '' }}</small>
                                        </td>
                                        <td>
                                            @if ($product->stok == 0)
                                                <span class="badge badge-modern bg-danger">
                                                    <i class="ri-close-circle-line"></i> Habis
                                                </span>
                                            @elseif ($product->stok < $product->warning_stok)
                                                <span class="badge badge-modern bg-warning text-dark">
                                                    <i class="ri-error-warning-line"></i> Sedikit
                                                </span>
                                            @else
                                                <span class="badge badge-modern bg-success">
                                                    <i class="ri-check-line"></i> Tersedia
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product->expired == 0)
                                                @if ($product->expired_count > 0)
                                                    <span class="badge badge-modern bg-warning text-dark d-block mb-1">
                                                        <i class="ri-alarm-warning-line"></i>
                                                        {{ $product->expired_count }} akan exp
                                                    </span>
                                                @endif
                                                @if ($product->expired_past_count > 0)
                                                    <span class="badge badge-modern bg-danger d-block mb-1">
                                                        <i class="ri-alert-line"></i>
                                                        {{ $product->expired_past_count }} expired
                                                    </span>
                                                @endif
                                                @if ($product->expired_count == 0 && $product->expired_past_count == 0)
                                                    <span class="badge badge-modern bg-success">
                                                        <i class="ri-shield-check-line"></i> Aman
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-action-group">
                                                <a href="{{ route('products.edit', $product->id) }}"
                                                    class="btn btn-warning btn-action btn-sm"
                                                    id="btnEditProduk{{ $product->id }}" title="Edit Produk">
                                                    <i class="ri-edit-line"></i>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spinnerEditProduk{{ $product->id }}"></span>
                                                </a>

                                                <a href="{{ route('product.addStock', $product->id) }}"
                                                    class="btn btn-success btn-action btn-sm"
                                                    id="btnTambahStok{{ $product->id }}" title="Kelola Stok">
                                                    <i class="ri-add-box-line"></i>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spinnerTambahStok{{ $product->id }}"></span>
                                                </a>

                                                @if ($product->stock > 0)
                                                    <button class="btn btn-danger btn-action btn-sm disabled" disabled
                                                        title="Tidak dapat dihapus (masih ada stok)">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                @else
                                                    <form action="{{ route('products.destroy', $product->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Yakin hapus produk ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-action btn-sm" type="submit"
                                                            title="Hapus Produk">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="ri-inbox-line" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="text-muted mt-2">Tidak ada data produk</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $products->appends(request()->query())->links() }}
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
