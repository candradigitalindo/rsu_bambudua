@extends('layouts.app')

@section('title', 'Histori Stok Reagensia')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row gx-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Histori Stok Reagensia</h5>
                    <a href="{{ route('lab.reagents.index') }}" class="btn btn-sm btn-secondary">
                        <i class="ri-arrow-left-line"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-3">
                            <input type="text" name="q" class="form-control form-control-sm"
                                placeholder="Cari nama reagen..." value="{{ request('q') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary w-100">Filter</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Reagen</th>
                                    <th>Tipe</th>
                                    <th>Jumlah</th>
                                    <th>Tgl Kedaluwarsa</th>
                                    <th>Keterangan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                    <tr>
                                        <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $tx->reagent->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($tx->type === 'in')
                                                <span class="badge bg-success">Masuk</span>
                                            @elseif($tx->type === 'out')
                                                <span class="badge bg-danger">Keluar</span>
                                            @else
                                                <span class="badge bg-warning">Penyesuaian</span>
                                            @endif
                                        </td>
                                        <td>{{ $tx->qty }}</td>
                                        <td>{{ $tx->expiry_date ? \Carbon\Carbon::parse($tx->expiry_date)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>{{ $tx->notes ?? '-' }}</td>
                                        <td>{{ $tx->user->name ?? 'Sistem' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Tidak ada data histori.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $transactions->links() }}
                        </div>
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
