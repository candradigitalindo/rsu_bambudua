@extends('layouts.app')
@section('title', 'Buat Tindakan Baru')
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
                    <form action="{{ route('tindakan.store') }}" method="POST" id="submit">
                        @csrf
                        <!-- Custom tabs starts -->
                        <div class="custom-tabs-container">

                            <!-- Nav tabs starts -->
                            <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA"
                                        role="tab" aria-controls="oneA" aria-selected="true"><i
                                            class="ri-stethoscope-line"></i>
                                        Form Tindakan Baru</a>
                                </li>
                            </ul>
                            <!-- Nav tabs ends -->

                            <!-- Tab content starts -->
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="oneA" role="tabpanel">

                                    <!-- Row starts -->
                                    <div class="row gx-3">
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Nama Tindakan <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">

                                                    <input type="text" class="form-control" id="a1" name="name"
                                                        value="{{ old('name') }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('name') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="harga">Harga Total <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="harga" name="harga"
                                                        value="{{ old('harga') }}" readonly>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('harga') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Status<span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select class="form-select" id="a7" name="status">
                                                        <option value="">Pilih Status</option>
                                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>
                                                            Aktif</option>
                                                        <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>
                                                            Tidak Aktif</option>
                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('status') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <hr class="my-3">
                                            <h6 class="mb-3"><i class="ri-money-dollar-circle-line"></i> Komposisi Harga
                                            </h6>
                                        </div>

                                        <div class="col-xxl-3 col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="honor_dokter"><i
                                                        class="ri-user-heart-line text-primary"></i> Honor Dokter</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control price-input" id="honor_dokter"
                                                        name="honor_dokter" value="{{ old('honor_dokter') }}"
                                                        placeholder="0">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('honor_dokter') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="bonus_perawat"><i
                                                        class="ri-nurse-line text-success"></i> Bonus Perawat</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control price-input"
                                                        id="bonus_perawat" name="bonus_perawat"
                                                        value="{{ old('bonus_perawat') }}" placeholder="0">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('bonus_perawat') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="biaya_bahan"><i
                                                        class="ri-test-tube-line text-info"></i> Biaya Bahan</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control price-input"
                                                        id="biaya_bahan" name="biaya_bahan"
                                                        value="{{ old('biaya_bahan') }}" placeholder="0">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('biaya_bahan') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-3 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="jasa_sarana"><i
                                                        class="ri-hospital-line text-warning"></i> Jasa Sarana RS</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control price-input"
                                                        id="jasa_sarana" name="jasa_sarana"
                                                        value="{{ old('jasa_sarana') }}" placeholder="0">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('jasa_sarana') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-xxl-12 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a2">Keterangan</label>
                                                <div class="input-group">

                                                    <textarea name="description" class="form-control" id="description" cols="10" rows="5">{{ old('description') }}</textarea>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('description') }}</p>
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
                            <a href="{{ route('tindakan.index') }}" class="btn btn-outline-secondary">
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

            // Format rupiah untuk semua input price
            $('.price-input').each(function() {
                $(this).on('keyup', function() {
                    this.value = formatRupiah(this.value);
                    calculateTotal();
                });
            });

            function formatRupiah(angka, prefix) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
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

            function calculateTotal() {
                var honor = parseInt($('#honor_dokter').val().replace(/\./g, '') || 0);
                var bonus = parseInt($('#bonus_perawat').val().replace(/\./g, '') || 0);
                var bahan = parseInt($('#biaya_bahan').val().replace(/\./g, '') || 0);
                var sarana = parseInt($('#jasa_sarana').val().replace(/\./g, '') || 0);
                var total = honor + bonus + bahan + sarana;
                $('#harga').val(formatRupiah(total.toString()));
            }

        });
    </script>
@endpush
