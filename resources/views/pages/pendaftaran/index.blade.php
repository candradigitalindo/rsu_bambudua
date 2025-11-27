@extends('layouts.app')
@section('title', 'Pendaftaran')
@push('style')
    <!-- Existing CSS -->
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
    <style>
        /* Fix for modal backdrop issues - prevent persistent black overlay */
        .modal-backdrop {
            display: none !important;
        }

        /* Ensure modal displays correctly without backdrop conflicts */
        .modal {
            background-color: transparent;
        }

        /* Only apply backdrop styling to actively shown modals */
        .modal.show {
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Ensure modal dialogs remain interactive */
        .modal .modal-dialog {
            pointer-events: auto;
            position: relative;
            z-index: 1060;
        }

        /* Prevent body scrolling when modal is open */
        body.modal-open {
            overflow: hidden;
        }

        /* Ensure main content is not affected by modal overlay */
        .main-content,
        .container-fluid,
        .row,
        .card,
        .tab-content {
            position: relative;
            z-index: auto;
            background-color: inherit;
        }

        /* Prevent any residual overlay effects on page content */
        body:not(.modal-open) {
            background-color: #f8f9fa;
            overflow: auto !important;
        }

        /* Fix for any persistent dark overlay */
        body::before,
        body::after {
            display: none !important;
        }

        /* Ensure tab content is always visible */
        .tab-content,
        .tab-pane {
            background-color: white;
            min-height: 100px;
        }

        /* Force visibility of main content elements */
        .card-body,
        .table,
        .btn,
        .form-control,
        .form-select {
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Prevent any dark overlays from covering content */
        .main-container,
        .content-wrapper {
            position: relative;
            z-index: 1;
            background: white;
        }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <!-- Row starts -->

        <div class="col-sm-9 col-12">
            <div class="card border mb-4 mt-3 shadow-sm">
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-pasien"
                            id="loket">
                            <i class="ri-folder-user-fill"></i>
                            <span class="btn-text" id="text-loket">Buka Data Pasien</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spiner-loket"></span>

                        </button>
                    </div>
                    {{-- Modal Pencarian Pasien dengan Komponen Baru --}}
                    <x-modal id="modal-pasien" title="Data Pasien" icon="ri-folder-user-fill" size="modal-xl" scrollable
                        backdrop="false" keyboard="false">

                        {{-- Advanced Search Component --}}
                        <x-search.advanced name="q" placeholder="Cari Nama, RM, RM Lama, No HP, KTP..."
                            ajax-url="{{ route('pendaftaran.caripasien') }}" result-container="data" :show-button="false"
                            debounce="300" min-length="2" input-class="form-control-lg" container-class="mb-4" />

                        {{-- Search Results --}}
                        <div id="data" class="search-results">
                            {{-- Results will be populated via AJAX --}}
                        </div>

                        {{-- Loading Component --}}
                        @include('components.loading', [
                            'id' => 'search-loading',
                            'message' => 'Mencari data pasien...',
                            'style' => 'display: none;',
                        ])

                        <x-slot name="footerButtons">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#form-pasien" id="btn-buatPasienBaru">
                                <i class="ri-user-add-fill"></i>
                                <span class="btn-text">Buat Data Pasien Baru</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </x-slot>
                    </x-modal>
                    {{-- Modal Edit Pasien dengan Komponen Baru --}}
                    <x-modal id="form-edit-pasien" title="Form Edit Data Pasien" icon="ri-user-edit-line" size="modal-xl"
                        scrollable backdrop="false" keyboard="false">

                        {{-- Error Alert Component --}}
                        <div id="error-edit-container"></div>

                        <form id="formpasien">
                            <div class="row gx-3">
                                <div class="col-xxl-2 col-lg-4 col-sm-6">
                                    <x-form.select name="jenis_identitas" label="Jenis Identitas"
                                        placeholder="Pilih Jenis Identitas" :options="[
                                            '1' => 'KTP',
                                            '2' => 'SIM',
                                            '3' => 'Paspor',
                                        ]" id="jenis_identitas_edit" />
                                </div>
                                <div class="col-xxl-5 col-lg-4 col-sm-6">
                                    <x-form.input name="no_identitas" label="Nomor Identitas" id="no_identitas_edit" />
                                </div>
                                <div class="col-xxl-5 col-lg-4 col-sm-6">
                                    <x-form.input name="name_pasien" label="Nama Pasien" required id="name_pasien_edit" />
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select name="jenis_kelamin" label="Jenis Kelamin"
                                        placeholder="Pilih Jenis Kelamin" :options="[
                                            '1' => 'Pria',
                                            '2' => 'Wanita',
                                        ]" id="jenis_kelamin_edit"
                                        required />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.input name="tgl_lahir" type="date" label="Tanggal Lahir" id="tgl_lahir_edit"
                                        required />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select name="golongan_darah" label="Golongan Darah"
                                        placeholder="-- Pilih Gol Darah --" :options="[
                                            'A' => 'A',
                                            'B' => 'B',
                                            'AB' => 'AB',
                                            'O' => 'O',
                                        ]" id="golongan_darah_edit" />
                                </div>
                            </div>

                            {{-- Continue with remaining form fields --}}
                            <div class="row gx-3">
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select name="kewarganegaraan" label="Kewarganegaraan" :options="[
                                        '1' => 'WNI',
                                        '2' => 'WNA',
                                    ]"
                                        id="kewarganegaraan_edit" />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select name="pekerjaan" label="Pekerjaan" placeholder="-- Pilih Pekerjaan --"
                                        :options="$pekerjaan->pluck('name', 'name')" id="pekerjaan_edit" />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select name="status_menikah" label="Status Menikah"
                                        placeholder="-- Pilih Status --" :options="[
                                            '1' => 'Belum Menikah',
                                            '2' => 'Menikah',
                                            '3' => 'Cerai Hidup',
                                            '4' => 'Cerai Mati',
                                        ]" id="status_menikah_edit" />
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select name="agama" label="Agama" placeholder="-- Pilih Agama --"
                                        :options="$agama->pluck('name', 'name')" id="agama_edit" />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.input name="no_hp" type="tel" label="No Handphone" required
                                        id="no_hp_edit" />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.input name="no_telepon" type="tel" label="No Telepon"
                                        id="no_telepon_edit" />
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.input name="alamat" label="Alamat" required id="alamat_edit" />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select name="province" label="Provinsi" placeholder="-- Pilih Provinsi --"
                                        :options="$provinsi->pluck('name', 'code')" id="province_edit" />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select name="city" label="Kota / Kabupaten"
                                        placeholder="-- Pilih Provinsi dulu --" :options="[]" id="city_edit" />
                                </div>
                            </div>

                            <input type="hidden" id="id">
                        </form>

                        <x-slot name="footerButtons">
                            <button type="button" class="btn btn-secondary" data-bs-target="#modal-pasien"
                                data-bs-toggle="modal" id="btn-edit-kembali">
                                Kembali
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-edit">
                                <span class="btn-text">Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </x-slot>
                    </x-modal>
                    <div class="modal fade" id="form-pasien" tabindex="-1"
                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" data-bs-backdrop="static"
                        data-bs-keyboard="false">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">
                                        Form Data Pasien
                                    </h5>

                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger print-error-msg" style="display:none" id="error">
                                        <ul></ul>
                                    </div>
                                    <form id="formpasien">
                                        <div class="row gx-3">
                                            <div class="col-xxl-2 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Jenis Identitas</label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="jenis_identitas"
                                                            id="jenis_identitas">
                                                            <option value="">Pilih Jenis Identitas</option>
                                                            <option value="1">
                                                                KTP</option>
                                                            <option value="2">
                                                                SIM</option>
                                                            <option value="3">
                                                                Paspor</option>

                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-5 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Nomor Identitas</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_identitas"
                                                            name="no_identitas">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-5 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Nama Pasien
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="name_pasien"
                                                            name="name_pasien">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Jenis Kelamin
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="jenis_kelamin"
                                                            id="jenis_kelamin">
                                                            <option value="">Pilih Jenis Kelamin</option>
                                                            <option value="1">
                                                                Pria</option>
                                                            <option value="2">
                                                                Wanita</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Tanggal Lahir
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" id="tgl_lahir"
                                                            name="tgl_lahir">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Gol Darah</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="golongan_darah"
                                                            id="golongan_darah">
                                                            <option value="">-- Pilih Gol Darah --</option>
                                                            <option value="A">A</option>
                                                            <option value="B">B</option>
                                                            <option value="AB">AB</option>
                                                            <option value="O">O</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Kewarganegaraan

                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="kewarganegaraan"
                                                            id="kewarganegaraan">

                                                            <option value="1">
                                                                WNI</option>
                                                            <option value="2">
                                                                WNA</option>


                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Pekerjaan</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="pekerjaan" id="pekerjaan">
                                                            <option value="">-- Pilih Pekerjaan --</option>
                                                            @foreach ($pekerjaan as $p)
                                                                <option value="{{ $p->name }}">{{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Status Menikah

                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="status_menikah"
                                                            id="status_menikah">
                                                            <option value="">-- Pilih Status --</option>
                                                            <option value="1">
                                                                Belum Menikah</option>
                                                            <option value="2">
                                                                Menikah</option>
                                                            <option value="3">
                                                                Cerai Hidup</option>
                                                            <option value="4">
                                                                Cerai Mati</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Agama</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="agama" id="agama">
                                                            <option value="">-- Pilih Agama --</option>
                                                            @foreach ($agama as $a)
                                                                <option value="{{ $a->name }}">{{ $a->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">No Handphone
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_hp"
                                                            name="no_hp">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">No Telp

                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_telepon"
                                                            name="no_telepon">
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Alamat
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="alamat"
                                                            name="alamat">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Provinsi</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="province" id="province">
                                                            <option value="">-- Pilih Provinsi --</option>
                                                            @foreach ($provinsi as $p)
                                                                <option value="{{ $p->code }}">{{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Kota / Kabupaten

                                                    </label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="city" id="city">
                                                            <option value="">-- Pilih Provinsi dulu --</option>

                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-target="#modal-pasien"
                                        data-bs-toggle="modal" id="btn-kembali">
                                        Kembali
                                    </button>
                                    <button type="button" class="btn btn-primary" id="btn-simpan">
                                        Simpan
                                    </button>

                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-rawatJalan" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="exampleModalXlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-primary-subtle">
                                    <h5 class="modal-title fw-bold text-primary d-flex align-items-center"
                                        id="exampleModalXlLabel">
                                        <i class="ri-stethoscope-line me-2"></i>
                                        Pendaftaran Rawat Jalan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <!-- Patient Information Card -->
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-white border-bottom">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="mb-0 text-dark fw-semibold">📋 Informasi Pasien</h6>
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                                    <i class="ri-check-line me-1"></i>Status: <span
                                                        id="status-pasien">Aktif</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="border-end pe-md-4">
                                                        <h6 class="text-primary fw-semibold mb-3">
                                                            <i class="ri-user-line me-2"></i>Identitas Pasien
                                                        </h6>
                                                        <div class="info-list">
                                                            <div class="info-item d-flex justify-content-between mb-2">
                                                                <span class="text-muted">No. Rekam Medis:</span>
                                                                <span class="fw-medium" id="no_rm_rawatJalan">-</span>
                                                            </div>
                                                            <div class="info-item d-flex justify-content-between mb-2">
                                                                <span class="text-muted">Nama Pasien:</span>
                                                                <span class="fw-medium" id="name_rawatJalan">-</span>
                                                            </div>
                                                            <div class="info-item d-flex justify-content-between mb-2">
                                                                <span class="text-muted">Usia:</span>
                                                                <span class="fw-medium" id="tgl_lahir_rawatJalan">-</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-primary fw-semibold mb-3">
                                                        <i class="ri-history-line me-2"></i>Kunjungan Terakhir
                                                    </h6>
                                                    <div class="info-list">
                                                        <div class="info-item d-flex justify-content-between mb-2">
                                                            <span class="text-muted">No. Kunjungan:</span>
                                                            <span class="fw-medium" id="no_encounter_rawatJalan">-</span>
                                                        </div>
                                                        <div class="info-item d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Tanggal:</span>
                                                            <span class="fw-medium" id="created_rawatJalan">-</span>
                                                        </div>
                                                        <div class="info-item d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Jenis:</span>
                                                            <span class="fw-medium" id="type_rawatJalan">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Error Alert -->
                                    <div class="alert alert-danger d-none" id="error-rawatJalan">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-error-warning-line me-2"></i>
                                            <div>
                                                <strong>Terjadi Kesalahan!</strong>
                                                <ul class="mb-0 mt-1"></ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Registration Form -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white border-bottom">
                                            <h6 class="mb-0 text-dark fw-semibold">
                                                <i class="ri-file-edit-line me-2"></i>Detail Pendaftaran
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-shield-check-line me-1 text-primary"></i>
                                                        Jenis Jaminan <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select form-select-lg" name="jenis_jaminan"
                                                        id="jenis_jaminan">
                                                        <option value="">Pilih jenis jaminan kesehatan</option>
                                                        @foreach ($jenisjaminan as $jaminan)
                                                            <option value="{{ $jaminan->id }}">{{ $jaminan->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-hospital-line me-1 text-primary"></i>
                                                        Poliklinik <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select form-select-lg" name="clinic"
                                                        id="clinic">
                                                        <option value="">Pilih poliklinik tujuan</option>
                                                        @foreach ($clinics as $clinic)
                                                            <option value="{{ $clinic->id }}">{{ $clinic->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-user-heart-line me-1 text-primary"></i>
                                                        Dokter <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="border rounded p-3 bg-light" id="dokter-container">
                                                        <div class="text-muted text-center py-3" id="dokter-placeholder">
                                                            <i class="ri-hospital-line me-2"></i>
                                                            Pilih poliklinik terlebih dahulu untuk melihat daftar dokter
                                                        </div>
                                                        <div id="dokter-list" class="d-none">
                                                            <!-- Doctor checkboxes will be populated here -->
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <div class="form-text text-muted">
                                                            <i class="ri-information-line me-1"></i>
                                                            Pilih satu atau lebih dokter sesuai kebutuhan
                                                        </div>
                                                        <div id="selected-count"
                                                            class="badge bg-primary rounded-pill px-3 py-2 d-none">
                                                            <i class="ri-user-check-line me-1"></i>
                                                            <span id="count-text">0 dipilih</span>
                                                        </div>
                                                    </div>

                                                    <!-- Hidden input to store selected doctors -->
                                                    <input type="hidden" name="dokter[]" id="dokter-selected"
                                                        value="">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-compass-3-line me-1 text-primary"></i>
                                                        Tujuan Kunjungan <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select form-select-lg" name="tujuan_kunjungan"
                                                        id="tujuan_kunjungan">
                                                        <option value="">Pilih tujuan kunjungan</option>
                                                        <option value="1">🟢 Kunjungan Sehat (Promotif/Preventif)
                                                        </option>
                                                        <option value="2">🔵 Rehabilitatif</option>
                                                        <option value="3">🟡 Kunjungan Sakit</option>
                                                        <option value="4">🔴 Darurat</option>
                                                        <option value="5">🟠 Kontrol / Tindak Lanjut</option>
                                                        <option value="6">🟣 Treatment</option>
                                                        <option value="7">⚪ Konsultasi</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="id-rawatJalan">
                                </div>

                                <div class="modal-footer bg-light border-top">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                        id="btn-tutup-rawatJalan">
                                        <i class="ri-close-line me-2"></i>Batal
                                    </button>
                                    <button type="button" class="btn btn-primary btn-lg px-4"
                                        id="btn-submit-rawatJalan">
                                        <i class="ri-save-line me-2"></i>Simpan Pendaftaran
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-rawatInap" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="exampleModalXlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalXlLabel">
                                        Pendaftaran Rawat Inap
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <div class="card border mt-3">
                                        <div class="card-body">
                                            <span class="badge bg-primary-subtle rounded-pill text-primary">
                                                <i class="ri-circle-fill me-1"></i>Status : <a
                                                    id="status-pasien-rawatRinap"></a></span>
                                            <hr>
                                            <div class="row justify-content-between">
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Identitas Pasien
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>No. RM</td>
                                                            <td>:</td>
                                                            <td id="no_rm_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Nama Pasien</td>
                                                            <td>:</td>
                                                            <td id="name_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Umur</td>
                                                            <td>:</td>
                                                            <td id="tgl_lahir_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Kunjungan Terakhir
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>No. Kunjungan</td>
                                                            <td>:</td>
                                                            <td id="no_encounter_rawatRinap">XXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tanggal Kujungan</td>
                                                            <td>:</td>
                                                            <td id="created_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tipe</td>
                                                            <td>:</td>
                                                            <td id="type_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger print-error-msg mt-2 mb-2" style="display:none"
                                        id="error-rawatRinap">
                                        <ul></ul>
                                    </div>
                                    <hr>
                                    <div class="row gx-3 mt-3">
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="ri-shield-check-line me-1 text-primary"></i>
                                                    Jenis Jaminan <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select form-select-lg" name="jenis_jaminan_rawatRinap"
                                                    id="jenis_jaminan_rawatRinap">
                                                    <option value="">Pilih jenis jaminan kesehatan</option>
                                                    @foreach ($jenisjaminan as $jaminan)
                                                        <option value="{{ $jaminan->id }}">{{ $jaminan->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="dokter_rawatRinap">
                                                    <i class="ri-stethoscope-line me-1 text-primary"></i>
                                                    Dokter Spesialis (DPJP)
                                                </label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <select class="form-select" name="dokter_rawatRinap"
                                                        id="dokter_rawatRinap">
                                                        <option value="">-- Pilih Dokter Spesialis --</option>
                                                        @foreach ($doctors as $doctor)
                                                            <option value="{{ $doctor->id }}">{{ $doctor->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Ruang Rawat Inap
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="ruangan" id="ruangan_rawatRinap">
                                                        <option value="">-- Pilih Ruangan --</option>
                                                        @foreach ($ruangan as $ruang)
                                                            <option value="{{ $ruang->id }}">{{ $ruang->no_kamar }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <input type="text" style="visibility: hidden" id="id-rawatRinap">
                                    </div>
                                    <hr>
                                    <div class="row gx-3">
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Nama Pendamping
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="name_companion"
                                                        placeholder="Nama Pendamping">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">NIK Pendamping

                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="nik_companion"
                                                        placeholder="NIK Pendamping">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">No Handphone Pendamping
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="phone_companion"
                                                        placeholder="No Handphone Pendamping">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Hubungan Pendamping
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="relation_companion"
                                                        id="relation_companion">
                                                        <option value="">-- Pilih Hubungan --</option>
                                                        <option value="1">Ayah</option>
                                                        <option value="2">Ibu</option>
                                                        <option value="3">Saudara</option>
                                                        <option value="4">Suami</option>
                                                        <option value="5">Istri</option>
                                                        <option value="6">Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="btn-submit-rawatRinap">
                                        <i class="ri-user-add-fill"></i>
                                        Simpan
                                    </button>

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        id="btn-tutup-rawatRinap">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-rawatDarurat" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="exampleModalXlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-danger-subtle">
                                    <h5 class="modal-title fw-bold text-danger d-flex align-items-center"
                                        id="exampleModalXlLabel">
                                        <i class="ri-alarm-warning-line me-2"></i>
                                        Pendaftaran Rawat Darurat (IGD)
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <!-- Patient Information Card -->
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-white border-bottom">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h6 class="mb-0 text-dark fw-semibold">🏥 Informasi Pasien</h6>
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                                    <i class="ri-check-line me-1"></i>Status: <span
                                                        id="status-pasien-rawatDarurat">Aktif</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="border-end pe-md-4">
                                                        <h6 class="text-danger fw-semibold mb-3">
                                                            <i class="ri-user-line me-2"></i>Identitas Pasien
                                                        </h6>
                                                        <div class="info-list">
                                                            <div class="info-item d-flex justify-content-between mb-2">
                                                                <span class="text-muted">No. Rekam Medis:</span>
                                                                <span class="fw-medium" id="no_rm_rawatDarurat">-</span>
                                                            </div>
                                                            <div class="info-item d-flex justify-content-between mb-2">
                                                                <span class="text-muted">Nama Pasien:</span>
                                                                <span class="fw-medium" id="name_rawatDarurat">-</span>
                                                            </div>
                                                            <div class="info-item d-flex justify-content-between mb-2">
                                                                <span class="text-muted">Usia:</span>
                                                                <span class="fw-medium"
                                                                    id="tgl_lahir_rawatDarurat">-</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-danger fw-semibold mb-3">
                                                        <i class="ri-history-line me-2"></i>Kunjungan Terakhir
                                                    </h6>
                                                    <div class="info-list">
                                                        <div class="info-item d-flex justify-content-between mb-2">
                                                            <span class="text-muted">No. Kunjungan:</span>
                                                            <span class="fw-medium"
                                                                id="no_encounter_rawatDarurat">-</span>
                                                        </div>
                                                        <div class="info-item d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Tanggal:</span>
                                                            <span class="fw-medium" id="created_rawatDarurat">-</span>
                                                        </div>
                                                        <div class="info-item d-flex justify-content-between mb-2">
                                                            <span class="text-muted">Jenis:</span>
                                                            <span class="fw-medium" id="type_rawatDarurat">-</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Error Alert -->
                                    <div class="alert alert-danger d-none" id="error-rawatDarurat">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-error-warning-line me-2"></i>
                                            <div>
                                                <strong>Terjadi Kesalahan!</strong>
                                                <ul class="mb-0 mt-1"></ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Registration Form -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white border-bottom">
                                            <h6 class="mb-0 text-dark fw-semibold">
                                                <i class="ri-alarm-warning-line me-2 text-danger"></i>Detail Pendaftaran
                                                IGD
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-shield-check-line me-1 text-danger"></i>
                                                        Jenis Jaminan <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select form-select-lg"
                                                        name="jenis_jaminan_rawatDarurat" id="jenis_jaminan_rawatDarurat">
                                                        <option value="">Pilih jenis jaminan kesehatan</option>
                                                        @foreach ($jenisjaminan as $jaminan)
                                                            <option value="{{ $jaminan->id }}">{{ $jaminan->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-user-heart-line me-1 text-danger"></i>
                                                        Dokter Jaga <span class="text-danger">*</span>
                                                    </label>

                                                    <select class="form-select form-select-lg"
                                                        name="dokter_rawatDarurat[]" id="dokter_rawatDarurat" multiple>
                                                        <option value="">Pilih dokter jaga IGD</option>
                                                        @foreach ($doctors as $doctor)
                                                            {{-- Assuming $doctors is available --}}
                                                            <option value="{{ $doctor->id }}">{{ $doctor->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="form-text text-muted">
                                                        <i class="ri-information-line me-1"></i>
                                                        Pilih dokter jaga yang tersedia di IGD
                                                    </div>
                                                    <!-- Alternatif pemilihan cepat: checkbox + pencarian -->
                                                    <div class="mt-2">
                                                        <input type="text" id="igdDoctorSearch"
                                                            class="form-control form-control-sm mb-2"
                                                            placeholder="Cari dokter...">
                                                        <div id="igdDoctorList" class="border rounded p-2"
                                                            style="max-height: 180px; overflow-y: auto;"></div>
                                                        <small class="text-muted">Centang untuk memilih. Pilihan tersinkron
                                                            dengan dropdown di atas.</small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-alarm-line me-1 text-danger"></i>
                                                        Tingkat Kegawatan <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select form-select-lg" name="tingkat_kegawatan"
                                                        id="tingkat_kegawatan">
                                                        <option value="">Pilih tingkat kegawatan</option>
                                                        <option value="1">🔴 Level 1 - Kritis (Resusitasi)</option>
                                                        <option value="2">🟠 Level 2 - Darurat (Tidak Stabil)</option>
                                                        <option value="3">🟡 Level 3 - Urgen (Stabil)</option>
                                                        <option value="4">🟢 Level 4 - Kurang Urgen</option>
                                                        <option value="5">🔵 Level 5 - Tidak Urgen</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-car-line me-1 text-danger"></i>
                                                        Cara Datang <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select form-select-lg" name="cara_datang"
                                                        id="cara_datang">
                                                        <option value="">Pilih cara kedatangan</option>
                                                        <option value="jalan_kaki">🚶 Jalan Kaki</option>
                                                        <option value="kendaraan_pribadi">🚗 Kendaraan Pribadi</option>
                                                        <option value="ambulans">🚑 Ambulans</option>
                                                        <option value="ojek_online">🏍️ Ojek/Taksi Online</option>
                                                        <option value="transportasi_umum">🚌 Transportasi Umum</option>
                                                        <option value="rujukan">🏥 Rujukan dari Faskes</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-stethoscope-line me-1 text-danger"></i>
                                                        Keluhan Utama <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea class="form-control form-control-lg" name="keluhan_utama" id="keluhan_utama" rows="3"
                                                        placeholder="Deskripsikan keluhan utama pasien secara singkat dan jelas..."></textarea>
                                                    <div class="form-text text-muted">
                                                        <i class="ri-information-line me-1"></i>
                                                        Contoh: Nyeri dada sejak 2 jam yang lalu, sesak nafas, demam tinggi,
                                                        dll.
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        <i class="ri-compass-3-line me-1 text-danger"></i>
                                                        Tujuan Kunjungan <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select form-select-lg"
                                                        name="tujuan_kunjungan_rawatDarurat"
                                                        id="tujuan_kunjungan_rawatDarurat">
                                                        <option value="">Pilih tujuan kunjungan</option>
                                                        <option value="4">🔴 Darurat - Kondisi Kritis</option>
                                                        <option value="3">🟡 Kunjungan Sakit - Kondisi Akut</option>
                                                        <option value="6">🟣 Treatment - Perawatan Lanjutan</option>
                                                        <option value="7">⚪ Konsultasi - Pemeriksaan</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="id-rawatDarurat">
                                </div>

                                <div class="modal-footer bg-light border-top">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                        id="btn-tutup-rawatDarurat">
                                        <i class="ri-close-line me-2"></i>Batal
                                    </button>
                                    <button type="button" class="btn btn-danger btn-lg px-4"
                                        id="btn-submit-rawatDarurat">
                                        <i class="ri-alarm-warning-line me-2"></i>Daftar IGD
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Custom tabs starts -->
                    <div class="custom-tabs-container mt-5">

                        <!-- Nav tabs starts -->
                        <ul class="nav nav-tabs" id="customTab2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-rawatJalan" data-bs-toggle="tab" href="#rawatJalan"
                                    role="tab" aria-controls="oneA" aria-selected="true"><i
                                        class="ri-stethoscope-line"></i>Rawat Jalan</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-igd" data-bs-toggle="tab" href="#rawatDarurat"
                                    role="tab" aria-controls="twoA" aria-selected="false"><i
                                        class="ri-dossier-fill"></i>
                                    IGD</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-rawatInap" data-bs-toggle="tab" href="#rawatInap"
                                    role="tab" aria-controls="threeA" aria-selected="false"><i
                                        class="ri-hotel-bed-fill"></i>
                                    Rawat Inap</a>
                            </li>

                        </ul>
                        <!-- Nav tabs ends -->

                        <!-- Tab content starts -->
                        <div class="tab-content h-350">
                            <div class="tab-pane fade show active" id="rawatJalan" role="tabpanel">
                                {{-- Tabel Rawat Jalan --}}
                                <div class="col-sm-12">
                                    <div class="table-outer">
                                        <div class="table-responsive">
                                            <div
                                                class="d-flex justify-content-center align-items-center my-3 gap-3 flex-wrap px-3">
                                                <div class="input-group" style="max-width: 320px;">
                                                    <span class="input-group-text bg-white"><i
                                                            class="ri-search-line"></i></span>
                                                    <input type="text" class="form-control" id="searchRawatJalan"
                                                        placeholder="Cari RM/Nama/No Kunjungan...">
                                                </div>
                                                <div class="input-group" style="width: 200px;">
                                                    <span class="input-group-text bg-white">Urutkan</span>
                                                    <select class="form-select" id="sortRawatJalan">
                                                        <option value="created_at:desc" selected>Waktu (baru → lama)
                                                        </option>
                                                        <option value="created_at:asc">Waktu (lama → baru)</option>
                                                        <option value="no_encounter:asc">No Kunjungan (A → Z)</option>
                                                        <option value="no_encounter:desc">No Kunjungan (Z → A)</option>
                                                        <option value="name_pasien:asc">Nama (A → Z)</option>
                                                        <option value="name_pasien:desc">Nama (Z → A)</option>
                                                    </select>
                                                </div>
                                                <div class="input-group" style="width: 140px;">
                                                    <span class="input-group-text bg-white">Per page</span>
                                                    <select class="form-select" id="perPageRawatJalan">
                                                        <option value="10" selected>10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-outline-success"
                                                    id="exportRawatJalan"><i
                                                        class="ri-download-2-line me-1"></i>Export</button>
                                            </div>
                                            <table class="table truncate m-0">
                                                <thead>
                                                    <tr>
                                                        <th>Kunjungan</th>
                                                        <th>Pasien / Dokter</th>
                                                        <th>Jaminan / Tujuan</th>
                                                        <th class="text-center">Aksi</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="showRawatJalan">


                                                </tbody>
                                            </table>
                                            <div id="paginateRawatJalan" class="mt-2"></div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="tab-pane fade" id="rawatDarurat" role="tabpanel">

                                {{-- Rawat Darurat --}}
                                <div class="col-sm-12">
                                    <div class="table-outer">
                                        <div class="table-responsive">
                                            <div
                                                class="d-flex justify-content-center align-items-center my-3 gap-3 flex-wrap px-3">
                                                <div class="input-group" style="max-width: 320px;">
                                                    <span class="input-group-text bg-white"><i
                                                            class="ri-search-line"></i></span>
                                                    <input type="text" class="form-control" id="searchRawatDarurat"
                                                        placeholder="Cari RM/Nama/No Kunjungan...">
                                                </div>
                                                <div class="input-group" style="width: 200px;">
                                                    <span class="input-group-text bg-white">Urutkan</span>
                                                    <select class="form-select" id="sortRawatDarurat">
                                                        <option value="updated_at:asc" selected>Terakhir update (awal →
                                                            akhir)</option>
                                                        <option value="updated_at:desc">Terakhir update (akhir → awal)
                                                        </option>
                                                        <option value="created_at:desc">Waktu (baru → lama)</option>
                                                        <option value="created_at:asc">Waktu (lama → baru)</option>
                                                        <option value="no_encounter:asc">No Kunjungan (A → Z)</option>
                                                        <option value="no_encounter:desc">No Kunjungan (Z → A)</option>
                                                        <option value="name_pasien:asc">Nama (A → Z)</option>
                                                        <option value="name_pasien:desc">Nama (Z → A)</option>
                                                    </select>
                                                </div>
                                                <div class="input-group" style="width: 140px;">
                                                    <span class="input-group-text bg-white">Per page</span>
                                                    <select class="form-select" id="perPageRawatDarurat">
                                                        <option value="10" selected>10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-outline-success"
                                                    id="exportRawatDarurat"><i
                                                        class="ri-download-2-line me-1"></i>Export</button>
                                            </div>
                                            <table class="table truncate m-0">
                                                <thead>
                                                    <tr>
                                                        <th>Kunjungan</th>
                                                        <th>Pasien / Dokter</th>
                                                        <th>Jaminan / Tujuan</th>
                                                        <th class="text-center">Aksi</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="showRawatDarurat">


                                                </tbody>
                                            </table>
                                            <div id="paginateRawatDarurat" class="mt-2"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="rawatInap" role="tabpanel">

                                {{-- Rawat Darurat --}}
                                <div class="col-sm-12">
                                    <div class="table-outer">
                                        <div class="table-responsive">
                                            <div
                                                class="d-flex justify-content-center align-items-center my-3 gap-3 flex-wrap px-3">
                                                <div class="input-group" style="max-width: 320px;">
                                                    <span class="input-group-text bg-white"><i
                                                            class="ri-search-line"></i></span>
                                                    <input type="text" class="form-control" id="searchRawatInap"
                                                        placeholder="Cari RM/Nama/No Kunjungan...">
                                                </div>
                                                <div class="input-group" style="width: 200px;">
                                                    <span class="input-group-text bg-white">Urutkan</span>
                                                    <select class="form-select" id="sortRawatInap">
                                                        <option value="updated_at:desc" selected>Terakhir update (akhir →
                                                            awal)</option>
                                                        <option value="updated_at:asc">Terakhir update (awal → akhir)
                                                        </option>
                                                        <option value="created_at:desc">Waktu (baru → lama)</option>
                                                        <option value="created_at:asc">Waktu (lama → baru)</option>
                                                        <option value="no_encounter:asc">No Kunjungan (A → Z)</option>
                                                        <option value="no_encounter:desc">No Kunjungan (Z → A)</option>
                                                        <option value="name_pasien:asc">Nama (A → Z)</option>
                                                        <option value="name_pasien:desc">Nama (Z → A)</option>
                                                    </select>
                                                </div>
                                                <div class="input-group" style="width: 140px;">
                                                    <span class="input-group-text bg-white">Per page</span>
                                                    <select class="form-select" id="perPageRawatInap">
                                                        <option value="10" selected>10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-outline-success"
                                                    id="exportRawatInap"><i
                                                        class="ri-download-2-line me-1"></i>Export</button>
                                            </div>
                                            <table class="table truncate m-0">
                                                <thead>
                                                    <tr>
                                                        <th>Kunjungan</th>
                                                        <th>Pasien / Dokter</th>
                                                        <th>Keterangan</th>
                                                        <th class="text-center">Aksi</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="showRawatInap">


                                                </tbody>
                                            </table>
                                            <div id="paginateRawatInap" class="mt-2"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <!-- Tab content ends -->

                    </div>
                </div>
            </div>

        </div>
        <div class="col-sm-3">
            <div class="card border mb-3">
                <div class="card-body">
                    @if (auth()->user()->role == 5)
                        <!-- Card details start -->
                        <div class="text-center">
                            <div class="icon-box md border border-primary rounded-5 mb-2 m-auto">
                                <i class="ri-empathize-line fs-5 text-primary"></i>
                            </div>
                            <h6>No Antrian sekarang</h6>
                            <h3 class="text-primary display-1" id="antrian">{{ $antrian['antrian'] }}</h3>

                            <button type="submit" class="btn btn-primary mt-2" id="btn-next">
                                <i class="ri-arrow-right-double-fill"></i>
                                <span class="btn-txt" id="text-next">Antrian Selanjutnya</span>
                                <span class="spinner-border spinner-border-sm d-none" id="spinner-next"></span>
                            </button>
                            <br>
                            <small class="text-primary">Ada <span class="fw-bold"
                                    id="jumlah">{{ $antrian['jumlah'] }}</span> antrian lagi</small>

                        </div>

                        <!-- Card details end -->
                    @else
                        <h6>Fitur Antrian hanya untuk Hak Akses Akun Pendaftaran</h6>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->
@endsection
@push('style')
    <style>
        .modal-header.bg-primary-subtle {
            background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
            border-bottom: 2px solid #e3f2fd;
        }

        .info-list .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .info-list .info-item:last-child {
            border-bottom: none;
        }

        .card.shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .form-select-lg,
        .form-control-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            border: 2px solid #e9ecef;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-select-lg:focus,
        .form-control-lg:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .badge.bg-success-subtle {
            background-color: #d1e7dd !important;
            color: #0a3622 !important;
        }

        .modal-footer.bg-light {
            background-color: #f8f9fa !important;
        }

        .form-text {
            font-size: 0.875rem;
        }

        .btn-lg.px-4 {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        /* Emergency Modal Specific Styles */
        .modal-header.bg-danger-subtle {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%);
            border-bottom: 2px solid #f5c2c7;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #9c2029 100%);
            box-shadow: 0 0.25rem 0.5rem rgba(220, 53, 69, 0.4);
            transform: translateY(-1px);
        }

        .badge.bg-success-subtle {
            font-weight: 500;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #721c24;
        }

        /* Emergency field highlighting */
        .modal#modal-rawatDarurat .form-label {
            font-weight: 600;
        }

        .modal#modal-rawatDarurat .text-danger {
            font-weight: 700;
        }
    </style>
    <style>
        /* Tabel lebih nyaman dibaca */
        .table tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #fff;
            box-shadow: inset 0 -1px 0 #e9ecef;
        }

        .table td,
        .table th {
            vertical-align: top;
        }
    </style>
@endpush

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Dropzone JS -->
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>

    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/validations.js') }}"></script>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=Pqiovi6G"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    {{-- Include BambuduaUtils BEFORE main script untuk memastikan function tersedia --}}
    @include('components.scripts.utils')
    <script>
        // Helper function untuk format status yang user-friendly - GLOBAL SCOPE
        // Alias untuk BambuduaUtils.formatStatus untuk backward compatibility
        function formatStatus(status) {
            console.log('formatStatus called with:', status, typeof status);

            // Jika BambuduaUtils sudah loaded, gunakan yang dari sana
            if (window.BambuduaUtils && window.BambuduaUtils.formatStatus) {
                console.log('Using BambuduaUtils.formatStatus');
                return window.BambuduaUtils.formatStatus(status);
            }

            console.log('Using fallback formatStatus');
            // Fallback jika BambuduaUtils belum loaded
            if (status === true || status === 'true' || status === '1' || status === 1) {
                return 'Aktif';
            } else if (status === false || status === 'false' || status === '0' || status === 0) {
                return 'Non-Aktif';
            } else {
                return status || 'Aktif';
            }
        }

        $(document).ready(function() {
            console.log('Document ready - initializing modal handlers');

            // [FIX] Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    withCredentials: true
                }
            });
            console.log('CSRF token setup for AJAX:', $('meta[name="csrf-token"]').attr('content'));

            // Comprehensive modal backdrop cleanup and management
            function cleanupModalBackdrops() {
                console.log('Cleaning up modal backdrops');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'padding-right': '',
                    'overflow': '',
                    'position': '',
                    'background-color': ''
                });
            }

            // Initialize page with clean state
            cleanupModalBackdrops();

            // Handle all modal events comprehensively
            $('.modal').on('show.bs.modal', function(e) {
                console.log('Modal showing:', $(this).attr('id'));
                cleanupModalBackdrops(); // Clean before showing new modal
            });

            $('.modal').on('shown.bs.modal', function(e) {
                console.log('Modal shown:', $(this).attr('id'));
                // Ensure proper z-index and visibility
                $(this).css('z-index', 1060);
            });

            $('.modal').on('hide.bs.modal', function(e) {
                console.log('Modal hiding:', $(this).attr('id'));
            });

            $('.modal').on('hidden.bs.modal', function(e) {
                console.log('Modal hidden:', $(this).attr('id'));
                cleanupModalBackdrops();
            });

            // Handle page visibility changes
            $(document).on('visibilitychange', function() {
                if (!document.hidden) {
                    console.log('Page became visible - cleaning up backdrops');
                    cleanupModalBackdrops();
                }
            });

            // Initialize Select2 untuk single select
            $('#dokter_rawatRinap').select2({
                placeholder: "Pilih Dokter",
                allowClear: true,
                width: '100%'
            });

            // Initialize Select2 untuk multi-select IGD
            $('#dokter_rawatDarurat').select2({
                placeholder: "Pilih satu atau lebih dokter",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-rawatDarurat') // Fix for search inside modal
            });

            // Muat dokter IGD via AJAX ketika modal dibuka dan sinkronkan dengan checkbox
            function syncIgdCheckboxWithSelect() {
                const selected = [];
                $('#igdDoctorList input[type="checkbox"]:checked').each(function() {
                    selected.push($(this).val());
                });
                $('#dokter_rawatDarurat').val(selected).trigger('change');
            }

            function renderIgdDoctorCheckboxes(list) {
                const $box = $('#igdDoctorList');
                $box.empty();
                (list || []).forEach(function(d) {
                    const id = 'igd-doc-' + d.id;
                    const checked = ($('#dokter_rawatDarurat').val() || []).includes(String(d.id)) ?
                        'checked' : '';
                    $box.append(
                        '<div class="form-check form-check-sm"><input class="form-check-input" type="checkbox" value="' +
                        d.id + '" id="' + id + '" ' + checked +
                        '><label class="form-check-label" for="' + id + '">' + d.name + '</label></div>'
                        );
                });
                // Bind change handler
                $('#igdDoctorList input[type="checkbox"]').off('change').on('change', syncIgdCheckboxWithSelect);
                // Filter by search
                $('#igdDoctorSearch').off('keyup').on('keyup', function() {
                    const q = $(this).val().toLowerCase();
                    $('#igdDoctorList .form-check').each(function() {
                        const txt = $(this).text().toLowerCase();
                        $(this).toggle(txt.indexOf(q) !== -1);
                    });
                });
            }

            function loadIgdDoctors() {
                const $sel = $('#dokter_rawatDarurat');
                $.get("{{ route('pendaftaran.getAllDoctors') }}")
                    .done(function(list) {
                        $sel.find('option:not(:first)').remove();
                        (list || []).forEach(function(d) {
                            $sel.append(new Option(d.name, d.id));
                        });
                        $sel.trigger('change.select2');
                        renderIgdDoctorCheckboxes(list || []);
                    })
                    .fail(function(xhr) {
                        console.error('Gagal memuat daftar dokter IGD:', xhr.status, xhr.responseText);
                    });
            }
            $('#modal-rawatDarurat').on('shown.bs.modal', loadIgdDoctors);
            $('#dokter_rawatDarurat').on('change', function() {
                const values = $(this).val() || [];
                $('#igdDoctorList input[type="checkbox"]').each(function() {
                    $(this).prop('checked', values.includes($(this).val()));
                });
            });

            // Ensure all loading elements are properly hidden on init
            console.log('Hiding loading elements and initializing tabs');
            $('#loading, #search-loading').hide();
            $('.spinner-border').addClass('d-none');

            // Initialize tabs and load data
            rawatJalan();
            $("#tab-rawatJalan").on("click", function() {
                console.log('Rawat Jalan tab clicked');
                rawatJalan();
            });
            $("#tab-igd").on("click", function() {
                console.log('IGD tab clicked');
                rawatDarurat();
            });
            $("#tab-rawatInap").on("click", function() {
                console.log('Rawat Inap tab clicked');
                rawatInap();
            });
            $("#submit").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-update").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });

            function rawatJalan() {
                console.log('Loading Rawat Jalan data');
                $.ajax({
                    url: "{{ route('pendaftaran.showRawatJalan') }}",
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}",
                        q: $.trim($('#searchRawatJalan').val() || ''),
                        per_page: $('#perPageRawatJalan').val(),
                        order_by: ($('#sortRawatJalan').val() || 'created_at:desc').split(':')[0],
                        order_dir: ($('#sortRawatJalan').val() || 'created_at:desc').split(':')[1]
                    },
                    dataType: 'json',
                    success: function(res) {
                        console.log('Rawat Jalan data loaded successfully');
                        if (typeof res === 'string') {
                            $('#showRawatJalan').html(res);
                            $('#paginateRawatJalan').empty();
                            return;
                        }
                        $('#showRawatJalan').html(res.rows);
                        $('#paginateRawatJalan').html(res.pagination);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Rawat Jalan data:', error);
                        $('#showRawatJalan').html(
                            '<tr><td colspan="4" class="text-center text-danger">Error loading data: ' +
                            error + '</td></tr>');
                    }
                });
            }

            function rawatDarurat() {
                console.log('Loading Rawat Darurat data');
                $.ajax({
                    url: "{{ route('pendaftaran.showRawatDarurat') }}",
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}",
                        q: $.trim($('#searchRawatDarurat').val() || ''),
                        per_page: $('#perPageRawatDarurat').val(),
                        order_by: ($('#sortRawatDarurat').val() || 'updated_at:asc').split(':')[0],
                        order_dir: ($('#sortRawatDarurat').val() || 'updated_at:asc').split(':')[1]
                    },
                    dataType: 'json',
                    success: function(res) {
                        console.log('Rawat Darurat data loaded successfully');
                        if (typeof res === 'string') {
                            $('#showRawatDarurat').html(res);
                            $('#paginateRawatDarurat').empty();
                            return;
                        }
                        $('#showRawatDarurat').html(res.rows);
                        $('#paginateRawatDarurat').html(res.pagination);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Rawat Darurat data:', error);
                        $('#showRawatDarurat').html(
                            '<tr><td colspan="4" class="text-center text-danger">Error loading data: ' +
                            error + '</td></tr>');
                    }
                });
            }

            function rawatInap() {
                console.log('Loading Rawat Inap data');
                $.ajax({
                    url: "{{ route('pendaftaran.showRawatInap') }}",
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}",
                        q: $.trim($('#searchRawatInap').val() || ''),
                        per_page: $('#perPageRawatInap').val(),
                        order_by: ($('#sortRawatInap').val() || 'updated_at:desc').split(':')[0],
                        order_dir: ($('#sortRawatInap').val() || 'updated_at:desc').split(':')[1]
                    },
                    dataType: 'json',
                    success: function(res) {
                        console.log('Rawat Inap data loaded successfully');
                        if (typeof res === 'string') {
                            $('#showRawatInap').html(res);
                            $('#paginateRawatInap').empty();
                            return;
                        }
                        $('#showRawatInap').html(res.rows);
                        $('#paginateRawatInap').html(res.pagination);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading Rawat Inap data:', error);
                        $('#showRawatInap').html(
                            '<tr><td colspan="4" class="text-center text-danger">Error loading data: ' +
                            error + '</td></tr>');
                    }
                });
            }


            // Debounced search handlers for each tab
            function debounce(fn, delay) {
                let t;
                return function() {
                    clearTimeout(t);
                    t = setTimeout(fn, delay);
                };
            }

            $('#searchRawatJalan').on('keyup', debounce(function() {
                rawatJalan();
            }, 300));
            $('#searchRawatDarurat').on('keyup', debounce(function() {
                rawatDarurat();
            }, 300));
            $('#searchRawatInap').on('keyup', debounce(function() {
                rawatInap();
            }, 300));
            $('#perPageRawatJalan').on('change', function() {
                rawatJalan();
            });
            $('#perPageRawatDarurat').on('change', function() {
                rawatDarurat();
            });
            $('#perPageRawatInap').on('change', function() {
                rawatInap();
            });
            $('#sortRawatJalan').on('change', function() {
                rawatJalan();
            });
            $('#sortRawatDarurat').on('change', function() {
                rawatDarurat();
            });
            $('#sortRawatInap').on('change', function() {
                rawatInap();
            });

            // Export buttons
            function openExport(url, params) {
                const u = new URL(url, window.location.origin);
                Object.keys(params).forEach(k => {
                    if (params[k] !== undefined && params[k] !== null && params[k] !== "") u.searchParams
                        .set(k, params[k]);
                });
                window.open(u.toString(), '_blank');
            }
            $('#exportRawatJalan').on('click', function() {
                openExport("{{ route('pendaftaran.exportRawatJalan') }}", {
                    q: $.trim($('#searchRawatJalan').val() || ''),
                    order_by: ($('#sortRawatJalan').val() || 'created_at:desc').split(':')[0],
                    order_dir: ($('#sortRawatJalan').val() || 'created_at:desc').split(':')[1]
                });
            });
            $('#exportRawatDarurat').on('click', function() {
                openExport("{{ route('pendaftaran.exportRawatDarurat') }}", {
                    q: $.trim($('#searchRawatDarurat').val() || ''),
                    order_by: ($('#sortRawatDarurat').val() || 'updated_at:asc').split(':')[0],
                    order_dir: ($('#sortRawatDarurat').val() || 'updated_at:asc').split(':')[1]
                });
            });
            $('#exportRawatInap').on('click', function() {
                openExport("{{ route('pendaftaran.exportRawatInap') }}", {
                    q: $.trim($('#searchRawatInap').val() || ''),
                    order_by: ($('#sortRawatInap').val() || 'updated_at:desc').split(':')[0],
                    order_dir: ($('#sortRawatInap').val() || 'updated_at:desc').split(':')[1]
                });
            });

            // Intercept pagination clicks and reload via AJAX
            $(document).on('click',
                '#paginateRawatJalan .pagination a, #paginateRawatDarurat .pagination a, #paginateRawatInap .pagination a',
                function(e) {
                    e.preventDefault();
                    const href = $(this).attr('href');
                    if (!href) return;
                    const url = new URL(href, window.location.origin);
                    const page = url.searchParams.get('page') || 1;
                    if ($(this).closest('#paginateRawatJalan').length) {
                        $.ajax({
                            url: "{{ route('pendaftaran.showRawatJalan') }}",
                            data: {
                                q: $.trim($('#searchRawatJalan').val() || ''),
                                per_page: $('#perPageRawatJalan').val(),
                                page: page,
                                order_by: ($('#sortRawatJalan').val() || 'created_at:desc').split(':')[
                                    0],
                                order_dir: ($('#sortRawatJalan').val() || 'created_at:desc').split(':')[
                                    1]
                            },
                            dataType: 'json',
                            success: function(res) {
                                $('#showRawatJalan').html(res.rows);
                                $('#paginateRawatJalan').html(res.pagination);
                            }
                        });
                    } else if ($(this).closest('#paginateRawatDarurat').length) {
                        $.ajax({
                            url: "{{ route('pendaftaran.showRawatDarurat') }}",
                            data: {
                                q: $.trim($('#searchRawatDarurat').val() || ''),
                                per_page: $('#perPageRawatDarurat').val(),
                                page: page,
                                order_by: ($('#sortRawatDarurat').val() || 'updated_at:asc').split(':')[
                                    0],
                                order_dir: ($('#sortRawatDarurat').val() || 'updated_at:asc').split(
                                    ':')[1]
                            },
                            dataType: 'json',
                            success: function(res) {
                                $('#showRawatDarurat').html(res.rows);
                                $('#paginateRawatDarurat').html(res.pagination);
                            }
                        });
                    } else if ($(this).closest('#paginateRawatInap').length) {
                        $.ajax({
                            url: "{{ route('pendaftaran.showRawatInap') }}",
                            data: {
                                q: $.trim($('#searchRawatInap').val() || ''),
                                per_page: $('#perPageRawatInap').val(),
                                page: page,
                                order_by: ($('#sortRawatInap').val() || 'updated_at:desc').split(':')[
                                    0],
                                order_dir: ($('#sortRawatInap').val() || 'updated_at:desc').split(':')[
                                    1]
                            },
                            dataType: 'json',
                            success: function(res) {
                                $('#showRawatInap').html(res.rows);
                                $('#paginateRawatInap').html(res.pagination);
                            }
                        });
                    }
                });

            $(document).on('keyup', '#search', function() {
                var query = $(this).val();
                if (query.length >= 2) {
                    console.log('Searching for:', query);
                    $('#loading').show();
                    $('#data').hide();
                    $.ajax({
                        url: "{{ route('pendaftaran.caripasien') }}",
                        method: 'GET',
                        data: {
                            q: query
                        },
                        dataType: 'json',
                        success: function(data) {
                            console.log('Search results received');
                            setTimeout(function() {
                                $('#loading').hide();
                                $('#data').show().html(data);
                            }, 1000);
                        },
                        error: function(xhr, status, error) {
                            console.error('Search error:', error);
                            $('#loading').hide();
                            $('#data').show().html(
                                '<div class="alert alert-danger">Error searching patients: ' +
                                error + '</div>');
                        }
                    });
                } else {
                    $('#loading').hide();
                    $('#data').hide();
                }
            });

            $("#btn-next").click(function() {
                $.ajax({
                    url: "{{ route('pendaftaran.update_antrian') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },

                    success: function(res) {
                        if (res.status == true) {
                            responsiveVoice.speak("Nomor Antrian " + res.antrian.prefix + "-" +
                                res.antrian.nomor +
                                ", ke loket " + res.loket.kode_loket, "Indonesian Female", {
                                    rate: 0.9,
                                    pitch: 1,
                                    volume: 1
                                });
                            $("#antrian").text(res.antrian.prefix + " " + res.antrian.nomor);
                            $("#jumlah").text(res.jumlah);
                        }
                    }
                });
            });

            $("#btn-buatPasienBaru").click(function() {
                $("#error").css("display", "none");
                $("#jenis_identitas").val("");
                $("#no_identitas").val(null);
                $("#name_pasien").val(null);
                $("#jenis_kelamin").val("");
                $("#tgl_lahir").val(null);
                $("#golongan_darah").val("");
                $("#kewarganegaraan").val(1);
                $("#pekerjaan").val("");
                $("#status_menikah").val("");
                $("#agama").val("");
                $("#no_hp").val(null);
                $("#no_telepon").val(null);
                $("#mr_lama").val(null);
                $("#alamat").val(null);
                $("#province").val("");
                $("#city").val(null);

            });

            $("#btn-simpan").click(function() {
                $.ajax({
                    url: "{{ route('pendaftaran.store_pasien') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        jenis_identitas: $("#jenis_identitas").val(),
                        no_identitas: $("#no_identitas").val(),
                        name_pasien: $("#name_pasien").val(),
                        jenis_kelamin: $("#jenis_kelamin").val(),
                        tgl_lahir: $("#tgl_lahir").val(),
                        golongan_darah: $("#golongan_darah").val(),
                        kewarganegaraan: $("#kewarganegaraan").val(),
                        pekerjaan: $("#pekerjaan").val(),
                        status_menikah: $("#status_menikah").val(),
                        agama: $("#agama").val(),
                        no_hp: $("#no_hp").val(),
                        no_telepon: $("#no_telepon").val(),
                        mr_lama: $("#mr_lama").val(),
                        alamat: $("#alamat").val(),
                        province: $("#province").val(),
                        city: $("#city").val()
                    },

                    success: function(res) {
                        if ($.isEmptyObject(res.error)) {
                            $("#error").css("display", "none");
                            if (res.status == false) {
                                swal(res.text, {
                                    icon: "error",
                                });
                            } else {
                                swal(res.text, {
                                    icon: "success",
                                });
                                $("#jenis_identitas").val("");
                                $("#no_identitas").val(null);
                                $("#name_pasien").val(null);
                                $("#jenis_kelamin").val("");
                                $("#tgl_lahir").val(null);
                                $("#golongan_darah").val("");
                                $("#kewarganegaraan").val(1);
                                $("#pekerjaan").val("");
                                $("#status_menikah").val("");
                                $("#agama").val("");
                                $("#no_hp").val(null);
                                $("#no_telepon").val(null);
                                $("#mr_lama").val(null);
                                $("#alamat").val(null);
                                $("#province").val("");
                                $("city").val(null);
                                $("#btn-kembali").click();

                            }
                        } else {
                            error(res.error)
                        }
                    }
                });

            });

            function error(msg) {
                $("#error").find("ul").html('');
                $("#error").css('display', 'block');
                $.each(msg, function(key, value) {
                    $("#error").find("ul").append('<li>' + value + '</li>');
                });
            }

            function showModalError(modalId, msg) {
                const errorEl = $("#error-" + modalId);
                errorEl.find("ul").html('');
                errorEl.removeClass('d-none');
                $.each(msg, function(key, value) {
                    errorEl.find("ul").append('<li>' + value + '</li>');
                });
            }

            // Submit handler untuk rawat darurat
            $("#btn-submit-rawatDarurat").on('click', function() {
                if ($(this).html().includes('Update') || $(this).text().includes('Update')) {
                    update_rawatDarurat();
                } else {
                    submit_rawatDarurat();
                }
            });

            function submit_rawatDarurat() {
                let url = "{{ route('pendaftaran.postRawatDarurat', ':id') }}"
                url = url.replace(':id', $("#id-rawatDarurat").val());

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        jenis_jaminan: $("#jenis_jaminan_rawatDarurat").val(),
                        dokter: $("#dokter_rawatDarurat").val(),
                        tujuan_kunjungan: $("#tujuan_kunjungan_rawatDarurat").val(),
                        tingkat_kegawatan: $("#tingkat_kegawatan").val(),
                        cara_datang: $("#cara_datang").val(),
                        keluhan_utama: $("#keluhan_utama").val()
                    },
                    success: function(res) {
                        if ($.isEmptyObject(res.error)) {
                            $("#error-rawatDarurat").addClass("d-none");
                            if (res.status == false) {
                                swal(res.text, {
                                    icon: "error",
                                });
                            } else {
                                $("#tab-igd").click();
                                $("#btn-tutup-rawatDarurat").click();
                                swal(res.text, {
                                    icon: "success",
                                });
                            }
                        } else {
                            error_rawatDarurat(res.error);
                        }
                    }
                });
            }

            function update_rawatDarurat() {
                let url = "{{ route('pendaftaran.updateRawatDarurat', ':id') }}"
                url = url.replace(':id', $("#id-rawatDarurat").val());

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        jenis_jaminan: $("#jenis_jaminan_rawatDarurat").val(),
                        dokter: $("#dokter_rawatDarurat").val(),
                        tujuan_kunjungan: $("#tujuan_kunjungan_rawatDarurat").val(),
                        tingkat_kegawatan: $("#tingkat_kegawatan").val(),
                        cara_datang: $("#cara_datang").val(),
                        keluhan_utama: $("#keluhan_utama").val()
                    },
                    success: function(res) {
                        if ($.isEmptyObject(res.error)) {
                            $("#error-rawatDarurat").addClass("d-none");
                            if (res.status == false) {
                                swal(res.text, {
                                    icon: "error",
                                });
                            } else {
                                $("#tab-igd").click();
                                $("#btn-tutup-rawatDarurat").click();
                                swal(res.text, {
                                    icon: "success",
                                });
                            }
                        } else {
                            error_rawatDarurat(res.error);
                        }
                    }
                });
            }

            function error_rawatDarurat(msg) {
                $("#error-rawatDarurat").find("ul").html('');
                $("#error-rawatDarurat").removeClass('d-none');
                $.each(msg, function(key, value) {
                    $("#error-rawatDarurat").find("ul").append('<li>' + value + '</li>');
                });
            }


        });
        document.getElementById('province').addEventListener('change', function() {
            var provinceId = this.value;
            let url = "{{ route('wilayah.city', ':code') }}";
            url = url.replace(':code', provinceId)
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    var cityDropdown = document.getElementById('city');
                    cityDropdown.innerHTML = '';
                    data.forEach(function(city) {
                        var option = document.createElement('option');
                        option.value = city.code;
                        option.textContent = city.name;
                        cityDropdown.appendChild(option);
                    });
                });
        });

        document.getElementById('province_edit').addEventListener('change', function() {
            var provinceId = this.value;
            console.log('=== PROVINCE CHANGE EVENT ===');
            console.log('Province edit changed to:', provinceId);

            const cityDropdown = $('#city_edit');
            const selectedCity = document.getElementById('city_edit').dataset.selectedCity;

            console.log('Target city from dataset:', selectedCity);

            if (!provinceId) {
                console.log('No province selected, clearing cities');
                cityDropdown.html('<option value="">-- Pilih Provinsi dulu --</option>');
                // Clear any stored city target
                document.getElementById('city_edit').dataset.selectedCity = '';
                return;
            }

            // Show loading state immediately
            cityDropdown.html('<option value="">🔄 Memuat kota/kabupaten...</option>');

            // Fetch cities for this province
            let url = "{{ route('wilayah.city', ':code') }}";
            url = url.replace(':code', provinceId);
            console.log('Fetching cities from URL:', url);

            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(cities => {
                    console.log('Cities data received:', cities.length, 'cities for province:', provinceId);

                    // Clear and populate city dropdown
                    cityDropdown.html('<option value="">-- Pilih Kota/Kabupaten --</option>');

                    let citySelected = false;
                    cities.forEach(function(city) {
                        const option = $('<option></option>').attr('value', city.code).text(city.name);

                        // Check for auto-selection
                        if (selectedCity && city.code === selectedCity) {
                            option.prop('selected', true);
                            citySelected = true;
                            console.log('✅ Auto-selected city:', city.name, 'with code:', city.code);
                        }

                        cityDropdown.append(option);
                    });

                    console.log('City dropdown populated. Target city selected:', citySelected);

                    // Verify final selection
                    const finalValue = cityDropdown.val();
                    console.log('Final city value after population:', finalValue);

                    // If city was selected, clear the dataset target
                    if (citySelected) {
                        setTimeout(function() {
                            document.getElementById('city_edit').dataset.selectedCity = '';
                            console.log('Cleared selectedCity dataset after successful selection');
                        }, 100);
                    }

                    console.log('=== PROVINCE CHANGE EVENT COMPLETE ===');
                })
                .catch(error => {
                    console.error('Error fetching cities:', error);
                    cityDropdown.html('<option value="">-- Error loading cities --</option>');
                });
        });
        $(document).on('click', '.edit', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.editPasien', ':id') }}"
            url = url.replace(':id', id);
            $("#error-edit").css("display", "none");

            console.log('Edit button clicked for patient ID:', id);

            // RESET: Clear all form fields to prevent stale data
            console.log('Clearing all form fields...');
            $('#jenis_identitas_edit').val('');
            $('#no_identitas_edit').val('');
            $('#name_pasien_edit').val('');
            $('#jenis_kelamin_edit').val('');
            $('#tgl_lahir_edit').val('');
            $('#golongan_darah_edit').val('');
            $('#kewarganegaraan_edit').val('');
            $('#pekerjaan_edit').val('');
            $('#status_menikah_edit').val('');
            $('#agama_edit').val('');
            $('#no_hp_edit').val('');
            $('#no_telepon_edit').val('');
            $('#mr_lama_edit').val('');
            $('#alamat_edit').val('');

            // IMPORTANT: Clear province and city dropdowns completely
            $('#province_edit').val('');
            $('#city_edit').html('<option value="">-- Pilih Provinsi dulu --</option>');
            document.getElementById('city_edit').dataset.selectedCity = '';

            console.log('Form reset completed, fetching patient data...');

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    const data = res.data;
                    console.log('Data pasien lengkap:', data); // Debug log
                    console.log('Province code dari server:', data.province_code);
                    console.log('City code dari server:', data.city_code);

                    $("#id").val(data.id);
                    $("#jenis_identitas_edit").val(data.jenis_identitas);
                    console.log('Setting jenis_identitas to:', data.jenis_identitas);
                    console.log('jenis_identitas_edit value after set:', $("#jenis_identitas_edit")
                        .val());
                    console.log('jenis_identitas_edit select element:', $('#jenis_identitas_edit')[0]);

                    $("#no_identitas_edit").val(data.no_identitas);
                    $("#name_pasien_edit").val(data.name);

                    $("#jenis_kelamin_edit").val(data.jenis_kelamin);
                    console.log('Setting jenis_kelamin to:', data.jenis_kelamin);
                    console.log('jenis_kelamin_edit value after set:', $("#jenis_kelamin_edit").val());
                    console.log('jenis_kelamin_edit select element:', $('#jenis_kelamin_edit')[0]);
                    // Set tanggal lahir dengan fallback untuk format ISO
                    console.log('tgl_lahir dari server:', data.tgl_lahir);
                    let tglLahir = data.tgl_lahir || '';

                    // Fallback: jika masih format ISO, convert ke YYYY-MM-DD
                    if (tglLahir && tglLahir.includes('T')) {
                        tglLahir = tglLahir.split('T')[0]; // Ambil bagian tanggal saja
                        console.log('Converted ISO date to:', tglLahir);
                    }

                    $("#tgl_lahir_edit").val(tglLahir);
                    console.log('tgl_lahir_edit value after set:', $("#tgl_lahir_edit").val());
                    $("#golongan_darah_edit").val(data.golongan_darah || '');
                    console.log('Setting golongan_darah to:', data.golongan_darah);
                    console.log('golongan_darah_edit value after set:', $("#golongan_darah_edit")
                        .val());
                    console.log('golongan_darah_edit select element:', $('#golongan_darah_edit')[0]);

                    $("#kewarganegaraan_edit").val(data.kewarganegaraan || '1');
                    $("#pekerjaan_edit").val(data.pekerjaan || '');

                    $("#status_menikah_edit").val(data.status_menikah);
                    console.log('Setting status_menikah to:', data.status_menikah);
                    console.log('status_menikah_edit value after set:', $("#status_menikah_edit")
                        .val());
                    console.log('status_menikah_edit select element:', $('#status_menikah_edit')[0]);

                    // Debug: Check all options in select
                    $('#jenis_identitas_edit option').each(function(i, option) {
                        console.log('jenis_identitas option', i, ':', option.value, '=', option
                            .text, 'selected:', option.selected);
                    });
                    $('#jenis_kelamin_edit option').each(function(i, option) {
                        console.log('jenis_kelamin option', i, ':', option.value, '=', option
                            .text, 'selected:', option.selected);
                    });
                    $('#golongan_darah_edit option').each(function(i, option) {
                        console.log('golongan_darah option', i, ':', option.value, '=', option
                            .text, 'selected:', option.selected);
                    });
                    $('#status_menikah_edit option').each(function(i, option) {
                        console.log('status_menikah option', i, ':', option.value, '=', option
                            .text, 'selected:', option.selected);
                    });
                    $("#agama_edit").val(data.agama || '');
                    $("#no_hp_edit").val(data.no_hp);
                    $("#no_telepon_edit").val(data.no_telepon || '');
                    $("#mr_lama_edit").val(data.mr_lama || '');
                    $("#alamat_edit").val(data.alamat);

                    // [FIX] Comprehensive province-city handling
                    const provinceDropdown = $('#province_edit');
                    const cityDropdown = $('#city_edit');

                    console.log('=== PROVINCE-CITY SETUP START ===');
                    console.log('Province code from data:', data.province_code);
                    console.log('City code from data:', data.city_code);

                    // Function to load cities for a province
                    function loadCitiesForProvince(provinceCode, targetCityCode) {
                        console.log('Loading cities for province:', provinceCode, 'with target city:',
                            targetCityCode);

                        if (!provinceCode) {
                            console.log('No province code, clearing city dropdown');
                            cityDropdown.html('<option value="">-- Pilih Provinsi dulu --</option>');
                            return;
                        }

                        // Show loading state
                        cityDropdown.html('<option value="">🔄 Memuat kota/kabupaten...</option>');

                        // Store target city in dataset
                        if (targetCityCode) {
                            document.getElementById('city_edit').dataset.selectedCity = targetCityCode;
                        }

                        // Fetch cities
                        let url = "{{ route('wilayah.city', ':code') }}";
                        url = url.replace(':code', provinceCode);
                        console.log('Fetching cities from URL:', url);

                        fetch(url)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(cities => {
                                console.log('Cities data received:', cities.length, 'cities');

                                // Clear and populate city dropdown
                                cityDropdown.html(
                                    '<option value="">-- Pilih Kota/Kabupaten --</option>');

                                let citySelected = false;
                                cities.forEach(function(city) {
                                    const option = $('<option></option>').attr('value', city
                                        .code).text(city.name);

                                    // Check for auto-selection
                                    if (targetCityCode && city.code === targetCityCode) {
                                        option.prop('selected', true);
                                        citySelected = true;
                                        console.log('✅ Auto-selected city:', city.name,
                                            'with code:', city.code);
                                    }

                                    cityDropdown.append(option);
                                });

                                console.log('City dropdown populated. Target city selected:',
                                    citySelected);

                                // Verify selection
                                const finalValue = cityDropdown.val();
                                console.log('Final city value after population:', finalValue);

                                // Clear dataset
                                setTimeout(function() {
                                    document.getElementById('city_edit').dataset
                                        .selectedCity = '';
                                }, 100);
                            })
                            .catch(error => {
                                console.error('Error loading cities:', error);
                                cityDropdown.html(
                                    '<option value="">-- Error loading cities --</option>');
                            });
                    }

                    // Clear existing dropdowns
                    console.log('Clearing existing dropdown values...');
                    provinceDropdown.val('');
                    cityDropdown.html('<option value="">-- Pilih Provinsi dulu --</option>');
                    document.getElementById('city_edit').dataset.selectedCity = '';

                    // Set province and load cities
                    if (data.province_code) {
                        console.log('Setting province to:', data.province_code);
                        provinceDropdown.val(data.province_code);

                        const actualProvinceValue = provinceDropdown.val();
                        console.log('Province dropdown value after set:', actualProvinceValue);

                        if (actualProvinceValue === data.province_code) {
                            // Province set successfully, now load cities
                            console.log('Province set successfully, loading cities...');
                            setTimeout(function() {
                                loadCitiesForProvince(data.province_code, data.city_code);
                            }, 100);
                        } else {
                            console.error('Province setting failed. Expected:', data.province_code,
                                'Got:', actualProvinceValue);
                        }
                    } else {
                        console.log('No province_code in data, leaving province empty');
                    }

                    console.log('=== PROVINCE-CITY SETUP END ===');

                    // Final verification with timeout untuk ensure all fields ter-set dengan benar
                    setTimeout(function() {
                        console.log('=== FINAL VERIFICATION ===');

                        // Re-set form fields untuk ensure compatibility
                        $("#jenis_identitas_edit").val(data.jenis_identitas);
                        $("#jenis_kelamin_edit").val(data.jenis_kelamin);
                        $("#golongan_darah_edit").val(data.golongan_darah || '');
                        $("#status_menikah_edit").val(data.status_menikah);

                        // Re-set date dengan proper format
                        let fallbackTglLahir = data.tgl_lahir || '';
                        if (fallbackTglLahir && fallbackTglLahir.includes('T')) {
                            fallbackTglLahir = fallbackTglLahir.split('T')[0];
                        }
                        $("#tgl_lahir_edit").val(fallbackTglLahir);

                        // Verify province-city setup
                        const currentProvinceValue = $('#province_edit').val();
                        const currentCityValue = $('#city_edit').val();

                        console.log('Final verification - Province:', {
                            expected: data.province_code,
                            actual: currentProvinceValue,
                            match: currentProvinceValue === data.province_code
                        });

                        console.log('Final verification - City:', {
                            expected: data.city_code,
                            actual: currentCityValue,
                            match: currentCityValue === data.city_code
                        });

                        // If province is set but city is not, trigger one more load
                        if (data.province_code && data.city_code &&
                            currentProvinceValue === data.province_code &&
                            (!currentCityValue || currentCityValue !== data.city_code)) {

                            console.log(
                                '⚠️ Final verification: Province OK but city missing, triggering reload...'
                            );
                            document.getElementById('city_edit').dataset.selectedCity = data
                                .city_code;

                            // Use the loadCitiesForProvince function from the closure
                            setTimeout(function() {
                                $('#province_edit').trigger('change');
                            }, 100);
                        }

                        console.log('=== FINAL VERIFICATION COMPLETE ===');
                    }, 600);
                }
            })
        });

        $("#btn-edit").click(function() {
            let url = "{{ route('pendaftaran.updatePasien', ':id') }}"
            url = url.replace(':id', $("#id").val());
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_identitas: $("#jenis_identitas_edit").val(),
                    no_identitas: $("#no_identitas_edit").val(),
                    name_pasien: $("#name_pasien_edit").val(),
                    jenis_kelamin: $("#jenis_kelamin_edit").val(),
                    tgl_lahir: $("#tgl_lahir_edit").val(),
                    golongan_darah: $("#golongan_darah_edit").val(),
                    kewarganegaraan: $("#kewarganegaraan_edit").val(),
                    pekerjaan: $("#pekerjaan_edit").val(),
                    status_menikah: $("#status_menikah_edit").val(),
                    agama: $("#agama_edit").val(),
                    no_hp: $("#no_hp_edit").val(),
                    no_telepon: $("#no_telepon_edit").val(),
                    mr_lama: $("#mr_lama_edit").val(),
                    alamat: $("#alamat_edit").val(),
                    province: $("#province_edit").val(),
                    city: $("#city_edit").val()
                },

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-edit").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#btn-edit-kembali").click();
                            swal(res.text, {
                                icon: "success",
                            });

                        }
                    } else {
                        error_edit(res.error)
                    }
                }
            });
        });

        function error_edit(msg) {
            $("#error-edit").find("ul").html('');
            $("#error-edit").css('display', 'block');
            $.each(msg, function(key, value) {
                $("#error-edit").find("ul").append('<li>' + value + '</li>');
            });
        }

        $(document).on('click', '.rawatJalan', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.showPasien', ':id') }}"
            url = url.replace(':id', id);

            console.log('New rawat jalan form opened for patient ID:', id);

            // Reset form completely
            $("#error-rawatJalan").addClass("d-none");
            $("#jenis_jaminan").val("");
            $("#tujuan_kunjungan").val("");
            $("#btn-submit-rawatJalan").text("Simpan");

            // [ENHANCED] Reset clinic and doctors properly (checkbox version)
            $("#clinic").val("");

            // Reset checkbox-based doctor selection
            $('#dokter-placeholder').removeClass('d-none').html(
                '<i class="ri-hospital-line me-2"></i>Pilih poliklinik terlebih dahulu untuk melihat daftar dokter'
            );
            $('#dokter-list').addClass('d-none').empty();
            $('#dokter-selected').val('');
            $('#dokter-container').removeClass('border-success border-danger');

            // Reset counter
            $('#selected-count').addClass('d-none');
            $('#count-text').text('0 dipilih');

            // Clean up event listeners
            $('.dokter-checkbox').off('change');
            $('.doctor-option').off('click hover');
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#no_rm_rawatJalan").text(res.data.rekam_medis);
                    $("#name_rawatJalan").text(res.data.name);
                    $("#tgl_lahir_rawatJalan").text(res.data.umur);
                    $("#id-rawatJalan").val(id);

                    // Display user-friendly status
                    $("#status-pasien").text(formatStatus(res.data.status));

                    $("#no_encounter_rawatJalan").text(res.data.no_encounter || '-');
                    $("#created_rawatJalan").text(res.data.tgl_encounter || '-');
                    $("#type_rawatJalan").text(res.data.type || '-');
                }
            })
        });
        $("#btn-submit-rawatJalan").on('click', function(e) {
            e.preventDefault(); // Prevent any default behavior
            console.log('Submit rawat jalan clicked!', $(this).text());

            // Get selected doctors from checkbox
            var selectedDoctorsString = $('#dokter-selected').val();
            var selectedDoctors = selectedDoctorsString ? selectedDoctorsString.split(',') : [];

            // Form validation check
            var formData = {
                jenis_jaminan: $("#jenis_jaminan").val(),
                clinic: $("#clinic").val(),
                dokter: selectedDoctors,
                tujuan_kunjungan: $("#tujuan_kunjungan").val(),
                id_rawatJalan: $("#id-rawatJalan").val()
            };
            console.log('Form values:', formData);
            console.log('Selected doctors from checkboxes:', selectedDoctors);

            // Basic validation
            if (!formData.jenis_jaminan) {
                alert('Jenis Jaminan harus dipilih!');
                $('#jenis_jaminan').focus();
                return false;
            }
            if (!formData.clinic) {
                alert('Poliklinik harus dipilih!');
                $('#clinic').focus();
                return false;
            }
            if (!formData.dokter || formData.dokter.length === 0) {
                alert('Minimal satu dokter harus dipilih!');
                $('#dokter-container').addClass('border-danger');
                return false;
            }
            if (!formData.tujuan_kunjungan) {
                alert('Tujuan Kunjungan harus dipilih!');
                $('#tujuan_kunjungan').focus();
                return false;
            }
            if (!formData.id_rawatJalan) {
                alert('Data pasien tidak valid!');
                return false;
            }

            // Remove error styling
            $('#dokter-container').removeClass('border-danger');

            if ($(this).text() === 'Update Rawat Jalan') {
                console.log('Calling update_rawatJalan()');
                update_rawatJalan();
            } else {
                console.log('Calling submit_rawatJalan()');
                submit_rawatJalan();
            }
        })

        function submit_rawatJalan() {
            console.log('submit_rawatJalan() called');

            let url = "{{ route('pendaftaran.postRawatJalan', ':id') }}"
            url = url.replace(':id', $("#id-rawatJalan").val());
            console.log('AJAX URL:', url);

            // Get selected doctors from checkbox
            var selectedDoctorsString = $('#dokter-selected').val();
            var selectedDoctors = selectedDoctorsString ? selectedDoctorsString.split(',') : [];
            console.log('Selected doctors from checkbox:', selectedDoctors);

            var requestData = {
                _token: "{{ csrf_token() }}",
                jenis_jaminan: $("#jenis_jaminan").val(),
                dokter: selectedDoctors,
                clinic_id: $("#clinic").val(),
                tujuan_kunjungan: $("#tujuan_kunjungan").val(),
            };
            console.log('Request data:', requestData);

            $.ajax({
                url: url,
                type: 'POST',
                data: requestData,
                success: function(res) {
                    console.log('AJAX success:', res);
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatJalan").addClass("d-none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error"
                            });
                        } else {
                            $("#tab-rawatJalan").click();
                            $("#btn-tutup-rawatJalan").click();
                            swal(res.text, {
                                icon: "success"
                            });
                        }
                    } else {
                        error_rawatJalan(res.error)
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        xhr,
                        status,
                        error
                    });
                    console.error('Response text:', xhr.responseText);
                    swal('Terjadi kesalahan dalam mengirim data. Silakan coba lagi.', {
                        icon: "error",
                    });
                }
            });
        }

        function update_rawatJalan() {
            console.log('update_rawatJalan() called');

            let url = "{{ route('pendaftaran.updateRawatJalan', ':id') }}"
            url = url.replace(':id', $("#id-rawatJalan").val());
            console.log('Update URL:', url);

            // Get selected doctors from checkbox
            var selectedDoctorsString = $('#dokter-selected').val();
            var selectedDoctors = selectedDoctorsString ? selectedDoctorsString.split(',') : [];
            console.log('Selected doctors for update:', selectedDoctors);

            var requestData = {
                _token: "{{ csrf_token() }}",
                jenis_jaminan: $("#jenis_jaminan").val(),
                dokter: selectedDoctors,
                clinic_id: $("#clinic").val(),
                tujuan_kunjungan: $("#tujuan_kunjungan").val()
            };
            console.log('Update request data:', requestData);

            $.ajax({
                url: url,
                type: 'POST',
                data: requestData,

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatJalan").addClass("d-none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#tab-rawatJalan").click();
                            $("#btn-tutup-rawatJalan").click();
                            swal(res.text, {
                                icon: "success",
                            });
                        }
                    } else {
                        error_rawatJalan(res.error)
                    }
                }
            });
        }

        $(document).on('click', '.editrawatJalan', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.editEncounterRajal', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatJalan").addClass("d-none");
            $("#jenis_jaminan").val("");
            $("#tujuan_kunjungan").val("");

            // Reset checkbox-based doctor selection
            $('#dokter-placeholder').removeClass('d-none').html(
                '<i class="ri-hospital-line me-2"></i>Pilih poliklinik terlebih dahulu untuk melihat daftar dokter'
            );
            $('#dokter-list').addClass('d-none').empty();
            $('#dokter-selected').val('');
            $('#dokter-container').removeClass('border-success border-danger');
            $('#selected-count').addClass('d-none');
            $('.dokter-checkbox').off('change');
            $('.doctor-option').off('click hover');
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log('=== RAW AJAX RESPONSE ===');
                    console.log('Full response object:', res);
                    console.log('Response.data:', res.data);
                    console.log('Response.data.dokter_ids:', res.data.dokter_ids);
                    console.log('Response.data.dokter_ids type:', typeof res.data.dokter_ids);

                    $("#no_rm_rawatJalan").text(res.data.rekam_medis);
                    $("#name_rawatJalan").text(res.data.name_pasien);
                    $("#tgl_lahir_rawatJalan").text(res.data.umur);
                    $("#id-rawatJalan").val(id);

                    // Display user-friendly status for edit
                    $("#status-pasien").text(formatStatus(res.data.status));

                    $("#no_encounter_rawatJalan").text(res.data.no_encounter || '-');
                    $("#created_rawatJalan").text(res.data.tgl_encounter || '-');
                    $("#type_rawatJalan").text(res.data.type || '-');
                    $("#jenis_jaminan").val(res.data.jenis_jaminan);
                    $("#tujuan_kunjungan").val(res.data.tujuan_kunjungan);
                    $("#btn-submit-rawatJalan").text("Update Rawat Jalan");

                    console.log('=== SETTING CLINIC AND DOCTORS FOR EDIT FORM ===');
                    console.log('Raw response data:', res.data);
                    console.log('Clinic ID:', res.data.clinic_id);
                    console.log('Doctor IDs (original):', res.data.dokter_ids);
                    console.log('Doctor IDs type:', typeof res.data.dokter_ids);
                    console.log('Doctor IDs length:', res.data.dokter_ids ? res.data.dokter_ids.length :
                        0);
                    console.log('Doctor IDs JSON:', JSON.stringify(res.data.dokter_ids));

                    // [ENHANCED] Set clinic first, then load doctors with selected values
                    $("#clinic").val(res.data.clinic_id);

                    // Load doctors for the selected clinic with pre-selected values
                    if (res.data.clinic_id) {
                        console.log('Loading doctors for edit - Clinic:', res.data.clinic_id,
                            'Selected doctors:', res.data.dokter_ids);

                        // Convert doctor IDs to strings for proper comparison
                        console.log('Raw dokter_ids:', res.data.dokter_ids);
                        console.log('dokter_ids is null?', res.data.dokter_ids === null);
                        console.log('dokter_ids is undefined?', res.data.dokter_ids === undefined);
                        console.log('dokter_ids is array?', Array.isArray(res.data.dokter_ids));

                        var selectedDoctorStrings = [];
                        if (res.data.dokter_ids && Array.isArray(res.data.dokter_ids) && res.data
                            .dokter_ids.length > 0) {
                            selectedDoctorStrings = res.data.dokter_ids.map(String);
                            console.log('Converted to strings:', selectedDoctorStrings);
                        } else {
                            console.warn('No dokter_ids found or invalid format. Using empty array.');
                            selectedDoctorStrings = [];
                        }

                        loadDoctorsByClinic(res.data.clinic_id, null, selectedDoctorStrings);
                    } else {
                        console.log('No clinic selected for edit form');
                        // Reset to placeholder state
                        $('#dokter-placeholder').removeClass('d-none');
                        $('#dokter-list').addClass('d-none');
                    }

                    $("#error-rawatJalan").addClass("d-none");
                }
            })
        });

        // [NEW] Handle delete for Rawat Jalan
        $(document).on('click', '.destroyRawatJalan', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.destroyEncounterRajal', ':id') }}";
            url = url.replace(':id', id);
            const $btn = $(this);

            console.log('Delete button clicked for encounter ID:', id);

            // Show confirmation dialog
            swal({
                    title: "Apakah Anda yakin?",
                    text: "Data pendaftaran rawat jalan akan dihapus permanen!",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Batal",
                            value: null,
                            visible: true,
                            className: "btn-secondary",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Ya, Hapus!",
                            value: true,
                            visible: true,
                            className: "btn-danger",
                            closeModal: false
                        }
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        console.log('User confirmed deletion, sending DELETE request');

                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                console.log('Delete response:', res);

                                if (res.status) {
                                    // Optimistically remove the row immediately for better UX
                                    $btn.closest('tr').fadeOut(200, function() {
                                        $(this).remove();
                                    });

                                    swal({
                                        title: "Berhasil!",
                                        text: res.text || "Data berhasil dihapus",
                                        icon: "success",
                                        timer: 1500
                                    });

                                    // Also refresh list to ensure consistency (e.g., if filters applied)
                                    rawatJalan();
                                } else {
                                    swal({
                                        title: "Gagal!",
                                        text: res.text || "Gagal menghapus data",
                                        icon: "error"
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Delete error:', {
                                    xhr,
                                    status,
                                    error
                                });
                                swal({
                                    title: "Error!",
                                    text: "Terjadi kesalahan saat menghapus data: " + error,
                                    icon: "error"
                                });
                            }
                        });
                    } else {
                        console.log('User cancelled deletion');
                        swal({
                            title: "Dibatalkan",
                            text: "Data tidak jadi dihapus",
                            icon: "info",
                            timer: 1500
                        });
                    }
                });
        });

        // [NEW] Handle edit for Rawat Darurat (IGD)
        $(document).on('click', '.editrawatDarurat', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.editEncounterRdarurat', ':id') }}";
            url = url.replace(':id', id);

            // Reset form
            $("#error-rawatDarurat").addClass("d-none");
            $("#jenis_jaminan_rawatDarurat").val("");
            $("#dokter_rawatDarurat").val(null).trigger('change');
            $("#tujuan_kunjungan_rawatDarurat").val("");
            $("#tingkat_kegawatan").val("");
            $("#cara_datang").val("");
            $("#keluhan_utama").val("");
            $("#btn-submit-rawatDarurat").html('<i class="ri-refresh-line me-2"></i>Update IGD');

            // [FIX] Properly reset the Select2 dropdown for IGD doctors before populating
            const dokterIgdSelect = $('#dokter_rawatDarurat');
            if (dokterIgdSelect.hasClass("select2-hidden-accessible")) {
                // If Select2 is initialized, destroy it to be re-initialized
                dokterIgdSelect.select2('destroy');
            }
            dokterIgdSelect.val(null); // Clear any selected values

            $.get(url, function(res) {
                const data = res.data;
                $("#no_rm_rawatDarurat").text(data.rekam_medis);
                $("#name_rawatDarurat").text(data.name_pasien);
                $("#tgl_lahir_rawatDarurat").text(data.umur);
                $("#id-rawatDarurat").val(id);

                // Display user-friendly status for rawat darurat
                $("#status-pasien-rawatDarurat").text(formatStatus(data.status));

                $("#no_encounter_rawatDarurat").text(data.no_encounter || '-');
                $("#created_rawatDarurat").text(data.tgl_encounter || '-');
                $("#type_rawatDarurat").text(data.type || '-');
                $("#jenis_jaminan_rawatDarurat").val(data.jenis_jaminan);
                $("#tujuan_kunjungan_rawatDarurat").val(data.tujuan_kunjungan);
                $("#dokter_rawatDarurat").val(data.dokter_ids).trigger('change');

                // [FIX] Set additional IGD fields if available
                $("#tingkat_kegawatan").val(data.tingkat_kegawatan || '');
                $("#cara_datang").val(data.cara_datang || '');
                $("#keluhan_utama").val(data.keluhan_utama || '');
            }).always(function(res) {
                // [FIX] Initialize or re-initialize Select2 after populating data
                $('#dokter_rawatDarurat').select2({
                    placeholder: "Pilih satu atau lebih dokter",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal-rawatDarurat')
                }).val(res.data.dokter_ids).trigger('change'); // Re-apply value after init
                console.log('Select2 for IGD initialized on edit, selected:', res.data.dokter_ids);
            });;
        });

        // Handler untuk modal rawat darurat baru
        $(document).on('click', '.rawatDarurat', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.showPasien', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatDarurat").addClass("d-none");
            $("#jenis_jaminan_rawatDarurat").val("");
            $("#dokter_rawatDarurat").val(null).trigger('change');
            $("#tujuan_kunjungan_rawatDarurat").val("");
            $("#tingkat_kegawatan").val("");
            $("#cara_datang").val("");
            $("#keluhan_utama").val("");
            $("#btn-submit-rawatDarurat").html('<i class="ri-alarm-warning-line me-2"></i>Daftar IGD');

            // [FIX] Properly reset the Select2 dropdown for IGD doctors
            const dokterIgdSelect = $('#dokter_rawatDarurat');
            if (dokterIgdSelect.hasClass("select2-hidden-accessible")) {
                // If Select2 is initialized, destroy it to be re-initialized on next open
                dokterIgdSelect.select2('destroy');
            }
            dokterIgdSelect.val(null); // Clear any selected values

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#no_rm_rawatDarurat").text(res.data.rekam_medis);
                    $("#name_rawatDarurat").text(res.data.name);
                    $("#tgl_lahir_rawatDarurat").text(res.data.umur);
                    $("#id-rawatDarurat").val(id);

                    // Display user-friendly status for rawat darurat
                    $("#status-pasien-rawatDarurat").text(formatStatus(res.data.status));

                    $("#no_encounter_rawatDarurat").text(res.data.no_encounter || '-');
                    $("#created_rawatDarurat").text(res.data.tgl_encounter || '-');
                    $("#type_rawatDarurat").text(res.data.type || '-');
                }
            }).always(function() {
                // [FIX] Initialize Select2 after the modal is fully populated and ready
                $('#dokter_rawatDarurat').select2({
                    placeholder: "Pilih satu atau lebih dokter",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal-rawatDarurat')
                });
                console.log('Select2 for IGD initialized on new registration.');
            });
        });

        // [NEW] Handle delete for Rawat Darurat (IGD)
        $(document).on('click', '.destroyRawatDarurat', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.destroyEncounterRdarurat', ':id') }}";
            url = url.replace(':id', id);
            const $btn = $(this);

            console.log('Delete IGD button clicked for encounter ID:', id);

            swal({
                    title: "Apakah Anda yakin?",
                    text: "Data pendaftaran rawat darurat (IGD) akan dihapus permanen!",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Batal",
                            value: null,
                            visible: true,
                            className: "btn-secondary",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Ya, Hapus!",
                            value: true,
                            visible: true,
                            className: "btn-danger",
                            closeModal: false
                        }
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.status) {
                                    // Remove row instantly
                                    $btn.closest('tr').fadeOut(200, function() {
                                        $(this).remove();
                                    });
                                    swal({
                                        title: "Berhasil!",
                                        text: res.text || "Data IGD berhasil dihapus",
                                        icon: "success",
                                        timer: 1500
                                    });
                                    rawatDarurat(); // Reload the IGD table
                                } else {
                                    swal({
                                        title: "Gagal!",
                                        text: res.text || "Gagal menghapus data IGD",
                                        icon: "error"
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Delete IGD error:', {
                                    xhr,
                                    status,
                                    error
                                });
                                swal({
                                    title: "Error!",
                                    text: "Terjadi kesalahan saat menghapus data IGD: " +
                                        error,
                                    icon: "error"
                                });
                            }
                        });
                    } else {
                        swal({
                            title: "Dibatalkan",
                            text: "Data IGD tidak jadi dihapus",
                            icon: "info",
                            timer: 1500
                        });
                    }
                });
        });

        // [NEW] Handle delete for Rawat Inap
        $(document).on('click', '.destroyEncounterRinap', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.destroyEncounterRinap', ':id') }}";
            url = url.replace(':id', id);
            const $btn = $(this);

            console.log('Delete Rawat Inap button clicked for encounter ID:', id);

            swal({
                    title: "Apakah Anda yakin?",
                    text: "Data pendaftaran rawat inap akan dihapus permanen!",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Batal",
                            value: null,
                            visible: true,
                            className: "btn-secondary",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Ya, Hapus!",
                            value: true,
                            visible: true,
                            className: "btn-danger",
                            closeModal: false
                        }
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.status) {
                                    // Remove row instantly
                                    $btn.closest('tr').fadeOut(200, function() {
                                        $(this).remove();
                                    });
                                    swal({
                                        title: "Berhasil!",
                                        text: res.text ||
                                            "Data rawat inap berhasil dihapus",
                                        icon: "success",
                                        timer: 1500
                                    });
                                    rawatInap(); // Reload the rawat inap table
                                } else {
                                    swal({
                                        title: "Gagal!",
                                        text: res.text || "Gagal menghapus data rawat inap",
                                        icon: "error"
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Delete Rawat Inap error:', {
                                    xhr,
                                    status,
                                    error
                                });
                                swal({
                                    title: "Error!",
                                    text: "Terjadi kesalahan saat menghapus data rawat inap: " +
                                        error,
                                    icon: "error"
                                });
                            }
                        });
                    } else {
                        swal({
                            title: "Dibatalkan",
                            text: "Data rawat inap tidak jadi dihapus",
                            icon: "info",
                            timer: 1500
                        });
                    }
                });
        });

        function error_rawatJalan(msg) {
            $("#error-rawatJalan").find("ul").html('');
            $("#error-rawatJalan").removeClass('d-none');
            $.each(msg, function(key, value) {
                $("#error-rawatJalan").find("ul").append('<li>' + value + '</li>');
            });
        }

        $(document).on('click', '.rawatInap', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.showPasien', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatInap").css("display", "none");
            $("#jenis_jaminan_rawatInap").val("");
            $("#dokter_rawatInap").val(null).trigger('change'); // Clear multiple selection
            $("#tujuan_kunjungan_rawatInap").val("");
            $("#btn-submit-rawatInap").text("Simpan");
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#no_rm_rawatInap").text(res.data.rekam_medis);
                    $("#name_rawatInap").text(res.data.name);
                    $("#tgl_lahir_rawatInap").text(res.data.umur);
                    $("#id-rawatRinap").val(id);

                    // Display user-friendly status for rawat inap
                    $("#status-pasien-rawatRinap").text(formatStatus(res.data.status));

                    $("#no_encounter_rawatRinap").text(res.data.no_encounter || '-');
                    $("#created_rawatRinap").text(res.data.tgl_encounter || '-');
                    $("#type_rawatRinap").text(res.data.type || '-');
                }
            });
        });
        $("#btn-submit-rawatInap").on('click', function() {
            if ($(this).text() === 'Update Rawat Inap') {
                update_rawatRinap();
            }
        });

        function update_rawatRinap() {
            let url = "{{ route('pendaftaran.postRawatInap', ':id') }}"
            url = url.replace(':id', $("#id-rawatRinap").val());

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_jaminan: $("#jenis_jaminan_rawatRinap").val(),
                    dokter: $("#dokter_rawatRinap").val(),
                    ruangan: $("#ruangan_rawatRinap").val(),
                    name_companion: $("#name_companion").val(),
                    nik_companion: $("#nik_companion").val(),
                    phone_companion: $("#phone_companion").val(),
                    relation_companion: $("#relation_companion").val(),
                },

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatRinap").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#tab-rawatInap").click();
                            $("#btn-tutup-rawatRinap").click();
                            swal(res.text, {
                                icon: "success",
                            });
                        }
                    } else {
                        error_rawatRinap(res.error)
                    }
                }
            });
        }

        // [ENHANCED] Event ketika poliklinik berubah - update dokter options untuk semua form
        function loadDoctorsByClinic(clinicId, targetDoctorDropdown, selectedDoctors = null) {
            console.log('=== CHECKBOX DOCTOR LOADING ===');
            console.log('Clinic ID:', clinicId);
            console.log('Selected doctors parameter:', selectedDoctors);
            console.log('Selected doctors type:', typeof selectedDoctors);
            console.log('Selected doctors JSON:', JSON.stringify(selectedDoctors));
            console.log('Is Array?', Array.isArray(selectedDoctors));

            const placeholder = $('#dokter-placeholder');
            const listContainer = $('#dokter-list');
            const hiddenInput = $('#dokter-selected');

            if (!clinicId || clinicId === '') {
                console.log('No clinic selected, showing placeholder');
                placeholder.removeClass('d-none').html(
                    '<i class="ri-hospital-line me-2"></i>Pilih poliklinik terlebih dahulu untuk melihat daftar dokter');
                listContainer.addClass('d-none');
                hiddenInput.val('');
                updateSelectedDoctors();
                return;
            }

            const url = "{{ route('ajax.dokterByClinic', ':id') }}".replace(':id', clinicId);
            console.log('Loading from URL:', url);

            // Show loading state
            placeholder.html(
                '<div class="text-center py-3"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Memuat daftar dokter...</div>'
            ).removeClass('d-none');
            listContainer.addClass('d-none');

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    console.log('AJAX Success! Response:', response);
                    console.log('Response type:', typeof response, 'Is array:', Array.isArray(response));

                    if (response && Array.isArray(response) && response.length > 0) {
                        console.log('Creating checkboxes for', response.length, 'doctors');

                        let checkboxHTML = '';

                        $.each(response, function(key, doctor) {
                            // [FIX] Directly use doctor.id as provided by the controller
                            const doctorId = doctor.id;
                            const isSelected = selectedDoctors && selectedDoctors.includes(doctorId
                                .toString())

                            // Debug selection logic
                            if (selectedDoctors) {
                                console.log(
                                    `Doctor ${doctor.name} (User ID: ${doctorUserId}, Doctor ID: ${doctor.id}) - Selected: ${isSelected}`
                                );
                                console.log('  Matching against selected IDs:', selectedDoctors);
                            } // [FIXED] - The console log was causing an error because doctorUserId was not defined.

                            checkboxHTML += `
                                <div class="doctor-card mb-3" data-doctor-id="${doctorId}">
                                    <div class="card border-2 h-100 doctor-option ${isSelected ? 'selected' : ''}"
                                         style="cursor: pointer; transition: all 0.3s ease; min-height: 65px;">
                                        <div class="card-body py-3 px-3">
                                            <div class="d-flex align-items-center h-100">
                                                <div class="form-check me-3 d-flex align-items-center">
                                                    <input class="form-check-input dokter-checkbox" type="checkbox"
                                                           value="${doctorId}" id="dokter_${doctorId}"
                                                           ${isSelected ? 'checked' : ''}
                                                           style="width: 20px; height: 20px; margin: 0;">
                                                </div>
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                        <i class="ri-user-heart-line fs-16"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 d-flex align-items-center">
                                                    <h6 class="mb-0 fw-semibold text-dark">${doctor.name}</h6>
                                                </div>
                                                <div class="selected-indicator d-none">
                                                    <div class="badge bg-success rounded-circle p-1">
                                                        <i class="ri-check-line text-white fs-12"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        listContainer.html(checkboxHTML);
                        placeholder.addClass('d-none');
                        listContainer.removeClass('d-none');

                        // Add event listeners to checkboxes with enhanced styling
                        $('.dokter-checkbox').on('change', function() {
                            const doctorCard = $(this).closest('.doctor-card');
                            const cardElement = doctorCard.find('.doctor-option');
                            const indicator = doctorCard.find('.selected-indicator');

                            if ($(this).is(':checked')) {
                                cardElement.addClass('selected border-success bg-success-subtle');
                                cardElement.removeClass('border-2');
                                indicator.removeClass('d-none');
                            } else {
                                cardElement.removeClass('selected border-success bg-success-subtle');
                                cardElement.addClass('border-2');
                                indicator.addClass('d-none');
                            }

                            updateSelectedDoctors();
                            console.log('Doctor selection changed:', $(this).val(), 'checked:', $(this)
                                .is(':checked'));
                        });

                        // Add click-to-select functionality on card
                        $('.doctor-option').on('click', function(e) {
                            if (!$(e.target).is('input[type="checkbox"]')) {
                                const checkbox = $(this).find('.dokter-checkbox');
                                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                            }
                        });

                        // Add hover effects
                        $('.doctor-option').hover(
                            function() {
                                if (!$(this).hasClass('selected')) {
                                    $(this).addClass('border-primary bg-primary-subtle');
                                }
                            },
                            function() {
                                if (!$(this).hasClass('selected')) {
                                    $(this).removeClass('border-primary bg-primary-subtle');
                                }
                            }
                        );

                        // Update styling for pre-selected cards
                        $('.dokter-checkbox:checked').each(function() {
                            const doctorCard = $(this).closest('.doctor-card');
                            const cardElement = doctorCard.find('.doctor-option');
                            const indicator = doctorCard.find('.selected-indicator');

                            cardElement.addClass('selected border-success bg-success-subtle');
                            cardElement.removeClass('border-2');
                            indicator.removeClass('d-none');

                            console.log('Applied pre-selected styling to doctor ID:', $(this).val());
                        });

                        // Update hidden input with current selection
                        updateSelectedDoctors();

                        console.log('Checkboxes created successfully with', $('.dokter-checkbox:checked')
                            .length, 'pre-selected');

                    } else {
                        console.log('No doctors found for clinic');
                        placeholder.html(
                            '<div class="text-muted text-center py-3"><i class="ri-user-forbid-line me-2"></i>Tidak ada dokter tersedia untuk poliklinik ini</div>'
                        ).removeClass('d-none');
                        listContainer.addClass('d-none');
                        hiddenInput.val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error loading doctors:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });

                    placeholder.html(
                        '<div class="text-danger text-center py-3"><i class="ri-error-warning-line me-2"></i>Error memuat daftar dokter</div>'
                    ).removeClass('d-none');
                    listContainer.addClass('d-none');
                    hiddenInput.val('');
                }
            });
        }

        // Function to update hidden input with selected doctors and counter
        function updateSelectedDoctors() {
            const selectedValues = [];
            $('.dokter-checkbox:checked').each(function() {
                selectedValues.push($(this).val());
            });

            console.log('Selected doctors updated:', selectedValues);
            $('#dokter-selected').val(selectedValues.join(','));

            // Update counter badge
            const countBadge = $('#selected-count');
            const countText = $('#count-text');

            if (selectedValues.length > 0) {
                countBadge.removeClass('d-none');
                countText.text(`${selectedValues.length} dipilih`);

                if (selectedValues.length === 1) {
                    countBadge.removeClass('bg-primary').addClass('bg-info');
                    countText.text('1 dokter dipilih');
                } else {
                    countBadge.removeClass('bg-info').addClass('bg-primary');
                    countText.text(`${selectedValues.length} dokter dipilih`);
                }
            } else {
                countBadge.addClass('d-none');
            }

            // Update form validation indicator
            const container = $('#dokter-container');
            if (selectedValues.length > 0) {
                container.removeClass('border-danger').addClass('border-success');
            } else {
                container.removeClass('border-success border-danger');
            }

            return selectedValues;
        }

        // Event handler untuk poliklinik di form rawat jalan
        $('#clinic').on('change', function(e) {
            console.log('=== CLINIC CHANGED ===');
            console.log('New clinic value:', $(this).val());
            console.log('Clinic element ID:', $(this).attr('id'));
            console.log('Event triggered by:', (e && e.isTrigger) ? 'programmatic' : 'user');

            var clinicId = $(this).val();

            // Check if there are pre-selected doctors in data attribute
            var selectedDoctors = $('#dokter').data('selected');
            console.log('Pre-selected doctors found:', selectedDoctors);

            // Always call loadDoctorsByClinic regardless of selection
            loadDoctorsByClinic(clinicId, '#dokter', selectedDoctors);

            // Clear the data attribute after use
            if (selectedDoctors) {
                $('#dokter').removeData('selected');
                console.log('Cleared selected doctors data attribute');
            }

            console.log('=== CLINIC CHANGE COMPLETE ===');
        });

        // Event handler untuk poliklinik di form rawat darurat (IGD)
        // Note: IGD biasanya tidak memerlukan poliklinik, tapi jika ada implementasi di masa depan

        // Event handler untuk poliklinik di form rawat inap
        // Note: Rawat inap biasanya tidak memerlukan poliklinik, tapi jika ada implementasi di masa depan

        // [ENHANCED] Setup modal when shown for checkbox-based doctor selection
        $(document).on('shown.bs.modal', '#modal-rawatJalan', function() {
            console.log('====== MODAL RAWAT JALAN SHOWN (CHECKBOX VERSION) ======');
            console.log('Modal element:', this);

            // Check all elements exist
            var clinicElement = $('#clinic');
            var doctorContainer = $('#dokter-container');
            var doctorPlaceholder = $('#dokter-placeholder');
            var doctorList = $('#dokter-list');

            console.log('Clinic element found:', clinicElement.length > 0);
            console.log('Doctor container found:', doctorContainer.length > 0);
            console.log('Doctor placeholder found:', doctorPlaceholder.length > 0);
            console.log('Doctor list found:', doctorList.length > 0);

            if (clinicElement.length === 0) {
                console.error('CRITICAL: #clinic element not found in modal!');
                return;
            }

            // Initialize doctor container
            console.log('Initializing doctor container...');
            doctorPlaceholder.removeClass('d-none');
            doctorList.addClass('d-none');

            // Add change listener specifically for this modal session
            clinicElement.off('change.modal').on('change.modal', function() {
                console.log('MODAL CLINIC CHANGE DETECTED');
                var newClinicId = $(this).val();
                console.log('New clinic ID from modal:', newClinicId);

                // Call the new loadDoctorsByClinic function
                loadDoctorsByClinic(newClinicId, null, null);
            });

            var clinicId = clinicElement.val();
            console.log('Current clinic value:', clinicId);

            if (clinicId && clinicId !== '') {
                console.log('Clinic already selected, triggering doctor load:', clinicId);
                setTimeout(function() {
                    loadDoctorsByClinic(clinicId, null, null);
                }, 300);
            }


            console.log('====== MODAL SETUP COMPLETE ======');
        });

        // [UPDATED] Reset modal for checkbox-based doctor selection
        $(document).on('hidden.bs.modal', '#modal-rawatJalan', function() {
            console.log('Rawat Jalan modal hidden, cleaning up');

            // Add delay to avoid clearing while user is still interacting
            setTimeout(function() {
                console.log('Performing delayed modal cleanup');

                // Reset form fields
                // [FIX] Use .val('').trigger('change') for select2 elements to properly reset
                $("#clinic").val("").trigger('change');
                $("#jenis_jaminan").val("");
                $("#tujuan_kunjungan").val("");
                $("#btn-submit-rawatJalan").text("Simpan");
                $("#error-rawatJalan").addClass("d-none");

                // Reset doctor selection
                $('#dokter-placeholder').removeClass('d-none').html(
                    '<i class="ri-hospital-line me-2"></i>Pilih poliklinik terlebih dahulu untuk melihat daftar dokter'
                );
                $('#dokter-list').addClass('d-none').empty();
                $('#dokter-selected').val('');
                $('#dokter-container').removeClass('border-success border-danger');

                // Reset counter
                $('#selected-count').addClass('d-none');
                $('#count-text').text('0 dipilih');

                // Remove any event listeners to prevent memory leaks
                $('#dokter-list').off('change', '.dokter-checkbox'); // More specific event removal
                $('.doctor-option').off('click hover');

                console.log('Modal cleanup complete');
            }, 500);
        });

        // [DEBUG] Manual test function - remove after debugging
        window.testDoctorLoad = function(clinicId) {
            console.log('=== MANUAL TEST STARTED ===');
            console.log('Testing doctor load for clinic:', clinicId);
            console.log('Clinic dropdown exists:', $('#clinic').length > 0);
            console.log('Doctor dropdown exists:', $('#dokter').length > 0);
            console.log('Current clinic value:', $('#clinic').val());
            console.log('Current doctor HTML:', $('#dokter').html());

            if (!clinicId) {
                clinicId = $('#clinic').val() || 'cf973ebf-a9ac-4f69-9514-116de395f540'; // Default to Poliklinik Umum
            }

            loadDoctorsByClinic(clinicId, '#dokter');
        };

        // [DEBUG] Direct API test function
        window.testAPI = function(clinicId) {
            if (!clinicId) clinicId = 'cf973ebf-a9ac-4f69-9514-116de395f540'; // Poliklinik Umum

            console.log('=== DIRECT API TEST ===');
            var url = "{{ route('ajax.dokterByClinic', ':id') }}".replace(':id', clinicId);
            console.log('Testing URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    console.log('API Response:', response);
                    console.log('Response type:', typeof response);
                    console.log('Is array:', Array.isArray(response));
                    console.log('Length:', response.length);

                    if (response && response.length > 0) {
                        console.log('First doctor:', response[0]);

                        // Try to manually populate dropdown
                        var dropdown = $('#dokter');
                        dropdown.empty().append('<option value="">-- Pilih Dokter --</option>');

                        $.each(response, function(key, doctor) {
                            console.log('Adding doctor:', doctor);
                            dropdown.append('<option value="' + doctor.id + '">' + doctor.name +
                                '</option>');
                        });

                        console.log('Dropdown after manual population:', dropdown.html());
                    }
                },
                error: function(xhr, status, error) {
                    console.error('API Error:', {
                        xhr: xhr,
                        status: status,
                        error: error
                    });
                    console.error('Response text:', xhr.responseText);
                }
            });
        };

        // [DEBUG] Modal debug function
        window.debugModal = function() {
            console.log('=== MODAL DEBUG ===');
            console.log('Modal exists:', $('#modal-rawatJalan').length > 0);
            console.log('Modal visible:', $('#modal-rawatJalan').is(':visible'));
            console.log('Clinic element:', $('#clinic').length > 0, $('#clinic')[0]);
            console.log('Doctor element:', $('#dokter').length > 0, $('#dokter')[0]);
            console.log('Clinic value:', $('#clinic').val());
            console.log('Doctor HTML:', $('#dokter').html());
            console.log('Doctor options count:', $('#dokter option').length);
        };

        // [DEBUG] Force populate function
        window.forcePopulate = function() {
            console.log('=== FORCE POPULATE ===');
            var dropdown = $('#dokter');
            dropdown.empty();
            dropdown.append('<option value="">-- Pilih Dokter --</option>');
            dropdown.append('<option value="2">Dokter A (Test)</option>');
            dropdown.append('<option value="3">Dokter B (Test)</option>');
            console.log('Force populate complete:', dropdown.html());
            console.log('Options count:', dropdown.find('option').length);
        };

        // [DEBUG] Watch for dropdown resets with detailed tracking
        window.watchDropdown = function() {
            console.log('=== WATCHING DROPDOWN RESETS ===');
            const dropdown = $('#dokter')[0];
            if (!dropdown) {
                console.error('Dropdown not found!');
                return;
            }

            // Override empty() method to catch resets
            const originalEmpty = $.fn.empty;
            $.fn.empty = function() {
                if (this.is('#dokter')) {
                    console.warn('🚨 DROPDOWN BEING EMPTIED VIA .empty():', {
                        current_options: this.find('option').length,
                        timestamp: new Date().toISOString()
                    });
                    console.trace('Call stack:');
                }
                return originalEmpty.apply(this, arguments);
            };

            // Override html() method to catch innerHTML resets
            const originalHtml = $.fn.html;
            $.fn.html = function() {
                if (this.is('#dokter') && arguments.length > 0) {
                    const newHTML = arguments[0];
                    console.warn('🚨 DROPDOWN HTML BEING CHANGED VIA .html():', {
                        old_html: this.html(),
                        new_html: newHTML,
                        timestamp: new Date().toISOString()
                    });
                    console.trace('Call stack:');
                }
                return originalHtml.apply(this, arguments);
            };

            // Watch direct DOM manipulation
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.target.id === 'dokter') {
                        const optionsCount = mutation.target.children.length;
                        if (mutation.removedNodes.length > 0) {
                            console.warn('🗑️ DROPDOWN OPTIONS REMOVED (DOM manipulation):', {
                                removed_count: mutation.removedNodes.length,
                                remaining_count: optionsCount,
                                timestamp: new Date().toISOString()
                            });

                            // Log what was removed
                            Array.from(mutation.removedNodes).forEach(function(node, index) {
                                if (node.nodeType === 1 && node.tagName === 'OPTION') {
                                    console.log(`  Removed option [${index}]:`, node.value, node
                                        .textContent);
                                }
                            });
                            console.trace('DOM removal stack:');
                        }
                    }
                });
            });

            observer.observe(dropdown, {
                childList: true,
                subtree: true
            });

            // Monitor Select2 events
            $('#dokter').on(
                'select2:opening select2:open select2:closing select2:close select2:clearing select2:unselecting',
                function(e) {
                    console.warn('🔄 SELECT2 EVENT on #dokter:', e.type, {
                        options_count: this.children.length,
                        timestamp: new Date().toISOString()
                    });

                    if (e.type === 'select2:clearing') {
                        console.trace('Select2 clearing trace:');
                    }
                });

            console.log('Dropdown reset monitoring active with full tracking');
        };

        // Start monitoring automatically
        watchDropdown();

        // [DATA VERIFICATION] Check what data is actually in the database
        window.verifyData = function() {
            console.log('=== VERIFYING ACTUAL DATABASE DATA ===');

            // Test API call to get doctors by clinic
            $.ajax({
                url: "{{ route('ajax.dokterByClinic', 'cf973ebf-a9ac-4f69-9514-116de395f540') }}",
                type: 'GET',
                success: function(doctors) {
                    console.log('Available doctors from API:', doctors);
                    doctors.forEach(function(doctor) {
                        console.log(`  - Doctor: ${doctor.name}, ID: ${doctor.id}`);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Failed to get doctors:', error);
                }
            });

            // Test the edit data with actual encounter
            const encounterId = 'a00ef350-65df-476f-be61-5f39d9ffd3da';
            console.log('Testing edit data with encounter ID:', encounterId);

            $.ajax({
                url: "{{ route('pendaftaran.editEncounterRajal', ':id') }}".replace(':id', encounterId),
                type: 'GET',
                success: function(res) {
                    console.log('Edit encounter response:', res);
                    console.log('Doctor IDs from backend:', res.data.dokter_ids);
                    console.log('Doctor IDs type:', typeof res.data.dokter_ids);
                    console.log('Expected to match with API doctor IDs above');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to get encounter data:', error);
                }
            });
        };

        // [EDIT FORM TEST] Test function for edit form with pre-selected doctors
        window.testEditForm = function() {
            console.log('=== TESTING EDIT FORM WITH PRE-SELECTED DOCTORS ===');

            const clinicId = 'cf973ebf-a9ac-4f69-9514-116de395f540';
            const preSelectedDoctors = ['2', '3']; // IDs as strings (user.id, bukan id_petugas)

            console.log('Testing with clinic:', clinicId);
            console.log('Pre-selected doctors (user IDs):', preSelectedDoctors);
            console.log('Current checkbox system should now work with these IDs');

            // Call loadDoctorsByClinic with pre-selected doctors
            loadDoctorsByClinic(clinicId, null, preSelectedDoctors);

            // Check results after 3 seconds
            setTimeout(function() {
                console.log('=== EDIT FORM TEST RESULTS ===');
                console.log('Checked checkboxes count:', $('.dokter-checkbox:checked').length);
                console.log('Checked values:', $('.dokter-checkbox:checked').map(function() {
                    return this.value;
                }).get());
                console.log('Hidden input value:', $('#dokter-selected').val());
                console.log('Counter text:', $('#count-text').text());
                console.log('Selected cards have green styling:', $('.doctor-option.selected').length);
            }, 3000);
        };

        // [MANUAL CHECKBOX TEST] Direct manual test
        window.testManualSelect = function() {
            console.log('=== MANUAL CHECKBOX SELECTION TEST ===');

            // First load doctors without any pre-selection
            const clinicId = 'cf973ebf-a9ac-4f69-9514-116de395f540';
            loadDoctorsByClinic(clinicId, null, null);

            // After 3 seconds, manually select doctors [2, 3]
            setTimeout(function() {
                console.log('Setting manual selection: [2, 3]');

                // Check checkboxes with values 2 and 3
                $('.dokter-checkbox[value="2"]').prop('checked', true).trigger('change');
                $('.dokter-checkbox[value="3"]').prop('checked', true).trigger('change');

                setTimeout(function() {
                    console.log('Manual selection results:');
                    console.log('Checked count:', $('.dokter-checkbox:checked').length);
                    console.log('Checked values:', $('.dokter-checkbox:checked').map(function() {
                        return this.value;
                    }).get());
                    console.log('Hidden input:', $('#dokter-selected').val());
                    console.log('Green cards:', $('.doctor-option.selected').length);
                }, 1000);
            }, 3000);
        };

        // [FORCE TEST] Force test with hardcoded selected doctors
        window.forceTestSelect = function() {
            console.log('=== FORCE TEST WITH HARDCODED SELECTED DOCTORS ===');

            const clinicId = 'cf973ebf-a9ac-4f69-9514-116de395f540';
            const hardcodedSelected = ['2', '3']; // Force these IDs

            console.log('Forcing selection with IDs:', hardcodedSelected);

            // Call loadDoctorsByClinic with hardcoded selection
            loadDoctorsByClinic(clinicId, null, hardcodedSelected);

            setTimeout(function() {
                console.log('=== FORCE TEST RESULTS ===');
                console.log('Checkboxes checked:', $('.dokter-checkbox:checked').length);
                console.log('Checked IDs:', $('.dokter-checkbox:checked').map(function() {
                    return this.value;
                }).get());
                console.log('Cards with selected class:', $('.doctor-option.selected').length);
                console.log('Hidden input value:', $('#dokter-selected').val());
                console.log('Counter badge text:', $('#count-text').text());
            }, 3000);
        };

        // [SIMPLE TEST] Test checkbox pre-selection with exact user IDs
        window.simpleTest = function() {
            console.log('=== SIMPLE CHECKBOX TEST ===');

            const clinicId = 'cf973ebf-a9ac-4f69-9514-116de395f540';
            const userIds = ['2', '3']; // These should be user.id values

            console.log('Testing clinic:', clinicId);
            console.log('Testing with user IDs:', userIds);

            // Load doctors with pre-selection
            loadDoctorsByClinic(clinicId, null, userIds);

            // Check after loading
            setTimeout(function() {
                console.log('=== TEST RESULTS ===');
                const checkedBoxes = $('.dokter-checkbox:checked');
                const selectedCards = $('.doctor-option.selected');

                console.log('Found', checkedBoxes.length, 'checked checkboxes');
                console.log('Found', selectedCards.length, 'selected cards');

                checkedBoxes.each(function() {
                    console.log('- Checked checkbox value:', $(this).val());
                });

                console.log('Hidden input value:', $('#dokter-selected').val());
                console.log('Counter text:', $('#count-text').text());
            }, 2000);
        };

        // [SIMPLIFIED CARD TEST] Test function for simplified doctor card selection
        window.testNow = function() {
            console.log('=== SIMPLIFIED DOCTOR CARD TEST ===');
            console.log('Testing with Poliklinik Umum UUID');

            const clinicId = 'cf973ebf-a9ac-4f69-9514-116de395f540';

            console.log('Doctor container exists:', $('#dokter-container').length > 0);
            console.log('Doctor placeholder exists:', $('#dokter-placeholder').length > 0);
            console.log('Doctor list exists:', $('#dokter-list').length > 0);
            console.log('Counter badge exists:', $('#selected-count').length > 0);

            // Call our enhanced card-based function directly
            loadDoctorsByClinic(clinicId, null, null);

            // Check result after 3 seconds
            setTimeout(function() {
                console.log('=== ENHANCED CARD RESULTS ===');
                console.log('Doctor cards count:', $('.doctor-card').length);
                console.log('Checkboxes count:', $('.dokter-checkbox').length);
                console.log('Selected doctors:', $('#dokter-selected').val());
                console.log('Counter visible:', !$('#selected-count').hasClass('d-none'));
                console.log('Placeholder visible:', !$('#dokter-placeholder').hasClass('d-none'));
                console.log('List visible:', !$('#dokter-list').hasClass('d-none'));

                // Test card interaction after 1 more second
                setTimeout(function() {
                    if ($('.dokter-checkbox').length > 0) {
                        console.log('Testing automatic selection of first doctor...');
                        $('.dokter-checkbox').first().trigger('click');

                        setTimeout(function() {
                            console.log('After auto-selection:');
                            console.log('Selected count:', $('#count-text').text());
                            console.log('Selected doctors:', $('#dokter-selected').val());
                            console.log('First card has selected class:', $('.doctor-card')
                                .first().find('.doctor-option').hasClass('selected'));
                        }, 500);
                    }
                }, 1000);
            }, 3000);
        };

        // [DEBUG] Test when document ready - commented out to prevent auto-trigger
        setTimeout(function() {
            console.log('=== DOCUMENT READY TEST ===');
            console.log('Clinic element:', $('#clinic')[0]);
            console.log('Doctor element:', $('#dokter')[0]);
            console.log('Modal element:', $('#modal-rawatJalan')[0]);

            // Test manual clinic change - commented out to prevent interference
            // console.log('Testing manual clinic change trigger...');
            // if ($('#clinic').length > 0) {
            //     $('#clinic').val('1').trigger('change');
            // }

            console.log('Debug test complete. Available commands:');
            console.log('- testDoctorLoad() : Test doctor loading function');
            console.log('- testAPI() : Test API call directly');
            console.log('- debugModal() : Debug modal elements');
            console.log('- forcePopulate() : Force populate dropdown with test data');
        }, 2000);

        // [FIX] Handle city selection after province change on edit form
        $('#province_edit').on('change', function() {
            const cityDropdown = $('#city_edit');
            const selectedCity = cityDropdown.data('selected-city');
            if (selectedCity) {
                // Set a brief timeout to allow city options to be populated by the other event listener
                setTimeout(() => cityDropdown.val(selectedCity).trigger('change'), 200);
                cityDropdown.removeData('selected-city'); // Clean up
            }
        });
    </script>

    <style>
        /* Enhanced Doctor Card Styles */
        .doctor-card .doctor-option {
            border: 2px solid #e9ecef !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative;
            overflow: hidden;
        }

        .doctor-card .doctor-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }

        .doctor-card .doctor-option.selected {
            border-color: #198754 !important;
            background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e8 100%) !important;
            box-shadow: 0 4px 20px rgba(25, 135, 84, 0.15) !important;
            transform: translateY(-1px);
        }

        .doctor-card .doctor-option.selected::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #198754;
        }

        .doctor-card .avatar-title {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-top: 1px;
            /* slight adjustment for perfect alignment */
        }

        .doctor-card .selected-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            animation: bounceIn 0.5s ease;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .doctor-card .form-check {
            display: flex;
            align-items: center;
            height: 100%;
            margin: 0;
        }

        .doctor-card .form-check-input {
            border: 2px solid #6c757d;
            transition: all 0.2s ease;
            margin-top: 0 !important;
            vertical-align: middle;
        }

        .doctor-card .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
            transform: scale(1.05);
        }

        .doctor-card .card-body {
            display: flex;
            align-items: center;
            min-height: 65px;
        }

        .doctor-card h6 {
            line-height: 1.3;
            margin: 0;
        }

        .doctor-card .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        #dokter-container {
            transition: border-color 0.3s ease;
        }

        #selected-count {
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Loading state styling */
        #dokter-placeholder .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .doctor-card .card-body {
                padding: 0.75rem;
                min-height: 55px;
            }

            .doctor-card .avatar-title {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .doctor-card h6 {
                font-size: 0.9rem;
            }

            .doctor-card .form-check-input {
                width: 18px;
                height: 18px;
            }
        }
    </style>
@endpush
