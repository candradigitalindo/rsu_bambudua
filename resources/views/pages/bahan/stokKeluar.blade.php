@extends('layouts.app')
@section('title', 'Buat Stok Keluar')
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
                    <form action="{{ route('bahan.stokKeluar', $bahan->id) }}" method="POST" id="submit">
                        @csrf
                        <!-- Custom tabs starts -->
                        <div class="custom-tabs-container">

                            <!-- Nav tabs starts -->
                            <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA"
                                        role="tab" aria-controls="oneA" aria-selected="true"><i
                                            class="ri-archive-line"></i>
                                        Form Stok Keluar</a>
                                </li>
                            </ul>
                            <!-- Nav tabs ends -->

                            <!-- Tab content starts -->
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                                    <div class="card border mt-3">
                                        <div class="card-body">
                                            <span class="badge bg-primary-subtle rounded-pill text-primary">
                                                <i class="ri-circle-fill me-1"></i>Status : {{ $bahan->is_active == 1 ? 'Aktif' : 'Tidak Aktif' }}<a
                                                    id="status-item"></a></span>
                                            <hr>
                                            <div class="row justify-content-between">
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Identitas Item
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>Nama Item</td>
                                                            <td>:</td>
                                                            <td id="no_rm_rawatJalan">{{ ucwords($bahan->name) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Memiliki Expired</td>
                                                            <td>:</td>
                                                            <td id="is_expired_rawatJalan">{{ $bahan->is_expired == 1 ? 'YA' : 'TIDAK' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Jumlah Stok</td>
                                                            <td>:</td>
                                                            <td id="jumlah_stok_rawatJalan">{{ $bahan->getStockQuantityAttribute() }}</td>
                                                        </tr>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- Row starts -->
                                    <div class="row gx-3 mt-3">
                                        <div class="col-xxl-6 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Jumlah <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">

                                                    <input type="text" class="form-control" id="a1"
                                                        name="quantity" value="{{ old('quantity') }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('quantity') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="expired_at">Tanggal Pengimputan Barang</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control" name="created_at" id="created_at" value="{{ old('created_at') }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('created_at') }}</p>
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
