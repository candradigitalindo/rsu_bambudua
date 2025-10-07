@extends('layouts.app')
@section('title', 'Buat Pengguna Baru')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Input tetap putih */
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #198754;
            border-radius: 0.375rem;
            padding: 0.375rem 0.5rem;
            background: #fff !important;
            color: #212529 !important;
        }

        /* Badge pilihan hijau */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #198754;
            border: none;
            color: #fff;
            padding: 2px 8px 2px 22px;
            margin-top: 2px;
            position: relative;
            font-size: 0.95em;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-weight: bold;
            font-size: 1em;
            margin: 0;
            padding: 0 2px;
            cursor: pointer;
            background: transparent;
            border: none;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffc107;
            background: transparent;
        }

        .select2-container--default .select2-selection--multiple .select2-search__field {
            width: auto !important;
            background: #fff !important;
            color: #212529 !important;
        }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pengguna.store') }}" method="POST" id="submit"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Custom tabs starts -->
                        <div class="custom-tabs-container">

                            <!-- Nav tabs starts -->
                            <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA"
                                        role="tab" aria-controls="oneA" aria-selected="true"><i
                                            class="ri-account-pin-circle-line"></i>
                                        Form Pengguna Baru</a>
                                </li>
                            </ul>
                            <!-- Nav tabs ends -->

                            <!-- Tab content starts -->
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="oneA" role="tabpanel">

                                    <!-- Row starts -->
                                    <div class="row gx-3">
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Nama Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-account-circle-line"></i>
                                                    </span>
                                                    <input type="text" class="form-control" id="a1" name="name"
                                                        value="{{ old('name') }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('name') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a2">Username <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-pass-pending-line"></i>
                                                    </span>
                                                    <input name="username" type="text" class="form-control"
                                                        id="a2" value="{{ old('username') }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('username') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Hak Akses <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-vip-crown-2-line"></i>
                                                    </span>
                                                    <select class="form-select" id="a7" name="role">
                                                        <option value="">Pilih Hak Akses</option>
                                                        <option value="1" {{ old('role') == '1' ? 'selected' : '' }}>
                                                            Owner</option>
                                                        <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>
                                                            Dokter</option>
                                                        <option value="3" {{ old('role') == '3' ? 'selected' : '' }}>
                                                            Perawat</option>
                                                        <option value="4" {{ old('role') == '4' ? 'selected' : '' }}>
                                                            Admin</option>
                                                        <option value="5" {{ old('role') == '5' ? 'selected' : '' }}>
                                                            Pendaftaran</option>
                                                        <option value="6" {{ old('role') == '6' ? 'selected' : '' }}>
                                                            Keuangan</option>
                                                        <option value="7" {{ old('role') == '7' ? 'selected' : '' }}>
                                                            Apotek</option>
                                                        <option value="8" {{ old('role') == '8' ? 'selected' : '' }}>
                                                            Laboratorium</option>
                                                        <option value="9" {{ old('role') == '9' ? 'selected' : '' }}>
                                                            Radiologi</option>
                                                        <option value="10" {{ old('role') == '10' ? 'selected' : '' }}>
                                                            Kasir</option>
                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('role') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Spesialis <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-vip-crown-2-line"></i>
                                                    </span>
                                                    <select class="form-select" id="a7" name="spesialis">
                                                        <option value="">Pilih Spesialis</option>
                                                        @foreach ($spesialis as $s)
                                                            <option value="{{ $s->kode }}"
                                                                {{ old('spesialis') == $s->kode ? 'selected' : '' }}>
                                                                {{ $s->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('spesialis') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="poliklinik">Poliklinik <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select select2" id="poliklinik" name="poliklinik[]"
                                                    multiple="multiple" style="width: 100%;">
                                                    <option></option>
                                                    @foreach ($clinics as $c)
                                                        <option value="{{ $c->id }}"
                                                            {{ collect(old('poliklinik'))->contains($c->id) ? 'selected' : '' }}>
                                                            {{ $c->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <p class="text-danger">{{ $errors->first('poliklinik') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a4">Password <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-secure-payment-line"></i>
                                                    </span>
                                                    <input type="text" name="password" class="form-control"
                                                        id="a4" value="{{ old('password') }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('password') }}</p>
                                            </div>
                                        </div>

                                    </div>

                                    <hr>
                                    <h6 class="mb-3">Data Perizinan (SIP/STR)</h6>
                                    <div class="row gx-3">
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Profesi</label>
                                                <select name="profession" class="form-select">
                                                    <option value="">- Pilih Profesi -</option>
                                                    <option value="dokter">Dokter</option>
                                                    <option value="perawat">Perawat</option>
                                                    <option value="apoteker">Apoteker</option>
                                                    <option value="asisten_apoteker">Asisten Apoteker</option>
                                                    <option value="radiografer">Radiografer</option>
                                                    <option value="analis_lab">Analis Lab</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nomor SIP</label>
                                                <input type="text" name="sip_number" class="form-control"
                                                    placeholder="SIP-xxx/.." value="{{ old('sip_number') }}">
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Kadaluarsa SIP</label>
                                                <input type="date" name="sip_expiry_date" class="form-control"
                                                    value="{{ old('sip_expiry_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Nomor STR</label>
                                                <input type="text" name="str_number" class="form-control"
                                                    placeholder="STR-xxx/.." value="{{ old('str_number') }}">
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Kadaluarsa STR</label>
                                                <input type="date" name="str_expiry_date" class="form-control"
                                                    value="{{ old('str_expiry_date') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row gx-3">
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Upload Berkas SIP (PDF/JPG/PNG, maks 2MB)</label>
                                                <input type="file" name="sip_file" class="form-control"
                                                    accept="application/pdf,image/*">
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label">Upload Berkas STR (PDF/JPG/PNG, maks 2MB)</label>
                                                <input type="file" name="str_file" class="form-control"
                                                    accept="application/pdf,image/*">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Row ends -->

                                </div>


                            </div>
                            <!-- Tab content ends -->

                        </div>
                        <!-- Custom tabs ends -->

                        <!-- Card acrions starts -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="btn-update">
                                <span class="btn-txt">Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </div>
                        <!-- Card acrions ends -->
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

    <!-- Dropzone JS -->
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>

    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/validations.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            function mapRoleToProfession(role) {
                switch (parseInt(role)) {
                    case 2:
                        return 'dokter';
                    case 3:
                        return 'perawat';
                    case 7:
                        return 'apoteker';
                    default:
                        return '';
                }
            }
            const roleSelect = $("select[name='role']");
            const profSelect = $("select[name='profession']");

            function syncProfession() {
                const mapped = mapRoleToProfession(roleSelect.val());
                if (mapped) {
                    profSelect.val(mapped).prop('disabled', true);
                } else {
                    profSelect.prop('disabled', false);
                }
            }
            roleSelect.on('change', syncProfession);
            syncProfession();
            $('.select2').select2({
                placeholder: "Pilih Poliklinik",
                allowClear: true
            });
            $("#submit").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-update").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });
        });
    </script>
@endpush
