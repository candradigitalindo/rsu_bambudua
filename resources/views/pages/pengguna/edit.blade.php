@extends('layouts.app')
@section('title', 'Edit Pengguna')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pengguna.store') }}" method="POST" id="submit">
                        @csrf
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
                                                        @foreach ($data['spesialis'] as $s)
                                                            <option value="{{ $s->kode }}" {{ $data['user']->role == $s->kode ? "selected" : "" }}>{{ $s->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('role') }}</p>
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
    <script>
        $(document).ready(function() {
            $("#submit").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-update").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });
        });
    </script>
@endpush
