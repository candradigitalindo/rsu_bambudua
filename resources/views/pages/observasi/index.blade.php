@extends('layouts.app')
@section('title')
    Pemeriksaan Observasi
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
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Form Pemeriksaan</h5>
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
                                <a class="nav-link" id="tab-pemeriksaan-fisik" data-bs-toggle="tab"
                                    href="#pemeriksaan-fisik" role="tab" aria-controls="pemeriksaan-fisik"
                                    aria-selected="false"><i class="ri-user-heart-line"></i>Pemeriksaan Fisik</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-pemeriksaan-penunjang" data-bs-toggle="tab"
                                    href="#pemeriksaan-penunjang" role="tab" aria-controls="pemeriksaan-penunjang"
                                    aria-selected="false"><i class="ri-user-heart-line"></i>Pemeriksaan Penunjang</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-tindakan-medis" data-bs-toggle="tab" href="#tindakan-medis"
                                    role="tab" aria-controls="tindakan-medis" aria-selected="false"><i
                                        class="ri-stethoscope-line"></i>Tindakan/Prosedur</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-diagnosis" data-bs-toggle="tab" href="#diagnosis" role="tab"
                                    aria-controls="diagnosis" aria-selected="false"><i
                                        class="ri-health-book-line"></i>Diagnosis</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-tatalaksana" data-bs-toggle="tab" href="#tatalaksana"
                                    role="tab" aria-controls="tatalaksana" aria-selected="false"><i
                                        class="ri-capsule-fill"></i>Tatalaksana</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-catatan" data-bs-toggle="tab" href="#catatan"
                                    role="tab" aria-controls="catatan" aria-selected="false"><i
                                        class="ri-draft-line"></i>Catatan</a>
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
                            <div class="tab-pane fade" id="pemeriksaan-fisik" role="tabpanel">
                                <div class="row gx-3">
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="nadi">Nadi</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="nadi" name="nadi"
                                                    value="{{ old('nadi') }}">
                                            </div>
                                            <p class="text-danger">{{ $errors->first('nadi') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="pernapasan">Pernapasan</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="pernapasan"
                                                    name="pernapasan" value="{{ old('pernapasan') }}">
                                            </div>
                                            <p class="text-danger">{{ $errors->first('pernapasan') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6" id="expired">
                                        <div class="mb-3">
                                            <label class="form-label" for="sistolik">TD Sistolik</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control " id="sistolik"
                                                    name="sistolik" value="{{ old('sistolik') }}">
                                            </div>
                                            <p class="text-danger">{{ $errors->first('sistolik') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="diastolik">TD Diastolik</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control " id="diastolik"
                                                    name="diastolik" value="{{ old('diastolik') }}">
                                            </div>
                                            <p class="text-danger">{{ $errors->first('diastolik') }}</p>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="suhu">Suhu</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="suhu" name="suhu"
                                                    value="{{ old('suhu') }}">
                                            </div>
                                            <p class="text-danger">{{ $errors->first('suhu') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="kesadaran">Kesadaran</label>
                                            <div class="input-group">
                                                <select name="kesadaran" id="kesadaran" class="form-control">
                                                    <option value="">Pilih Kesadaran</option>
                                                    <option value="Compos Mentis"
                                                        {{ old('kesadaran') == 'Compos Mentis' ? 'selected' : '' }}>
                                                        Compos Mentis</option>
                                                    <option value="Somnolent"
                                                        {{ old('kesadaran') == 'Somnolent' ? 'selected' : '' }}>
                                                        Somnolent</option>
                                                    <option value="Sopor"
                                                        {{ old('kesadaran') == 'Sopor' ? 'selected' : '' }}>
                                                        Sopor</option>
                                                    <option value="Coma"
                                                        {{ old('kesadaran') == 'Coma' ? 'selected' : '' }}>
                                                        Coma</option>
                                                </select>
                                            </div>
                                            <p class="text-danger">{{ $errors->first('kesadaran') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="tinggi_badan">Tinggi Badan</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="tinggi_badan"
                                                    name="tinggi_badan" value="{{ old('tinggi_badan') }}">
                                            </div>
                                            <p class="text-danger">{{ $errors->first('tinggi_badan') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="berat_badan">Berat Badan</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="berat_badan"
                                                    name="berat_badan" value="{{ old('berat_badan') }}">
                                            </div>
                                            <p class="text-danger">{{ $errors->first('berat_badan') }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-end mt-4">
                                        <button type="submit" class="btn btn-primary" id="btn-ttv">
                                            <span class="btn-txt" id="text-ttv">Simpan</span>
                                            <span class="spinner-border spinner-border-sm d-none" id="spinner-ttv"></span>
                                        </button>
                                        <a href="{{ route('kunjungan.rawatJalan') }}" class="btn btn-secondary"
                                            id="btn-kembali-ttv">
                                            <span class="btn-txt" id="text-kembali-ttv">Kembali</span>
                                            <span class="spinner-border spinner-border-sm d-none"
                                                id="spinner-kembali-ttv"></span>
                                        </a>

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pemeriksaan-penunjang" role="tabpanel">
                                <!-- Row startss -->
                                <div class="row gx-3">
                                    <div class="col-xxl-6 col-sm-6">
                                        <div class="card mb-1">
                                            <div class="card-header">
                                                <h5 class="card-title">Pemeriksaan Penunjang</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Jenis Pemeriksaan</label>
                                                    <div class="input-group">
                                                        <select name="jenis_pemeriksaan" id="jenis_pemeriksaan"
                                                            class="form-control">
                                                            <option value="">Pilih Jenis Pemeriksaan</option>
                                                            <option value="EKG"
                                                                {{ old('jenis_pemeriksaan') == 'EKG' ? 'selected' : '' }}>
                                                                EKG</option>
                                                            <option value="Ekokardiografi"
                                                                {{ old('jenis_pemeriksaan') == 'Ekokardiografi' ? 'selected' : '' }}>
                                                                Ekokardiografi</option>
                                                            <option value="Rontgen Dada"
                                                                {{ old('jenis_pemeriksaan') == 'Rontgen Dada' ? 'selected' : '' }}>
                                                                Rontgen Dada</option>
                                                            <option value="Tes Treadmill"
                                                                {{ old('jenis_pemeriksaan') == 'Tes Treadmill' ? 'selected' : '' }}>
                                                                Tes Treadmill</option>
                                                            <option value="Holter Monitoring"
                                                                {{ old('jenis_pemeriksaan') == 'Holter Monitoring' ? 'selected' : '' }}>
                                                                Holter Monitoring</option>
                                                            <option value="CT Koroner"
                                                                {{ old('jenis_pemeriksaan') == 'CT Koroner' ? 'selected' : '' }}>
                                                                CT Koroner</option>
                                                            <option value="MRI Jantung"
                                                                {{ old('jenis_pemeriksaan') == 'MRI Jantung' ? 'selected' : '' }}>
                                                                MRI Jantung</option>
                                                            <option value="Kateterisasi Jantung"
                                                                {{ old('jenis_pemeriksaan') == 'Kateterisasi Jantung' ? 'selected' : '' }}>
                                                                Kateterisasi Jantung</option>
                                                            <option value="USG Jantung"
                                                                {{ old('jenis_pemeriksaan') == 'USG Jantung' ? 'selected' : '' }}>
                                                                USG Jantung</option>
                                                            <option value="CT Scan"
                                                                {{ old('jenis_pemeriksaan') == 'CT Scan' ? 'selected' : '' }}>
                                                                CT Scan</option>
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
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Dokumen Pemeriksaan (Jika
                                                        ada)</label>
                                                    <div class="input-group">
                                                        <input type="file" class="form-control"
                                                            id="dokumen_pemeriksaan" name="dokumen_pemeriksaan">
                                                        <input type="hidden" name="dokumen_pemeriksaan"
                                                            id="dokumen_pemeriksaan"
                                                            value="{{ old('dokumen_pemeriksaan') }}">
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
                                    <div class="col-xxl-6 col-sm-12">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">Data Pemeriksaan Penunjang</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Jenis Pemeriksaan</th>
                                                                    <th>Hasil</th>
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
                            <div class="tab-pane fade" id="tindakan-medis" role="tabpanel">
                                <!-- Row startss -->
                                <div class="row gx-3">
                                    <div class="col-xxl-6 col-sm-6">
                                        <div class="card mb-1">
                                            <div class="card-header">
                                                <h5 class="card-title">Tindakan Medis</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Jenis Tindakan</label>
                                                    <div class="input-group">
                                                        <select name="jenis_tindakan" id="jenis_tindakan"
                                                            class="form-control">
                                                            <option value="">Pilih Jenis Tindakan</option>

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" for="a2">Jumlah</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="qty"
                                                            name="qty" value="{{ old('qty', 1) }}">
                                                        <p class="text-danger">{{ $errors->first('qty') }}</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 justify-content-end mt-4">
                                                    <button type="submit" class="btn btn-primary"
                                                        id="btn-tindakan-medis">
                                                        <span class="btn-txt" id="text-tindakan-medis">Simpan</span>
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            id="spinner-tindakan-medis"></span>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-6 col-sm-12">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">Data Tindakan Medis</h5>
                                                <hr class="mb-2">
                                            </div>
                                            <div class="card-body">
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Aksi</th>
                                                                    <th>Nama Tindakan</th>
                                                                    <th>Qty</th>
                                                                    <th>Harga</th>
                                                                    <th>Sub Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbody-tindakan">


                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                                                    <td class="text-end">
                                                                        <span id="total-harga" class="fw-bold">0</span>
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
                                                        <select name="product_apotek_id" id="product_apotek_id" class="form-control">
                                                            <option value="">Pilih Obat</option>

                                                        </select>
                                                        <input type="hidden" name="product_apotek_id" id="product_apotek_id"
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
                                                                    <td class="text-end fw-bold" id="total-resep">Rp. 0</td>
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
                                    <div class="col-xxl-6 col-sm-6">
                                        <div class="card mb-1">
                                            <div class="card-header">
                                                <h5 class="card-title">Tindakan</h5>
                                                <hr class="mb-1">
                                            </div>
                                            <div class="card-body">

                                                <div class="mb-1">
                                                    <label class="form-label" for="a2">Diskon Tindakan</label>

                                                    <form method="GET" class="mb-3">
                                                        <div class="input-group">
                                                            <input type="text" name="diskon_tindakan"
                                                                class="form-control" placeholder="Diskon Tindakan"
                                                                id="diskon_tindakan">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="btn-buat-diskon-tindakan">
                                                                <span id="text-buat-diskon-tindakan">Buat Diskon</span>
                                                                <span class="spinner-border spinner-border-sm d-none"
                                                                    id="spinner-buat-diskon-tindakan" role="status"
                                                                    aria-hidden="true"></span>
                                                            </button>
                                                        </div>
                                                    </form>

                                                </div>
                                                <div class="table-outer">
                                                    <div class="table-responsive">
                                                        <table class="table truncate m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Aksi</th>
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
                                                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                                                    <td class="text-end">
                                                                        <span id="catatan-total-harga" class="fw-bold">0</span>
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

                                                    <form method="GET" class="mb-3">
                                                        <div class="input-group">
                                                            <input type="text" name="diskon_resep"
                                                                class="form-control" placeholder="Diskon Resep"
                                                                id="diskon_resep">
                                                            <button class="btn btn-primary" type="submit"
                                                                id="btn-buat-diskon-resep">
                                                                <span id="text-buat-diskon-resep">Buat Diskon</span>
                                                                <span class="spinner-border spinner-border-sm d-none"
                                                                    id="spinner-buat-diskon-resep" role="status"
                                                                    aria-hidden="true"></span>
                                                            </button>
                                                        </div>
                                                    </form>

                                                </div>
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
                                                            <tbody id="tbody-catatan-resep">
                                                                <!-- Data resep diisi via JS -->
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="5" class="text-end fw-bold">Total</td>
                                                                    <td class="text-end fw-bold" id="catatan-total-resep">Rp. 0</td>
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
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!-- Row ends -->
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
                    url = url.replace(':id', "{{ $observasi }}");
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
                    url = url.replace(':id', "{{ $observasi }}");
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
                            $("#spinner-anamnesis").removeClass("d-none");
                            $("#text-anamnesis").addClass("d-none");
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                swal(data.message, {
                                    icon: "success",
                                });
                                $("#spinner-anamnesis").addClass("d-none");
                                $("#text-anamnesis").removeClass("d-none");
                            } else {
                                swal('Terjadi kesalahan saat menyimpan data.', {
                                    icon: "error",
                                });
                                $("#spinner-anamnesis").addClass("d-none");
                                $("#text-anamnesis").removeClass("d-none");
                            }
                        }
                    });
                });
                // tab-pemeriksaan-fisik click
                $("#tab-pemeriksaan-fisik").click(function() {
                    // ajax tanda-tanda vital
                    let url = "{{ route('observasi.tandaVital', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function(data) {
                            $("#nadi").val(data.nadi);
                            $("#pernapasan").val(data
                                .pernapasan); // Updated to match the new data structure
                            $("#sistolik").val(data.sistolik);
                            $("#diastolik").val(data.diastolik);
                            $("#suhu").val(data.suhu);
                            $("#kesadaran").val(data
                                .kesadaran); // Updated to match the new data structure
                            $("#tinggi_badan").val(data
                                .tinggi_badan); // Updated to match the new data structure
                            $("#berat_badan").val(data
                                .berat_badan); // Updated to match the new data structure
                        }
                    });
                });
                // btn-ttv click
                $("#btn-ttv").click(function() {
                    // ajax post ttv
                    let url = "{{ route('observasi.postTandaVital', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
                    let nadi = $("#nadi").val();
                    let pernapasan = $("#pernapasan").val();
                    let sistolik = $("#sistolik").val();
                    let diastolik = $("#diastolik").val();
                    let suhu = $("#suhu").val();
                    let kesadaran = $("#kesadaran").val();
                    let tinggi_badan = $("#tinggi_badan").val();
                    let berat_badan = $("#berat_badan").val();

                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            nadi: nadi,
                            pernapasan: pernapasan,
                            sistolik: sistolik,
                            diastolik: diastolik,
                            suhu: suhu,
                            kesadaran: kesadaran,
                            tinggi_badan: tinggi_badan,
                            berat_badan: berat_badan,
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function() {
                            $("#spinner-ttv").removeClass("d-none");
                            $("#text-ttv").addClass("d-none");
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                swal(data
                                    .message, {
                                        icon: "success",
                                    });
                                $("#spinner-ttv").addClass("d-none");
                                $("#text-ttv").removeClass("d-none");
                            } else {
                                swal('Terjadi kesalahan saat menyimpan data.', {
                                    icon: "error",
                                });
                                $("#spinner-ttv").addClass("d-none");
                                $("#text-ttv").removeClass("d-none");
                            }
                        }
                    });
                });
                // btn-kembali-ttv click
                $("#btn-kembali-ttv").click(function() {
                    $("#spiner-kembali-ttv").removeClass("d-none");
                    $("#btn-kembali-ttv").addClass("disabled", true);
                    $("#text-kembali-ttv").text("Mohon Tunggu ...");
                });
                // tab-pemeriksaan-penunjang click
                $("#tab-pemeriksaan-penunjang").click(function() {
                    // ajax pemeriksaan penunjang
                    let url = "{{ route('observasi.pemeriksaanPenunjang', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function(data) {
                            // Populate the table with data
                            let tbody = $("#tbody-pendukung");
                            tbody.empty(); // Clear existing rows
                            $.each(data, function(index, item) {
                                tbody.append(
                                    `<tr>
                                        <td>${item.jenis_pemeriksaan}</td>
                                        <td>${item.hasil_pemeriksaan}</td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm btn-hapus-pemeriksaan" data-id="${item.id}">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>`
                                );
                            });
                            // kosongkan kolom dokumen pemeriksaan
                            $("#jenis_pemeriksaan").val(null);
                            // kembalikan editor ke default
                            quill.setContents(0);

                        }
                    });
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
                            let url = "{{ route('observasi.deletePemeriksaanPenunjang', ':id') }}"
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
                                    $("#tab-pemeriksaan-penunjang")
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

                // btn-pemeriksaan click
                $("#btn-pemeriksaan").click(function() {
                    // URL untuk AJAX
                    let url = "{{ route('observasi.postPemeriksaanPenunjang', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");

                    // Ambil data dari form
                    let jenis_pemeriksaan = $("#jenis_pemeriksaan").val();
                    let hasil_pemeriksaan = quill.root.innerHTML; // Ambil isi editor
                    let dokumen_pemeriksaan = $("#dokumen_pemeriksaan")[0].files[0]; // Ambil file

                    // Validasi input
                    if (jenis_pemeriksaan == '') {
                        alert("Jenis Pemeriksaan tidak boleh kosong");
                        return;
                    }
                    if (hasil_pemeriksaan == '') {
                        alert("Hasil Pemeriksaan tidak boleh kosong");
                        return;
                    }

                    // Buat objek FormData
                    let formData = new FormData();
                    formData.append("jenis_pemeriksaan", jenis_pemeriksaan);
                    formData.append("hasil_pemeriksaan", hasil_pemeriksaan);
                    formData.append("dokumen_pemeriksaan", dokumen_pemeriksaan); // Tambahkan file
                    formData.append("_token", "{{ csrf_token() }}"); // Tambahkan CSRF token

                    // Kirim data melalui AJAX
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: formData,
                        processData: false, // Jangan proses data
                        contentType: false, // Jangan tetapkan header Content-Type
                        beforeSend: function() {
                            $("#spinner-pemeriksaan").removeClass("d-none");
                            $("#text-pemeriksaan").addClass("d-none");
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                swal(data.message, {
                                    icon: "success",
                                });
                                // Refresh the table after successful submission
                                $("#tab-pemeriksaan-penunjang").click();
                            } else {
                                swal('Terjadi kesalahan saat menyimpan data.', {
                                    icon: "error",
                                });
                            }
                            $("#spinner-pemeriksaan").addClass("d-none");
                            $("#text-pemeriksaan").removeClass("d-none");
                        },
                        error: function(xhr) {
                            swal('Terjadi kesalahan saat mengirim data.', {
                                icon: "error",
                            });
                            $("#spinner-pemeriksaan").addClass("d-none");
                            $("#text-pemeriksaan").removeClass("d-none");
                        }
                    });
                });

                $('#jenis_tindakan').select2({
                    placeholder: 'Pilih Bahan Tindakan',
                    allowClear: true,
                    width: '100%',

                });

                // tab-tindakan-medis click
                $("#tab-tindakan-medis").click(function() {
                    // ajax getTindakan jenis_tindakan
                    let url = "{{ route('observasi.getTindakan', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
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
                    url2 = url2.replace(':id', "{{ $observasi }}");
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
                $("#btn-tindakan-medis").click(function() {
                    // ajax post tindakan medis
                    let url = "{{ route('observasi.postTindakanEncounter', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
                    let jenis_tindakan = $("#jenis_tindakan").val();
                    let qty = $("#qty").val();
                    if (jenis_tindakan == '') {
                        alert("Jenis Tindakan tidak boleh kosong");
                        return;
                    }
                    if (qty == '') {
                        alert("Jumlah tidak boleh kosong");
                        return;
                    }
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            jenis_tindakan: jenis_tindakan,
                            qty: qty,
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function() {
                            $("#spinner-tindakan-medis").removeClass("d-none");
                            $("#text-tindakan-medis").addClass("d-none");
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                swal(data.message, {
                                    icon: "success",
                                });
                                // Refresh the table after successful submission
                                $("#tab-tindakan-medis").click();
                            } else {
                                swal('Terjadi kesalahan saat menyimpan data.', {
                                    icon: "error",
                                });
                            }
                            $("#spinner-tindakan-medis").addClass("d-none");
                            $("#text-tindakan-medis").removeClass("d-none");
                        }
                    });
                });
                // Event delegation untuk tombol hapus
                $('#tbody-tindakan').on('click', '.btn-hapus-tindakan', function() {
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
                            let url = "{{ route('observasi.deleteTindakanEncounter', ':id') }}"
                                .replace(':id', id);
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == true) {
                                        swal(data.message, {
                                            icon: "success"
                                        });
                                    } else {
                                        swal(data.message, {
                                            icon: "error"
                                        });

                                    }
                                    $("#tab-tindakan-medis")
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
                // tab-diagnosis click
                $("#tab-diagnosis").click(function() {
                    // Kosongkan kolom ICD10 dan Diagnosis Type
                    $("#icd10_id").val(null).trigger('change'); // untuk select2
                    $("#diagnosis_type").val(''); // untuk select biasa

                    // ajax getDiagnosis
                    let url = "{{ route('observasi.getDiagnosis', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
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
                    url = url.replace(':id', "{{ $observasi }}");
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
                            $("#spinner-diagnosis-medis").removeClass("d-none");
                            $("#text-diagnosis-medis").addClass("d-none");
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                swal(data.message, {
                                    icon: "success",
                                });
                                // Refresh the table after successful submission
                                $("#tab-diagnosis").click();
                            } else {
                                swal('Terjadi kesalahan saat menyimpan data.', {
                                    icon: "error",
                                });
                            }
                            $("#spinner-diagnosis-medis").addClass("d-none");
                            $("#text-diagnosis-medis").removeClass("d-none");
                        }
                    });
                });
                // tab-tatalaksana click
                $('#icd10_id').select2({
                    placeholder: 'Cari kode atau nama diagnosis...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: "{{ route('observasi.getIcd10', $observasi) }}", // sesuaikan dengan route Anda
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
                                            text: item.description + (item.code ? ' - [' + item.code + ']' : '')
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
                $("#tab-tatalaksana").click(function() {
                    // ajax getResep
                    let url = "{{ route('observasi.getResep', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
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
                                $("#kode_resep").text("[" + data.kode_resep + "] " + data.masa_pemakaian_hari + " hari");
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
                        url: "{{ route('observasi.getProdukApotek', $observasi) }}", // sesuaikan dengan route Anda
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
                    $("#spinner-buat-resep").removeClass("d-none");
                    $("#text-buat-resep").addClass("d-none");
                    $("#btn-buat-resep").prop("disabled", true);

                    // ajax post resep
                    let url = "{{ route('observasi.postResep', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            masa_pemakaian_hari: masa_pemakaian_hari
                        },
                        success: function(data) {
                            $("#spinner-buat-resep").addClass("d-none");
                            $("#text-buat-resep").removeClass("d-none");
                            $("#btn-buat-resep").prop("disabled", false);

                            if (data.status == 200) {
                                swal(data.message, {
                                    icon: "success"
                                });
                                // Tampilkan kolom resep
                                $("#resep").removeClass("d-none");
                                $("#kode_resep").text(data.kode_resep);
                            } else {
                                swal(data.message, {
                                    icon: "error"
                                });
                            }
                        },
                        error: function(xhr) {
                            $("#spinner-buat-resep").addClass("d-none");
                            $("#text-buat-resep").removeClass("d-none");
                            $("#btn-buat-resep").prop("disabled", false);

                            // Tampilkan error validasi dari server jika ada
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                let errors = xhr.responseJSON.errors;
                                let errorMsg = Object.values(errors).map(function(msgArr) {
                                    return msgArr.join('<br>');
                                }).join('<br>');
                                swal({
                                    title: "Validasi Gagal",
                                    html: true,
                                    text: errorMsg,
                                    icon: "error"
                                });
                            } else {
                                swal('Terjadi kesalahan saat menyimpan data.', {
                                    icon: "error"
                                });
                            }
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
                    $("#spinner-tambah-obat").removeClass("d-none");
                    $("#text-tambah-obat").addClass("d-none");
                    $("#btn-tambah-obat").prop("disabled", true);
                    // ajax post resep detail
                    let url = "{{ route('observasi.postResepDetail', ':id') }}";
                    url = url.replace(':id', "{{ $observasi }}");
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
                            $("#spinner-tambah-obat").addClass("d-none");
                            $("#text-tambah-obat").removeClass("d-none");
                            $("#btn-tambah-obat").prop("disabled", false);

                            if (data.status == 200) {
                                swal(data.message, {
                                    icon: "success"
                                });
                                // Refresh the table after successful submission
                                $("#tab-tatalaksana").click();
                            } else {
                                swal('Terjadi kesalahan saat menyimpan data.', {
                                    icon: "error"
                                });
                            }
                        },
                        error: function(xhr) {
                            $("#spinner-tambah-obat").addClass("d-none");
                            $("#text-tambah-obat").removeClass("d-none");
                            $("#btn-tambah-obat").prop("disabled", false);

                            // Tampilkan error validasi dari server jika ada
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                let errors = xhr.responseJSON.errors;
                                let errorMsg = Object.values(errors).map(function(msgArr) {
                                    return msgArr.join('<br>');
                                }).join('<br>');
                                swal({
                                    title: "Validasi Gagal",
                                    html: true,
                                    text: errorMsg,
                                    icon: "error"
                                });
                            } else {
                                swal('Terjadi kesalahan saat menyimpan data.', {
                                    icon: "error"
                                });
                            }
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
            });
        </script>
    @endpush
