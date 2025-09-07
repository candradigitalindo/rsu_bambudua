@extends('layouts.app')
@section('title')
    Ubah Pengeluaran Operasional
@endsection

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Ubah Pengeluaran</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('operasional.update', $expense->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="expense_date" class="form-label">Tanggal Pengeluaran</label>
                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror"
                                id="expense_date" name="expense_date"
                                value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" value="{{ old('description', $expense->description) }}"
                                placeholder="Contoh: Pembelian ATK" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah (Rp)</label>
                            <!-- Input yang terlihat oleh pengguna -->
                            <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                id="amount_display"
                                value="{{ old('amount', number_format($expense->amount, 0, ',', '.')) }}"
                                placeholder="Contoh: 500.000" required>
                            <!-- Input tersembunyi untuk menyimpan nilai asli -->
                            <input type="hidden" name="amount" id="amount"
                                value="{{ old('amount', $expense->amount) }}">
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('operasional.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        $(document).ready(function() {
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
                // Ambil nilai dari input display
                let displayValue = $(this).val();

                // Hapus semua karakter non-digit untuk mendapatkan nilai asli
                let realValue = displayValue.replace(/\./g, '');

                // Update input display dengan format Rupiah
                $(this).val(formatRupiah(displayValue));
                // Update input hidden dengan nilai asli
                $('#amount').val(realValue);
            });
        });
    </script>
@endpush
