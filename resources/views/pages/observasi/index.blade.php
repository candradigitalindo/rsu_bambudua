@extends('layouts.app')
@section('title')
    Pemeriksaan Observasi
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
                                    <a href="{{ route('kunjungan.rawatJalan') }}" class="btn btn-secondary" id="btn-kembali-anamnesis">
                                        <span class="btn-txt" id="text-kembali-anamnesis">Kembali</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="spinner-kembali-anamnesis"></span>
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
                                        <a href="{{ route('kunjungan.rawatJalan') }}" class="btn btn-secondary" id="btn-kembali-ttv">
                                            <span class="btn-txt" id="text-kembali-ttv">Kembali</span>
                                            <span class="spinner-border spinner-border-sm d-none" id="spinner-kembali-ttv"></span>
                                        </a>

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pemeriksaan-penunjang" role="tabpanel">
                                <h3 class="text-primary">Some Description</h3>
                            </div>
                            <div class="tab-pane fade" id="tindakan-medis" role="tabpanel">
                                <h3 class="text-primary">Some Description</h3>
                            </div>
                            <div class="tab-pane fade" id="diagnosis" role="tabpanel">
                                <h3 class="text-primary">Some Description</h3>
                            </div>
                            <div class="tab-pane fade" id="tatalaksana" role="tabpanel">
                                <h3 class="text-success">Some Description</h3>
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
        <!-- Custom JS files -->
        <script src="{{ asset('js/custom.js') }}"></script>
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
                            $("#pernapasan").val(data.pernapasan); // Updated to match the new data structure
                            $("#sistolik").val(data.sistolik);
                            $("#diastolik").val(data.diastolik);
                            $("#suhu").val(data.suhu);
                            $("#kesadaran").val(data.kesadaran); // Updated to match the new data structure
                            $("#tinggi_badan").val(data.tinggi_badan); // Updated to match the new data structure
                            $("#berat_badan").val(data.berat_badan); // Updated to match the new data structure
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
                    alert("tab-pemeriksaan-penunjang clicked");
                });
                // tab-tindakan-medis click
                $("#tab-tindakan-medis").click(function() {
                    alert("tab-tindakan-medis clicked");
                });
                // tab-diagnosis click
                $("#tab-diagnosis").click(function() {
                    alert("tab-diagnosis clicked");
                });
                // tab-tatalaksana click
                $("#tab-tatalaksana").click(function() {
                    alert("tab-tatalaksana clicked");
                });
            });
        </script>
    @endpush
