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

                        <!-- Insentif Tenaga Medis -->
                        <div class="setting-section">
                            <div class="section-header">
                                <div class="section-icon icon-primary">
                                    <i class="ri-user-heart-line"></i>
                                </div>
                                <div class="section-title">
                                    <h5>Insentif Tenaga Medis</h5>
                                    <small>Pengaturan insentif per kunjungan pasien</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="ri-nurse-line text-primary"></i> Insentif Perawat per Jenis Pelayanan
                                </label>

                                <!-- Rawat Jalan -->
                                <div class="mb-3">
                                    <label for="perawat_per_encounter_rawat_jalan_display" class="form-label">
                                        Rawat Jalan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text"
                                            class="form-control @error('perawat_per_encounter_rawat_jalan') is-invalid @enderror"
                                            id="perawat_per_encounter_rawat_jalan_display"
                                            value="{{ old('perawat_per_encounter_rawat_jalan', isset($settings['perawat_per_encounter_rawat_jalan']) ? number_format($settings['perawat_per_encounter_rawat_jalan'], 0, ',', '.') : '') }}"
                                            placeholder="0">
                                    </div>
                                    <input type="hidden" name="perawat_per_encounter_rawat_jalan"
                                        id="perawat_per_encounter_rawat_jalan"
                                        value="{{ old('perawat_per_encounter_rawat_jalan', isset($settings['perawat_per_encounter_rawat_jalan']) ? (int) $settings['perawat_per_encounter_rawat_jalan'] : '') }}">
                                    @error('perawat_per_encounter_rawat_jalan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- IGD -->
                                <div class="mb-3">
                                    <label for="perawat_per_encounter_igd_display" class="form-label">
                                        IGD (Instalasi Gawat Darurat)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text"
                                            class="form-control @error('perawat_per_encounter_igd') is-invalid @enderror"
                                            id="perawat_per_encounter_igd_display"
                                            value="{{ old('perawat_per_encounter_igd', isset($settings['perawat_per_encounter_igd']) ? number_format($settings['perawat_per_encounter_igd'], 0, ',', '.') : '') }}"
                                            placeholder="0">
                                    </div>
                                    <input type="hidden" name="perawat_per_encounter_igd" id="perawat_per_encounter_igd"
                                        value="{{ old('perawat_per_encounter_igd', isset($settings['perawat_per_encounter_igd']) ? (int) $settings['perawat_per_encounter_igd'] : '') }}">
                                    @error('perawat_per_encounter_igd')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Rawat Inap -->
                                <div class="mb-0">
                                    <label for="perawat_per_encounter_rawat_inap_display" class="form-label">
                                        Rawat Inap (per tindakan)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text"
                                            class="form-control @error('perawat_per_encounter_rawat_inap') is-invalid @enderror"
                                            id="perawat_per_encounter_rawat_inap_display"
                                            value="{{ old('perawat_per_encounter_rawat_inap', isset($settings['perawat_per_encounter_rawat_inap']) ? number_format($settings['perawat_per_encounter_rawat_inap'], 0, ',', '.') : '') }}"
                                            placeholder="0">
                                    </div>
                                    <input type="hidden" name="perawat_per_encounter_rawat_inap"
                                        id="perawat_per_encounter_rawat_inap"
                                        value="{{ old('perawat_per_encounter_rawat_inap', isset($settings['perawat_per_encounter_rawat_inap']) ? (int) $settings['perawat_per_encounter_rawat_inap'] : '') }}">
                                    @error('perawat_per_encounter_rawat_inap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text mt-2">
                                    <i class="ri-information-line"></i>
                                    Nominal yang diberikan kepada setiap perawat berdasarkan jenis pelayanan
                                </div>
                            </div>

                            <div class="mb-0">
                                <label for="dokter_per_encounter_display" class="form-label">
                                    <i class="ri-stethoscope-line text-primary"></i> Insentif Dokter per Pasien
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text"
                                        class="form-control @error('dokter_per_encounter') is-invalid @enderror"
                                        id="dokter_per_encounter_display"
                                        value="{{ old('dokter_per_encounter', isset($settings['dokter_per_encounter']) ? number_format($settings['dokter_per_encounter'], 0, ',', '.') : '') }}"
                                        placeholder="0">
                                </div>
                                <input type="hidden" name="dokter_per_encounter" id="dokter_per_encounter"
                                    value="{{ old('dokter_per_encounter', isset($settings['dokter_per_encounter']) ? (int) $settings['dokter_per_encounter'] : '') }}">
                                <div class="form-text">
                                    <i class="ri-information-line"></i>
                                    Nominal yang diberikan kepada dokter yang menangani 1 pasien hingga selesai
                                </div>
                                @error('dokter_per_encounter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fee Layanan Penunjang -->
                        <div class="setting-section">
                            <div class="section-header">
                                <div class="section-icon icon-success">
                                    <i class="ri-microscope-line"></i>
                                </div>
                                <div class="section-title">
                                    <h5>Fee Layanan Penunjang</h5>
                                    <small>Fee untuk pemeriksaan laboratorium dan radiologi</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ri-test-tube-line text-success"></i> Fee Laboratorium
                                </label>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <select class="form-select" name="fee_lab_mode" id="fee_lab_mode">
                                            <option value="0"
                                                {{ old('fee_lab_mode', $settings['fee_lab_mode'] ?? 1) == 0 ? 'selected' : '' }}>
                                                💰 Flat (Rp)</option>
                                            <option value="1"
                                                {{ old('fee_lab_mode', $settings['fee_lab_mode'] ?? 1) == 1 ? 'selected' : '' }}>
                                                📊 Persentase (%)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text"
                                                id="lab-prefix">{{ old('fee_lab_mode', $settings['fee_lab_mode'] ?? 1) == 0 ? 'Rp' : '%' }}</span>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                name="fee_lab_value"
                                                value="{{ old('fee_lab_value', $settings['fee_lab_value'] ?? 10) }}"
                                                placeholder="Masukkan nilai" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <i class="ri-information-line"></i>
                                    Fee diberikan untuk setiap pemeriksaan laboratorium yang dilakukan
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ri-image-line text-success"></i> Fee Radiologi (Dokter)
                                </label>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <select class="form-select" name="fee_radiologi_mode" id="fee_radiologi_mode">
                                            <option value="0"
                                                {{ old('fee_radiologi_mode', $settings['fee_radiologi_mode'] ?? 1) == 0 ? 'selected' : '' }}>
                                                💰 Flat (Rp)</option>
                                            <option value="1"
                                                {{ old('fee_radiologi_mode', $settings['fee_radiologi_mode'] ?? 1) == 1 ? 'selected' : '' }}>
                                                📊 Persentase (%)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text"
                                                id="radiologi-prefix">{{ old('fee_radiologi_mode', $settings['fee_radiologi_mode'] ?? 1) == 0 ? 'Rp' : '%' }}</span>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                name="fee_radiologi_value"
                                                value="{{ old('fee_radiologi_value', $settings['fee_radiologi_value'] ?? 10) }}"
                                                placeholder="Masukkan nilai" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <i class="ri-information-line"></i>
                                    Fee diberikan untuk setiap pemeriksaan radiologi (Rontgen, USG, CT Scan, dll)
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">
                                    <i class="ri-nurse-line text-success"></i> Fee Radiologi (Perawat)
                                </label>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <select class="form-select" name="perawat_fee_radiologi_mode"
                                            id="perawat_fee_radiologi_mode">
                                            <option value="0"
                                                {{ old('perawat_fee_radiologi_mode', $settings['perawat_fee_radiologi_mode'] ?? 1) == 0 ? 'selected' : '' }}>
                                                💰 Flat (Rp)</option>
                                            <option value="1"
                                                {{ old('perawat_fee_radiologi_mode', $settings['perawat_fee_radiologi_mode'] ?? 1) == 1 ? 'selected' : '' }}>
                                                📊 Persentase (%)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text"
                                                id="perawat-radiologi-prefix">{{ old('perawat_fee_radiologi_mode', $settings['perawat_fee_radiologi_mode'] ?? 1) == 0 ? 'Rp' : '%' }}</span>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                name="perawat_fee_radiologi_value"
                                                value="{{ old('perawat_fee_radiologi_value', $settings['perawat_fee_radiologi_value'] ?? 5) }}"
                                                placeholder="Masukkan nilai" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <i class="ri-information-line"></i>
                                    Fee diberikan kepada perawat yang membantu proses pemeriksaan radiologi
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
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                name="fee_obat_value"
                                                value="{{ old('fee_obat_value', $settings['fee_obat_value'] ?? 5) }}"
                                                placeholder="Masukkan nilai" />
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
                                        value="{{ old('cutoff_day', $settings['cutoff_day'] ?? 25) }}"
                                        placeholder="1-28">
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

            // Format currency for all nurse incentive fields
            $('#perawat_per_encounter_rawat_jalan_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#perawat_per_encounter_rawat_jalan').val(realValue);
            });

            $('#perawat_per_encounter_igd_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#perawat_per_encounter_igd').val(realValue);
            });

            $('#perawat_per_encounter_rawat_inap_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#perawat_per_encounter_rawat_inap').val(realValue);
            });

            $('#dokter_per_encounter_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#dokter_per_encounter').val(realValue);
            });

            // Update prefix saat mode berubah
            $('#fee_lab_mode').on('change', function() {
                if ($(this).val() == '0') {
                    $('#lab-prefix').text('Rp');
                } else {
                    $('#lab-prefix').text('%');
                }
            });

            $('#fee_radiologi_mode').on('change', function() {
                if ($(this).val() == '0') {
                    $('#radiologi-prefix').text('Rp');
                } else {
                    $('#radiologi-prefix').text('%');
                }
            });

            $('#perawat_fee_radiologi_mode').on('change', function() {
                if ($(this).val() == '0') {
                    $('#perawat-radiologi-prefix').text('Rp');
                } else {
                    $('#perawat-radiologi-prefix').text('%');
                }
            });

            $('#fee_obat_mode').on('change', function() {
                if ($(this).val() == '0') {
                    $('#obat-prefix').text('Rp');
                } else {
                    $('#obat-prefix').text('%');
                }
            });
        });
    </script>
@endpush
