@extends('layouts.app')

@section('title', 'Detail Permintaan Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Permintaan Radiologi</h5>
                    <a href="{{ route('radiologi.requests.index') }}" class="btn btn-light btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">No. RM</div>
                            <div class="fw-semibold">{{ $req->pasien->rekam_medis ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Nama Pasien</div>
                            <div class="fw-semibold">{{ $req->pasien->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Pemeriksaan</div>
                            <div class="fw-semibold">{{ $req->jenis->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Tarif</div>
                            <div class="fw-semibold">{{ 'Rp ' . number_format($req->price ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Status</div>
                            <div><span class="badge bg-secondary">{{ ucfirst($req->status ?? '-') }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Dokter Pengirim</div>
                            <div class="fw-semibold">{{ $req->dokter->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Tanggal Permintaan</div>
                            <div class="fw-semibold">{{ optional($req->created_at)->format('d M Y H:i') }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Catatan</div>
                            <div class="fw-semibold">{{ $req->notes ?? '-' }}</div>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <h6 class="mb-3">Hasil Pemeriksaan Radiologi</h6>
                        @if (!empty($latestResult))
                            <div class="card border-primary mb-3">
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="ri-user-star-line me-2"></i>
                                            <strong>Dokter Spesialis Radiologi</strong>
                                        </div>
                                        <span class="badge bg-light text-primary">
                                            {{ optional($latestResult->reported_at)->format('d M Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="text-primary mb-3">
                                        {{ optional($latestResult->radiologist)->name ?? 'Belum tercatat' }}</h5>

                                    <div class="small text-muted mb-1">
                                        <i class="ri-user-line me-1"></i>Dilaporkan oleh:
                                        <span class="text-dark">{{ optional($latestResult->reporter)->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            @if (!empty($latestResult->payload) && is_array($latestResult->payload))
                                <div class="card bg-light mb-3">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0">Data Pemeriksaan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            @foreach ($latestResult->payload as $key => $value)
                                                <div class="col-md-6">
                                                    <div class="small text-muted">
                                                        {{ ucwords(str_replace('_', ' ', $key)) }}</div>
                                                    <div class="fw-semibold">{{ $value ?: '-' }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-2">
                                <div class="small text-muted">Temuan (Findings)</div>
                                <div style="white-space: pre-wrap;">{{ $latestResult->findings }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="small text-muted">Kesimpulan (Impression)</div>
                                <div style="white-space: pre-wrap;">{{ $latestResult->impression }}</div>
                            </div>
                            @if (is_array($latestResult->files) && count($latestResult->files))
                                <div class="mb-2">
                                    <div class="small text-muted">Lampiran</div>
                                    <ul class="mb-0">
                                        @foreach ($latestResult->files as $file)
                                            <li><a href="{{ asset('storage/' . $file) }}"
                                                    target="_blank">{{ basename($file) }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="text-muted">Belum ada hasil.</div>
                        @endif
                    </div>
                </div>
                <div class="card-footer d-flex gap-2 align-items-center flex-wrap">
                    <a href="{{ route('radiologi.requests.index') }}" class="btn btn-light">Kembali</a>
                    @php($st = $req->status)
                    @if ($st === 'processing')
                        <a href="{{ route('radiologi.requests.results.edit', $req->id) }}" class="btn btn-success">
                            <i class="bi bi-pencil-square me-1"></i>Input Hasil Pemeriksaan
                        </a>
                    @elseif($st === 'completed')
                        <a href="{{ route('radiologi.requests.print', $req->id) }}" target="_blank"
                            class="btn btn-outline-primary">
                            <i class="bi bi-printer me-1"></i>Cetak Hasil
                        </a>
                    @endif
                    @if ($st === 'processing')
                        <form method="POST" action="{{ route('radiologi.requests.status', $req->id) }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="canceled">
                            <button type="submit" class="btn btn-outline-danger"
                                onclick="return confirm('Yakin ingin membatalkan permintaan radiologi ini?')">
                                <i class="bi bi-x-circle me-1"></i>Batalkan
                            </button>
                        </form>
                    @endif
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
