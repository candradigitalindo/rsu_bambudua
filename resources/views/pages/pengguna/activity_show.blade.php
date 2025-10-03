@extends('layouts.app')
@section('title')
    Detail Aktivitas
@endsection

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
<div class="row gx-3">
    <div class="col-xxl-12 col-sm-12">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Detail Aktivitas</h5>
                <a href="{{ route('pengguna.activity.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Subject</dt>
                    <dd class="col-sm-9">{{ $log->subject ?? '-' }}</dd>

                    <dt class="col-sm-3">Nama Petugas</dt>
                    <dd class="col-sm-9">{{ $log->user->name ?? '-' }}</dd>

                    <dt class="col-sm-3">Module</dt>
                    <dd class="col-sm-9">{{ $log->module ?? '-' }}</dd>

                    <dt class="col-sm-3">Method</dt>
                    <dd class="col-sm-9"><span class="badge bg-secondary">{{ $log->method }}</span></dd>

                    <dt class="col-sm-3">URL</dt>
                    <dd class="col-sm-9"><code>{{ $log->url }}</code></dd>

                    <dt class="col-sm-3">Route</dt>
                    <dd class="col-sm-9"><code>{{ $log->route_name }}</code></dd>

                    <dt class="col-sm-3">IP Address</dt>
                    <dd class="col-sm-9">{{ $log->ip }}</dd>

                    <dt class="col-sm-3">User Agent</dt>
                    <dd class="col-sm-9">{{ $log->user_agent }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">{{ $log->status }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $log->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Update At</dt>
                    <dd class="col-sm-9">{{ $log->updated_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Payload</dt>
                    <dd class="col-sm-9">
                        @if (is_array($log->payload))
                            <pre class="m-0"><code>{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        @else
                            -
                        @endif
                    </dd>
                </dl>
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
