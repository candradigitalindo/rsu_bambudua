@extends('layouts.app')

@section('title', 'Histori Transaksi Stok')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header Card -->
        <div class="page-header-card mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold">
                        <i class="ri-history-line me-2"></i>
                        Histori Transaksi Stok
                    </h4>
                    <p class="text-muted mb-0">Riwayat penambahan dan pengurangan stok bahan radiologi</p>
                </div>
                <a href="{{ route('radiologi.supplies.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card mb-4">
            <form method="GET" action="{{ route('radiologi.supplies.history') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Bahan</label>
                    <select class="form-select" name="supply_id">
                        <option value="">Semua Bahan</option>
                        @foreach ($supplies as $supply)
                            <option value="{{ $supply->id }}" {{ request('supply_id') == $supply->id ? 'selected' : '' }}>
                                {{ $supply->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Tipe</label>
                    <select class="form-select" name="type">
                        <option value="">Semua</option>
                        <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
                        <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-header-primary w-100">
                        <i class="ri-filter-3-line me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 20%;">Bahan</th>
                            <th style="width: 12%;">Batch</th>
                            <th style="width: 8%;">Tipe</th>
                            <th style="width: 8%;">Jumlah</th>
                            <th style="width: 15%;">User</th>
                            <th style="width: 17%;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $transaction)
                            <tr>
                                <td>{{ $transactions->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $transaction->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $transaction->supply->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $transaction->batch->batch_number ?? '-' }}</span>
                                </td>
                                <td>
                                    @if ($transaction->type === 'in')
                                        <span class="badge bg-success">
                                            <i class="ri-arrow-down-line"></i> Masuk
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="ri-arrow-up-line"></i> Keluar
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="fw-bold {{ $transaction->type === 'in' ? 'text-success' : 'text-warning' }}">
                                        {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $transaction->user->name ?? '-' }}</div>
                                    <small class="text-muted">{{ $transaction->user->email ?? '' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $transaction->notes ?: '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="ri-history-line fs-1 text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada histori transaksi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="card-footer bg-white">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .page-header-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            border-left: 5px solid #10b981;
        }

        .filter-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .btn-header-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-header-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .table-modern thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table-modern thead th:first-child {
            border-top-left-radius: 12px;
        }

        .table-modern thead th:last-child {
            border-top-right-radius: 12px;
        }

        .table-modern tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background-color: #f0fdf4;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
        }
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
