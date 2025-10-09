@extends('layouts.app')

@section('title', 'Atur Kolom untuk ' . $jenisPemeriksaan->name)

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <!-- Form Tambah Kolom -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tambah Kolom Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('jenis-pemeriksaan.fields.store', $jenisPemeriksaan->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="field_label" class="form-label">Label Kolom <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('field_label') is-invalid @enderror"
                                id="field_label" name="field_label" value="{{ old('field_label') }}" required
                                placeholder="Contoh: LV EDD (mm)">
                            @error('field_label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="field_type" class="form-label">Tipe Input <span class="text-danger">*</span></label>
                            <select class="form-select @error('field_type') is-invalid @enderror" id="field_type"
                                name="field_type" required>
                                <option value="text" {{ old('field_type') == 'text' ? 'selected' : '' }}>Teks Singkat
                                </option>
                                <option value="number" {{ old('field_type') == 'number' ? 'selected' : '' }}>Angka
                                </option>
                                <option value="textarea" {{ old('field_type') == 'textarea' ? 'selected' : '' }}>Teks
                                    Panjang</option>
                            </select>
                            @error('field_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="placeholder" class="form-label">Placeholder (Opsional)</label>
                            <input type="text" class="form-control @error('placeholder') is-invalid @enderror"
                                id="placeholder" name="placeholder" value="{{ old('placeholder') }}"
                                placeholder="Contoh: Normal: 35-52 mm">
                            @error('placeholder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Tambah Kolom</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Kolom yang Sudah Ada -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Kolom untuk: {{ $jenisPemeriksaan->name }}</h4>
                    <a href="{{ route('jenis-pemeriksaan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Label Kolom</th>
                                    <th>Nama Kolom (DB)</th>
                                    <th>Tipe</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jenisPemeriksaan->templateFields as $key => $field)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $field->field_label }}</td>
                                        <td><code>{{ $field->field_name }}</code></td>
                                        <td><span class="badge bg-info">{{ $field->field_type }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('jenis-pemeriksaan.fields.destroy', ['field_id' => $field->id]) }}"
                                               class="btn btn-danger btn-sm" data-confirm-delete="true">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada kolom yang ditambahkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
