@extends('layouts.app')
@section('title')
    Pengaturan Insentif
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            color: gray;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pengaturan Nominal Insentif</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('keuangan.incentive.settings.simpan') }}" method="POST" id="form-insentif">
                        @csrf
                        <div class="mb-3">
                            <label for="perawat_per_encounter_display" class="form-label">Insentif Perawat per Pasien
                                (Rp)</label>
                            <input type="text" class="form-control @error('perawat_per_encounter') is-invalid @enderror"
                                id="perawat_per_encounter_display"
                                value="{{ old('perawat_per_encounter', isset($settings['perawat_per_encounter']) ? number_format($settings['perawat_per_encounter'], 0, ',', '.') : '') }}"
                                placeholder="Masukkan nominal insentif">
                            <input type="hidden" name="perawat_per_encounter" id="perawat_per_encounter"
                                value="{{ old('perawat_per_encounter', isset($settings['perawat_per_encounter']) ? (int) $settings['perawat_per_encounter'] : '') }}">
                            <div class="form-text">Nominal ini akan diberikan kepada setiap perawat yang menangani 1 pasien
                                hingga selesai.</div>
                            @error('perawat_per_encounter')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="dokter_per_encounter_display" class="form-label">Insentif Dokter per Pasien
                                (Rp)</label>
                            <input type="text" class="form-control @error('dokter_per_encounter') is-invalid @enderror"
                                id="dokter_per_encounter_display"
                                value="{{ old('dokter_per_encounter', isset($settings['dokter_per_encounter']) ? number_format($settings['dokter_per_encounter'], 0, ',', '.') : '') }}"
                                placeholder="Masukkan nominal insentif">
                            <input type="hidden" name="dokter_per_encounter" id="dokter_per_encounter"
                                value="{{ old('dokter_per_encounter', isset($settings['dokter_per_encounter']) ? (int) $settings['dokter_per_encounter'] : '') }}">
                            <div class="form-text">Nominal ini akan diberikan kepada dokter yang menangani 1 pasien
                                hingga selesai.</div>
                            @error('dokter_per_encounter')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fee_dokter_penunjang" class="form-label">Fee Dokter Pemeriksaan Penunjang
                                (%)</label>
                            <div class="input-group">
                                <input type="number"
                                    class="form-control @error('fee_dokter_penunjang') is-invalid @enderror"
                                    id="fee_dokter_penunjang" name="fee_dokter_penunjang"
                                    value="{{ old('fee_dokter_penunjang', (int) ($settings['fee_dokter_penunjang'] ?? 0)) }}"
                                    placeholder="Contoh: 10" min="0" max="100" step="1">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Persentase fee dari total harga pemeriksaan (Lab/Radiologi) yang akan
                                diberikan kepada dokter perujuk.</div>
                            @error('fee_dokter_penunjang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cutoff_day" class="form-label">Tanggal Cut-off Gaji</label>
                            <input type="number" class="form-control @error('cutoff_day') is-invalid @enderror"
                                id="cutoff_day" name="cutoff_day"
                                value="{{ old('cutoff_day', number_format($settings['cutoff_day'], 0, ',', '.') ?? 25) }}"
                                placeholder="Masukkan tanggal (1-28)" min="1" max="28">
                            <div class="form-text">Tanggal batas perhitungan insentif setiap bulan. Contoh: Jika diisi 25,
                                maka periode gaji adalah dari tanggal 26 bulan lalu s/d 25 bulan ini.</div>
                            @error('cutoff_day')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" id="btn-simpan">
                                <span class="btn-text">Simpan Pengaturan</span>
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
            $('#form-insentif').on('submit', function(e) {
                $('#btn-simpan').prop('disabled', true);
                $('#btn-simpan .btn-text').addClass('d-none');
                $('#btn-simpan .spinner-border').removeClass('d-none');
            });

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

            $('#perawat_per_encounter_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#perawat_per_encounter').val(realValue);
            });

            $('#dokter_per_encounter_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#dokter_per_encounter').val(realValue);
            });
        });
    </script>
@endpush
