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
                                <option value="group" {{ old('field_type') == 'group' ? 'selected' : '' }}>Grup Pemeriksaan
                                    (dengan Satuan & Nilai Normal)</option>
                            </select>
                            @error('field_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Pilih "Grup Pemeriksaan" untuk membuat struktur dengan
                                sub-field (Satuan & Nilai Normal)</small>
                        </div>

                        <div id="group-info" style="display: none;">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> <strong>Mode Grup:</strong> Field ini akan secara otomatis
                                membuat 3 sub-field:
                                <ul class="mb-0 mt-2">
                                    <li><strong>Pemeriksaan:</strong> Untuk input hasil pemeriksaan</li>
                                    <li><strong>Satuan:</strong> Untuk input satuan (mm, %, mg/dL, dll)</li>
                                    <li><strong>Nilai Normal:</strong> Untuk input range nilai normal</li>
                                </ul>
                            </div>
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
                                    <tr class="main-field">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <strong>{{ $field->field_label }}</strong>
                                            @if ($field->isGroup())
                                                <i class="bi bi-folder ms-1 text-primary" title="Grup dengan sub-field"></i>
                                            @endif
                                        </td>
                                        <td><code>{{ $field->field_name }}</code></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $field->field_type === 'group' ? 'primary' : 'info' }}">
                                                {{ $field->field_type === 'group' ? 'Grup' : $field->field_type }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('jenis-pemeriksaan.fields.destroy', ['field_id' => $field->id]) }}"
                                                class="btn btn-danger btn-sm" data-confirm-delete="true">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>

                                    {{-- Tampilkan sub-field jika tipe group --}}
                                    @if ($field->isGroup())
                                        {{-- Form untuk tambah pemeriksaan ke grup --}}
                                        <tr class="group-form bg-light">
                                            <td colspan="5">
                                                <div class="p-3">
                                                    <h6 class="mb-3">
                                                        <i class="bi bi-plus-circle text-success me-1"></i>
                                                        Tambah Pemeriksaan ke Grup: {{ $field->field_label }}
                                                    </h6>
                                                    <form
                                                        action="{{ route('jenis-pemeriksaan.examinations.store', $field->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="row g-2">
                                                            <div class="col-md-4">
                                                                <input type="text" name="examination_name"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Nama Pemeriksaan" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" name="unit"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Satuan (mm, %, mg/dL)" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" name="normal_range"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="Range Normal" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                                    <i class="bi bi-plus"></i> Tambah
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- Tampilkan daftar pemeriksaan dalam grup --}}
                                        @if ($field->fieldItems->isNotEmpty())
                                            @foreach ($field->fieldItems as $item)
                                                <tr class="sub-field">
                                                    <td></td>
                                                    <td class="ps-4">
                                                        <i class="bi bi-arrow-return-right text-muted me-1"></i>
                                                        <strong>{{ $item->examination_name }}</strong>
                                                        <small class="text-muted d-block">{{ $item->unit }} | Normal:
                                                            {{ $item->normal_range }}</small>
                                                    </td>
                                                    <td><code
                                                            class="text-muted">{{ $field->field_name }}.{{ $item->item_name }}</code>
                                                    </td>
                                                    <td><span
                                                            class="badge bg-light text-dark">{{ $item->item_type }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('jenis-pemeriksaan.examinations.destroy', $item->id) }}"
                                                            class="btn btn-danger btn-sm" data-confirm-delete="true">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="sub-field">
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Belum ada pemeriksaan dalam grup ini. Silakan tambah pemeriksaan di
                                                    atas.
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fieldTypeSelect = document.getElementById('field_type');
            const groupInfo = document.getElementById('group-info');

            function toggleGroupInfo() {
                if (fieldTypeSelect.value === 'group') {
                    groupInfo.style.display = 'block';
                } else {
                    groupInfo.style.display = 'none';
                }
            }

            // Check on page load
            toggleGroupInfo();

            // Listen for changes
            fieldTypeSelect.addEventListener('change', toggleGroupInfo);
        });
    </script>

    <style>
        .sub-field {
            background-color: #f8f9fa;
        }

        .sub-field td {
            border-top: 1px dashed #dee2e6;
            font-size: 0.9em;
        }

        .main-field {
            border-bottom: 2px solid #dee2e6;
        }
    </style>
@endpush
