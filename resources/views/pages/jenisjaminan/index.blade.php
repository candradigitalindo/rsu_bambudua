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

        <div class="col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('jenisjaminan.store') }}" method="POST" id="submit">
                        @csrf
                        <!-- Row starts -->
                        <div class="row gx-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="a5">Nama Jaminan <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-file-shield-2-line"></i>
                                        </span>
                                        <input name="name" type="text" class="form-control" id="a5"
                                            value="{{ old('name') }}">
                                    </div>
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="a7">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri-file-shield-2-line"></i>
                                    </span>
                                    <select class="form-select" id="a7" name="status">
                                        <option value="">Pilih Status</option>
                                        <option value="1" {{ old('status' == 1 ? 'selected' : '') }}>Aktif</option>
                                        <option value="2" {{ old('status' == 2 ? 'selected' : '') }}>Non Aktif</option>
                                    </select>

                                </div>
                                <p class="text-danger">{{ $errors->first('status') }}</p>
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
                                        <th>Nama Jaminan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jaminans as $j)
                                        <tr>
                                            <td>
                                                {{ $j->name }}
                                            </td>
                                            <td class="text-center">
                                                @if ($j->status == 1)
                                                    <span class="badge border border-primary text-primary">Aktif</span>
                                                @else
                                                    <span class="badge border border-danger text-danger">Non Aktif</span>
                                                @endif

                                            </td>
                                            <td class="text-center">
                                                @if ($j->status == 1)
                                                    <a href="{{ route('jenisjaminan.edit', $j->id) }}" class="btn btn-info btn-sm">Non Aktif</a>
                                                @else
                                                    <a href="{{ route('jenisjaminan.edit', $j->id) }}" class="btn btn-primary btn-sm">Aktif</a>
                                                @endif

                                                <a href="{{ route('jenisjaminan.destroy', $j->id) }}" class="btn btn-danger btn-sm" data-confirm-delete="true">Hapus</a>
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
