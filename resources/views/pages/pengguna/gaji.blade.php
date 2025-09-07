@extends('layouts.app')
@section('title')
    Atur Gaji Karyawan
@endsection
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Atur Gaji Pokok untuk: {{ $user->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pengguna.gaji.simpan', $user->id) }}" method="POST" id="form-gaji">
                        @csrf
                        <div class="mb-3">
                            <label for="base_salary" class="form-label">Gaji Pokok (Rp)</label>
                            <!-- Input yang terlihat oleh pengguna -->
                            <input type="text" class="form-control @error('base_salary') is-invalid @enderror"
                                id="base_salary_display"
                                value="{{ old('base_salary', isset($user->salary->base_salary) ? number_format($user->salary->base_salary, 0, ',', '.') : '') }}"
                                placeholder="Masukkan jumlah gaji pokok">
                            <!-- Input tersembunyi untuk menyimpan nilai asli -->
                            <input type="hidden" name="base_salary" id="base_salary"
                                value="{{ old('base_salary', $user->salary->base_salary ?? '') }}">
                            @error('base_salary')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Catatan tambahan jika ada">{{ old('notes', $user->salary->notes ?? '') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('pengguna.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btn-simpan">
                                <span class="btn-text">Simpan Gaji</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                            </button>
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
            $('#form-gaji').on('submit', function(e) {
                // Mencegah form submit ganda
                $('#btn-simpan').prop('disabled', true);

                // Tampilkan spinner
                $('#btn-simpan .btn-text').addClass('d-none');
                $('#btn-simpan .spinner-border').removeClass('d-none');

                // Lanjutkan submit
            });

            // Fungsi untuk format Rupiah
            function formatRupiah(angka) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah;
            }

            // Event listener untuk input gaji
            $('#base_salary_display').on('keyup', function() {
                // Ambil nilai dari input display
                let displayValue = $(this).val();

                // Hapus semua karakter non-digit untuk mendapatkan nilai asli
                let realValue = displayValue.replace(/\./g, '');

                // Update input display dengan format Rupiah
                $(this).val(formatRupiah(displayValue));
                // Update input hidden dengan nilai asli
                $('#base_salary').val(realValue);
            });
        });
    </script>
@endpush
