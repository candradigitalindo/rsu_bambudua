@extends('layouts.app')
@section('title', 'Data Loket')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <!-- Row starts -->

        <div class="col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('loket.store') }}" method="POST" id="submit">
                        @csrf
                        <!-- Row starts -->
                        <div class="row gx-3">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="a5">Kode Loket <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input name="kode_loket" type="text" class="form-control" id="a5"
                                            value="{{ old('kode_loket') }}">
                                    </div>
                                    <p class="text-danger">{{ $errors->first('kode_loket') }}</p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="a5">Keterangan </label>
                                    <div class="input-group">
                                        <input name="keterangan" type="text" class="form-control" id="a5"
                                            value="{{ old('keterangan') }}">
                                    </div>
                                    <p class="text-danger">{{ $errors->first('keterangan') }}</p>
                                </div>
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
        <div class="col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Kode Loket</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lokets as $p)
                                        <tr>
                                            <td>
                                                {{ $p->kode_loket }}
                                            </td>
                                            <td>
                                                {{ $p->keterangan }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('loket.destroy', $p->id) }}" class="btn btn-danger btn-sm" data-confirm-delete="true">Hapus</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Data tidak ada</td>
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
