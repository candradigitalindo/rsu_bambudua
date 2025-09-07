@extends('layouts.app')
@section('title')
    Tambah Insentif Manual
@endsection

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Tambah Insentif Manual</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('keuangan.insentif.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Karyawan</label>
                            <select class="form-select select2 @error('user_id') is-invalid @enderror" id="user_id"
                                name="user_id" required>
                                <option value="" disabled selected>Pilih Karyawan</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" value="{{ old('description') }}"
                                placeholder="Contoh: Bonus kinerja bulan Mei" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah (Rp)</label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                id="amount_display" value="{{ old('amount') }}" placeholder="Contoh: 500.000" required>
                            <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('keuangan.insentif.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih Karyawan",
                allowClear: true
            });

            // Fungsi untuk format Rupiah
            function formatRupiah(angka) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return rupiah;
            }

            // Event listener untuk input jumlah
            $('#amount_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');

                // Update input display dengan format Rupiah
                $(this).val(formatRupiah(displayValue));
                // Update input hidden dengan nilai asli
                $('#amount').val(realValue);
            });

        });
    </script>
@endpush
