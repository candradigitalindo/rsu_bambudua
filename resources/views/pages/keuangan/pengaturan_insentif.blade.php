@extends('layouts.app')
@section('title')
    Pengaturan Insentif
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        a.disabled {
            color: gray;
            pointer-events: none;
        }

        .info-card {
            background: #238781;
            border-radius: 15px;
            padding: 25px;
            color: white;
            margin-bottom: 25px;
            box-shadow: 0 8px 20px rgba(35, 135, 129, 0.25);
        }

        .info-card h4 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .info-card p {
            margin-bottom: 0;
            opacity: 0.95;
            line-height: 1.6;
        }

        .setting-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .setting-section:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-right: 15px;
        }

        .icon-primary {
            background: #238781;
            color: white;
        }

        .icon-success {
            background: #238781;
            color: white;
        }

        .icon-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .icon-warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .section-title {
            flex: 1;
        }

        .section-title h5 {
            margin: 0;
            font-weight: 600;
            color: #2d3748;
        }

        .section-title small {
            color: #718096;
            font-size: 13px;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #238781;
            box-shadow: 0 0 0 3px rgba(35, 135, 129, 0.1);
        }

        .form-text {
            color: #718096;
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: start;
        }

        .form-text i {
            margin-right: 6px;
            margin-top: 2px;
            font-size: 14px;
        }

        .btn-primary {
            background: #238781;
            border: none;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1a6b66;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(35, 135, 129, 0.3);
        }

        .fee-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }

        .badge-flat {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-percent {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>
@endpush

@section('content')
    <!-- Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="info-card">
                <div class="d-flex align-items-center">
                    <i class="ri-settings-3-line" style="font-size: 40px; margin-right: 20px;"></i>
                    <div>
                        <h4><i class="ri-money-dollar-circle-line"></i> Pengaturan Insentif & Fee</h4>
                        <p>Kelola pengaturan insentif untuk tenaga medis dan fee untuk berbagai layanan. Perubahan akan
                            diterapkan untuk perhitungan periode berikutnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <form action="{{ route('keuangan.incentive.settings.simpan') }}" method="POST" id="form-insentif">
                        @csrf

                        <!-- Info: Insentif telah dipindahkan ke Master Data Tindakan -->
                        <div class="setting-section" style="background: #e8f5e9; border-left: 4px solid #238781;">
                            <div class="d-flex align-items-start">
                                <div class="me-3" style="font-size: 32px; color: #238781;">
                                    <i class="ri-information-line"></i>
                                </div>
                                <div>
                                    <h5 class="mb-2" style="color: #238781;">
                                        <i class="ri-arrow-right-line"></i> Insentif Tenaga Medis Telah Dipindahkan
                                    </h5>
                                    <p class="mb-2" style="color: #2d5016;">
                                        <strong>Honor Dokter</strong> dan <strong>Bonus Perawat</strong> sekarang diatur
                                        langsung di <strong>Master Data Tindakan</strong> pada menu:
                                    </p>
                                    <div class="ms-3 mb-2">
                                        <p class="mb-1" style="color: #2d5016;">
                                            <i class="ri-corner-down-right-line"></i>
                                            <strong>Master Data → Data Tindakan</strong>
                                        </p>
                                    </div>
                                    <p class="mb-2" style="color: #2d5016;">
                                        Setiap tindakan dapat memiliki nilai honor/bonus yang berbeda sesuai dengan
                                        tingkat kesulitan dan jenis pelayanan yang diberikan.
                                    </p>
                                    <div class="alert alert-info mb-0 mt-3" style="border-left: 3px solid #0288d1;">
                                        <small>
                                            <i class="ri-lightbulb-line"></i>
                                            <strong>Keuntungan:</strong> Sistem lebih fleksibel dan honor/bonus dihitung
                                            otomatis berdasarkan tindakan yang dilakukan.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info: Fee Penunjang telah dipindahkan ke Master Data -->
                        <div class="setting-section" style="background: #e3f2fd; border-left: 4px solid #2196f3;">
                            <div class="d-flex align-items-start">
                                <div class="me-3" style="font-size: 32px; color: #2196f3;">
                                    <i class="ri-information-line"></i>
                                </div>
                                <div>
                                    <h5 class="mb-2" style="color: #1565c0;">
                                        <i class="ri-arrow-right-line"></i> Fee Layanan Penunjang Telah Dipindahkan
                                    </h5>
                                    <p class="mb-2" style="color: #0d47a1;">
                                        <strong>Fee Laboratorium</strong> dan <strong>Fee Radiologi</strong> sekarang diatur
                                        langsung di <strong>Master Data Jenis Pemeriksaan Penunjang</strong> pada menu:
                                    </p>
                                    <div class="ms-3 mb-2">
                                        <p class="mb-1" style="color: #0d47a1;">
                                            <i class="ri-corner-down-right-line"></i>
                                            <strong>Master Data → Jenis Pemeriksaan Penunjang</strong>
                                        </p>
                                    </div>
                                    <p class="mb-2" style="color: #0d47a1;">
                                        Setiap jenis pemeriksaan dapat memiliki struktur harga yang berbeda mencakup:
                                    </p>
                                    <ul class="mb-2" style="color: #0d47a1;">
                                        <li><strong>Fee Dokter Penunjang</strong> - untuk dokter yang request</li>
                                        <li><strong>Fee Perawat Penunjang</strong> - untuk perawat yang membantu</li>
                                        <li><strong>Fee Pelaksana</strong> - untuk petugas lab/radiologi</li>
                                        <li><strong>Biaya Bahan</strong> - biaya bahan habis pakai</li>
                                        <li><strong>Jasa Sarana</strong> - jasa rumah sakit/klinik</li>
                                    </ul>
                                    <div class="alert alert-info mb-0 mt-3" style="border-left: 3px solid #2196f3;">
                                        <small>
                                            <i class="ri-lightbulb-line"></i>
                                            <strong>Keuntungan:</strong> Sistem lebih fleksibel dan fee dihitung
                                            otomatis berdasarkan jenis pemeriksaan yang dilakukan. Harga pemeriksaan
                                            otomatis menyesuaikan dengan total struktur harga.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Farmasi -->
                        <div class="setting-section">
                            <div class="section-header">
                                <div class="section-icon icon-info">
                                    <i class="ri-capsule-line"></i>
                                </div>
                                <div class="section-title">
                                    <h5>Fee Farmasi</h5>
                                    <small>Pengaturan fee untuk resep obat</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ri-medicine-bottle-line text-info"></i> Nilai Fee Obat
                                </label>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <select class="form-select" name="fee_obat_mode" id="fee_obat_mode">
                                            <option value="0"
                                                {{ old('fee_obat_mode', $settings['fee_obat_mode'] ?? 1) == 0 ? 'selected' : '' }}>
                                                💰 Flat (Rp)</option>
                                            <option value="1"
                                                {{ old('fee_obat_mode', $settings['fee_obat_mode'] ?? 1) == 1 ? 'selected' : '' }}>
                                                📊 Persentase (%)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text"
                                                id="obat-prefix">{{ old('fee_obat_mode', $settings['fee_obat_mode'] ?? 1) == 0 ? 'Rp' : '%' }}</span>
                                            <input type="text" class="form-control" id="fee_obat_value_display"
                                                value="{{ old('fee_obat_mode', $settings['fee_obat_mode'] ?? 1) == 0 ? number_format(old('fee_obat_value', $settings['fee_obat_value'] ?? 5), 0, ',', '.') : old('fee_obat_value', $settings['fee_obat_value'] ?? 5) }}"
                                                placeholder="Masukkan nilai" />
                                            <input type="hidden" name="fee_obat_value" id="fee_obat_value_hidden"
                                                value="{{ old('fee_obat_value', $settings['fee_obat_value'] ?? 5) }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">
                                    <i class="ri-user-star-line text-info"></i> Target Penerima Fee
                                </label>
                                <select class="form-select" name="fee_obat_target_mode">
                                    <option value="0"
                                        {{ old('fee_obat_target_mode', $settings['fee_obat_target_mode'] ?? 0) == 0 ? 'selected' : '' }}>
                                        👨‍⚕️ DPJP (Dokter Penanggung Jawab Pelayanan)
                                    </option>
                                    <option value="1"
                                        {{ old('fee_obat_target_mode', $settings['fee_obat_target_mode'] ?? 0) == 1 ? 'selected' : '' }}>
                                        💊 Dokter Prescriber (Penulis Resep)
                                    </option>
                                </select>
                                <div class="form-text">
                                    <i class="ri-information-line"></i>
                                    Fee obat akan diberikan kepada dokter sesuai pilihan. Jika prescriber tidak tersedia,
                                    otomatis ke DPJP
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Periode -->
                        <div class="setting-section">
                            <div class="section-header">
                                <div class="section-icon icon-warning">
                                    <i class="ri-calendar-check-line"></i>
                                </div>
                                <div class="section-title">
                                    <h5>Periode Perhitungan</h5>
                                    <small>Pengaturan tanggal cut-off gaji bulanan</small>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label for="cutoff_day" class="form-label">
                                    <i class="ri-calendar-2-line text-warning"></i> Tanggal Cut-off Gaji
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                    <input type="text" class="form-control @error('cutoff_day') is-invalid @enderror"
                                        id="cutoff_day" name="cutoff_day"
                                        value="{{ old('cutoff_day', $settings['cutoff_day'] ?? 25) }}" placeholder="1-28">
                                    <span class="input-group-text">setiap bulan</span>
                                </div>
                                <div class="form-text">
                                    <i class="ri-information-line"></i>
                                    <span>Tanggal batas perhitungan insentif. <strong>Contoh:</strong> Jika diisi
                                        <code>25</code>, maka periode gaji dari tanggal <strong>26 bulan lalu</strong> s/d
                                        <strong>25 bulan ini</strong></span>
                                </div>
                                @error('cutoff_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    <i class="ri-information-line"></i>
                                    <small>Pastikan semua pengaturan sudah sesuai sebelum menyimpan</small>
                                </div>
                                <button type="submit" class="btn btn-primary" id="btn-simpan">
                                    <i class="ri-save-line"></i>
                                    <span class="btn-text">Simpan Pengaturan</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="ri-lightbulb-line text-warning"></i> Informasi Penting</h6>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1"><strong>💰 Mode Flat:</strong></small>
                        <small class="text-muted">Fee diberikan dalam nominal tetap (Rupiah) tanpa melihat nilai
                            transaksi</small>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1"><strong>📊 Mode Persentase:</strong></small>
                        <small class="text-muted">Fee dihitung berdasarkan persentase dari nilai total layanan</small>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1"><strong>📅 Cut-off Date:</strong></small>
                        <small class="text-muted">Menentukan periode perhitungan gaji. Biasanya diatur di akhir bulan
                            (25-28)</small>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm"
                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div class="card-body">
                    <h6 class="fw-bold mb-2"><i class="ri-alert-line"></i> Perhatian</h6>
                    <small>Perubahan pengaturan akan mempengaruhi perhitungan insentif pada periode berikutnya. Pastikan
                        untuk mengkomunikasikan perubahan kepada tim terkait.</small>
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

            function unformatRupiah(angka) {
                return angka.replace(/\./g, '');
            }

            // Update prefix dan format saat mode berubah
            $('#fee_obat_mode').on('change', function() {
                var currentValue = $('#fee_obat_value_hidden').val();

                if ($(this).val() == '0') {
                    // Mode Flat (Rp) - tampilkan format ribuan
                    $('#obat-prefix').text('Rp');
                    $('#fee_obat_value_display').val(formatRupiah(currentValue));
                } else {
                    // Mode Persentase - tampilkan angka biasa
                    $('#obat-prefix').text('%');
                    $('#fee_obat_value_display').val(currentValue);
                }
            });

            // Format input saat mengetik
            $('#fee_obat_value_display').on('keyup', function() {
                var mode = $('#fee_obat_mode').val();
                var input = $(this).val();

                if (mode == '0') {
                    // Mode Flat - format sebagai rupiah
                    var formatted = formatRupiah(input);
                    $(this).val(formatted);
                    $('#fee_obat_value_hidden').val(unformatRupiah(formatted));
                } else {
                    // Mode Persentase - simpan langsung
                    $(this).val(input.replace(/[^0-9]/g, ''));
                    $('#fee_obat_value_hidden').val(input.replace(/[^0-9]/g, ''));
                }
            });

            // Set nilai awal berdasarkan mode
            var initialMode = $('#fee_obat_mode').val();
            if (initialMode == '0') {
                var currentDisplay = $('#fee_obat_value_display').val();
                $('#fee_obat_value_display').val(formatRupiah(unformatRupiah(currentDisplay)));
            }
        });
    </script>
@endpush
