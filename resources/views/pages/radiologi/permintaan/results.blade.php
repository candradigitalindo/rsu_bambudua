@extends('layouts.app')

@section('title', 'Input Hasil Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Input Hasil Radiologi</h5>
                    <a href="{{ route('radiologi.requests.show', $req->id) }}" class="btn btn-light btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="m-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="text-muted small">Pasien</div>
                        <div class="fw-semibold">{{ $req->pasien->rekam_medis ?? '-' }} — {{ $req->pasien->name ?? '-' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Pemeriksaan</div>
                        <div class="fw-semibold">{{ $req->jenis->name ?? '-' }}</div>
                    </div>

                    <form method="POST" action="{{ route('radiologi.requests.results.store', $req->id) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Dokter Spesialis Radiologi <span class="text-danger">*</span></label>
                            <select name="radiologist_id" class="form-select" required>
                                <option value="">-- Pilih Radiolog --</option>
                                @php
                                    $radiologists = \App\Models\User::where('role', 2)
                                        ->where('is_active', 1)
                                        ->orderBy('name')
                                        ->get();
                                @endphp
                                @foreach ($radiologists as $radiolog)
                                    <option value="{{ $radiolog->id }}"
                                        {{ old('radiologist_id') == $radiolog->id ? 'selected' : '' }}>
                                        {{ $radiolog->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih dokter spesialis radiologi yang melakukan pemeriksaan</div>
                        </div>

                        @if ($req->jenis && $req->jenis->templateFields && $req->jenis->templateFields->isNotEmpty())
                            <div class="card bg-light mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Data Pemeriksaan {{ $req->jenis->name }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach ($req->jenis->templateFields as $field)
                                            <div class="col-md-6">
                                                <label class="form-label">{{ $field->field_label }}</label>
                                                @if ($field->field_type === 'textarea')
                                                    <textarea name="payload[{{ $field->field_name }}]" class="form-control" rows="3"
                                                        placeholder="{{ $field->placeholder }}">{{ old('payload.' . $field->field_name) }}</textarea>
                                                @elseif($field->field_type === 'number')
                                                    <input type="number" step="0.01"
                                                        name="payload[{{ $field->field_name }}]" class="form-control"
                                                        value="{{ old('payload.' . $field->field_name) }}"
                                                        placeholder="{{ $field->placeholder }}">
                                                @elseif($field->field_type === 'select')
                                                    <select name="payload[{{ $field->field_name }}]" class="form-select">
                                                        <option value="">-- Pilih --</option>
                                                        @if ($field->placeholder)
                                                            @foreach (explode('|', $field->placeholder) as $option)
                                                                <option value="{{ $option }}"
                                                                    {{ old('payload.' . $field->field_name) == $option ? 'selected' : '' }}>
                                                                    {{ $option }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                @else
                                                    <input type="text" name="payload[{{ $field->field_name }}]"
                                                        class="form-control"
                                                        value="{{ old('payload.' . $field->field_name) }}"
                                                        placeholder="{{ $field->placeholder }}">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Temuan (Findings) <span class="text-danger">*</span></label>
                            <textarea name="findings" class="form-control" rows="6" required>{{ old('findings') }}</textarea>
                            <div class="form-text">Deskripsi lengkap temuan radiologi</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kesimpulan (Impression) <span class="text-danger">*</span></label>
                            <textarea name="impression" class="form-control" rows="4" required>{{ old('impression') }}</textarea>
                            <div class="form-text">Kesimpulan dan diagnosis radiologi</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lampiran Gambar/File (opsional)</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept="image/*,.pdf">
                            <div class="form-text">Anda bisa mengunggah beberapa file gambar atau PDF (maks 10MB per file).
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('radiologi.requests.show', $req->id) }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Simpan Hasil & Selesaikan
                            </button>
                        </div>
                    </form>
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
