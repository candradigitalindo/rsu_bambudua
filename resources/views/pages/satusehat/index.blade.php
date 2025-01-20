@extends('layouts.app')
@section('title', 'Satusehat')
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
                    <form action="{{ route('satusehat.store') }}" method="POST" id="submit">
                        @csrf
                        <!-- Row starts -->
                        <div class="row gx-3">
                            <div class=" col-xxl-6 col-lg-4 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label" for="a3">Status <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-tools-fill"></i>
                                        </span>
                                        <select class="form-select" id="a7" name="status">
                                            @if ($satusehat == null)
                                                <option value="0">Tidak Aktif</option>
                                                <option value="1">Sandbox</option>
                                                <option value="2">Production</option>
                                            @else
                                                <option value="0" {{ $satusehat->status == 0 ? "selected" : "" }}>Tidak Aktif</option>
                                                <option value="1" {{ $satusehat->status == 1 ? "selected" : "" }}>Sandbox</option>
                                                <option value="2" {{ $satusehat->status == 2 ? "selected" : "" }}>Production</option>
                                            @endif

                                        </select>
                                    </div>
                                    <p class="text-danger">{{ $errors->first('status') }}</p>
                                </div>
                            </div>
                            <div class="col-xxl-6 col-lg-4 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label" for="selectGender1">Organization ID<span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-secure-payment-line"></i>
                                        </span>
                                        <input type="text" name="organization_id" class="form-control" id="a4"
                                            value="{{ $satusehat == null ? null : $satusehat->organization_id }}">
                                    </div>
                                    <p class="text-danger">{{ $errors->first('organization_id') }}</p>
                                </div>
                            </div>

                            <div class="col-xxl-6 col-lg-4 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label" for="a5">Client ID <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-secure-payment-line"></i>
                                        </span>
                                        <input name="client_id" type="text" class="form-control" id="a5"
                                            value="{{ $satusehat == null ? null : $satusehat->client_id }}">
                                    </div>
                                    <p class="text-danger">{{ $errors->first('client_id') }}</p>
                                </div>
                            </div>
                            <div class="col-xxl-6 col-lg-4 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label" for="a6">Client Secret <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-secure-payment-line"></i>
                                        </span>
                                        <input name="client_secret" type="text" class="form-control" id="a6"
                                            value="{{ $satusehat == null ? null : $satusehat->client_secret }}">
                                    </div>
                                    <p class="text-danger">{{ $errors->first('client_secret') }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Row ends -->


                        <!-- Card acrions starts -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                SINKRON
                            </a>

                            <button type="submit" class="btn btn-primary" id="btn-update">
                                <span class="btn-txt">SIMPAN</span>
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
