@extends('layouts.app')
@section('title', 'Edit Metode Pembayaran')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Metode Pembayaran</h5>
                    <a href="{{ route('payment-methods.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('payment-methods.update', $method->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Nama Metode Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name', $method->name) }}"
                                placeholder="Contoh: Transfer Bank, E-Wallet, Kartu Kredit" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                                value="{{ old('code', $method->code) }}" placeholder="Contoh: BANK_TRANSFER, EWALLET"
                                required>
                            <small class="text-muted">Kode unik untuk identifikasi sistem</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="ri-money-dollar-circle-line"></i> Pengaturan Biaya Fee</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Tipe Fee <span class="text-danger">*</span></label>
                                    <select class="form-select @error('fee_type') is-invalid @enderror" name="fee_type"
                                        id="fee_type" required>
                                        <option value="percentage"
                                            {{ old('fee_type', $method->fee_type) == 'percentage' ? 'selected' : '' }}>
                                            Persentase (%)</option>
                                        <option value="fixed"
                                            {{ old('fee_type', $method->fee_type) == 'fixed' ? 'selected' : '' }}>Tetap
                                            (Nominal)</option>
                                        <option value="both"
                                            {{ old('fee_type', $method->fee_type) == 'both' ? 'selected' : '' }}>Kombinasi
                                            (Persentase + Tetap)</option>
                                    </select>
                                    @error('fee_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3" id="fee_percentage_group">
                                        <label class="form-label">Fee Persentase (%)</label>
                                        <input type="number" step="0.01" min="0" max="100"
                                            class="form-control @error('fee_percentage') is-invalid @enderror"
                                            name="fee_percentage"
                                            value="{{ old('fee_percentage', $method->fee_percentage) }}"
                                            placeholder="0.00">
                                        <small class="text-muted">Contoh: 2.5 untuk fee 2.5%</small>
                                        @error('fee_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3" id="fee_fixed_group">
                                        <label class="form-label">Fee Tetap (Rp)</label>
                                        <input type="number" step="0.01" min="0"
                                            class="form-control @error('fee_fixed') is-invalid @enderror" name="fee_fixed"
                                            value="{{ old('fee_fixed', $method->fee_fixed) }}" placeholder="0">
                                        <small class="text-muted">Contoh: 5000 untuk fee Rp 5.000</small>
                                        @error('fee_fixed')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <small>
                                        <i class="ri-information-line"></i>
                                        <strong>Contoh perhitungan:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Persentase:</strong> Tagihan Rp 100.000, Fee 2.5% = Rp 2.500</li>
                                            <li><strong>Tetap:</strong> Tagihan Rp 100.000, Fee Rp 5.000 = Rp 5.000</li>
                                            <li><strong>Kombinasi:</strong> Tagihan Rp 100.000, Fee 2.5% + Rp 5.000 = Rp
                                                7.500</li>
                                        </ul>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3"
                                placeholder="Deskripsi metode pembayaran (opsional)">{{ old('description', $method->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="active" id="active"
                                {{ old('active', $method->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                <i class="ri-checkbox-circle-line"></i> Aktifkan metode pembayaran ini
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('payment-methods.index') }}" class="btn btn-secondary">
                                <i class="ri-close-line"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Update
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
    <script>
        $(document).ready(function() {
            function toggleFeeFields() {
                const feeType = $('#fee_type').val();
                const percentageGroup = $('#fee_percentage_group');
                const fixedGroup = $('#fee_fixed_group');

                if (feeType === 'percentage') {
                    percentageGroup.show();
                    fixedGroup.hide();
                    $('input[name="fee_fixed"]').val(0);
                } else if (feeType === 'fixed') {
                    percentageGroup.hide();
                    fixedGroup.show();
                    $('input[name="fee_percentage"]').val(0);
                } else { // both
                    percentageGroup.show();
                    fixedGroup.show();
                }
            }

            // Initial toggle
            toggleFeeFields();

            // Toggle on change
            $('#fee_type').on('change', toggleFeeFields);
        });
    </script>
@endpush
