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
                                        <style>
                                            .measurement-table-result {
                                                width: 100%;
                                                border-collapse: collapse;
                                                background-color: white;
                                            }

                                            .measurement-table-result th {
                                                background-color: #198754;
                                                color: white;
                                                padding: 12px;
                                                text-align: center;
                                                font-weight: 600;
                                                border: 1px solid #198754;
                                            }

                                            .measurement-table-result tbody td {
                                                padding: 10px 12px;
                                                border: 1px solid #dee2e6;
                                                text-align: center !important;
                                            }

                                            .measurement-table-result tbody td:first-child {
                                                background-color: #f8f9fa;
                                                font-weight: 600;
                                                text-align: left !important;
                                                vertical-align: middle;
                                                width: 20%;
                                                padding-left: 15px;
                                            }

                                            .measurement-table-result tbody td:nth-child(2) {
                                                background-color: #f8f9fa;
                                                width: 30%;
                                                font-weight: 500;
                                                text-align: left !important;
                                                padding-left: 15px;
                                            }

                                            .measurement-table-result tbody td:nth-child(3) {
                                                background-color: #ffffff;
                                                width: 150px;
                                                font-weight: 600;
                                                color: #000000 !important;
                                            }

                                            .measurement-table-result tbody td:nth-child(4) {
                                                background-color: #fffbf0;
                                                color: #6c757d;
                                                width: auto;
                                                font-size: 13px;
                                                font-style: italic;
                                            }
                                        </style>

                                        <table class="measurement-table-result">
                                            <thead>
                                                <tr>
                                                    <th>Bagian</th>
                                                    <th>Parameter</th>
                                                    <th>Nilai</th>
                                                    <th>Normal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $echoStructure = [
                                                        'Aorta' => [['param' => 'Root diam', 'normal' => '20–37 mm']],
                                                        'Ventrikel Kiri<br>(Left Ventricle)' => [
                                                            ['param' => 'EDD', 'normal' => '35–52 mm'],
                                                            ['param' => 'ESD', 'normal' => '26–36 mm'],
                                                            ['param' => 'IVS Diastole', 'normal' => '7–11 mm'],
                                                            ['param' => 'IVS Systole', 'normal' => ''],
                                                            ['param' => 'PW Diastole', 'normal' => '7–11 mm'],
                                                            ['param' => 'PW Systole', 'normal' => ''],
                                                        ],
                                                        'Atrium Kiri<br>(Left Atrium)' => [
                                                            ['param' => 'LA Dimension', 'normal' => '15–40 mm'],
                                                            ['param' => 'LA/Ao ratio', 'normal' => '< 1.33'],
                                                        ],
                                                        'Ventrikel Kanan<br>(Right Ventricle)' => [
                                                            ['param' => 'RV Dimension', 'normal' => '< 43 mm'],
                                                            ['param' => 'M.V.A', 'normal' => '> 3 cm²'],
                                                            ['param' => 'TAPSE', 'normal' => '≥ 16 mm'],
                                                            ['param' => 'RA mayor', 'normal' => ''],
                                                        ],
                                                        'Fungsi' => [
                                                            ['param' => 'EF', 'normal' => '52–77%'],
                                                            ['param' => 'FS', 'normal' => '> 25%'],
                                                            ['param' => 'EPSS', 'normal' => '< 10 mm'],
                                                        ],
                                                    ];
                                                @endphp

                                                @foreach ($echoStructure as $bagian => $parameters)
                                                    @foreach ($parameters as $index => $item)
                                                        <tr>
                                                            @if ($index === 0)
                                                                <td rowspan="{{ count($parameters) }}">
                                                                    {!! $bagian !!}</td>
                                                            @endif
                                                            <td>{{ $item['param'] }}</td>
                                                            <td style="text-align: center !important;">
                                                                {{ $latestResult->payload[$item['param']] ?? '-' }}</td>
                                                            <td style="text-align: center !important;">
                                                                {!! $item['normal'] !!}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            @php
                                // Define measurement parameters to exclude
                                $measurementParams = [
                                    'Root diam',
                                    'EDD',
                                    'ESD',
                                    'IVS Diastole',
                                    'IVS Systole',
                                    'PW Diastole',
                                    'PW Systole',
                                    'LA Dimension',
                                    'LA/Ao ratio',
                                    'RV Dimension',
                                    'M.V.A',
                                    'TAPSE',
                                    'RA mayor',
                                    'EF',
                                    'FS',
                                    'EPSS',
                                ];

                                // Get valve assessment data (non-measurement fields)
                                $valveData = [];
                                if (!empty($latestResult->payload) && is_array($latestResult->payload)) {
                                    foreach ($latestResult->payload as $key => $value) {
                                        // Skip empty values and measurement parameters
                                        if (in_array($key, $measurementParams)) {
                                            continue;
                                        }
                                        // Add non-empty values
                                        if (!empty(trim($value))) {
                                            $valveData[$key] = $value;
                                        }
                                    }
                                }
                                $hasValveData = !empty($valveData);
                            @endphp

                            @if ($hasValveData)
                                <div class="card bg-light mb-3">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0">Penilaian Katup & Gerakan Otot</h6>
                                    </div>
                                    <div class="card-body">
                                        <style>
                                            .valve-assessment-table {
                                                width: 100%;
                                                border-collapse: collapse;
                                                background-color: white;
                                            }

                                            .valve-assessment-table td {
                                                padding: 8px 15px;
                                                border: 1px solid #dee2e6;
                                            }

                                            .valve-assessment-table td:first-child {
                                                background-color: #f8f9fa;
                                                font-weight: 500;
                                                width: 45%;
                                            }

                                            .valve-assessment-table td:nth-child(2) {
                                                background-color: #ffffff;
                                                width: 55%;
                                            }
                                        </style>
                                        <table class="valve-assessment-table">
                                            @foreach ($valveData as $field => $value)
                                                <tr>
                                                    <td>{{ $field }}</td>
                                                    <td>: {{ $value }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @else
                                {{-- Tampilkan semua non-measurement data untuk debugging --}}
                                @if (!empty($latestResult->payload) && is_array($latestResult->payload))
                                    @php
                                        $allNonMeasurement = [];
                                        foreach ($latestResult->payload as $k => $v) {
                                            if (!in_array($k, $measurementParams)) {
                                                $allNonMeasurement[$k] = $v;
                                            }
                                        }
                                    @endphp
                                    @if (!empty($allNonMeasurement))
                                        <div class="card bg-light mb-3">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0">Penilaian Katup & Gerakan Otot</h6>
                                            </div>
                                            <div class="card-body">
                                                <style>
                                                    .valve-assessment-table {
                                                        width: 100%;
                                                        border-collapse: collapse;
                                                        background-color: white;
                                                    }

                                                    .valve-assessment-table td {
                                                        padding: 8px 15px;
                                                        border: 1px solid #dee2e6;
                                                    }

                                                    .valve-assessment-table td:first-child {
                                                        background-color: #f8f9fa;
                                                        font-weight: 500;
                                                        width: 45%;
                                                    }

                                                    .valve-assessment-table td:nth-child(2) {
                                                        background-color: #ffffff;
                                                        width: 55%;
                                                    }
                                                </style>
                                                <table class="valve-assessment-table">
                                                    @foreach ($allNonMeasurement as $field => $value)
                                                        <tr>
                                                            <td>{{ $field }}</td>
                                                            <td>: {{ $value }}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif

                            <div class="card bg-light mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Saran / Recommendation</h6>
                                </div>
                                <div class="card-body">
                                    <div style="white-space: pre-wrap;">{{ $latestResult->findings }}</div>
                                </div>
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
                        <a href="{{ route('radiologi.requests.print', $req->id) }}?auto=1" target="_blank"
                            class="btn btn-outline-primary">
                            <i class="bi bi-printer me-1"></i>Cetak Hasil
                        </a>
                    @endif
                    @if ($st === 'processing')
                        <form method="POST" action="{{ route('radiologi.requests.status', $req->id) }}"
                            class="d-inline">
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
