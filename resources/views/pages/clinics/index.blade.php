@extends('layouts.app')
@section('title', 'Data Poliklinik')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('clinics.store') }}" method="POST" id="submit">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="clinic_nama">Nama Poliklinik <span
                                    class="text-danger">*</span></label>
                            <input name="nama" type="text" class="form-control" id="clinic_nama"
                                value="{{ old('nama') }}">
                            <p class="text-danger">{{ $errors->first('nama') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="clinic_alamat">Alamat</label>
                            <input name="alamat" type="text" class="form-control" id="clinic_alamat"
                                value="{{ old('alamat') }}">
                            <p class="text-danger">{{ $errors->first('alamat') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="clinic_telepon">Telepon</label>
                            <input name="telepon" type="text" class="form-control" id="clinic_telepon"
                                value="{{ old('telepon') }}">
                            <p class="text-danger">{{ $errors->first('telepon') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="clinic_deskripsi">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" id="clinic_deskripsi">{{ old('deskripsi') }}</textarea>
                            <p class="text-danger">{{ $errors->first('deskripsi') }}</p>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary" id="btn-update">
                                <span class="btn-txt">SIMPAN</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Telepon</th>
                                        <th>Deskripsi</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clinics as $clinic)
                                        <tr>
                                            <td>{{ $clinic->nama }}</td>
                                            <td>{{ $clinic->alamat }}</td>
                                            <td>{{ $clinic->telepon }}</td>
                                            <td>{{ $clinic->deskripsi }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('clinics.show', $clinic->id) }}"
                                                    class="btn btn-info btn-sm">Detail</a>
                                                <form action="{{ route('clinics.destroy', $clinic->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Hapus data ini?')">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Data tidak ada</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
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
