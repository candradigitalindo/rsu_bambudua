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
                                                            <option value="Visit"
                                                                {{ old('jenis_pemeriksaan') == 'Visit' ? 'selected' : '' }}>
                                                                Visit</option>
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
                                                                    <th>Jumlah</th>
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
                                                                    <th>Qty</th>
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
                                                                    <td colspan="6" class="text-end fw-bold">Total</td>
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
                                                                    <th>Qty</th>
                                                                    <th>Nama Obat</th>
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
        $(document).ready(function() {
            const admissionId = "{{ $getInpatientAdmission->id }}";
            const encounterId = "{{ $getInpatientAdmission->encounter_id }}";
            const csrfToken = "{{ csrf_token() }}";

            const ROUTES = {
                riwayatPenyakit: "{{ route('observasi.riwayatPenyakit', ':id') }}",
                postAnemnesis: "{{ route('observasi.postAnemnesis', ':id') }}",
                getInpatientTreatment: "{{ route('observasi.getInpatientTreatment', ':id') }}",
                postInpatientTreatment: "{{ route('observasi.postInpatientTreatment', ':id') }}",
                deleteInpatientTreatment: "{{ route('observasi.deleteInpatientTreatment', ':id') }}",
                getTindakan: "{{ route('observasi.getTindakan', ':id') }}",
                getDiagnosis: "{{ route('observasi.getDiagnosis', ':id') }}",
                postDiagnosis: "{{ route('observasi.postDiagnosis', ':id') }}",
                deleteDiagnosis: "{{ route('observasi.deleteDiagnosis', ':id') }}",
                getIcd10: "{{ route('observasi.getIcd10', ':id') }}",
                getDailyMedications: "{{ route('observasi.getInpatientDailyMedications', ':id') }}",
                postDailyMedication: "{{ route('observasi.postInpatientDailyMedication', ':id') }}",
                deleteDailyMedication: "{{ route('observasi.deleteInpatientDailyMedication', ':id') }}",
                updateDailyMedicationStatus: "{{ route('observasi.updateInpatientDailyMedicationStatus', ':id') }}",
                getResep: "{{ route('observasi.getResep', ':id') }}",
                postResep: "{{ route('observasi.postResep', ':id') }}",
                postResepDetail: "{{ route('observasi.postResepDetail', ':id') }}",
                deleteResepDetail: "{{ route('observasi.deleteResepDetail', ':id') }}",
                getProdukApotek: "{{ route('observasi.getProdukApotek', ':id') }}",
                postCatatanEncounter: "{{ route('observasi.postCatatanEncounter', ':id') }}",
            };

            // --- Helper Functions ---
            const setButtonLoading = (btn, isLoading) => {
                const spinner = btn.find('.spinner-border');
                const text = btn.find('.btn-txt');
                if (isLoading) {
                    btn.prop('disabled', true);
                    spinner.removeClass('d-none');
                    text.addClass('d-none');
                } else {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    text.removeClass('d-none');
                }
            };

            const showNotification = (message, type = 'success') => swal(message, {
                icon: type
            });

            const ajaxRequest = (options) => {
                return $.ajax({
                    ...options,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                });
            };

            const formatRupiah = (angka, prefix) => {
                angka = angka ? String(angka) : '0';
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    rupiah += (sisa ? '.' : '') + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix === undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
            };

            const confirmAction = (title, text) => {
                return swal({
                    title,
                    text,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true
                });
            };

            // --- Anamnesis Tab ---
            const loadAnamnesis = async () => {
                try {
                    const data = await ajaxRequest({
                        url: ROUTES.riwayatPenyakit.replace(':id', encounterId)
                    });
                    $("#riwayat_penyakit").val(data.riwayatPenyakit.riwayat_penyakit);
                    $("#riwayat_penyakit_keluarga").val(data.riwayatPenyakit.riwayat_penyakit_keluarga);
                    $("#keluhan_utama").val(data.anamnesis ? data.anamnesis.keluhan_utama : '');
                } catch (error) {
                    console.error("Gagal memuat anamnesis:", error);
                }
            };

            $('#btn-anamnesis').on('click', async function() {
                const btn = $(this);
                const payload = {
                    dokter_id: "{{ $getInpatientAdmission->doctor->id ?? '' }}",
                    keluhan_utama: $("#keluhan_utama").val(),
                    riwayat_penyakit: $("#riwayat_penyakit").val(),
                    riwayat_penyakit_keluarga: $("#riwayat_penyakit_keluarga").val(),
                };

                if (!payload.keluhan_utama || !payload.riwayat_penyakit || !payload
                    .riwayat_penyakit_keluarga) {
                    return showNotification("Semua field anamnesis harus diisi.", "error");
                }

                setButtonLoading(btn, true);
                try {
                    const response = await ajaxRequest({
                        url: ROUTES.postAnemnesis.replace(':id', encounterId),
                        method: 'POST',
                        data: payload
                    });
                    showNotification(response.message);
                } catch (error) {
                    showNotification('Gagal menyimpan anamnesis.', 'error');
                } finally {
                    setButtonLoading(btn, false);
                }
            });

            // --- Treatment Tab ---
            const loadTreatments = async () => {
                try {
                    const data = await ajaxRequest({
                        url: ROUTES.getInpatientTreatment.replace(':id', admissionId)
                    });
                    const tbody = $("#tbody-pendukung").empty();
                    if (!data || data.length === 0) return;

                    data.forEach(item => {
                        const docButton = item.document ?
                            `<button class="btn btn-info btn-sm btn-view-doc" data-doc="${item.document}" data-name="${item.tindakan_name || 'Dokumen'}"><i class="bi bi-eye"></i> Dokumen</button>` :
                            '<span class="text-muted">-</span>';

                        tbody.append(`
                            <tr>
                                <td>${item.treatment_date_formatted}</td>
                                <td>${item.quantity}</td>
                                <td>${item.request_type} | ${item.tindakan_name}</td>
                                <td>${item.result || '-'}</td>
                                <td class="text-center">${docButton}</td>
                                <td>${item.performed_by}</td>
                                <td class="text-center">
                                    <button class="btn btn-danger btn-sm btn-hapus-pemeriksaan" data-id="${item.id}"><i class="bi bi-trash"></i> Hapus</button>
                                </td>
                            </tr>`);
                    });
                } catch (error) {
                    console.error("Gagal memuat tindakan:", error);
                    $("#tbody-pendukung").html(
                        '<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>'
                    );
                }
            };

            $('#btn-pemeriksaan').on('click', async function() {
                const btn = $(this);
                const formData = new FormData();
                formData.append("type", $("#jenis_pemeriksaan").val());
                formData.append("tindakan_id", $("#jenis_tindakan").val());
                formData.append("result", quill.root.innerHTML);
                formData.append("document", $("#dokumen_pemeriksaan")[0].files[0]);
                formData.append("treatment_date", $("#treatment_date").val());

                if (!formData.get('type') || !formData.get('tindakan_id') || !formData.get(
                        'treatment_date')) {
                    return showNotification("Tipe, Tindakan, dan Tanggal harus diisi.", "error");
                }

                setButtonLoading(btn, true);
                try {
                    const response = await ajaxRequest({
                        url: ROUTES.postInpatientTreatment.replace(':id', admissionId),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                    });
                    showNotification(response.message);
                    loadTreatments();
                    $('#dokumen_pemeriksaan').val('');
                    quill.setContents([]);
                } catch (error) {
                    showNotification('Gagal menyimpan tindakan.', 'error');
                } finally {
                    setButtonLoading(btn, false);
                }
            });

            $('#tbody-pendukung').on('click', '.btn-hapus-pemeriksaan', async function() {
                const id = $(this).data('id');
                const confirmed = await confirmAction("Hapus Tindakan?",
                    "Data ini akan dihapus permanen.");
                if (confirmed) {
                    try {
                        const response = await ajaxRequest({
                            url: ROUTES.deleteInpatientTreatment.replace(':id', id),
                            method: 'DELETE'
                        });
                        showNotification(response.message);
                        loadTreatments();
                    } catch (error) {
                        showNotification('Gagal menghapus data.', 'error');
                    }
                }
            });

            $('#tbody-pendukung').on('click', '.btn-view-doc', function() {
                const docUrl = $(this).data('doc');
                const docName = $(this).data('name');
                const modal = $('#modalViewDocument');
                const container = $('#documentContainer');
                const downloadBtn = $('#btnDownloadDocument');

                modal.find('#modalViewDocumentLabel').text(`Dokumen: ${docName}`);
                container.html('<div class="spinner-border"></div>'); // Show spinner
                downloadBtn.attr('href', docUrl).attr('download', docName);

                const fileExtension = docUrl.split('.').pop().toLowerCase();

                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                    const img = $('<img>', {
                        src: docUrl,
                        class: 'img-fluid rounded',
                        alt: 'Dokumen',
                        css: {
                            'max-height': '70vh',
                            'object-fit': 'contain'
                        }
                    });
                    container.html(img);
                } else if (fileExtension === 'pdf') {
                    const embed =
                        `<embed src="${docUrl}" type="application/pdf" width="100%" height="500px" />`;
                    container.html(embed);
                } else {
                    container.html(
                    '<p class="text-danger">Format file tidak didukung untuk pratinjau.</p>');
                }
                modal.modal('show');
            });

            // --- Daily Medication Tab ---
            const loadDailyMedications = async () => {
                try {
                    const data = await ajaxRequest({
                        url: ROUTES.getDailyMedications.replace(':id', admissionId)
                    });
                    const tbody = $("#tbody-resep-daily").empty();
                    let total = 0;
                    if (!data || data.length === 0) return;

                    data.forEach(item => {
                        let actionButtons = '';
                        let statusBadge = '';

                        if (item.status === "Diajukan") {
                            actionButtons =
                                `<button class="btn btn-danger btn-sm btn-hapus-resep-daily" data-id="${item.id}"><i class="bi bi-trash"></i> Hapus</button>`;
                            statusBadge = `<span class="badge bg-warning">Menunggu Apotek</span>`;
                        } else if (item.status === "Disiapkan") {
                            actionButtons =
                                `<button class="btn btn-primary btn-sm btn-diberikan-resep-daily" data-id="${item.id}"><i class="bi bi-check-circle"></i> Berikan ke Pasien</button>`;
                            statusBadge = `<span class="badge bg-info">Siap Diberikan</span>`;
                        } else { // Diberikan
                            statusBadge = `<span class="badge bg-success">Sudah Diberikan</span>`;
                        }
                        tbody.append(`
                            <tr>
                                <td class="text-center">${actionButtons}</td>
                                <td>Jadwal: ${item.medicine_date}<br>Diberikan: ${item.administered_at || '-'}<br>${statusBadge}</td>
                                <td>${item.jumlah}</td>
                                <td>${item.medication_name}</td>
                                <td>${item.dosage_instructions} | ${item.route} | ${item.frequency}</td>
                                <td><span class="badge bg-success">Dokter: ${item.authorized_name}</span><br><span class="badge bg-primary">Perawat: ${item.administered_name}</span></td>
                                <td class="text-end">${formatRupiah(item.total, 'Rp. ')}</td>
                            </tr>`);
                        total += parseInt(item.total || 0);
                    });
                    $("#total-resep-daily").text(formatRupiah(total, 'Rp. '));
                } catch (error) {
                    console.error("Gagal memuat obat harian:", error);
                    $("#tbody-resep-daily").html(
                        '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>'
                    );
                }
            };

            $('#btn-tambah-obat-daily').on('click', async function() {
                const btn = $(this);
                const payload = {
                    product_apotek_id: $("#product_apotek_id_daily").val(),
                    jumlah: $("#jumlah_daily").val(),
                    dosage_instructions: $("#dosis_daily").val(),
                    frequensi: $("#frequensi_daily").val(),
                    route: $("#route_daily").val(),
                    notes: $("#note_daily").val(),
                    medicine_date: $("#medicine_date").val(),
                };

                if (!payload.product_apotek_id || !payload.jumlah || !payload.dosage_instructions || !
                    payload.frequensi || !payload.route || !payload.medicine_date) {
                    return showNotification("Semua field obat harian (kecuali catatan) harus diisi.",
                        "error");
                }

                setButtonLoading(btn, true);
                try {
                    const response = await ajaxRequest({
                        url: ROUTES.postDailyMedication.replace(':id', admissionId),
                        method: 'POST',
                        data: payload
                    });
                    showNotification(response.message);
                    loadDailyMedications();
                    $('#obat_harian').find('input, select').val('').trigger('change');
                } catch (error) {
                    showNotification('Gagal menambah obat harian.', 'error');
                } finally {
                    setButtonLoading(btn, false);
                }
            });

            $('#tbody-resep-daily').on('click', '.btn-hapus-resep-daily', async function() {
                const id = $(this).data('id');
                const confirmed = await confirmAction("Hapus Obat?", "Data ini akan dihapus permanen.");
                if (confirmed) {
                    try {
                        const response = await ajaxRequest({
                            url: ROUTES.deleteDailyMedication.replace(':id', id),
                            method: 'DELETE'
                        });
                        showNotification(response.message);
                        loadDailyMedications();
                    } catch (error) {
                        showNotification(error.responseJSON?.message || 'Gagal menghapus data.',
                            'error');
                    }
                }
            });

            $('#tbody-resep-daily').on('click', '.btn-diberikan-resep-daily', async function() {
                const id = $(this).data('id');
                const confirmed = await confirmAction("Konfirmasi Pemberian Obat",
                    "Anda yakin obat ini sudah diberikan ke pasien?");
                if (confirmed) {
                    try {
                        const response = await ajaxRequest({
                            url: ROUTES.updateDailyMedicationStatus.replace(':id', id),
                            method: 'POST'
                        });
                        showNotification(response.message);
                        loadDailyMedications();
                    } catch (error) {
                        showNotification('Gagal memperbarui status.', 'error');
                    }
                }
            });

            // --- Diagnosis Tab ---
            const loadDiagnosis = async () => {
                try {
                    const data = await ajaxRequest({
                        url: ROUTES.getDiagnosis.replace(':id', encounterId)
                    });
                    const tbody = $("#tbody-diagnosis").empty();
                    if (!data || data.length === 0) return;

                    data.forEach(item => {
                        tbody.append(`
                            <tr>
                                <td class="text-center">
                                    <button class="btn btn-danger btn-sm btn-hapus-diagnosis" data-id="${item.id}"><i class="bi bi-trash"></i> Hapus</button>
                                </td>
                                <td>${item.diagnosis_code}</td>
                                <td>${item.diagnosis_description}</td>
                                <td>${item.diagnosis_type}</td>
                                <td>${item.petugas_name}</td>
                            </tr>`);
                    });
                } catch (error) {
                    console.error("Gagal memuat diagnosis:", error);
                    $("#tbody-diagnosis").html(
                        '<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>'
                    );
                }
            };

            $('#btn-diagnosis-medis').on('click', async function() {
                const btn = $(this);
                const payload = {
                    icd10_id: $("#icd10_id").val(),
                    diagnosis_type: $("#diagnosis_type").val(),
                };

                if (!payload.icd10_id || !payload.diagnosis_type) {
                    return showNotification("Diagnosis (ICD10) dan Tipe Diagnosis harus diisi.",
                        "error");
                }

                setButtonLoading(btn, true);
                try {
                    const response = await ajaxRequest({
                        url: ROUTES.postDiagnosis.replace(':id', encounterId),
                        method: 'POST',
                        data: payload
                    });
                    showNotification(response.message);
                    loadDiagnosis();
                    $('#icd10_id').val(null).trigger('change');
                    $('#diagnosis_type').val('');
                } catch (error) {
                    showNotification('Gagal menyimpan diagnosis.', 'error');
                } finally {
                    setButtonLoading(btn, false);
                }
            });

            $('#tbody-diagnosis').on('click', '.btn-hapus-diagnosis', async function() {
                const id = $(this).data('id');
                const confirmed = await confirmAction("Hapus Diagnosis?",
                    "Data ini akan dihapus permanen.");
                if (confirmed) {
                    try {
                        const response = await ajaxRequest({
                            url: ROUTES.deleteDiagnosis.replace(':id', id),
                            method: 'DELETE'
                        });
                        showNotification(response.message);
                        loadDiagnosis();
                    } catch (error) {
                        showNotification('Gagal menghapus data.', 'error');
                    }
                }
            });

            // --- Resep Pulang (Tatalaksana) Tab ---
            const loadResepPulang = async () => {
                try {
                    const data = await ajaxRequest({
                        url: ROUTES.getResep.replace(':id', encounterId)
                    });
                    const tbody = $("#tbody-resep").empty();

                    if (data && data.id) {
                        $("#resep").removeClass("d-none");
                        $("#kode_resep").text(`[${data.kode_resep}] ${data.masa_pemakaian_hari} hari`);

                        let total = 0;
                        if (data.details && data.details.length > 0) {
                            data.details.forEach(item => {
                                tbody.append(`
                                    <tr>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm btn-hapus-resep" data-id="${item.id}"><i class="bi bi-trash"></i> Hapus</button>
                                        </td>
                                        <td>${item.qty}</td>
                                        <td>${item.nama_obat}</td>
                                        <td>${item.aturan_pakai}</td>
                                        <td class="text-end">${formatRupiah(item.harga, 'Rp. ')}</td>
                                        <td class="text-end">${formatRupiah(item.total_harga, 'Rp. ')}</td>
                                    </tr>`);
                                total += parseInt(item.total_harga || 0);
                            });
                        }
                        $("#total-resep").text(formatRupiah(total, 'Rp. '));
                    } else {
                        $("#resep").addClass("d-none");
                        $("#total-resep").text(formatRupiah(0, 'Rp. '));
                    }
                } catch (error) {
                    console.error("Gagal memuat resep pulang:", error);
                    $("#tbody-resep").html(
                        '<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>'
                    );
                }
            };

            $('#btn-buat-resep').on('click', async function(e) {
                e.preventDefault();
                const btn = $(this);
                const masa_pemakaian_hari = $("#masa_pemakaian_hari").val();

                if (!masa_pemakaian_hari) {
                    return showNotification("Jumlah hari resep harus diisi.", "error");
                }

                setButtonLoading(btn, true);
                try {
                    const response = await ajaxRequest({
                        url: ROUTES.postResep.replace(':id', encounterId),
                        method: 'POST',
                        data: {
                            masa_pemakaian_hari
                        }
                    });
                    showNotification(response.message);
                    loadResepPulang();
                } catch (error) {
                    showNotification('Gagal membuat resep.', 'error');
                } finally {
                    setButtonLoading(btn, false);
                }
            });

            $('#btn-tambah-obat').on('click', async function() {
                const btn = $(this);
                const payload = {
                    product_apotek_id: $("#product_apotek_id").val(),
                    qty_obat: $("#qty").val(),
                    aturan_pakai: $("#aturan_pakai").val(),
                };

                if (!payload.product_apotek_id || !payload.qty_obat || !payload.aturan_pakai) {
                    return showNotification("Obat, Jumlah, dan Aturan Pakai harus diisi.", "error");
                }

                setButtonLoading(btn, true);
                try {
                    const response = await ajaxRequest({
                        url: ROUTES.postResepDetail.replace(':id', encounterId),
                        method: 'POST',
                        data: payload
                    });
                    showNotification(response.data.message || 'Obat berhasil ditambahkan.');
                    loadResepPulang();
                    $('#product_apotek_id').val(null).trigger('change');
                    $('#qty').val(1);
                    $('#aturan_pakai').val('');
                } catch (error) {
                    showNotification(error.responseJSON?.message || 'Gagal menambah obat.', 'error');
                } finally {
                    setButtonLoading(btn, false);
                }
            });

            $('#tbody-resep').on('click', '.btn-hapus-resep', async function() {
                const id = $(this).data('id');
                const confirmed = await confirmAction("Hapus Obat dari Resep?",
                    "Obat ini akan dihapus dari resep.");
                if (confirmed) {
                    const response = await ajaxRequest({
                        url: ROUTES.deleteResepDetail.replace(':id', id),
                        method: 'DELETE'
                    });
                    showNotification(response.message);
                    loadResepPulang();
                }
            });

            const setupSelect2 = (selector, url, placeholder, textMapper) => {
                $(selector).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            search: params.term
                        }),
                        processResults: data => ({
                            results: (data.data || data).map(textMapper)
                        }),
                        cache: true
                    }
                });
            };

            setupSelect2('#jenis_tindakan', ROUTES.getTindakan.replace(':id', admissionId), 'Pilih Tindakan',
                item => ({
                    id: item.id,
                    text: item.name
                }));
            setupSelect2('#icd10_id', ROUTES.getIcd10.replace(':id', encounterId),
                'Cari kode atau nama diagnosis...', item => ({
                    id: item.code,
                    text: `${item.code} - ${item.description}`
                }));
            setupSelect2('#product_apotek_id_daily', ROUTES.getProdukApotek.replace(':id', encounterId),
                'Pilih Obat', item => ({
                    id: item.id,
                    text: `${item.name} - [${item.satuan}] - [${formatRupiah(item.harga, 'Rp. ')}]`
                }));
            setupSelect2('#product_apotek_id', ROUTES.getProdukApotek.replace(':id', encounterId), 'Pilih Obat',
                item => ({
                    id: item.id,
                    text: `${item.name} - [${formatRupiah(item.harga, 'Rp. ')}]`
                }));

            // --- Tab Click Handlers ---
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const targetId = $(e.target).attr("href");
                switch (targetId) {
                    case "#anamnesis":
                        loadAnamnesis();
                        break;
                    case "#treatment":
                        loadTreatments();
                        break;
                    case "#daily":
                        loadDailyMedications();
                        break;
                    case "#diagnosis":
                        loadDiagnosis();
                        break;
                    case "#tatalaksana":
                        loadResepPulang();
                        break;
                        // Add other tab load functions here
                }
            });

            // Initial load for the active tab
            loadAnamnesis();
        });
    </script>
@endpush
