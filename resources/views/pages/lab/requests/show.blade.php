@extends('layouts.app')
@section('title', 'Detail Permintaan Lab')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-12 col-lg-10">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Permintaan Lab</h5>
                    <div>
                        @if ($req->status === 'completed')
                            <a href="{{ route('lab.requests.print.medical', $req->id) }}" target="_blank"
                                class="btn btn-sm btn-success">
                                <i class="ri-printer-line"></i> Cetak Hasil Lab
                            </a>
                        @endif
                        <a href="{{ route('lab.requests.edit', $req->id) }}"
                            class="btn btn-sm btn-outline-primary">Hasil/Status</a>
                        <a href="{{ route('lab.requests.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6"><small class="text-muted">Tanggal</small>
                            <div>{{ $req->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <div class="col-md-6"><small class="text-muted">Status</small>
                            <div><span class="badge bg-secondary">{{ ucfirst($req->status) }}</span></div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6"><small class="text-muted">RM</small>
                            <div>{{ $req->encounter->rekam_medis ?? '-' }}</div>
                        </div>
                        <div class="col-md-6"><small class="text-muted">Pasien</small>
                            <div>{{ $req->encounter->name_pasien ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th style="width:25%">Pemeriksaan</th>
                                    <th>Detail Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($req->items as $it)
                                    <tr>
                                        <td>{{ $it->test_name }}</td>
                                        <td>
                                            @if (is_array($it->result_payload) && count($it->result_payload))
                                                {{-- Cek apakah ada grup dengan sub-field --}}
                                                @php
                                                    $hasGroupData = false;
                                                    foreach ($it->result_payload as $key => $value) {
                                                        if (is_array($value)) {
                                                            $hasGroupData = true;
                                                            break;
                                                        }
                                                    }
                                                @endphp

                                                @if ($hasGroupData && $it->jenisPemeriksaan)
                                                    {{-- Tampilkan dengan struktur grup --}}
                                                    @foreach ($it->jenisPemeriksaan->templateFields as $field)
                                                        @if ($field->field_type === 'group' && isset($it->result_payload[$field->field_name]))
                                                            <div class="mb-3">
                                                                <h6 class="text-primary mb-2">{{ $field->field_label }}
                                                                </h6>
                                                                <table class="table table-sm table-bordered">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Pemeriksaan</th>
                                                                            <th>Hasil</th>
                                                                            <th>Satuan</th>
                                                                            <th>Normal</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($field->fieldItems as $item)
                                                                            <tr>
                                                                                <td class="fw-semibold">
                                                                                    {{ $item->examination_name }}</td>
                                                                                <td>{{ $it->result_payload[$field->field_name][$item->item_name] ?? '-' }}
                                                                                </td>
                                                                                <td class="text-muted">{{ $item->unit }}
                                                                                </td>
                                                                                <td class="text-muted">
                                                                                    {{ $item->normal_range }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @elseif($field->field_type !== 'group' && isset($it->result_payload[$field->field_name]))
                                                            <div class="mb-2">
                                                                <strong>{{ $field->field_label }}:</strong>
                                                                {{ $it->result_payload[$field->field_name] }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{-- Tampilkan format lama untuk kompatibilitas --}}
                                                    <dl class="row mb-0">
                                                        @foreach ($it->result_payload as $k => $v)
                                                            <dt class="col-sm-4 text-muted small">
                                                                {{ str_replace('_', ' ', ucfirst($k)) }}</dt>
                                                            <dd class="col-sm-8">{{ is_array($v) ? json_encode($v) : $v }}
                                                            </dd>
                                                        @endforeach
                                                    </dl>
                                                @endif
                                            @else
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <div><small class="text-muted">Nilai</small></div>
                                                        <div>{{ $it->result_value ?? '-' }}</div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div><small class="text-muted">Satuan</small></div>
                                                        <div>{{ $it->result_unit ?? '-' }}</div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div><small class="text-muted">Rujukan</small></div>
                                                        <div>{{ $it->result_reference ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($it->result_notes)
                                                <div class="mt-1"><small class="text-muted">Catatan:</small>
                                                    {{ $it->result_notes }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($req->notes)
                        <div class="mt-2"><small class="text-muted">Catatan:</small>
                            <div>{{ $req->notes }}</div>
                        </div>
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
