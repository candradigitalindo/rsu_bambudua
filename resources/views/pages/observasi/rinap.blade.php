@extends('layouts.app')
@section('title')
    Pemeriksaan Rawat Inap
@endsection
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{ asset('vendor/quill/quill.core.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        .select2-container {
            width: 100% !important;
        }

        .input-group .select2-container--default .select2-selection--single {
            height: 100%;
            line-height: 2.4rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
        }
    </style>
    <style>
        /* Membuat diagnosis_description wrap dan tidak overflow */
        .table td,
        .table th {
            white-space: normal !important;
            word-break: break-word;
            vertical-align: top;
        }
    </style>
    <style>
        #modalViewDocument .modal-dialog {
            max-width: 80vw;
        }

        #documentContainer img {
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-view-doc {
            padding: 4px 8px;
            font-size: 12px;
        }
    </style>
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Form Pemeriksaan Pasien : {{ $getInpatientAdmission->encounter->name_pasien }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="custom-tabs-container">
                        <ul class="nav nav-tabs" id="customTab3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-anamnesis" data-bs-toggle="tab" href="#anamnesis"
                                    role="tab" aria-controls="anamnesis" aria-selected="true"><i
                                        class="ri-dossier-line"></i>Anamnesis</a>
                            </li>

                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-treatment" data-bs-toggle="tab" href="#treatment" role="tab"
                                    aria-controls="tindakan-medis" aria-selected="false"><i
                                        class="ri-stethoscope-line"></i>Tindakan/Prosedur</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-diagnosis" data-bs-toggle="tab" href="#diagnosis" role="tab"
                                    aria-controls="diagnosis" aria-selected="false"><i
                                        class="ri-health-book-line"></i>Diagnosis</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-daily" data-bs-toggle="tab" href="#daily" role="tab"
                                    aria-controls="daily" aria-selected="false"><i class="ri-capsule-fill"></i>Obat Harian
                                    Rinap</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-tatalaksana" data-bs-toggle="tab" href="#tatalaksana"
                                    role="tab" aria-controls="tatalaksana" aria-selected="false"><i
                                        class="ri-capsule-fill"></i>Resep Pulang</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-catatan" data-bs-toggle="tab" href="#catatan" role="tab"
                                    aria-controls="catatan" aria-selected="false"><i class="ri-draft-line"></i>Catatan</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="customTabContent3">
                            <div class="tab-pane fade show active" id="anamnesis" role="tabpanel">
                                <!-- Row starts -->
                                <div class="alert alert-danger print-error-msg mt-2 mb-2" style="display:none"
                                    id="error-anamnesis">
                                    <ul></ul>
                                </div>
                                <div class="row gx-3">
                                    <div class="col-sm-12 col-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="a2">Keluhan Utama</label>
                                            <div class="input-group">

                                                <textarea name="keluhan_utama" class="form-control" id="keluhan_utama" cols="10" rows="5">{{ old('keluhan_utama') }}</textarea>
                                            </div>
                                            <p class="text-danger">{{ $errors->first('keluhan_utama') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="a2">Riwayat Penyakit</label>
                                            <div class="input-group">

                                                <textarea name="riwayat_penyakit" class="form-control" id="riwayat_penyakit" cols="10" rows="5">{{ old('riwayat_penyakit') }}</textarea>
                                            </div>
                                            <p class="text-danger">{{ $errors->first('riwayat_penyakit') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="a2">Riwayat Penyakit keluarga</label>
                                            <div class="input-group">
                                                <textarea name="riwayat_penyakit_keluarga" class="form-control" id="riwayat_penyakit_keluarga" cols="10"
                                                    rows="5">{{ old('riwayat_penyakit_keluarga') }}</textarea>
                                            </div>
                                            <p class="text-danger">{{ $errors->first('riwayat_penyakit_keluarga') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary" id="btn-anamnesis">
                                        <span class="btn-txt" id="text-anamnesis">Simpan</span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                            id="spinner-anamnesis"></span>
                                    </button>
                                    <a href="{{ route('kunjungan.rawatJalan') }}" class="btn btn-secondary"
                                        id="btn-kembali-anamnesis">
                                        <span class="btn-txt" id="text-kembali-anamnesis">Kembali</span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                            id="spinner-kembali-anamnesis"></span>
                                    </a>
                                </div>
                                <!-- Row ends -->
                            </div>

                            <div class="tab-pane fade" id="treatment" role="tabpanel">
                                <!-- Row startss -->
                                <div class="row gx-3">
                                    <div class="col-xxl-4 col-sm-6">
                                        <div class="card mb-1">
                                            <div class="card-header">
                                                <h5 class="card-title">Tindakan</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Tipe</label>
                                                    <div class="input-group">
                                                        <select name="jenis_pemeriksaan" id="jenis_pemeriksaan"
                                                            class="form-control">
                                                            <option value="">Pilih Tipe Pemeriksaan</option>
                                                            <option value="Fisik"
                                                                {{ old('jenis_pemeriksaan') == 'Fisik' ? 'selected' : '' }}>
                                                                Fisik</option>
                                                            <option value="Penunjang"
                                                                {{ old('jenis_pemeriksaan') == 'Penunjang' ? 'selected' : '' }}>
                                                                Penunjang</option>
                                                            <option value="Konsultasi"
                                                                {{ old('jenis_pemeriksaan') == 'Konsultasi' ? 'selected' : '' }}>
                                                                Konsultasi</option>
                                                            <option value="Lab"
                                                                {{ old('jenis_pemeriksaan') == 'Lab' ? 'selected' : '' }}>
                                                                Laboratorium</option>
                                                            <option value="Radiologi"
                                                                {{ old('jenis_pemeriksaan') == 'Radiologi' ? 'selected' : '' }}>
                                                                Radiologi</option>
                                                            <option value="Lainnya"
                                                                {{ old('jenis_pemeriksaan') == 'Lainnya' ? 'selected' : '' }}>
                                                                Lainnya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Tindakan</label>
                                                    <div class="input-group">
                                                        <select name="jenis_tindakan" id="jenis_tindakan"
                                                            class="form-control">
                                                            <option value="">Pilih Tindakan</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Hasil Pemeriksaan</label>
                                                    <div class="col-sm-12">
                                                        <div id="fullEditor">

                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- treatment_date timestamp --}}
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Tanggal Tindakan</label>
                                                    <div class="input-group">
                                                        <input type="datetime-local" class="form-control"
                                                            id="treatment_date" name="treatment_date"
                                                            value="{{ old('treatment_date', now()->format('Y-m-d\TH:i')) }}">
                                                        <p class="text-danger">{{ $errors->first('treatment_date') }}</p>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Dokumen Pemeriksaan (Jika
                                                        ada)</label>
                                                    <div class="input-group">
                                                        <input type="file" class="form-control"
                                                            id="dokumen_pemeriksaan" name="dokumen_pemeriksaan">
                                                        <p class="text-danger">
                                                            {{ $errors->first('dokumen_pemeriksaan') }}</p>

                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 justify-content-end mt-4">
                                                    <button type="submit" class="btn btn-primary" id="btn-pemeriksaan">
                                                        <span class="btn-txt" id="text-pemeriksaan">Simpan</span>
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            id="spinner-pemeriksaan"></span>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-8 col-sm-12">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">Tabel Tindakan</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tanggal</th>
                                                                    <th>Tipe | Jenis Tindakan</th>
                                                                    <th>Hasil</th>
                                                                    <th>Dokumen Pendukung</th>
                                                                    <th>Pemeriksa</th>
                                                                    <th class="text-center">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbody-pendukung">


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row ends -->
                            </div>

                            <div class="tab-pane fade" id="diagnosis" role="tabpanel">
                                <!-- Row startss -->
                                <div class="row gx-3">
                                    <div class="col-xxl-6 col-sm-6">
                                        <div class="card mb-1">
                                            <div class="card-header">
                                                <h5 class="card-title">Diagnosis Medis</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Diagnosis (ICD10)</label>
                                                    <div class="input-group">
                                                        <select name="icd10_id" id="icd10_id" class="form-control">
                                                            <option value="">Pilih Jenis Diagnosis</option>

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Tipe Diagnosis</label>
                                                    <div class="input-group">
                                                        <select name="diagnosis_type" id="diagnosis_type"
                                                            class="form-control">
                                                            <option value="">Pilih Tipe Diagnosis</option>
                                                            <option value="Primer"
                                                                {{ old('diagnosis_type') == 'Primer' ? 'selected' : '' }}>
                                                                Primer</option>
                                                            <option value="Sekunder"
                                                                {{ old('diagnosis_type') == 'Sekunder' ? 'selected' : '' }}>
                                                                Sekunder</option>
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 justify-content-end mt-4">
                                                    <button type="submit" class="btn btn-primary"
                                                        id="btn-diagnosis-medis">
                                                        <span class="btn-txt" id="text-diagnosis-medis">Simpan</span>
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            id="spinner-diagnosis-medis"></span>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-6 col-sm-12">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">Data Diagnosis</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Aksi</th>
                                                                    <th>Kode</th>
                                                                    <th>Diagnosa</th>
                                                                    <th>Type</th>
                                                                    <th>Dokter</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbody-diagnosis">


                                                            </tbody>

                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="daily" role="tabpanel">
                                <!-- Row startss -->
                                <div class="row gx-3">
                                    <div class="col-xxl-4 col-sm-6">
                                        <div class="card mb-1">
                                            <div class="card-header">
                                                <h5 class="card-title">Obat Harian Rinap</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3" id="obat_harian">
                                                    <label class="form-label" for="a2">Obat</label>
                                                    <div class="input-group">
                                                        <select name="product_apotek_id_daily"
                                                            id="product_apotek_id_daily" class="form-control">
                                                            <option value="">Pilih Obat</option>

                                                        </select>

                                                    </div>
                                                    <label class="form-label mt-3" for="a2">Jumlah</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="jumlah_daily"
                                                            name="jumlah_daily" value="{{ old('jumlah_daily', 1) }}">
                                                        <p class="text-danger">{{ $errors->first('jumlah_daily') }}</p>
                                                    </div>
                                                    <label class="form-label mt-3" for="a2">Dosis</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="dosis_daily"
                                                            name="dosis_daily" value="{{ old('dosis_daily') }}">
                                                        <p class="text-danger">{{ $errors->first('dosis_daily') }}</p>
                                                    </div>
                                                    <label class="form-label mt-3" for="a2">Frequensi
                                                        (x/hari)</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="frequensi_daily"
                                                            name="frequensi_daily" value="{{ old('frequensi_daily') }}">
                                                        <p class="text-danger">{{ $errors->first('frequensi_daily') }}</p>
                                                    </div>
                                                    <label class="form-label mt-3" for="a2">Route (Oral, IV,
                                                        dll)</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="route_daily"
                                                            name="route_daily" value="{{ old('route_daily') }}">
                                                        <p class="text-danger">{{ $errors->first('route_daily') }}</p>
                                                    </div>
                                                    <label class="form-label mt-3" for="a2">Catatan</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="note_daily"
                                                            name="note_daily" value="{{ old('note_daily') }}">
                                                        <p class="text-danger">{{ $errors->first('note_daily') }}</p>
                                                    </div>
                                                    <label class="form-label mt-3" for="a2">Tanggal Mulai
                                                        Pemberian</label>
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" id="medicine_date"
                                                            name="medicine_date" value="{{ old('medicine_date') }}">
                                                        <p class="text-danger">{{ $errors->first('medicine_date') }}</p>
                                                    </div>
                                                    <div class="d-flex gap-2 justify-content-end mt-4">
                                                        <button type="submit" class="btn btn-primary"
                                                            id="btn-tambah-obat-daily">
                                                            <span class="btn-txt" id="text-tambah-obat-daily">Tambah
                                                                Obat</span>
                                                            <span class="spinner-border spinner-border-sm d-none"
                                                                id="spinner-tambah-obat-daily"></span>
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-8 col-sm-12">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">List Obat Harian</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Aksi</th>
                                                                    <th>Tanggal</th>
                                                                    <th>Nama Obat</th>
                                                                    <th>Aturan Pakai</th>
                                                                    <th>Keterangan</th>
                                                                    <th>Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbody-resep-daily">
                                                                <!-- Data resep diisi via JS -->
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="5" class="text-end fw-bold">Total</td>
                                                                    <td class="text-end fw-bold" id="total-resep-daily">
                                                                        Rp. 0
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tatalaksana" role="tabpanel">
                                <!-- Row startss -->
                                <div class="row gx-3">
                                    <div class="col-xxl-6 col-sm-6">
                                        <div class="card mb-1">
                                            <div class="card-header">
                                                <h5 class="card-title">Resep Obat</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">

                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Masa Resep (Hari)</label>

                                                    <form method="GET" class="mb-3">
                                                        <div class="input-group">
                                                            <input type="text" name="masa_pemakaian_hari"
                                                                class="form-control" placeholder="Jumlah Hari Resep"
                                                                id="masa_pemakaian_hari">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="btn-buat-resep">
                                                                <span id="text-buat-resep">Buat Resep</span>
                                                                <span class="spinner-border spinner-border-sm d-none"
                                                                    id="spinner-buat-resep" role="status"
                                                                    aria-hidden="true"></span>
                                                            </button>
                                                        </div>
                                                    </form>

                                                </div>
                                                <hr>
                                                <div class="mb-3 d-none" id="resep">
                                                    <label class="form-label" for="a2">Obat</label>
                                                    <div class="input-group">
                                                        <select name="product_apotek_id" id="product_apotek_id"
                                                            class="form-control">
                                                            <option value="">Pilih Obat</option>

                                                        </select>
                                                        <input type="hidden" name="product_apotek_id"
                                                            id="product_apotek_id"
                                                            value="{{ old('product_apotek_id') }}">

                                                    </div>
                                                    <label class="form-label mt-3" for="a2">Jumlah</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="qty"
                                                            name="qty" value="{{ old('qty', 1) }}">
                                                        <p class="text-danger">{{ $errors->first('qty') }}</p>
                                                    </div>
                                                    <label class="form-label mt-3" for="a2">Aturan Pakai</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="aturan_pakai"
                                                            name="aturan_pakai" value="{{ old('aturan_pakai') }}">
                                                        <p class="text-danger">{{ $errors->first('aturan_pakai') }}</p>
                                                    </div>
                                                    <div class="d-flex gap-2 justify-content-end mt-4">
                                                        <button type="submit" class="btn btn-primary"
                                                            id="btn-tambah-obat">
                                                            <span class="btn-txt" id="text-tambah-obat">Tambah
                                                                Obat</span>
                                                            <span class="spinner-border spinner-border-sm d-none"
                                                                id="spinner-tambah-obat"></span>
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-6 col-sm-12">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">List Obat Resep <span id="kode_resep"></span></h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Aksi</th>
                                                                    <th>Nama Obat</th>
                                                                    <th>Jumlah</th>
                                                                    <th>Aturan Pakai</th>
                                                                    <th>Harga</th>
                                                                    <th>Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbody-resep">
                                                                <!-- Data resep diisi via JS -->
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="5" class="text-end fw-bold">Total</td>
                                                                    <td class="text-end fw-bold" id="total-resep">Rp. 0
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="catatan" role="tabpanel">
                                <!-- Row startss -->
                                <div class="row gx-3">
                                    {{-- <div class="col-xxl-6 col-sm-12">
                                        <div class="card mb-1">
                                            <div class="card-header">
                                                <h5 class="card-title">Tindakan</h5>
                                                <hr class="mb-1">
                                            </div>
                                            <div class="card-body">

                                                <div class="mb-1">
                                                    <label class="form-label" for="a2">Diskon Tindakan</label>


                                                    <div class="input-group">
                                                        <input type="number" name="diskon_tindakan" class="form-control"
                                                            placeholder="Diskon Tindakan" id="diskon_tindakan">
                                                        <div class="input-group-text">%</div>
                                                        <button class="btn btn-primary" type="submit"
                                                            id="btn-buat-diskon-tindakan">
                                                            <span id="text-buat-diskon-tindakan">Buat Diskon</span>
                                                            <span class="spinner-border spinner-border-sm d-none"
                                                                id="spinner-buat-diskon-tindakan" role="status"
                                                                aria-hidden="true"></span>
                                                        </button>
                                                    </div>


                                                </div>
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Nama Tindakan</th>
                                                                    <th>Qty</th>
                                                                    <th>Harga</th>
                                                                    <th>Sub Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbody-catatan-tindakan">


                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="3" class="text-end fw-bold">Nominal
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <span id="total-tindakan" class="fw-bold">0</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3" class="text-end fw-bold">Diskon
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <span id="total-tindakan-diskon"
                                                                            class="fw-bold">0</span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                                                    <td class="text-end">
                                                                        <span id="total-tindakan-harga"
                                                                            class="fw-bold">0</span>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-6 col-sm-12">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">Resep</h5>
                                                <hr class="mb-1">
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-1">
                                                    <label class="form-label" for="a2">Diskon Resep</label>


                                                    <div class="input-group">
                                                        <input type="number" name="diskon_resep" class="form-control"
                                                            placeholder="Diskon Resep" id="diskon_resep">
                                                        <div class="input-group-text">%</div>
                                                        <button class="btn btn-primary" type="submit"
                                                            id="btn-buat-diskon-resep">
                                                            <span id="text-buat-diskon-resep">Buat Diskon</span>
                                                            <span class="spinner-border spinner-border-sm d-none"
                                                                id="spinner-buat-diskon-resep" role="status"
                                                                aria-hidden="true"></span>
                                                        </button>
                                                    </div>


                                                </div>
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>

                                                                    <th>Nama Obat</th>
                                                                    <th>Jumlah</th>
                                                                    <th>Aturan Pakai</th>
                                                                    <th>Harga</th>
                                                                    <th>Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbody-catatan-resep">
                                                                <!-- Data resep diisi via JS -->
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="4" class="text-end fw-bold">Nominal
                                                                    </td>
                                                                    <td class="text-end fw-bold" id="total-resep-catatan">
                                                                        Rp. 0</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="text-end fw-bold">Diskon
                                                                    </td>
                                                                    <td class="text-end fw-bold" id="total-resep-diskon">
                                                                        Rp. 0</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                                                    <td class="text-end fw-bold" id="total-resep-harga">
                                                                        Rp. 0</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-xxl-12 col-sm-12">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">Catatan</h5>
                                                <hr class="mb-1">
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Catatan Dokter</label>
                                                    <div class="col-sm-12">
                                                        <div id="catatanEditor" class="quill-editor">


                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Status Pulang</label>
                                                    <div class="col-sm-12">
                                                        <select name="status_pulang" id="status_pulang"
                                                            class="form-control">
                                                            <option value="">Pilih Status Pulang</option>
                                                            <option value="1" {{ old('status_pulang') == 1 ?: '' }}>
                                                                Kondisi Stabil</option>
                                                            <option value="2" {{ old('status_pulang') == 2 ?: '' }}>
                                                                Pulang Kontrol Kembali</option>

                                                            <option value="4" {{ old('status_pulang') == 4 ?: '' }}>
                                                                Rujukan RSU Lain</option>
                                                            <option value="5" {{ old('status_pulang') == 5 ?: '' }}>
                                                                Meninggal</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- button --}}
                                                <hr>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <button type="button" class="btn btn-primary"
                                                        id="btn-simpan-catatan">
                                                        <span id="text-simpan-catatan">Selesai Pemeriksaan</span>
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            id="spinner-simpan-catatan" role="status"
                                                            aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <!-- Row ends -->
        </div>
    </div>

    {{-- Modal View Document (Simple) --}}
    <div class="modal fade" id="modalViewDocument" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewDocumentLabel">Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="documentContainer" class="text-center">
                        <div class="spinner-border"></div>
                    </div>
                    <div id="documentInfo" class="mt-3 d-none">
                        <strong>Nama File:</strong>
                        <p id="documentName"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnDownloadDocument">
                        <i class="bi bi-download"></i> Download
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Sweet Alert JS -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- Quill Editor JS -->
    <script src="{{ asset('vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('vendor/quill/custom.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Helper function to manage button state during AJAX calls
        function setButtonState(buttonId, textId, spinnerHTMLId, isLoading, text) {
            const button = $(`#${buttonId}`);
            const textElement = $(`#${textId}`);
            const spinner = $(`#${spinnerHTMLId}`);

            if (isLoading) {
                button.prop('disabled', true);
                textElement.addClass('d-none');
                spinner.removeClass('d-none');
            } else {
                button.prop('disabled', false);
                textElement.removeClass('d-none').text(text);
                spinner.addClass('d-none');
            }
        }

        // Helper function for consistent Swal notifications
        function showSwal(message, type = 'success') {
            swal(message, {
                icon: type,
            });
        }

        // Helper function to handle AJAX errors
        function handleAjaxError(xhr, defaultMessage = 'Terjadi kesalahan saat memproses permintaan.') {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                let errorMsg = Object.values(xhr.responseJSON.errors).map(msgArr => msgArr.join('<br>')).join('<br>');
                swal({
                    title: "Validasi Gagal",
                    html: true,
                    text: errorMsg,
                    icon: "error"
                });
            } else {
                const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : defaultMessage;
                showSwal(message, 'error');
            }
        }

        $(document).ready(function() {
            // tab-anamnesis auto click
            autoClickTab(); // Call the function to auto-click the tab

            $("#btn-kembali-anamnesis").click(function() {
                $("#spiner-kembali-anamnesis").removeClass("d-none");
                $("#btn-kembali-anamnesis").addClass("disabled", true);
                $("#text-kembali-anamnesis").text("Mohon Tunggu ...");
            });

            function autoClickTab() {
                // ajax riwayat penyakit
                let url = "{{ route('observasi.riwayatPenyakit', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->encounter->id }}");
                $.ajax({
                    url: url,
                    type: "GET",
                    _token: "{{ csrf_token() }}", // Added CSRF token for security

                    success: function(data) {
                        $("#riwayat_penyakit").val(data.riwayatPenyakit.riwayat_penyakit);
                        $("#riwayat_penyakit_keluarga").val(data.riwayatPenyakit
                            .riwayat_penyakit_keluarga);
                        $("#keluhan_utama").val(data.anamnesis ? data.anamnesis.keluhan_utama :
                            ''); // Update keluhan_utama
                    }
                });
            }
            // tab-anamnesis click
            $("#tab-anamnesis").click(function() {
                autoClickTab(); // Call the function to auto-click the tab
            });
            // btn-anamnesis click
            $("#btn-anamnesis").click(function() {
                // ajax post anamnesis
                let url = "{{ route('observasi.postAnemnesis', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->encounter->id }}");
                let keluhan_utama = $("#keluhan_utama").val();
                let riwayat_penyakit = $("#riwayat_penyakit").val();
                let riwayat_penyakit_keluarga = $("#riwayat_penyakit_keluarga").val();
                // Validate input fields
                if (keluhan_utama == '') {
                    alert("Keluhan Utama tidak boleh kosong");
                    return;
                }
                if (riwayat_penyakit == '') {
                    alert("Riwayat Penyakit tidak boleh kosong");
                    return;
                }
                if (riwayat_penyakit_keluarga == '') {
                    alert("Riwayat Penyakit Keluarga tidak boleh kosong");
                    return;
                }
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        keluhan_utama: keluhan_utama,
                        riwayat_penyakit: riwayat_penyakit,
                        riwayat_penyakit_keluarga: riwayat_penyakit_keluarga,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        setButtonState('btn-anamnesis', 'text-anamnesis', 'spinner-anamnesis',
                            true);
                    },
                    success: function(data) {
                        if (data.status == 200) {
                            showSwal(data.message);
                        } else {
                            showSwal('Terjadi kesalahan saat menyimpan data.', 'error');
                        }
                    },
                    error: (xhr) => handleAjaxError(xhr),
                    complete: function() {
                        setButtonState('btn-anamnesis', 'text-anamnesis', 'spinner-anamnesis',
                            false, 'Simpan');
                    }
                });
            });

            // tab-treatment click (update yang sudah ada)
            $("#tab-treatment").click(function() {
                // ajax treatment
                let url = "{{ route('observasi.getInpatientTreatment', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->id }}");
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(data) {
                        console.log(data);
                        // Populate the table with data
                        let tbody = $("#tbody-pendukung");
                        tbody.empty(); // Clear existing rows
                        $.each(data, function(index, item) {
                            // Format kolom dokumen
                            let documentColumn = '';
                            if (item.document && item.document !== '' && item
                                .document !== null) {
                                documentColumn =
                                    `<button class="btn btn-info btn-sm btn-view-doc" data-doc="${item.document}" data-name="${item.tindakan_name || 'Dokumen'}" title="Lihat Dokumen"><i class="bi bi-eye">Dokumen</i></button>`;
                            } else {
                                documentColumn = '<span class="text-muted">-</span>';
                            }

                            tbody.append(
                                `<tr>
                                    <td>${item.treatment_date_formatted}</td>
                                    <td>${item.request_type} | ${item.tindakan_name}</td>
                                    <td>${item.result}</td>
                                    <td class="text-center">${documentColumn}</td>
                                    <td>${item.performed_by}</td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm btn-hapus-pemeriksaan" data-id="${item.id}">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>`
                            );
                        });

                        // Reset form (existing code)
                        $("#jenis_pemeriksaan").val(null);
                        quill.setContents(0);
                    }
                });
            });

            // Event handler untuk view dokumen (tambah ini di akhir document ready)
            $('#tbody-pendukung').on('click', '.btn-view-doc', function() {
                let docPath = $(this).data('doc');
                let docName = $(this).data('name');
                let docUrl = docPath;

                // Set modal content
                $('#modalViewDocumentLabel').text('Dokumen: ' + docName);
                $('#documentName').text(docName);

                // Detect file type
                let ext = docPath.split('.').pop().toLowerCase();
                let container = $('#documentContainer');

                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    // Image
                    container.html(`<img src="${docUrl}" class="img-fluid" style="max-height: 400px;">`);
                } else if (ext === 'pdf') {
                    // PDF
                    container.html(
                        `<embed src="${docUrl}" type="application/pdf" width="100%" height="400px">`);
                } else {
                    // Other files
                    container.html(`<div class="alert alert-info text-center">
                        <i class="bi bi-file-earmark fs-1"></i><br>
                        <p>File tidak dapat ditampilkan di browser</p>
                        <a href="${docUrl}" target="_blank" class="btn btn-primary">Download File</a>
                    </div>`);
                }

                // Set download button
                $('#btnDownloadDocument').off('click').on('click', function() {
                    window.open(docUrl, '_blank');
                });

                // Show modal
                $('#modalViewDocument').modal('show');
            });

            // Event delegation untuk tombol hapus
            $('#tbody-pendukung').on('click', '.btn-hapus-pemeriksaan', function() {
                let id = $(this).data('id');
                // Konfirmasi hapus
                swal({
                    title: "Apakah Anda yakin?",
                    text: "Data ini akan dihapus!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        let url = "{{ route('observasi.deleteInpatientTreatment', ':id') }}"
                            .replace(':id', id);
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data) {
                                swal(data.message, {
                                    icon: "success"
                                });
                                $("#tab-treatment").trigger('click'); // Refresh tabel
                            },
                            error: function() {
                                swal('Terjadi kesalahan saat menghapus data.', {
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            // btn-pemeriksaan click
            $("#btn-pemeriksaan").click(function() {
                // URL untuk AJAX
                let url = "{{ route('observasi.postInpatientTreatment', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->id }}");

                // Ambil data dari form
                let type = $("#jenis_pemeriksaan").val();
                let tindakan_id = $("#jenis_tindakan").val();
                let result = quill.root.innerHTML; // Ambil isi editor
                let document = $("#dokumen_pemeriksaan")[0].files[0]; // Ambil file
                let treatment_date = $("#treatment_date").val(); // Ambil tanggal tindakan

                // Validasi input
                if (type == '') {
                    alert("Tipe Pemeriksaan tidak boleh kosong");
                    return;
                }
                if (result == '') {
                    alert("Hasil Pemeriksaan tidak boleh kosong");
                    return;
                }
                if (tindakan_id == '') {
                    alert("Tindakan tidak boleh kosong");
                    return;
                }
                if (document && document.size > 5048000) { // 5MB
                    alert("Dokumen Pemeriksaan tidak boleh lebih dari 5MB");
                    return;
                }
                if (treatment_date == '') {
                    alert("Tanggal Tindakan tidak boleh kosong");
                    return;
                }

                // Buat objek FormData
                let formData = new FormData();
                formData.append("type", type);
                formData.append("result", result);
                formData.append("document", document); // Tambahkan file
                formData.append("tindakan_id", tindakan_id);
                formData.append("treatment_date", treatment_date);
                formData.append("_token", "{{ csrf_token() }}"); // Tambahkan CSRF token

                // Kirim data melalui AJAX
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false, // Jangan proses data
                    contentType: false, // Jangan tetapkan header Content-Type
                    beforeSend: function() {
                        setButtonState('btn-pemeriksaan', 'text-pemeriksaan',
                            'spinner-pemeriksaan', true);
                    },
                    success: function(data) {
                        if (data.status == 200) {
                            showSwal(data.message);
                            // Refresh the table after successful submission
                            $("#tab-treatment").click();
                        } else {
                            showSwal('Terjadi kesalahan saat menyimpan data.', 'error');
                        }
                    },
                    error: (xhr) => {
                        handleAjaxError(xhr, 'Terjadi kesalahan saat mengirim data.');
                    },
                    complete: function() {
                        setButtonState('btn-pemeriksaan', 'text-pemeriksaan',
                            'spinner-pemeriksaan', false, 'Simpan');
                    }
                });
            });

            $('#jenis_tindakan').select2({
                placeholder: 'Pilih Tindakan',
                allowClear: true,
                width: '100%',

            });

            // tab-treatment-medis click
            $("#tab-treatment").click(function() {
                // ajax getTindakan jenis_tindakan
                let url = "{{ route('observasi.getTindakan', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->id }}");
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        let select = $("#jenis_tindakan");
                        select.empty(); // Clear existing options
                        select.append('<option value="">Pilih Jenis Tindakan</option>');
                        $.each(data, function(index, item) {
                            select.append(
                                `<option value="${item.id}">${item.name}</option>`
                            );
                        });
                    }
                });
                // ajax getTindakanEncounter
                let url2 = "{{ route('observasi.getTindakanEncounter', ':id') }}";
                url2 = url2.replace(':id', "{{ $getInpatientAdmission->id }}");
                $.ajax({
                    url: url2,
                    type: "GET",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        // Populate the table with data
                        let tbody = $("#tbody-tindakan");
                        tbody.empty(); // Clear existing rows
                        let total_harga = 0;
                        $.each(data, function(index, item) {
                            tbody.append(
                                `<tr>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm btn-hapus-tindakan" data-id="${item.id}">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </td>
                                        <td>${item.tindakan_name}</td>
                                        <td>${item.qty}</td>
                                        <td class="text-end">${formatRupiah(item.tindakan_harga)}</td>
                                        <td class="text-end">${formatRupiah(item.total_harga)}</td>
                                    </tr>`
                            );
                            total_harga += item.tindakan_harga * item.qty;
                        });
                        $("#total-harga").text(formatRupiah(total_harga));
                    }
                });
            });
            // format rupiah
            function formatRupiah(angka, prefix) {
                angka = angka ? angka.toString() : '0'; // Pastikan angka adalah string
                let number_string = angka.replace(/[^,\d]/g, ''),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
            }

            // tab-diagnosis click
            $("#tab-diagnosis").click(function() {
                // Kosongkan kolom ICD10 dan Diagnosis Type
                $("#icd10_id").val(null).trigger('change'); // untuk select2
                $("#diagnosis_type").val(''); // untuk select biasa

                // ajax getDiagnosis
                let url = "{{ route('observasi.getDiagnosis', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->encounter_id }}");
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        // Populate the table with data
                        let tbody = $("#tbody-diagnosis");
                        tbody.empty(); // Clear existing rows
                        $.each(data, function(index, item) {
                            tbody.append(
                                `<tr>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm btn-hapus-diagnosis" data-id="${item.id}">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </td>
                                        <td>${item.diagnosis_code}</td>
                                        <td>${item.diagnosis_description}</td>
                                        <td>${item.diagnosis_type}</td>
                                        <td>${item.petugas_name}</td>
                                    </tr>`
                            );
                        });
                    }
                });
            });
            // btn-diagnosis-medis click
            $("#btn-diagnosis-medis").click(function() {
                // ajax post diagnosis medis
                let url = "{{ route('observasi.postDiagnosis', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->encounter_id }}");
                let icd10_id = $("#icd10_id").val();
                let diagnosis_type = $("#diagnosis_type").val();
                if (icd10_id == '') {
                    alert("Jenis Diagnosis tidak boleh kosong");
                    return;
                }
                if (diagnosis_type == '') {
                    alert("Tipe Diagnosis tidak boleh kosong");
                    return;
                }
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        icd10_id: icd10_id,
                        diagnosis_type: diagnosis_type,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        setButtonState('btn-diagnosis-medis', 'text-diagnosis-medis',
                            'spinner-diagnosis-medis', true);
                    },
                    success: function(data) {
                        if (data.status == 200) {
                            showSwal(data.message);
                            // Refresh the table after successful submission
                            $("#tab-diagnosis").click();
                        } else {
                            showSwal('Terjadi kesalahan saat menyimpan data.', 'error');
                        }
                    },
                    complete: function() {
                        setButtonState('btn-diagnosis-medis', 'text-diagnosis-medis',
                            'spinner-diagnosis-medis', false, 'Simpan');
                    }
                });
            });
            // tab-tatalaksana click
            $('#icd10_id').select2({
                placeholder: 'Cari kode atau nama diagnosis...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('observasi.getIcd10', $getInpatientAdmission->encounter_id) }}", // sesuaikan dengan route Anda
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term // kata kunci pencarian
                        };
                    },
                    processResults: function(data) {
                        // Jika response adalah array langsung:
                        if (Array.isArray(data)) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.code,
                                        text: item.code + ' - ' + item.description
                                    }
                                })
                            };
                        }
                        // Jika response adalah object dengan key 'data'
                        if (data.data && Array.isArray(data.data)) {
                            return {
                                results: data.data.map(function(item) {
                                    return {
                                        id: item.code,
                                        text: item.description + (item.code ? ' - [' + item
                                            .code + ']' : '')
                                    }
                                })
                            };
                        }
                        // Jika response tidak sesuai, kembalikan array kosong
                        return {
                            results: []
                        };
                    },
                    cache: true
                }
            });
            // Event delegation untuk tombol hapus
            $('#tbody-diagnosis').on('click', '.btn-hapus-diagnosis', function() {
                let id = $(this).data('id');
                // Konfirmasi hapus
                swal({
                    title: "Apakah Anda yakin?",
                    text: "Data ini akan dihapus!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        let url = "{{ route('observasi.deleteDiagnosis', ':id') }}"
                            .replace(':id', id);
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data) {

                                if (data.status == true) {
                                    swal(data.message, {
                                        icon: "success"
                                    });
                                } else {
                                    swal(data.message, {
                                        icon: "error"
                                    });

                                }
                                $("#tab-diagnosis")
                                    .click(); // Refresh tabel
                            },
                            error: function() {
                                swal('Terjadi kesalahan saat menghapus data.', {
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
            $("#tab-daily").click(function() {
                // ajax getInpatientDailyMedications
                let url = "{{ route('observasi.getInpatientDailyMedications', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->id }}");
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        console.log(data);
                        // Populate the table with data
                        let tbody = $("#tbody-resep-daily");
                        tbody.empty();
                        let total = 0;
                        $.each(data, function(index, item) {
                            if (item.status == "Diajukan") {
                                tbody.append(
                                    `<tr>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm btn-hapus-resep-daily" data-id="${item.id}">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                            <button class="btn btn-primary btn-sm btn-diserahkan-resep-daily" data-id="${item.id}">
                                                <i class="bi bi-check"></i> Diserahkan
                                            </button>
                                        </td>
                                        <td>
                                            Tanggal Anjuran : ${item.medicine_date} <br>
                                            Tanggal Diberikan : ${item.administered_date || '-'}
                                        </td>
                                        <td>${item.medication_name}</td>
                                        <td>${item.dosage_instructions + ' | ' + item.route + ' | ' + item.frequency}</td>
                                        <td>
                                            <span class="badge bg-success">Dokter : ${item.authorized_name}</span><br>
                                            <span class="badge bg-primary">Perawat : ${item.administered_name}</span>
                                        </td>
                                        <td class="text-end">${formatRupiah(item.total, 'Rp. ')}</td>

                                    </tr>`
                                );
                                total += parseInt(item.total || 0);
                            } else {
                                tbody.append(
                                    `<tr>
                                        <td class="text-center">

                                        </td>
                                        <td>
                                            Jadwal : ${item.medicine_date} <br>
                                            Diberikan : ${item.administered_at || '-'}
                                        </td>
                                        <td>${item.medication_name}</td>
                                        <td>${item.dosage_instructions + ' | ' + item.route + ' | ' + item.frequency}</td>
                                        <td>
                                            <span class="badge bg-success">Dokter : ${item.authorized_name}</span><br>
                                            <span class="badge bg-primary">Perawat : ${item.administered_name}</span>
                                        </td>
                                        <td class="text-end">${formatRupiah(item.total, 'Rp. ')}</td>

                                    </tr>`
                                );
                                total += parseInt(item.total || 0);
                            }
                        });


                        $("#total-resep-daily").text(formatRupiah(total, 'Rp. '));
                    }
                });
            });
            $('#product_apotek_id_daily').select2({
                placeholder: 'Pilih Obat',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('observasi.getProdukApotek', $getInpatientAdmission->encounter_id) }}", // sesuaikan dengan route Anda
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term // kata kunci pencarian
                        };
                    },
                    processResults: function(data) {
                        // Jika response adalah array langsung:
                        if (Array.isArray(data)) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + ' - [' + item.satuan + ']' + (item
                                            .harga ? ' - [' + formatRupiah(
                                                item.harga, 'Rp. ') + ']' : '')
                                    }
                                })
                            };
                        }
                        // Jika response adalah object dengan key 'data'
                        if (data.data && Array.isArray(data.data)) {
                            return {
                                results: data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + ' - [' + item.satuan + ']' + (item
                                            .harga ? ' - [' + formatRupiah(
                                                item.harga, 'Rp. ') + ']' : '')
                                    }
                                })
                            };
                        }
                        // Jika response tidak sesuai, kembalikan array kosong
                        return {
                            results: []
                        };
                    },
                    cache: true
                }

            });
            // btn-tambah-obat-daily click
            $("#btn-tambah-obat-daily").click(function(e) {
                e.preventDefault();
                // Validasi input
                let product_apotek_id_daily = $("#product_apotek_id_daily").val();
                let jumlah_daily = $("#jumlah_daily").val();
                let dosis_daily = $("#dosis_daily").val();
                let frequensi_daily = $("#frequensi_daily").val();
                let route_daily = $("#route_daily").val();
                let note_daily = $("#note_daily").val();
                let medicine_date = $("#medicine_date").val();
                if (product_apotek_id_daily == '') {
                    alert("Obat tidak boleh kosong");
                    return;
                }
                if (jumlah_daily == '') {
                    alert("Jumlah tidak boleh kosong");
                    return;
                }
                if (dosis_daily == '') {
                    alert("Dosis tidak boleh kosong");
                    return;
                }
                if (frequensi_daily == '') {
                    alert("Frekuensi tidak boleh kosong");
                    return;
                }
                if (route_daily == '') {
                    alert("Rute tidak boleh kosong");
                    return;
                }
                if (medicine_date == '') {
                    alert("Tanggal obat tidak boleh kosong");
                    return;
                }

                // Tampilkan spinner dan disable tombol
                setButtonState('btn-tambah-obat-daily', 'text-tambah-obat-daily',
                    'spinner-tambah-obat-daily', true);
                // ajax post resep
                let url = "{{ route('observasi.postInpatientDailyMedication', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->id }}");
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_apotek_id: product_apotek_id_daily,
                        jumlah: jumlah_daily,
                        dosage_instructions: dosis_daily,
                        frequensi: frequensi_daily,
                        route: route_daily,
                        notes: note_daily,
                        medicine_date: medicine_date
                    },
                    success: function(data) {
                        if (data.status == 200) {
                            showSwal(data.message);
                            $("#tab-daily")
                                .click(); // Refresh the table after successful submission
                        } else {
                            showSwal(data.message, 'error');
                            $("#tab-daily")
                                .click(); // Refresh the table even if there's an error
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    },
                    complete: function() {
                        setButtonState('btn-tambah-obat-daily', 'text-tambah-obat-daily',
                            'spinner-tambah-obat-daily', false, 'Tambah Obat');
                    }
                });
            });

            $('#tbody-resep-daily').on('click', '.btn-hapus-resep-daily', function() {
                let id = $(this).data('id');
                // Konfirmasi hapus
                swal({
                    title: "Apakah Anda yakin?",
                    text: "Data ini akan dihapus!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        let url = "{{ route('observasi.deleteInpatientDailyMedication', ':id') }}"
                            .replace(':id', id);
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data) {
                                if (data.status == 200) {
                                    swal(data.message, {
                                        icon: "success"
                                    });
                                } else {
                                    swal(data.message, {
                                        icon: "error"
                                    });

                                }
                                $("#tab-daily")
                                    .click(); // Refresh tabel
                            },
                            error: function() {
                                swal('Terjadi kesalahan saat menghapus data.', {
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
            $('#tbody-resep-daily').on('click', '.btn-diserahkan-resep-daily', function() {
                let id = $(this).data('id');
                // Konfirmasi hapus
                swal({
                    title: "Apakah Anda yakin sudah diberikan ke pasien?",
                    text: "Jika sudah diberikan data ini tidak bisa dihapus!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        let url =
                            "{{ route('observasi.updateInpatientDailyMedicationStatus', ':id') }}"
                            .replace(':id', id);
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data) {
                                if (data.status == 200) {
                                    swal(data.message, {
                                        icon: "success"
                                    });
                                } else {
                                    swal(data.message, {
                                        icon: "error"
                                    });

                                }
                                $("#tab-daily")
                                    .click(); // Refresh tabel
                            },
                            error: function() {
                                swal('Terjadi kesalahan saat menghapus data.', {
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
            $("#tab-tatalaksana").click(function() {
                // ajax getResep
                let url = "{{ route('observasi.getResep', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->encounter_id }}");
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        // Jika data resep ada, tampilkan form resep
                        if (data.id) {
                            $("#resep").removeClass("d-none");
                            $("#kode_resep").text("[" + data.kode_resep + "] " + data
                                .masa_pemakaian_hari + " hari");
                        } else {
                            $("#resep").addClass("d-none");
                        }

                        // Populate the table with data
                        let tbody = $("#tbody-resep");
                        tbody.empty();
                        let total = 0;
                        if (data && data.details) {
                            $.each(data.details, function(index, item) {
                                tbody.append(
                                    `<tr>
                                            <td class="text-center">
                                                <button class="btn btn-danger btn-sm btn-hapus-resep" data-id="${item.id}">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </td>
                                            <td>${item.nama_obat}</td>
                                            <td>${item.qty}</td>
                                            <td>${item.aturan_pakai}</td>
                                            <td class="text-end">${formatRupiah(item.harga, 'Rp. ')}</td>
                                            <td class="text-end">${formatRupiah(item.total_harga, 'Rp. ')}</td>
                                        </tr>`
                                );
                                total += parseInt(item.total_harga || 0);
                            });
                        }
                        $("#total-resep").text(formatRupiah(total, 'Rp. '));
                    }
                });
            });
            $('#product_apotek_id').select2({
                placeholder: 'Pilih Obat',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('observasi.getProdukApotek', $getInpatientAdmission->encounter_id) }}", // sesuaikan dengan route Anda
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term // kata kunci pencarian
                        };
                    },
                    processResults: function(data) {
                        // Jika response adalah array langsung:
                        if (Array.isArray(data)) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + (item.harga ? ' - [' + formatRupiah(
                                            item.harga, 'Rp. ') + ']' : '')
                                    }
                                })
                            };
                        }
                        // Jika response adalah object dengan key 'data'
                        if (data.data && Array.isArray(data.data)) {
                            return {
                                results: data.data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + (item.harga ? ' - [' + formatRupiah(
                                            item.harga, 'Rp. ') + ']' : '')
                                    }
                                })
                            };
                        }
                        // Jika response tidak sesuai, kembalikan array kosong
                        return {
                            results: []
                        };
                    },
                    cache: true
                }

            });
            // btn-resep click
            $("#btn-buat-resep").click(function(e) {
                e.preventDefault();
                // validasi input masa_pemakaian_hari
                let masa_pemakaian_hari = $("#masa_pemakaian_hari").val();
                if (masa_pemakaian_hari == '') {
                    alert("Jumlah hari tidak boleh kosong");
                    return;
                }
                // Tampilkan spinner dan disable tombol
                setButtonState('btn-buat-resep', 'text-buat-resep', 'spinner-buat-resep', true);
                // ajax post resep
                let url = "{{ route('observasi.postResep', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->encounter_id }}");
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        masa_pemakaian_hari: masa_pemakaian_hari
                    },
                    success: function(data) {
                        if (data.status == 200) {
                            showSwal(data.message);
                            // Tampilkan kolom resep
                            $("#resep").removeClass("d-none");
                            $("#kode_resep").text("[" + data.kode_resep + "] " + data
                                .masa_pemakaian_hari + " hari");
                        } else {
                            showSwal(data.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, 'Terjadi kesalahan saat menyimpan data.');
                    },
                    complete: function() {
                        setButtonState('btn-buat-resep', 'text-buat-resep',
                            'spinner-buat-resep', false, 'Buat Resep');
                    }
                });
            });
            // btn-tambah-obat click
            $("#btn-tambah-obat").click(function(e) {
                e.preventDefault();
                // validasi input
                let product_apotek_id = $("#product_apotek_id").val();
                let qty = $("#qty").val();
                let aturan_pakai = $("#aturan_pakai").val();
                if (product_apotek_id == '') {
                    alert("Obat tidak boleh kosong");
                    return;
                }
                if (qty == '') {
                    alert("Jumlah tidak boleh kosong");
                    return;
                }
                if (aturan_pakai == '') {
                    alert("Aturan pakai tidak boleh kosong");
                    return;
                }
                // Tampilkan spinner dan disable tombol
                setButtonState('btn-tambah-obat', 'text-tambah-obat', 'spinner-tambah-obat', true);
                let url = "{{ route('observasi.postResepDetail', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->encounter_id }}");
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_apotek_id: product_apotek_id,
                        qty: qty,
                        aturan_pakai: aturan_pakai
                    },
                    success: function(data) {
                        if (data.status == 200) {
                            showSwal(data.message);
                            // Refresh the table after successful submission
                            $("#tab-tatalaksana").click();
                        } else {
                            showSwal(data.message, 'error');
                            $("#tab-tatalaksana").click();
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    },
                    complete: function() {
                        setButtonState('btn-tambah-obat', 'text-tambah-obat',
                            'spinner-tambah-obat', false, 'Tambah Obat');
                    }
                });
            });
            // Hapus obat
            $('#tbody-resep').on('click', '.btn-hapus-resep', function() {
                let id = $(this).data('id');
                // Konfirmasi hapus
                swal({
                    title: "Apakah Anda yakin?",
                    text: "Data ini akan dihapus!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        let url = "{{ route('observasi.deleteResepDetail', ':id') }}"
                            .replace(':id', id);
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data) {
                                if (data.status == true) {
                                    swal(data.message, {
                                        icon: "success"
                                    });
                                } else {
                                    swal(data.message, {
                                        icon: "error"
                                    });

                                }
                                $("#tab-tatalaksana")
                                    .click(); // Refresh tabel
                            },
                            error: function() {
                                swal('Terjadi kesalahan saat menghapus data.', {
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
           
            // btn-simpan-catatan
            $("#btn-simpan-catatan").click(function(e) {
                e.preventDefault();
                // validasi input
                let status_pulang = $("#status_pulang").val();
                let catatan = quillCatatan.root.innerHTML;
                if (status_pulang == '') {
                    alert("Status Pulang tidak boleh kosong");
                    return;
                }
                // Tampilkan spinner dan disable tombol
                setButtonState('btn-simpan-catatan', 'text-simpan-catatan', 'spinner-simpan-catatan', true);
                let url = "{{ route('observasi.postCatatanEncounter', ':id') }}";
                url = url.replace(':id', "{{ $getInpatientAdmission->encounter_id }}");
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        // ambil isi catatanEditor
                        catatan: catatan,
                        status_pulang: status_pulang
                    },
                    success: function(data) {
                        if (data.success == true) {
                            showSwal(data.message);
                            // redirect ke halaman observasi
                            window.location.href = data.url;

                        } else {
                            showSwal(data.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, 'Terjadi kesalahan saat menyimpan data.');
                    },
                    complete: function() {
                        setButtonState('btn-simpan-catatan', 'text-simpan-catatan',
                            'spinner-simpan-catatan', false, 'Selesai Pemeriksaan');
                    }
                });
            });
        });
    </script>
@endpush
