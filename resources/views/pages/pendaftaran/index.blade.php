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
            <div class="card border mb-3">
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
                <div class="card border mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center text-center">
                            <div class="spinner-border text-success me-2" role="status" aria-hidden="true"></div>
                            <strong>Loading...</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border mb-3">
                <div class="card-body">
                    @if (auth()->user()->role == 5)
                        <!-- Card details start -->
                        <div class="text-center">
                            <div class="icon-box md border border-primary rounded-5 mb-2 m-auto">
                                <i class="ri-empathize-line fs-5 text-primary"></i>
                            </div>
                            <h6>No Antrian sekarang</h6>
                            <h3 class="text-primary display-1" id="antrian">{{ $antrian['antrian'] }}</h3>

                            <button type="submit" class="btn btn-primary mt-2" id="btn-next">
                                <i class="ri-arrow-right-double-fill"></i>
                                <span class="btn-txt" id="text-next">Antrian Selanjutnya</span>
                                <span class="spinner-border spinner-border-sm d-none" id="spinner-next"></span>
                            </button>
                            <br>
                            <small class="text-primary">Ada <span class="fw-bold"
                                    id="jumlah">{{ $antrian['jumlah'] }}</span> antrian lagi</small>

                        </div>

                        <!-- Card details end -->
                    @else
                        <h6>Fitur Antrian hanya untuk Hak Akses Akun Pendaftaran</h6>
                    @endif

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
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=Pqiovi6G"></script>
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

            $("#btn-next").click(function() {
                $.ajax({
                    url: "{{ route('pendaftaran.update_antrian') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },

                    success: function(res) {
                        if (res.status == true) {
                            responsiveVoice.speak("Nomor Antrian " + res.antrian.prefix + "-" +
                                res.antrian.nomor +
                                ", ke loket " + res.loket.kode_loket, "Indonesian Female", {
                                    rate: 0.9,
                                    pitch: 1,
                                    volume: 1
                                });
                            $("#antrian").text(res.antrian.prefix + " " + res.antrian.nomor);
                            $("#jumlah").text(res.jumlah);
                        }
                    }
                })
            });
        });
    </script>
@endpush
