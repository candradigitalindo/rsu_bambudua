@extends('layouts.app')
@section('title')
    Aktifitas Pengguna
@endsection

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        .search-form .form-control { min-width: 180px; }
        code.small { font-size: .8rem; }
        .table td { vertical-align: middle; }
        .payload { max-width: 360px; }
        .truncate-1 { display: inline-block; max-width: 420px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    </style>
@endpush

@section('content')
<div class="row gx-3">
    <div class="col-xxl-12 col-sm-12">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Aktifitas Pengguna</h5>
                <form method="GET" class="search-form d-flex gap-2 align-items-end">
                    <div>
                        <label class="form-label small">Pengguna</label>
                        <select name="user" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected(request('user')==$u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label small">Method</label>
                        <select name="method" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach (['GET','POST','PUT','PATCH','DELETE'] as $m)
                                <option value="{{ $m }}" @selected(request('method')==$m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label small">Dari</label>
<input type="date" name="date_from" value="{{ request('date_from', now()->toDateString()) }}" class="form-control form-control-sm">
                    </div>
                    <div>
                        <label class="form-label small">Sampai</label>
<input type="date" name="date_to" value="{{ request('date_to', now()->toDateString()) }}" class="form-control form-control-sm">
                    </div>
                    <div>
                        <label class="form-label small">Cari</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="URL / route / IP" class="form-control form-control-sm">
                    </div>
                    <div>
                        <button class="btn btn-sm btn-primary"><i class="ri-search-line"></i> Cari</button>
                        <a href="{{ route('pengguna.activity.index') }}" class="btn btn-sm btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th>Subject</th>
                                    <th>Nama Petugas</th>
                                    <th>IP Address</th>
                                    <th>Created At</th>
                                    <th>Update At</th>
                                    <th style="width:120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $i => $log)
                                    <tr>
                                        <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $i + 1 }}</td>
                                        <td class="payload"><span class="truncate-1" title="{{ $log->subject ?? $log->url }}">{{ $log->subject ?? $log->url }}</span></td>
                                        <td>{{ $log->user->name ?? '-' }}</td>
                                        <td>{{ $log->ip }}</td>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $log->updated_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('pengguna.activity.show', $log->id) }}" class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data aktivitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-2">{{ $logs->links('pagination::bootstrap-4') }}</div>
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
@endpush
