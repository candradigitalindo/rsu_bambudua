@extends('layouts.app')
@section('title', 'Master Metode Pembayaran')
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
                    <h5 class="mb-0">Metode Pembayaran</h5>
                    <div>
                        <a class="btn btn-sm btn-primary" href="{{ route('payment-methods.create') }}">
                            <i class="ri-add-line"></i> Tambah
                            <a href="{{ route('payment-methods.index') }}" class="btn btn-sm btn-secondary">Refresh</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" name="q" class="form-control" placeholder="Cari nama/kode..."
                                value="{{ $q ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary" type="submit">Cari</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kode</th>
                                    <th>Tipe Fee</th>
                                    <th class="text-end">Fee %</th>
                                    <th class="text-end">Fee Tetap</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($methods as $m)
                                    <tr>
                                        <td>
                                            <strong>{{ $m->name }}</strong>
                                            @if ($m->description)
                                                <br><small class="text-muted">{{ $m->description }}</small>
                                            @endif
                                        </td>
                                        <td><code>{{ $m->code }}</code></td>
                                        <td>
                                            @if ($m->fee_type == 'percentage')
                                                <span class="badge bg-info">Persentase</span>
                                            @elseif($m->fee_type == 'fixed')
                                                <span class="badge bg-warning">Tetap</span>
                                            @else
                                                <span class="badge bg-primary">Kombinasi</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if (in_array($m->fee_type, ['percentage', 'both']))
                                                {{ number_format($m->fee_percentage, 2) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if (in_array($m->fee_type, ['fixed', 'both']))
                                                Rp {{ number_format($m->fee_fixed, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($m->active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('payment-methods.edit', $m->id) }}">
                                                <i class="ri-edit-line"></i> Edit
                                            </a>
                                            <form action="{{ route('payment-methods.destroy', $m->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Hapus metode ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="ri-delete-bin-line"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $methods->onEachSide(1)->links() }}
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
