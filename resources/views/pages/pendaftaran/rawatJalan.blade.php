@extends('layouts.app')
@section('title', 'Data Pekerjaan')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <!-- Row starts -->

        <div class="col-sm-8 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <!-- Row starts -->
                    <div class="row gx-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="a5">Cari Pasien <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input name="name" type="text" class="form-control" id="search"
                                        placeholder="Cari Nama, RM, RM Lama, No HP, KTP">
                                </div>
                                <p class="text-danger">{{ $errors->first('name') }}</p>
                                <div class="d-flex gap-2 justify-content-end mt-2">
                                        <button type="submit" class="btn btn-primary" id="btn-update">
                                            <i class="ri-search-line"></i>
                                            <span class="btn-txt">Cari Pasien</span>
                                            <span class="spinner-border spinner-border-sm d-none"></span>
                                        </button>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row ends -->
                    <!-- Card acrions starts -->
                    <!-- Card acrions ends -->

                </div>
            </div>
            <div id="data">

            </div>
            <div id="loading">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center text-center">
                            <div class="spinner-border text-success me-2" role="status" aria-hidden="true"></div>
                            <strong>Loading...</strong>
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

    <!-- Dropzone JS -->
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>

    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/validations.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#loading').hide();
            $("#submit").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-update").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });

            $(document).on('keyup', '#search', function() {
                var query = $(this).val();
                if (query.length >= 2) {
                    $('#loading').show();
                    $.ajax({
                        url: "{{ route('pendaftaran.caripasien') }}",
                        method: 'GET',
                        data: {
                            q: query
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#data').hide();
                            setTimeout(function() {
                                $('#loading').hide();
                                $('#data').show();
                                $('#data').html(data);
                            }, 1000);

                        }
                    })
                } else {
                    $('#loading').hide();
                    $('#data').hide();
                }
            });
        });
    </script>
@endpush
