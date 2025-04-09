@extends('layouts.app')
@section('title', 'Edit Tindakan')
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
                    <form action="{{ route('tindakan.update', $tindakan->id) }}" method="POST" id="submit">
                        @csrf
                        @method('PUT')
                        <!-- Custom tabs starts -->
                        <div class="custom-tabs-container">

                            <!-- Nav tabs starts -->
                            <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA"
                                        role="tab" aria-controls="oneA" aria-selected="true"><i
                                            class="ri-hotel-bed-fill"></i>
                                        Form Tindakan Edit</a>
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

                                                    <input type="text" class="form-control" id="a1"
                                                        name="name" value="{{ $tindakan->name }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('name') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="harga">Harga <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">

                                                    <input type="text" class="form-control" id="harga" name="harga"
                                                        value="{{ $tindakan->getHargaFormattedAttribute() }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('harga') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Status<span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select class="form-select" id="a7" name="status">
                                                        <option value="">Pilih Status</option>
                                                        <option value="1" {{ old('status', $tindakan->status) == 1 ? 'selected' : '' }}>Aktif</option>
                                                        <option value="2" {{ old('status', $tindakan->status) == 2 ? 'selected' : '' }}>Tidak Aktif</option>
                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('status') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-xxl-12 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a2">Keterangan</label>
                                                <div class="input-group">

                                                    <textarea name="description" class="form-control" id="description" cols="10" rows="5">{{ $tindakan->description }}</textarea>
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

            var tanpa_rupiah = document.getElementById('harga');
            tanpa_rupiah.addEventListener('keyup', function(e) {
                tanpa_rupiah.value = formatRupiah(this.value);
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

        });
    </script>
@endpush
