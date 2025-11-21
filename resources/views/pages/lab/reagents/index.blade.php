@extends('layouts.app')
@section('title', 'Reagensia Laboratorium')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Reagensia</h5>
                    <div>
                        <a href="{{ route('lab.reagents.history') }}" class="btn btn-outline-info btn-sm">
                            <i class="ri-history-line"></i> Histori Stok
                        </a>
                        <a href="{{ route('lab.reagents.create') }}" class="btn btn-primary btn-sm"><i
                                class="ri-add-line"></i> Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (request('filter') === 'habis')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri-alert-line"></i> <strong>Filter Aktif:</strong> Menampilkan reagensia yang stoknya
                            habis
                            <a href="{{ route('lab.reagents.index') }}" class="btn btn-sm btn-outline-danger ms-2">
                                <i class="ri-close-line"></i> Reset Filter
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (request('filter') === 'kadaluarsa')
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="ri-time-line"></i> <strong>Filter Aktif:</strong> Menampilkan reagensia yang memiliki
                            batch kadaluarsa
                            <a href="{{ route('lab.reagents.index') }}" class="btn btn-sm btn-outline-warning ms-2">
                                <i class="ri-close-line"></i> Reset Filter
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" name="q" class="form-control" placeholder="Cari reagensia..."
                                value="{{ $q ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <select name="filter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="habis" {{ request('filter') === 'habis' ? 'selected' : '' }}>
                                    🔴 Stok Habis
                                </option>
                                <option value="kadaluarsa" {{ request('filter') === 'kadaluarsa' ? 'selected' : '' }}>
                                    ⚠️ Kadaluarsa
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2"><button class="btn btn-primary">Terapkan</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Stok</th>
                                    <th>Warning</th>
                                    <th>Status Kedaluwarsa</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reagents as $r)
                                    <tr>
                                        <td>{{ $r->name }}</td>
                                        <td>{{ $r->unit ?? '-' }}</td>
                                        <td><span
                                                class="badge {{ $r->stock <= $r->warning_stock ? 'bg-danger' : 'bg-success' }}">{{ $r->stock }}</span>
                                        </td>
                                        <td>{{ $r->warning_stock }}</td>
                                        <td>
                                            @if ($r->expired_count > 0)
                                                <span class="badge bg-danger">
                                                    Expired: {{ $r->expired_count }} batch
                                                </span>
                                            @endif
                                            @if ($r->expiring_soon_count > 0)
                                                <span class="badge bg-warning text-dark">
                                                    Segera Expired: {{ $r->expiring_soon_count }}
                                                </span>
                                            @endif
                                            @if ($r->expired_count == 0 && $r->expiring_soon_count == 0)
                                                <span class="badge bg-success">Aman</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-warning"
                                                href="{{ route('lab.reagents.edit', $r->id) }}">Edit</a>
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('lab.reagents.stock.form', $r->id) }}">Manajemen
                                                Stok</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $reagents->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
