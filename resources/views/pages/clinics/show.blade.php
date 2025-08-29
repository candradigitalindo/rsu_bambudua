{{-- filepath: /Users/candrasyahputra/PROJEK-APLIKASI/bambudua/resources/views/pages/clinics/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Poliklinik')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-sm-8 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('clinics.update', $clinic->id) }}" method="POST" id="submit">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label" for="clinic_nama">Nama Poliklinik <span
                                    class="text-danger">*</span></label>
                            <input name="nama" type="text" class="form-control" id="clinic_nama"
                                value="{{ old('nama', $clinic->nama) }}">
                            <p class="text-danger">{{ $errors->first('nama') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="clinic_alamat">Alamat</label>
                            <input name="alamat" type="text" class="form-control" id="clinic_alamat"
                                value="{{ old('alamat', $clinic->alamat) }}">
                            <p class="text-danger">{{ $errors->first('alamat') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="clinic_telepon">Telepon</label>
                            <input name="telepon" type="text" class="form-control" id="clinic_telepon"
                                value="{{ old('telepon', $clinic->telepon) }}">
                            <p class="text-danger">{{ $errors->first('telepon') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="clinic_deskripsi">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" id="clinic_deskripsi">{{ old('deskripsi', $clinic->deskripsi) }}</textarea>
                            <p class="text-danger">{{ $errors->first('deskripsi') }}</p>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary" id="btn-update">
                                <span class="btn-txt">UPDATE</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                            <a href="{{ route('clinics.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
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
