@extends('layouts.app')
@section('title', 'Jenis Jaminan')
@push('style')
    <!-- Scrollbar CSS -->

    <!-- Uploader CSS -->
@endpush
@section('content')
    <div class="row gx-3">
        <!-- Row starts -->

        <div class="col-sm-12 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('discounts.update') }}" method="POST" id="submit">
                        @csrf
                        <!-- Row starts -->
                        <div class="row gx-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="diskon-tindakan-persen">Maksimal Diskon Tindakan (%)</label>
                                    <div class="input-group">
                                        <input name="diskon_tindakan" type="number" class="form-control" id="diskon-tindakan-persen"
                                            value="{{ old('diskon_tindakan') ?? $discounts->diskon_tindakan }}">
                                        <div class="input-group-text">%</div>
                                    </div>
                                    <p class="text-danger">{{ $errors->first('diskon_tindakan') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="diskon-tindakan-nominal">Maksimal Diskon Tindakan (Rp)</label>
                                <div class="input-group">
                                    <div class="input-group-text">Rp</div>
                                    <input name="diskon_tindakan_nominal" type="number" class="form-control"
                                        id="diskon-tindakan-nominal"
                                        value="{{ old('diskon_tindakan_nominal') ?? $discounts->diskon_tindakan_nominal }}">
                                </div>
                                <p class="text-danger">{{ $errors->first('diskon_tindakan_nominal') }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="diskon-resep-persen">Maksimal Diskon Resep (%)</label>
                                    <div class="input-group">
                                        <input name="diskon_resep" type="number" class="form-control" id="diskon-resep-persen"
                                            value="{{ old('diskon_resep') ?? $discounts->diskon_resep }}">
                                        <div class="input-group-text">%</div>
                                    </div>
                                    <p class="text-danger">{{ $errors->first('diskon_resep') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="diskon-resep-nominal">Maksimal Diskon Resep (Rp)</label>
                                <div class="input-group">
                                    <div class="input-group-text">Rp</div>
                                    <input name="diskon_resep_nominal" type="number" class="form-control"
                                        id="diskon-resep-nominal"
                                        value="{{ old('diskon_resep_nominal') ?? $discounts->diskon_resep_nominal }}">
                                </div>
                                <p class="text-danger">{{ $errors->first('diskon_resep_nominal') }}</p>
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

    </div>
    <!-- Row ends -->
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->

    <!-- Dropzone JS -->

    <!-- Custom JS files -->
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
