@extends('layouts.app')
@section('title', 'Jenis Jaminan')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <!-- Row starts -->

        <div class="col-sm-12 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('discounts.update') }}" method="POST" id="submit">
                        @csrf
                        <!-- Row starts -->
                        <div class="row gx-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="a5">Maksimal Diskon Tindakan (%)</label>
                                    <div class="input-group">
                                        <input name="diskon_tindakan" type="number" class="form-control" id="a5"
                                            value="{{ old('diskon_tindakan') ?? $discounts->diskon_tindakan }}">
                                        <div class="input-group-text">%</div>
                                    </div>
                                    <p class="text-danger">{{ $errors->first('diskon_tindakan') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="a7">Maksimal Diskon Resep (%)</label>
                                <div class="input-group">
                                    <input name="diskon_resep" type="number" class="form-control" id="a7"
                                        value="{{ old('diskon_resep') ?? $discounts->diskon_resep }}">
                                    <div class="input-group-text">%</div>
                                </div>
                                <p class="text-danger">{{ $errors->first('diskon_resep') }}</p>
                            </div>
                        </div>
                        <!-- Row ends -->


                        <!-- Card acrions starts -->
                        <div class="d-flex gap-2 justify-content-end mt-2">
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
    <!-- Row ends -->
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
