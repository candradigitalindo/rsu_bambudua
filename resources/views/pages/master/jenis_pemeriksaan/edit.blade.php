@extends('layouts.app')

@section('title', 'Edit Jenis Pemeriksaan Penunjang')

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Jenis Pemeriksaan</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('jenis-pemeriksaan.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Pemeriksaan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $item->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe Pemeriksaan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="lab" {{ old('type', $item->type) == 'lab' ? 'selected' : '' }}>
                                    Laboratorium
                                </option>
                                <option value="radiologi" {{ old('type', $item->type) == 'radiologi' ? 'selected' : '' }}>
                                    Radiologi</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control @error('harga') is-invalid @enderror"
                                    id="harga_display" value="{{ old('harga', number_format($item->harga, 0, ',', '.')) }}"
                                    required>
                                <!-- Input tersembunyi untuk menyimpan nilai asli -->
                                <input type="hidden" name="harga" id="harga"
                                    value="{{ old('harga', $item->harga) }}">
                            </div>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('jenis-pemeriksaan.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        $(document).ready(function() {
            function formatRupiah(angka) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return rupiah;
            }

            // Set initial value correctly on page load
            let initialDisplayValue = $('#harga_display').val();
            let initialRealValue = initialDisplayValue.replace(/\./g, '');
            $('#harga').val(initialRealValue);

            $('#harga_display').on('keyup', function() {
                let displayValue = $(this).val();
                let realValue = displayValue.replace(/\./g, '');
                $(this).val(formatRupiah(displayValue));
                $('#harga').val(realValue);
            });
        });
    </script>
@endpush
