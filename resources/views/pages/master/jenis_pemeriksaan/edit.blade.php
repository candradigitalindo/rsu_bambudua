@extends('layouts.app')

@section('title', 'Edit Jenis Pemeriksaan Penunjang')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        .info-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .info-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
        }

        .info-item i {
            font-size: 1.2rem;
            margin-right: 10px;
            width: 25px;
        }

        .info-item strong {
            min-width: 110px;
        }

        .fee-card {
            border-left: 4px solid #10b981;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .fee-card .fee-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 50%;
            margin-right: 12px;
        }

        .fee-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.95rem;
        }

        .fee-description {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 2px;
        }

        .form-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .section-title i {
            font-size: 1.3rem;
            margin-right: 10px;
            color: #10b981;
        }

        .input-group-text {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
        }

        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <!-- Left Column: Form -->
        <div class="col-md-7">
            <div class="form-section">
                <form action="{{ route('jenis-pemeriksaan.update', $item->id) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')

                    <div class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informasi Dasar
                    </div>

                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="name" class="form-label">Nama Pemeriksaan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $item->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="type" class="form-label">Tipe <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="lab" {{ old('type', $item->type) == 'lab' ? 'selected' : '' }}>
                                    Laboratorium</option>
                                <option value="radiologi" {{ old('type', $item->type) == 'radiologi' ? 'selected' : '' }}>
                                    Radiologi</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga Pemeriksaan <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control @error('harga') is-invalid @enderror"
                                id="harga_display" value="{{ old('harga', number_format($item->harga, 0, ',', '.')) }}"
                                readonly style="background-color: #f0f0f0;">
                            <input type="hidden" name="harga" id="harga" value="{{ old('harga', $item->harga) }}">
                        </div>
                        <small class="text-muted">Harga otomatis dihitung dari total struktur harga di bawah</small>
                        @error('harga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="section-title mt-4">
                        <i class="fas fa-money-bill-wave"></i>
                        Struktur Harga
                    </div>

                    <div class="fee-card">
                        <div class="d-flex align-items-start">
                            <div class="fee-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="flex-grow-1">
                                <label for="fee_dokter_penunjang" class="fee-label">Fee Dokter Penunjang</label>
                                <div class="fee-description">Fee untuk dokter yang merequest pemeriksaan</div>
                                <div class="input-group mt-2">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="fee_dokter_display"
                                        value="{{ old('fee_dokter_penunjang', number_format($item->fee_dokter_penunjang ?? 0, 0, ',', '.')) }}">
                                    <input type="hidden" name="fee_dokter_penunjang" id="fee_dokter_penunjang"
                                        value="{{ old('fee_dokter_penunjang', $item->fee_dokter_penunjang ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fee-card">
                        <div class="d-flex align-items-start">
                            <div class="fee-icon">
                                <i class="fas fa-user-nurse"></i>
                            </div>
                            <div class="flex-grow-1">
                                <label for="fee_perawat_penunjang" class="fee-label">Fee Perawat Penunjang</label>
                                <div class="fee-description">Fee untuk perawat yang membantu pemeriksaan</div>
                                <div class="input-group mt-2">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="fee_perawat_display"
                                        value="{{ old('fee_perawat_penunjang', number_format($item->fee_perawat_penunjang ?? 0, 0, ',', '.')) }}">
                                    <input type="hidden" name="fee_perawat_penunjang" id="fee_perawat_penunjang"
                                        value="{{ old('fee_perawat_penunjang', $item->fee_perawat_penunjang ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fee-card">
                        <div class="d-flex align-items-start">
                            <div class="fee-icon">
                                <i class="fas fa-flask"></i>
                            </div>
                            <div class="flex-grow-1">
                                <label for="fee_pelaksana" class="fee-label">Fee Pelaksana</label>
                                <div class="fee-description">Fee untuk petugas lab/radiologi yang melaksanakan</div>
                                <div class="input-group mt-2">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="fee_pelaksana_display"
                                        value="{{ old('fee_pelaksana', number_format($item->fee_pelaksana ?? 0, 0, ',', '.')) }}">
                                    <input type="hidden" name="fee_pelaksana" id="fee_pelaksana"
                                        value="{{ old('fee_pelaksana', $item->fee_pelaksana ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fee-card">
                        <div class="d-flex align-items-start">
                            <div class="fee-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="flex-grow-1">
                                <label for="biaya_bahan" class="fee-label">Biaya Bahan</label>
                                <div class="fee-description">Biaya bahan habis pakai untuk pemeriksaan</div>
                                <div class="input-group mt-2">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="biaya_bahan_display"
                                        value="{{ old('biaya_bahan', number_format($item->biaya_bahan ?? 0, 0, ',', '.')) }}">
                                    <input type="hidden" name="biaya_bahan" id="biaya_bahan"
                                        value="{{ old('biaya_bahan', $item->biaya_bahan ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fee-card">
                        <div class="d-flex align-items-start">
                            <div class="fee-icon">
                                <i class="fas fa-hospital"></i>
                            </div>
                            <div class="flex-grow-1">
                                <label for="jasa_sarana" class="fee-label">Jasa Sarana</label>
                                <div class="fee-description">Jasa sarana rumah sakit/klinik</div>
                                <div class="input-group mt-2">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="jasa_sarana_display"
                                        value="{{ old('jasa_sarana', number_format($item->jasa_sarana ?? 0, 0, ',', '.')) }}">
                                    <input type="hidden" name="jasa_sarana" id="jasa_sarana"
                                        value="{{ old('jasa_sarana', $item->jasa_sarana ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('jenis-pemeriksaan.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Info Card -->
        <div class="col-md-5">
            <div class="info-card">
                <h3><i class="fas fa-file-medical-alt me-2"></i>Informasi Pemeriksaan</h3>
                <div class="info-item">
                    <i class="fas fa-tag"></i>
                    <strong>Nama:</strong>
                    <span id="preview_name">{{ $item->name }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clipboard-list"></i>
                    <strong>Tipe:</strong>
                    <span id="preview_type">{{ $item->type == 'lab' ? 'Laboratorium' : 'Radiologi' }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-money-bill"></i>
                    <strong>Harga:</strong>
                    <span id="preview_harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                </div>
                <hr style="border-color: rgba(255,255,255,0.3); margin: 20px 0;">
                <h3 style="font-size: 1rem; margin-bottom: 15px;"><i class="fas fa-hand-holding-usd me-2"></i>Struktur
                    Harga
                </h3>
                <div class="info-item">
                    <i class="fas fa-user-md"></i>
                    <strong>Dokter:</strong>
                    <span id="preview_fee_dokter">Rp
                        {{ number_format($item->fee_dokter_penunjang ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-user-nurse"></i>
                    <strong>Perawat:</strong>
                    <span id="preview_fee_perawat">Rp
                        {{ number_format($item->fee_perawat_penunjang ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-flask"></i>
                    <strong>Pelaksana:</strong>
                    <span id="preview_fee_pelaksana">Rp {{ number_format($item->fee_pelaksana ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-box"></i>
                    <strong>Biaya Bahan:</strong>
                    <span id="preview_biaya_bahan">Rp {{ number_format($item->biaya_bahan ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-hospital"></i>
                    <strong>Jasa Sarana:</strong>
                    <span id="preview_jasa_sarana">Rp {{ number_format($item->jasa_sarana ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="info-item" style="background: rgba(255,255,255,0.2); margin-top: 10px;">
                    <i class="fas fa-calculator"></i>
                    <strong>Total Harga:</strong>
                    <span id="preview_total_fee">Rp
                        {{ number_format(($item->fee_dokter_penunjang ?? 0) + ($item->fee_perawat_penunjang ?? 0) + ($item->fee_pelaksana ?? 0) + ($item->biaya_bahan ?? 0) + ($item->jasa_sarana ?? 0), 0, ',', '.') }}</span>
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

            function updateTotalFee() {
                let feeDokter = parseInt($('#fee_dokter_penunjang').val()) || 0;
                let feePerawat = parseInt($('#fee_perawat_penunjang').val()) || 0;
                let feePelaksana = parseInt($('#fee_pelaksana').val()) || 0;
                let biayaBahan = parseInt($('#biaya_bahan').val()) || 0;
                let jasaSarana = parseInt($('#jasa_sarana').val()) || 0;
                let total = feeDokter + feePerawat + feePelaksana + biayaBahan + jasaSarana;

                // Update preview total
                $('#preview_total_fee').text('Rp ' + formatRupiah(total.toString()));

                // Update harga pemeriksaan otomatis
                $('#harga_display').val(formatRupiah(total.toString()));
                $('#harga').val(total);
                $('#preview_harga').text('Rp ' + formatRupiah(total.toString()));
            }

            // Set initial values correctly on page load
            let initialDisplayValue = $('#harga_display').val();
            let initialRealValue = initialDisplayValue.replace(/\./g, '');
            $('#harga').val(initialRealValue);

            let feeDokterDisplay = $('#fee_dokter_display').val();
            $('#fee_dokter_penunjang').val(feeDokterDisplay.replace(/\./g, ''));

            let feePerawatDisplay = $('#fee_perawat_display').val();
            $('#fee_perawat_penunjang').val(feePerawatDisplay.replace(/\./g, ''));

            let feePelaksanaDisplay = $('#fee_pelaksana_display').val();
            $('#fee_pelaksana').val(feePelaksanaDisplay.replace(/\./g, ''));

            let biayaBahanDisplay = $('#biaya_bahan_display').val();
            $('#biaya_bahan').val(biayaBahanDisplay.replace(/\./g, ''));

            let jasaSaranaDisplay = $('#jasa_sarana_display').val();
            $('#jasa_sarana').val(jasaSaranaDisplay.replace(/\./g, ''));

            // Update harga saat halaman load
            updateTotalFee();

            // Live preview for name
            $('#name').on('keyup', function() {
                $('#preview_name').text($(this).val() || '{{ $item->name }}');
            });

            // Live preview for type
            $('#type').on('change', function() {
                let type = $(this).val() == 'lab' ? 'Laboratorium' : 'Radiologi';
                $('#preview_type').text(type);
            });

            // Harga with live preview
            $('#harga_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#harga').val(realValue);
                $('#preview_harga').text('Rp ' + formatRupiah(displayValue));
            });

            // Fee Dokter with live preview
            $('#fee_dokter_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#fee_dokter_penunjang').val(realValue);
                $('#preview_fee_dokter').text('Rp ' + formatRupiah(displayValue));
                updateTotalFee();
            });

            // Fee Perawat with live preview
            $('#fee_perawat_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#fee_perawat_penunjang').val(realValue);
                $('#preview_fee_perawat').text('Rp ' + formatRupiah(displayValue));
                updateTotalFee();
            });

            // Fee Pelaksana with live preview
            $('#fee_pelaksana_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#fee_pelaksana').val(realValue);
                $('#preview_fee_pelaksana').text('Rp ' + formatRupiah(displayValue));
                updateTotalFee();
            });

            // Biaya Bahan with live preview
            $('#biaya_bahan_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#biaya_bahan').val(realValue);
                $('#preview_biaya_bahan').text('Rp ' + formatRupiah(displayValue));
                updateTotalFee();
            });

            // Jasa Sarana with live preview
            $('#jasa_sarana_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#jasa_sarana').val(realValue);
                $('#preview_jasa_sarana').text('Rp ' + formatRupiah(displayValue));
                updateTotalFee();
            });
        });
    </script>
@endpush
