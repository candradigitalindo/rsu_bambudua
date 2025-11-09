@extends('layouts.app')

@section('title', 'SIP Tenaga Kesehatan')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar SIP</h5>
                    <div>
                        <a href="{{ route('professional-licenses.create') }}" class="btn btn-primary">Tambah SIP</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="q" value="{{ $q }}"
                                placeholder="Cari nama/username/id petugas">
                        </div>
                        <div class="col-md-3">
                            <select name="profession" class="form-select">
                                <option value="">Semua Profesi</option>
                                @foreach ($professions as $p)
                                    <option value="{{ $p }}" @selected($selectedProfession === $p)>
                                        {{ ucfirst(str_replace('_', ' ', $p)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" type="submit">Filter</button>
                        </div>
                    </form>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tenaga Kesehatan</th>
                                    <th>Profesi</th>
                                    <th>No. SIP</th>
                                    <th>Expired</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($licenses as $lic)
                                    <tr>
                                        <td>{{ $lic->user->name ?? '-' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $lic->profession)) }}</td>
                                        <td>{{ $lic->sip_number ?? '-' }}</td>
                                        <td>{{ optional($lic->sip_expiry_date)->format('Y-m-d') }}</td>
                                        <td class="d-flex gap-2">
                                            <a href="{{ route('professional-licenses.edit', $lic->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                            <form method="POST"
                                                action="{{ route('professional-licenses.destroy', $lic->id) }}"
                                                onsubmit="return confirm('Hapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        {{ $licenses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
