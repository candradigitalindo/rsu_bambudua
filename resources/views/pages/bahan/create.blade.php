@extends('layouts.app')
@section('title', 'Buat Stok Perlengkapan Baru')
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
                    <form action="{{ route('bahans.store') }}" method="POST" id="submit">
                        @csrf
                        <!-- Custom tabs starts -->
                        <div class="custom-tabs-container">

                            <!-- Nav tabs starts -->
                            <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA"
                                        role="tab" aria-controls="oneA" aria-selected="true"><i
                                            class="ri-hotel-bed-fill"></i>
                                        Form Stok Perlengkapan Baru</a>
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
                                                <label class="form-label" for="a1">Nama Stok Perlengkapan <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">

                                                    <input type="text" class="form-control" id="a1"
                                                        name="name" value="{{ old('name') }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('name') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Memiliki Expired<span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select class="form-select" id="a7" name="is_expired">
                                                        <option value="">Pilih Status</option>
                                                        <option value="1" {{ old('is_expired') == 1 ? 'selected' : '' }}>YA</option>
                                                        <option value="2" {{ old('is_expired') == 2 ? 'selected' : '' }}>TIDAK </option>
                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('is_expired') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Status<span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <select class="form-select" id="a7" name="is_active">
                                                        <option value="">Pilih Status</option>
                                                        <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>Aktif</option>
                                                        <option value="2" {{ old('is_active') == 2 ? 'selected' : '' }}>Tidak Aktif</option>
                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('is_active') }}</p>
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
                            <a href="{{ route('bahans.index') }}" class="btn btn-outline-secondary">
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
