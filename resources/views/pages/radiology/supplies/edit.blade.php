@extends('layouts.app')

@section('title', 'Edit Bahan Radiologi')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Header -->
                <div class="page-header-card mb-4">
                    <h4 class="mb-1 fw-bold">
                        <i class="ri-edit-line me-2"></i>
                        Edit Bahan Radiologi
                    </h4>
                    <p class="text-muted mb-0">Perbarui informasi bahan radiologi</p>
                </div>

                <!-- Form Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="{{ route('radiologi.supplies.update', $supply) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    Nama Bahan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $supply->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="unit" class="form-label fw-semibold">Satuan</label>
                                <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                    id="unit" name="unit" value="{{ old('unit', $supply->unit) }}">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Stok Saat Ini</label>
                                <input type="text" class="form-control" value="{{ number_format($supply->stock) }}"
                                    disabled>
                                <small class="text-muted">
                                    <i class="ri-information-line"></i>
                                    Stok tidak dapat diubah secara langsung, gunakan fitur transaksi untuk mengatur stok
                                </small>
                            </div>

                            <div class="mb-4">
                                <label for="warning_stock" class="form-label fw-semibold">
                                    Stok Peringatan <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('warning_stock') is-invalid @enderror"
                                    id="warning_stock" name="warning_stock"
                                    value="{{ old('warning_stock', $supply->warning_stock) }}" min="0" required>
                                <small class="text-muted">
                                    <i class="ri-information-line"></i>
                                    Sistem akan memberi peringatan jika stok mencapai atau di bawah nilai ini
                                </small>
                                @error('warning_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-header-primary">
                                    <i class="ri-save-line me-1"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('radiologi.supplies.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-header-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            border-left: 5px solid #10b981;
        }

        .btn-header-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-header-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
        }
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
