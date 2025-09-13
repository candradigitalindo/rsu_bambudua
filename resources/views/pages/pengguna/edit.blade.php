@extends('layouts.app')
@section('title', 'Edit Pengguna')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #198754;
            border-radius: 0.375rem;
            padding: 0.375rem 0.5rem;
            background: #fff !important;
            color: #212529 !important;
        }

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
                    <form action="{{ route('pengguna.update', $data['user']->id) }}" method="POST" id="submit">
                        @csrf
                        @method('PUT')
                        <!-- Custom tabs starts -->
                        <div class="custom-tabs-container">

                            <!-- Nav tabs starts -->
                            <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA"
                                        role="tab" aria-controls="oneA" aria-selected="true"><i
                                            class="ri-account-pin-circle-line"></i>
                                        Form Edit Pengguna</a>
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
                                                        value="{{ $data['user']->name }}">
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
                                                        id="a2" value="{{ $data['user']->username }}">
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
                                                        <option value="1"
                                                            {{ $data['user']->role == '1' ? 'selected' : '' }}>Owner
                                                        </option>
                                                        <option value="2"
                                                            {{ $data['user']->role == '2' ? 'selected' : '' }}>Dokter
                                                        </option>
                                                        <option value="3"
                                                            {{ $data['user']->role == '3' ? 'selected' : '' }}>Perawat
                                                        </option>
                                                        <option value="4"
                                                            {{ $data['user']->role == '4' ? 'selected' : '' }}>Admin
                                                        </option>
                                                        <option value="5"
                                                            {{ $data['user']->role == '5' ? 'selected' : '' }}>Pendaftaran
                                                        </option>
                                                        <option value="6"
                                                            {{ $data['user']->role == '6' ? 'selected' : '' }}>Keuangan
                                                        </option>
                                                        <option value="7"
                                                            {{ $data['user']->role == '7' ? 'selected' : '' }}>Apotek
                                                        </option>
                                                        <option value="8"
                                                            {{ $data['user']->role == '8' ? 'selected' : '' }}>Gudang
                                                        </option>
                                                        <option value="9"
                                                            {{ $data['user']->role == '9' ? 'selected' : '' }}>Teknisi
                                                        </option>
                                                        <option value="10"
                                                            {{ $data['user']->role == '10' ? 'selected' : '' }}>Kasir
                                                        </option>
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
                                                        <option value="">Pilih Hak Akses</option>
                                                        @foreach ($data['spesialis'] as $s)
                                                            <option value="{{ $s->kode }}"
                                                                {{ $data['user']->profile->spesialis == $s->kode ? 'selected' : '' }}>
                                                                {{ $s->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('spesialis') }}</p>
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

                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="poliklinik">Poliklinik <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select select2" id="poliklinik" name="poliklinik[]"
                                                    multiple style="width: 100%;">
                                                    @foreach ($clinics as $c)
                                                        <option value="{{ $c->id }}"
                                                            {{ (isset($data['user']) && $data['user']->clinics->contains($c->id)) || collect(old('poliklinik'))->contains($c->id) ? 'selected' : '' }}>
                                                            {{ $c->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <p class="text-danger">{{ $errors->first('poliklinik') }}</p>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/validations.js') }}"></script>
    <script>
        $(document).ready(function() {
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
