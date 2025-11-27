@extends('layouts.app')

@section('title', 'Tambah Jenis Pemeriksaan Penunjang')

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Jenis Pemeriksaan</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('jenis-pemeriksaan.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Pemeriksaan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe Pemeriksaan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="lab" {{ old('type') == 'lab' ? 'selected' : '' }}>Laboratorium</option>
                                <option value="radiologi" {{ old('type') == 'radiologi' ? 'selected' : '' }}>Radiologi
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control @error('harga') is-invalid @enderror"
                                    id="harga_display" value="{{ old('harga', '0') }}" readonly
                                    style="background-color: #f0f0f0;">
                                <!-- Input tersembunyi untuk menyimpan nilai asli -->
                                <input type="hidden" name="harga" id="harga" value="{{ old('harga', 0) }}">
                            </div>
                            <small class="text-muted">Harga otomatis dihitung dari total struktur harga di bawah</small>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Struktur Harga</h5>

                        <div class="mb-3">
                            <label for="fee_dokter_penunjang" class="form-label">Fee Dokter Penunjang</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="fee_dokter_display"
                                    value="{{ old('fee_dokter_penunjang', 0) }}">
                                <input type="hidden" name="fee_dokter_penunjang" id="fee_dokter_penunjang"
                                    value="{{ old('fee_dokter_penunjang', 0) }}">
                            </div>
                            <small class="text-muted">Fee untuk dokter yang merequest pemeriksaan penunjang</small>
                        </div>

                        <div class="mb-3">
                            <label for="fee_perawat_penunjang" class="form-label">Fee Perawat Penunjang</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="fee_perawat_display"
                                    value="{{ old('fee_perawat_penunjang', 0) }}">
                                <input type="hidden" name="fee_perawat_penunjang" id="fee_perawat_penunjang"
                                    value="{{ old('fee_perawat_penunjang', 0) }}">
                            </div>
                            <small class="text-muted">Fee untuk perawat yang membantu pemeriksaan penunjang</small>
                        </div>

                        <div class="mb-3">
                            <label for="fee_pelaksana" class="form-label">Fee Pelaksana</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="fee_pelaksana_display"
                                    value="{{ old('fee_pelaksana', 0) }}">
                                <input type="hidden" name="fee_pelaksana" id="fee_pelaksana"
                                    value="{{ old('fee_pelaksana', 0) }}">
                            </div>
                            <small class="text-muted">Fee untuk petugas lab/radiologi yang melaksanakan pemeriksaan</small>
                        </div>

                        <div class="mb-3">
                            <label for="biaya_bahan" class="form-label">Biaya Bahan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="biaya_bahan_display"
                                    value="{{ old('biaya_bahan', 0) }}">
                                <input type="hidden" name="biaya_bahan" id="biaya_bahan"
                                    value="{{ old('biaya_bahan', 0) }}">
                            </div>
                            <small class="text-muted">Biaya bahan habis pakai untuk pemeriksaan</small>
                        </div>

                        <div class="mb-3">
                            <label for="jasa_sarana" class="form-label">Jasa Sarana</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="jasa_sarana_display"
                                    value="{{ old('jasa_sarana', 0) }}">
                                <input type="hidden" name="jasa_sarana" id="jasa_sarana"
                                    value="{{ old('jasa_sarana', 0) }}">
                            </div>
                            <small class="text-muted">Jasa sarana rumah sakit/klinik</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('jenis-pemeriksaan.index') }}" class="btn btn-secondary me-2">Batal</a>
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
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        $(document).ready(function() {
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

            function updateHargaFromFees() {
                let feeDokter = parseInt($('#fee_dokter_penunjang').val()) || 0;
                let feePerawat = parseInt($('#fee_perawat_penunjang').val()) || 0;
                let feePelaksana = parseInt($('#fee_pelaksana').val()) || 0;
                let biayaBahan = parseInt($('#biaya_bahan').val()) || 0;
                let jasaSarana = parseInt($('#jasa_sarana').val()) || 0;
                let total = feeDokter + feePerawat + feePelaksana + biayaBahan + jasaSarana;

                // Update harga pemeriksaan otomatis
                $('#harga_display').val(formatRupiah(total.toString()));
                $('#harga').val(total);
            }

            // Fee Dokter
            $('#fee_dokter_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#fee_dokter_penunjang').val(realValue);
                updateHargaFromFees();
            });

            // Fee Perawat
            $('#fee_perawat_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#fee_perawat_penunjang').val(realValue);
                updateHargaFromFees();
            });

            // Fee Pelaksana
            $('#fee_pelaksana_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#fee_pelaksana').val(realValue);
                updateHargaFromFees();
            });

            // Biaya Bahan
            $('#biaya_bahan_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#biaya_bahan').val(realValue);
                updateHargaFromFees();
            });

            // Jasa Sarana
            $('#jasa_sarana_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#jasa_sarana').val(realValue);
                updateHargaFromFees();
            });
        });
    </script>
@endpush
