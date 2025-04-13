@extends('layouts.app')
@section('title', 'Bahan Tindakan')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endpush
@section('content')
    <div class="row gx-3">
        <!-- Row starts -->

        <div class="col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('tindakan.storeBahan', $tindakan->id) }}" method="POST" id="submit">
                        @csrf
                        <!-- Row starts -->
                        <div class="row gx-3">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="a5">Nama Bahan Tindakan <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control" name="bahan" id="exampleDataList"
                                            >
                                            <option value="">Pilih Bahan Tindakan</option>
                                            @foreach ($bahans as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <p class="text-danger">{{ $errors->first('bahan') }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="a5">Jumlah <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input name="quantity" type="text" class="form-control" id="a5"
                                            value="{{ old('quantity') }}">
                                    </div>
                                    <p class="text-danger">{{ $errors->first('quantity') }}</p>
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
                                        <th>Nama Bahan</th>
                                        <th>Jumlah</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tindakan->bahan as $p)
                                        <tr>
                                            <td>
                                                {{ $p->name }}
                                            </td>
                                            <td>
                                                {{ $p->pivot->quantity }}
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('tindakan.destroyBahan', $p->pivot->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                    </button>
                                                </form>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#submit").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-update").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });

            // Initialize Select2
            $('#exampleDataList').select2({
                placeholder: 'Pilih Bahan Tindakan',
                allowClear: true,
                widhth: '300%',

            });
        });
    </script>
@endpush
