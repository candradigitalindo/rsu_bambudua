@extends('layouts.app')
@section('title', 'Master Satuan')
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
                    <h5 class="mb-0">Satuan</h5>
                    <div>
                        <a class="btn btn-sm btn-primary" href="{{ route('units.create') }}">
                            <i class="ri-add-line"></i> Tambah
                        </a>
                        <a href="{{ route('units.index') }}" class="btn btn-sm btn-secondary">Refresh</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" name="q" class="form-control" placeholder="Cari nama/singkatan..."
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
                                    <th>Singkatan</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($units as $u)
                                    <tr>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->abbrev }}</td>
                                        <td>
                                            @if ($u->active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('units.edit', $u->id) }}">Edit</a>
                                            <form action="{{ route('units.destroy', $u->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Hapus satuan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $units->onEachSide(1)->links() }}
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
